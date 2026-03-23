(function ($, Drupal, drupalSettings) {
    'use strict';

    if (!Drupal.AjaxCommands || !Drupal.AjaxCommands.prototype) {
        return;
    }

    const proto = Drupal.AjaxCommands.prototype;

    if (proto.openDialog.__appointmentDialogStackPatched) {
        return;
    }

    const originalOpenDialog = proto.openDialog;
    const originalCloseDialog = proto.closeDialog;
    const STACK_KEY = 'dialogStack';
    const BASE_Z_INDEX = 1260;

    function getStack() {
        if (!Array.isArray(drupalSettings[STACK_KEY])) {
            drupalSettings[STACK_KEY] = [];
        }

        return drupalSettings[STACK_KEY];
    }

    function getDialogElement(selector) {
        if (!selector) {
            return $();
        }

        try {
            return $(selector);
        }
        catch (error) {
            return $();
        }
    }

    function getDialogWidget($dialog) {
        if (!$dialog.length || typeof $dialog.dialog !== 'function') {
            return $();
        }

        try {
            return $dialog.dialog('widget');
        }
        catch (error) {
            return $();
        }
    }

    function isDialogRecordUsable(record) {
        if (!record || !record.selector) {
            return false;
        }

        const $dialog = getDialogElement(record.selector);
        const $widget = getDialogWidget($dialog);
        return $dialog.length > 0 && $widget.length > 0;
    }

    function pruneStack() {
        const stack = getStack();

        for (let index = stack.length - 1; index >= 0; index -= 1) {
            if (!isDialogRecordUsable(stack[index])) {
                stack.splice(index, 1);
            }
        }
    }

    function removeFromStack(selector) {
        const stack = getStack();
        const index = stack.findIndex((record) => record.selector === selector);

        if (index !== -1) {
            stack.splice(index, 1);
        }

        return index;
    }

    function captureOverlayElement() {
        const $overlays = $('.ui-widget-overlay');
        return $overlays.length ? $overlays.last() : $();
    }

    function bindDialogLifecycle(record) {
        const $dialog = getDialogElement(record.selector);

        if (!$dialog.length) {
            return;
        }

        $dialog.off('.appointmentDialogStack');
        $dialog.on('dialogclose.appointmentDialogStack', function () {
            removeFromStack(record.selector);
            syncStack();
            focusTopDialog();
        });
    }

    function syncStack() {
        pruneStack();

        const stack = getStack();
        stack.forEach((record, index) => {
            const $dialog = getDialogElement(record.selector);
            const $widget = getDialogWidget($dialog);
            const zIndex = BASE_Z_INDEX + (index * 2);

            if ($widget.length) {
                $widget.css('z-index', zIndex + 1);
                $widget.attr('data-dialog-stack-index', index);
            }

            if (record.modal) {
                const $overlay = record.overlaySelector ? $(record.overlaySelector) : $();
                if ($overlay.length) {
                    $overlay.css('z-index', zIndex);
                    $overlay.attr('data-dialog-stack-index', index);
                }
            }
        });
    }

    function focusTopDialog() {
        pruneStack();

        const stack = getStack();
        if (!stack.length) {
            return;
        }

        const top = stack[stack.length - 1];
        const $dialog = getDialogElement(top.selector);
        const $widget = getDialogWidget($dialog);

        if ($widget.length) {
            $widget.trigger('focus');
        }
    }

    proto.openDialog = function (ajax, response, status) {
        pruneStack();

        const selector = response && response.selector ? response.selector : null;
        const modal = !response || !response.dialogOptions || response.dialogOptions.modal !== false;

        if (selector) {
            removeFromStack(selector);
        }

        originalOpenDialog.call(this, ajax, response, status);

        if (!selector) {
            syncStack();
            return;
        }

        const $dialog = getDialogElement(selector);
        const $widget = getDialogWidget($dialog);

        if (!$dialog.length || !$widget.length) {
            syncStack();
            return;
        }

        const record = {
            selector,
            modal,
            overlaySelector: null,
        };

        if (modal) {
            const $overlay = captureOverlayElement();
            if ($overlay.length) {
                const overlayId = $overlay.attr('id') || ('appointment-dialog-overlay-' + Date.now() + '-' + Math.round(Math.random() * 10000));
                $overlay.attr('id', overlayId);
                record.overlaySelector = '#' + overlayId;
            }
        }

        getStack().push(record);
        bindDialogLifecycle(record);
        syncStack();
        focusTopDialog();
    };

    proto.closeDialog = function (ajax, response, status) {
        const selector = response && response.selector ? response.selector : null;

        originalCloseDialog.call(this, ajax, response, status);

        if (selector) {
            removeFromStack(selector);
        }

        syncStack();
        focusTopDialog();
    };

    proto.openDialog.__appointmentDialogStackPatched = true;
})(jQuery, Drupal, drupalSettings);