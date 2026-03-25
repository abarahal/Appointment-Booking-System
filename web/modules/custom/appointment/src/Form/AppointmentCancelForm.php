<?php

declare(strict_types=1);

namespace Drupal\appointment\Form;

use Drupal\appointment\Entity\AppointmentEntity;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Cancel appointment confirmation form.
 */
class AppointmentCancelForm extends ConfirmFormBase
{

    /**
     * Appointment entity from route.
     */
    protected ?AppointmentEntity $appointment = NULL;

    /**
     * {@inheritdoc}
     */
    public function getFormId(): string
    {
        return 'appointment_cancel_form';
    }

    /**
     * {@inheritdoc}
     */
    public function getQuestion(): string
    {
        $id = $this->appointment?->id() ?? 0;
        return (string) $this->t('Are you sure you want to cancel appointment #@id?', ['@id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public function getCancelUrl(): Url
    {
        return Url::fromRoute('appointment.my_appointments');
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmText(): string
    {
        return (string) $this->t('Cancel appointment');
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, ?AppointmentEntity $appointment = NULL): array
    {
        $this->appointment = $appointment;
        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state): void
    {
        if ($this->appointment) {
            $this->appointment->set('status', 'cancelled');
            $this->appointment->softDelete();
            \Drupal::service('appointment.email_service')->sendAppointmentEmail($this->appointment, 'cancellation');
            $this->messenger()->addStatus($this->t('Appointment cancelled.'));
        }

        $form_state->setRedirectUrl(Url::fromRoute('appointment.my_appointments'));
    }
}
