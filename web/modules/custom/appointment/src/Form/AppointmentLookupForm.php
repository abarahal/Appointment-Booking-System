<?php

declare(strict_types=1);

namespace Drupal\appointment\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Anonymous lookup form by client email.
 */
class AppointmentLookupForm extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId(): string
    {
        return 'appointment_lookup_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state): array
    {
        $form['email'] = [
            '#type' => 'email',
            '#title' => $this->t('Email used for booking'),
            '#required' => TRUE,
        ];

        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Find my appointments'),
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state): void
    {
        $email = (string) $form_state->getValue('email');
        $form_state->setRedirectUrl(Url::fromRoute('appointment.my_appointments', [], ['query' => ['email' => $email]]));
    }
}
