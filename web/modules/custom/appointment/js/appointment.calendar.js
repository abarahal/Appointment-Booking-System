(function (Drupal, drupalSettings, once) {
    'use strict';

    var SLOT_DURATION = '01:00:00';
    var MIN_TIME = '08:00:00';
    var MAX_TIME = '18:00:00';

    Drupal.behaviors.appointmentCalendar = {
        attach: function (context) {
            var elements = once('appointment-calendar', '#appointment-fullcalendar', context);
            if (!elements.length) {
                return;
            }

            
            var calendarEl = elements[0];
            var settings = drupalSettings.appointmentCalendar || {};
            var adviserEmail = settings.adviserEmail || '';
            var bookedSlotsUrl = settings.bookedSlotsUrl || '';
            var excludeId = settings.excludeId || null;
            var dateInput = document.querySelector('input[name="appointment_date"]');
            var timeInput = document.querySelector('input[name="appointment_time"]');

            if (!dateInput || !timeInput) {
                return;
            }

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                locale: 'fr',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'timeGridWeek,timeGridDay'
                },
                slotDuration: SLOT_DURATION,
                slotMinTime: MIN_TIME,
                slotMaxTime: MAX_TIME,
                allDaySlot: false,
                selectable: true,
                selectMirror: true,
                unselectAuto: true,
                nowIndicator: true,
                height: 'auto',
                validRange: {
                    start: new Date().toISOString().split('T')[0]
                },
                businessHours: {
                    daysOfWeek: [1, 2, 3, 4, 5],
                    startTime: MIN_TIME,
                    endTime: MAX_TIME
                },
                selectConstraint: 'businessHours',
                eventSources: [
                    {
                        id: 'booked',
                        events: function (info, successCallback, failureCallback) {
                            if (!bookedSlotsUrl || !adviserEmail) {
                                successCallback([]);
                                return;
                            }

                            var url = bookedSlotsUrl
                                + '?adviser_email=' + encodeURIComponent(adviserEmail)
                                + '&start=' + encodeURIComponent(info.startStr)
                                + '&end=' + encodeURIComponent(info.endStr);

                            if (excludeId) {
                                url += '&exclude_id=' + encodeURIComponent(excludeId);
                            }

                            fetch(url, {
                                credentials: 'same-origin',
                                headers: { 'Accept': 'application/json' }
                            })
                                .then(function (response) {
                                    if (!response.ok) {
                                        throw new Error('HTTP ' + response.status);
                                    }
                                    return response.json();
                                })
                                .then(function (data) {
                                    var events = (data || []).map(function (slot) {
                                        return {
                                            title: Drupal.t('Unavailable'),
                                            start: slot.start,
                                            end: slot.end,
                                            display: 'background',
                                            color: '#e74c3c',
                                            classNames: ['appointment-booked-slot']
                                        };
                                    });
                                    successCallback(events);
                                })
                                .catch(function () {
                                    failureCallback();
                                });
                        }
                    }
                ],
                select: function (info) {
                    var start = info.start;
                    var pad = function (n) { return n < 10 ? '0' + n : '' + n; };
                    var dateStr = start.getFullYear() + '-' + pad(start.getMonth() + 1) + '-' + pad(start.getDate());
                    var timeStr = pad(start.getHours()) + ':' + pad(start.getMinutes());

                    dateInput.value = dateStr;
                    timeInput.value = timeStr;

                    var existingSelected = calendarEl.querySelectorAll('.appointment-selected-slot');
                    existingSelected.forEach(function (el) { el.remove(); });

                    calendar.addEvent({
                        title: Drupal.t('Your selection'),
                        start: info.start,
                        end: info.end || new Date(info.start.getTime() + 3600000),
                        color: '#2ecc71',
                        classNames: ['appointment-selected-slot']
                    });

                    updateSelectionDisplay(dateStr, timeStr);
                },
                eventDidMount: function (info) {
                    if (info.event.extendedProps && info.event.display === 'background') {
                        info.el.title = Drupal.t('This slot is unavailable');
                    }
                }
            });

            calendar.render();

            if (dateInput.value && timeInput.value) {
                var initDate = new Date(dateInput.value + 'T' + timeInput.value + ':00');
                if (!isNaN(initDate.getTime())) {
                    calendar.gotoDate(initDate);
                    calendar.addEvent({
                        title: Drupal.t('Your selection'),
                        start: initDate,
                        end: new Date(initDate.getTime() + 3600000),
                        color: '#2ecc71',
                        classNames: ['appointment-selected-slot']
                    });
                }
            }

            function updateSelectionDisplay(date, time) {
                var display = document.getElementById('appointment-selection-display');
                if (display) {
                    display.textContent = Drupal.t('Selected: @date at @time', {
                        '@date': date,
                        '@time': time
                    });
                    display.classList.add('appointment-selection-confirmed');
                }
            }
        }
    };
})(Drupal, drupalSettings, once);
