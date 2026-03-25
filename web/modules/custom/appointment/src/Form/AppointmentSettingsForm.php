<?php

declare(strict_types=1);

namespace Drupal\appointment\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configuration form for appointment module settings.
 */
class AppointmentSettingsForm extends ConfigFormBase
{

    /**
     * Config name used by this form.
     */
    protected const CONFIG_NAME = 'appointment.settings';

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames(): array
    {
        return [self::CONFIG_NAME];
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId(): string
    {
        return 'appointment_settings_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state): array
    {
        $config = $this->config(self::CONFIG_NAME);

        $form['slot_duration'] = [
            '#type' => 'number',
            '#title' => $this->t('Slot duration (minutes)'),
            '#description' => $this->t('Default duration of an appointment slot.'),
            '#default_value' => $config->get('slot_duration') ?? 60,
            '#min' => 15,
            '#max' => 240,
            '#required' => TRUE,
        ];

        $form['working_hours_start'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Working hours start'),
            '#description' => $this->t('Start of working hours in HH:MM format (24h).'),
            '#default_value' => $config->get('working_hours_start') ?? '08:00',
            '#size' => 5,
            '#maxlength' => 5,
            '#required' => TRUE,
        ];

        $form['working_hours_end'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Working hours end'),
            '#description' => $this->t('End of working hours in HH:MM format (24h).'),
            '#default_value' => $config->get('working_hours_end') ?? '18:00',
            '#size' => 5,
            '#maxlength' => 5,
            '#required' => TRUE,
        ];

        $form['max_advance_days'] = [
            '#type' => 'number',
            '#title' => $this->t('Maximum advance booking (days)'),
            '#description' => $this->t('How far in the future clients can book.'),
            '#default_value' => $config->get('max_advance_days') ?? 90,
            '#min' => 1,
            '#max' => 365,
            '#required' => TRUE,
        ];

        $form['csv_batch_size'] = [
            '#type' => 'number',
            '#title' => $this->t('CSV export batch size'),
            '#description' => $this->t('Number of rows processed per batch when exporting CSV.'),
            '#default_value' => $config->get('csv_batch_size') ?? 100,
            '#min' => 10,
            '#max' => 1000,
            '#required' => TRUE,
        ];

        $form['email_notifications_enabled'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Enable email notifications'),
            '#description' => $this->t('When unchecked, no appointment emails (confirmations, cancellations, reminders) will be sent.'),
            '#default_value' => $config->get('email_notifications_enabled') ?? TRUE,
        ];

        $form['reminder_hours_before'] = [
            '#type' => 'number',
            '#title' => $this->t('Send reminder (hours before)'),
            '#description' => $this->t('How many hours before the appointment to send a reminder email. Set to 0 to disable reminders.'),
            '#default_value' => $config->get('reminder_hours_before') ?? 24,
            '#min' => 0,
            '#max' => 168,
            '#required' => TRUE,
        ];

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state): void
    {
        parent::validateForm($form, $form_state);

        $pattern = '/^\d{2}:\d{2}$/';
        foreach (['working_hours_start', 'working_hours_end'] as $field) {
            $value = (string) $form_state->getValue($field);
            if (!preg_match($pattern, $value)) {
                $form_state->setErrorByName($field, $this->t('Please enter a valid time in HH:MM format.'));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state): void
    {
        $this->config(self::CONFIG_NAME)
            ->set('slot_duration', (int) $form_state->getValue('slot_duration'))
            ->set('working_hours_start', (string) $form_state->getValue('working_hours_start'))
            ->set('working_hours_end', (string) $form_state->getValue('working_hours_end'))
            ->set('max_advance_days', (int) $form_state->getValue('max_advance_days'))
            ->set('csv_batch_size', (int) $form_state->getValue('csv_batch_size'))
            ->set('email_notifications_enabled', (bool) $form_state->getValue('email_notifications_enabled'))
            ->set('reminder_hours_before', (int) $form_state->getValue('reminder_hours_before'))
            ->save();

        parent::submitForm($form, $form_state);
    }
}
