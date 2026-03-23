<?php

declare(strict_types=1);

namespace Drupal\appointment\Form;

use Drupal\appointment\Entity\AgencyEntity;
use Drupal\appointment\Entity\AppointmentEntity;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Multi-step booking and modification form.
 */
class AppointmentBookForm extends FormBase
{

    /**
     * Total number of booking steps.
     */
    protected const TOTAL_STEPS = 6;

    /**
     * {@inheritdoc}
     */
    public function getFormId(): string
    {
        return 'appointment_book_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state): array
    {
        $step = $this->getCurrentStep();
        $route_appointment = \Drupal::routeMatch()->getParameter('appointment');
        $appointment = $route_appointment instanceof AppointmentEntity ? $route_appointment : NULL;
        $store_key = $this->getStoreKey($appointment);
        $tempstore = \Drupal::service('tempstore.private')->get('appointment_booking');
        $data = $tempstore->get($store_key) ?? [];

        if ($appointment && empty($data)) {
            $data = $this->seedDataFromAppointment($appointment);
            $tempstore->set($store_key, $data);
        }

        $form['step'] = [
            '#type' => 'value',
            '#value' => $step,
        ];

        $form['store_key'] = [
            '#type' => 'value',
            '#value' => $store_key,
        ];

        $form['appointment_id'] = [
            '#type' => 'value',
            '#value' => $appointment?->id(),
        ];

        $form['progress'] = [
            '#type' => 'markup',
            '#markup' => '<div class="appointment-progress">Step ' . $step . ' / ' . self::TOTAL_STEPS . '</div>',
        ];

        $form['wizard'] = [
            '#type' => 'container',
            '#attributes' => ['class' => ['appointment-wizard-step']],
        ];

        switch ($step) {
            case 1:
                $form['wizard']['agency'] = [
                    '#type' => 'select',
                    '#title' => $this->t('Choose agency'),
                    '#required' => TRUE,
                    '#options' => $this->loadAgencyOptions(),
                    '#default_value' => $data['agency'] ?? NULL,
                ];
                break;

            case 2:
                $form['wizard']['appointment_type'] = [
                    '#type' => 'select',
                    '#title' => $this->t('Choose appointment type'),
                    '#required' => TRUE,
                    '#options' => $this->loadTypeOptions(),
                    '#default_value' => $data['appointment_type'] ?? NULL,
                ];
                break;

            case 3:
                $agency_id = isset($data['agency']) ? (int) $data['agency'] : 0;
                $form['wizard']['adviser_email'] = [
                    '#type' => 'select',
                    '#title' => $this->t('Choose adviser'),
                    '#required' => TRUE,
                    '#options' => $this->loadAdviserOptions($agency_id),
                    '#default_value' => $data['adviser_email'] ?? NULL,
                    '#empty_option' => $this->t('- Select -'),
                ];
                break;

            case 4:
                $adviser_email = (string) ($data['adviser_email'] ?? '');
                $exclude_id = $appointment?->id() ? (int) $appointment->id() : NULL;

                $form['wizard']['calendar_container'] = [
                    '#type' => 'markup',
                    '#markup' => '<div id="appointment-selection-display">' . $this->t('Click a time slot on the calendar to select your appointment.') . '</div><div id="appointment-fullcalendar"></div>',
                ];
                $form['wizard']['appointment_date'] = [
                    '#type' => 'hidden',
                    '#default_value' => $data['appointment_date'] ?? '',
                    '#attributes' => ['id' => 'edit-appointment-date'],
                ];
                $form['wizard']['appointment_time'] = [
                    '#type' => 'hidden',
                    '#default_value' => $data['appointment_time'] ?? '',
                    '#attributes' => ['id' => 'edit-appointment-time'],
                ];
                $form['#attached']['library'][] = 'appointment/appointment.calendar';
                $form['#attached']['drupalSettings']['appointmentCalendar'] = [
                    'adviserEmail' => $adviser_email,
                    'bookedSlotsUrl' => \Drupal\Core\Url::fromRoute('appointment.api_booked_slots')->toString(),
                    'excludeId' => $exclude_id,
                ];
                break;

            case 5:
                $form['wizard']['client_name'] = [
                    '#type' => 'textfield',
                    '#title' => $this->t('Full name'),
                    '#required' => TRUE,
                    '#default_value' => $data['client_name'] ?? '',
                    '#maxlength' => 255,
                ];
                $form['wizard']['client_email'] = [
                    '#type' => 'email',
                    '#title' => $this->t('Email'),
                    '#required' => TRUE,
                    '#default_value' => $data['client_email'] ?? '',
                ];
                $form['wizard']['client_phone'] = [
                    '#type' => 'textfield',
                    '#title' => $this->t('Phone'),
                    '#default_value' => $data['client_phone'] ?? '',
                    '#maxlength' => 64,
                ];
                $form['wizard']['notes'] = [
                    '#type' => 'textarea',
                    '#title' => $this->t('Notes'),
                    '#default_value' => $data['notes'] ?? '',
                ];
                break;

            case 6:
                $form['wizard']['summary'] = [
                    '#type' => 'item',
                    '#title' => $this->t('Confirm your appointment'),
                    '#markup' => $this->buildSummaryMarkup($data),
                ];
                break;
        }

        $form['actions'] = ['#type' => 'actions'];

        if ($step > 1) {
            $form['actions']['previous'] = [
                '#type' => 'submit',
                '#value' => $this->t('Previous'),
                '#name' => 'previous',
                '#limit_validation_errors' => [],
            ];
        }

        if ($step < self::TOTAL_STEPS) {
            $form['actions']['next'] = [
                '#type' => 'submit',
                '#value' => $this->t('Next'),
                '#name' => 'next',
            ];
        } else {
            $form['actions']['confirm'] = [
                '#type' => 'submit',
                '#value' => $appointment ? $this->t('Confirm changes') : $this->t('Confirm appointment'),
                '#name' => 'confirm',
            ];
        }

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state): void
    {
        $step = (int) $form_state->getValue('step');
        $trigger = $form_state->getTriggeringElement()['#name'] ?? '';

        if ($trigger === 'previous') {
            return;
        }

        if ($step === 4) {
            $date = (string) $form_state->getValue('appointment_date');
            $time = (string) $form_state->getValue('appointment_time');

            if ($date === '' || $time === '') {
                $form_state->setErrorByName('appointment_date', $this->t('Please select a date and time on the calendar.'));
                return;
            }

            if (!preg_match('/^(2[0-3]|[01]\d):[0-5]\d$/', $time)) {
                $form_state->setErrorByName('appointment_time', $this->t('Time must use HH:MM format.'));
                return;
            }

            $start = strtotime($date . ' ' . $time);
            if (!$start) {
                $form_state->setErrorByName('appointment_date', $this->t('Invalid date/time.'));
                return;
            }

            $tempstore = \Drupal::service('tempstore.private')->get('appointment_booking');
            $data = $tempstore->get((string) $form_state->getValue('store_key')) ?? [];
            $adviser_email = (string) ($data['adviser_email'] ?? '');
            if ($adviser_email === '') {
                $form_state->setErrorByName('appointment_time', $this->t('Please select an adviser first.'));
                return;
            }

            $exclude_id = $form_state->getValue('appointment_id') ? (int) $form_state->getValue('appointment_id') : NULL;
            if (!$this->isSlotAvailable($adviser_email, (int) $start, (int) $start + 3600, $exclude_id)) {
                $form_state->setErrorByName('appointment_time', $this->t('This adviser is already booked for the selected time slot.'));
            }
        }

        if ($step === 5) {
            $phone = (string) $form_state->getValue('client_phone');
            if ($phone !== '' && !preg_match('/^[0-9+().\-\s]{6,20}$/', $phone)) {
                $form_state->setErrorByName('client_phone', $this->t('Phone number format is invalid.'));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state): void
    {
        $step = (int) $form_state->getValue('step');
        $trigger = $form_state->getTriggeringElement()['#name'] ?? 'next';
        $store_key = (string) $form_state->getValue('store_key');
        $tempstore = \Drupal::service('tempstore.private')->get('appointment_booking');
        $data = $tempstore->get($store_key) ?? [];

        if ($trigger === 'previous') {
            $form_state->setRedirectUrl($this->stepUrl($form_state, max(1, $step - 1)));
            return;
        }

        $data = $this->storeCurrentStepValues($data, $step, $form_state);
        $tempstore->set($store_key, $data);

        if ($trigger === 'next') {
            $form_state->setRedirectUrl($this->stepUrl($form_state, min(self::TOTAL_STEPS, $step + 1)));
            return;
        }

        $appointment_id = $form_state->getValue('appointment_id') ? (int) $form_state->getValue('appointment_id') : NULL;
        /** @var AppointmentEntity|null $appointment */
        $appointment = $appointment_id ? \Drupal::entityTypeManager()->getStorage('appointment')->load($appointment_id) : NULL;

        if (!$appointment) {
            $appointment = AppointmentEntity::create([
                'uid' => (int) $this->currentUser()->id(),
                'access_token' => hash('sha256', uniqid('appointment_', TRUE)),
            ]);
        }

        $agency_id = (int) ($data['agency'] ?? 0);
        $adviser_email = (string) ($data['adviser_email'] ?? '');
        $adviser_name = $this->loadAdviserName($agency_id, $adviser_email);
        $start = (int) strtotime(((string) ($data['appointment_date'] ?? '')) . ' ' . ((string) ($data['appointment_time'] ?? '')));

        $appointment->set('uid', (int) $this->currentUser()->id());
        $appointment->set('agency', $agency_id);
        $appointment->set('adviser_name', $adviser_name);
        $appointment->set('adviser_email', $adviser_email);
        $appointment->set('appointment_type', (int) ($data['appointment_type'] ?? 0));
        $appointment->set('start_time', $start);
        $appointment->set('end_time', $start + 3600);
        $appointment->set('client_name', (string) ($data['client_name'] ?? ''));
        $appointment->set('client_email', (string) ($data['client_email'] ?? ''));
        $appointment->set('client_phone', (string) ($data['client_phone'] ?? ''));
        $appointment->set('notes', (string) ($data['notes'] ?? ''));
        $appointment->set('status', 'booked');
        $appointment->save();

        \Drupal::service('appointment.email_service')->sendAppointmentEmail($appointment, $appointment_id ? 'modification' : 'confirmation');
        $tempstore->delete($store_key);

        $this->messenger()->addStatus($appointment_id ? $this->t('Appointment updated successfully.') : $this->t('Appointment booked successfully.'));
        $form_state->setRedirect('appointment.confirmation', ['appointment' => $appointment->id()]);
    }

    /**
     * Gets current wizard step from route.
     */
    protected function getCurrentStep(): int
    {
        $raw = \Drupal::routeMatch()->getParameter('step');
        $step = is_numeric($raw) ? (int) $raw : 1;
        if ($step < 1) {
            return 1;
        }
        if ($step > self::TOTAL_STEPS) {
            return self::TOTAL_STEPS;
        }
        return $step;
    }

    /**
     * Returns tempstore key for this booking flow.
     */
    protected function getStoreKey(?AppointmentEntity $appointment): string
    {
        if ($appointment) {
            return 'edit_' . $appointment->id();
        }

        $session_id = \Drupal::request()->getSession()->getId();
        return 'new_' . $this->currentUser()->id() . '_' . $session_id;
    }

    /**
     * Builds route URL for a specific step.
     */
    protected function stepUrl(FormStateInterface $form_state, int $step): Url
    {
        $appointment_id = $form_state->getValue('appointment_id') ? (int) $form_state->getValue('appointment_id') : NULL;
        if ($appointment_id) {
            return Url::fromRoute('appointment.edit_step', ['appointment' => $appointment_id, 'step' => $step]);
        }
        return Url::fromRoute('appointment.book_step', ['step' => $step]);
    }

    /**
     * Stores values from the current step.
     */
    protected function storeCurrentStepValues(array $data, int $step, FormStateInterface $form_state): array
    {
        switch ($step) {
            case 1:
                $data['agency'] = (int) $form_state->getValue('agency');
                break;

            case 2:
                $data['appointment_type'] = (int) $form_state->getValue('appointment_type');
                break;

            case 3:
                $data['adviser_email'] = (string) $form_state->getValue('adviser_email');
                break;

            case 4:
                $data['appointment_date'] = (string) $form_state->getValue('appointment_date');
                $data['appointment_time'] = (string) $form_state->getValue('appointment_time');
                break;

            case 5:
                $data['client_name'] = (string) $form_state->getValue('client_name');
                $data['client_email'] = (string) $form_state->getValue('client_email');
                $data['client_phone'] = (string) $form_state->getValue('client_phone');
                $data['notes'] = (string) $form_state->getValue('notes');
                break;
        }
        return $data;
    }

    /**
     * Builds summary HTML for step 6.
     */
    protected function buildSummaryMarkup(array $data): string
    {
        $agency_label = '';
        if (!empty($data['agency'])) {
            $agency = \Drupal::entityTypeManager()->getStorage('agency')->load((int) $data['agency']);
            $agency_label = $agency ? $agency->label() : '';
        }

        $type_label = '';
        if (!empty($data['appointment_type'])) {
            $type = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load((int) $data['appointment_type']);
            $type_label = $type ? $type->label() : '';
        }

        $summary = [
            '<ul>',
            '<li><strong>' . $this->t('Agency') . ':</strong> ' . htmlspecialchars($agency_label) . '</li>',
            '<li><strong>' . $this->t('Type') . ':</strong> ' . htmlspecialchars($type_label) . '</li>',
            '<li><strong>' . $this->t('Adviser') . ':</strong> ' . htmlspecialchars((string) ($data['adviser_email'] ?? '')) . '</li>',
            '<li><strong>' . $this->t('Date') . ':</strong> ' . htmlspecialchars((string) ($data['appointment_date'] ?? '')) . '</li>',
            '<li><strong>' . $this->t('Time') . ':</strong> ' . htmlspecialchars((string) ($data['appointment_time'] ?? '')) . '</li>',
            '<li><strong>' . $this->t('Name') . ':</strong> ' . htmlspecialchars((string) ($data['client_name'] ?? '')) . '</li>',
            '<li><strong>' . $this->t('Email') . ':</strong> ' . htmlspecialchars((string) ($data['client_email'] ?? '')) . '</li>',
            '</ul>',
        ];

        return implode('', $summary);
    }

    /**
     * Seeds step data from appointment entity.
     */
    protected function seedDataFromAppointment(AppointmentEntity $appointment): array
    {
        return [
            'agency' => (int) $appointment->get('agency')->target_id,
            'appointment_type' => (int) $appointment->get('appointment_type')->target_id,
            'adviser_email' => (string) $appointment->get('adviser_email')->value,
            'appointment_date' => date('Y-m-d', (int) $appointment->get('start_time')->value),
            'appointment_time' => date('H:i', (int) $appointment->get('start_time')->value),
            'client_name' => (string) $appointment->get('client_name')->value,
            'client_email' => (string) $appointment->get('client_email')->value,
            'client_phone' => (string) $appointment->get('client_phone')->value,
            'notes' => (string) $appointment->get('notes')->value,
        ];
    }

    /**
     * Returns agency options.
     */
    protected function loadAgencyOptions(): array
    {
        $options = [];
        try {
            $ids = \Drupal::entityTypeManager()->getStorage('agency')->getQuery()->accessCheck(FALSE)->condition('status', 1)->sort('name')->execute();
            foreach (\Drupal::entityTypeManager()->getStorage('agency')->loadMultiple($ids) as $agency) {
                $options[(int) $agency->id()] = $agency->label();
            }
        } catch (\Throwable) {
            $this->messenger()->addError($this->t('Agency storage is not installed yet. Run database updates then reload this page.'));
        }
        return $options;
    }

    /**
     * Returns appointment type options.
     */
    protected function loadTypeOptions(): array
    {
        $options = [];
        $ids = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->getQuery()->accessCheck(FALSE)->condition('vid', 'appointment_type')->sort('name')->execute();
        foreach (\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($ids) as $term) {
            $options[(int) $term->id()] = $term->label();
        }
        return $options;
    }

    /**
     * Returns adviser options for selected agency.
     */
    protected function loadAdviserOptions(int $agency_id): array
    {
        if ($agency_id <= 0) {
            return [];
        }

        /** @var AgencyEntity|null $agency */
        $agency = \Drupal::entityTypeManager()->getStorage('agency')->load($agency_id);
        if (!$agency) {
            return [];
        }

        $raw = (string) $agency->get('advisers')->value;
        if ($raw === '') {
            return [];
        }

        try {
            $decoded = json_decode($raw, TRUE, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return [];
        }

        $options = [];
        foreach ($decoded as $item) {
            if (!is_array($item) || empty($item['email']) || empty($item['name'])) {
                continue;
            }
            $options[(string) $item['email']] = (string) $item['name'] . ' (' . (string) $item['email'] . ')';
        }

        return $options;
    }

    /**
     * Returns adviser name for selected agency/email.
     */
    protected function loadAdviserName(int $agency_id, string $adviser_email): string
    {
        $options = $this->loadAdviserOptions($agency_id);
        $label = $options[$adviser_email] ?? $adviser_email;
        if (str_contains($label, ' (')) {
            return explode(' (', $label)[0];
        }
        return $label;
    }

    /**
     * Checks whether a time slot is available.
     */
    protected function isSlotAvailable(string $adviser_email, int $start, int $end, ?int $exclude_id = NULL): bool
    {
        $query = \Drupal::entityTypeManager()->getStorage('appointment')->getQuery()->accessCheck(FALSE)
            ->condition('adviser_email', $adviser_email)
            ->condition('status', 'booked');

        if ($exclude_id) {
            $query->condition('id', $exclude_id, '<>');
        }

        $ids = $query->execute();
        $appointments = \Drupal::entityTypeManager()->getStorage('appointment')->loadMultiple($ids);
        /** @var AppointmentEntity $existing */
        foreach ($appointments as $existing) {
            $existing_start = (int) $existing->get('start_time')->value;
            $existing_end = (int) $existing->get('end_time')->value;
            if ($start < $existing_end && $end > $existing_start) {
                return FALSE;
            }
        }

        return TRUE;
    }
}
