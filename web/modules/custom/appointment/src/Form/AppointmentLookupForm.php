<?php

declare(strict_types=1);

namespace Drupal\appointment\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Anonymous lookup form by client email or phone number.
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
        $form['description'] = [
            '#type' => 'markup',
            '#markup' => '<p>' . $this->t('Enter your email address or phone number to find your appointments.') . '</p>',
        ];

        $form['lookup_method'] = [
            '#type' => 'radios',
            '#title' => $this->t('Search by'),
            '#options' => [
                'email' => $this->t('Email'),
                'phone' => $this->t('Phone number'),
            ],
            '#default_value' => 'email',
            '#required' => TRUE,
        ];

        $form['email'] = [
            '#type' => 'email',
            '#title' => $this->t('Email used for booking'),
            '#states' => [
                'visible' => [':input[name="lookup_method"]' => ['value' => 'email']],
                'required' => [':input[name="lookup_method"]' => ['value' => 'email']],
            ],
        ];

        $form['phone'] = [
            '#type' => 'tel',
            '#title' => $this->t('Phone number used for booking'),
            '#states' => [
                'visible' => [':input[name="lookup_method"]' => ['value' => 'phone']],
                'required' => [':input[name="lookup_method"]' => ['value' => 'phone']],
            ],
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
    public function validateForm(array &$form, FormStateInterface $form_state): void
    {
        $method = (string) $form_state->getValue('lookup_method');
        if ($method === 'email' && empty($form_state->getValue('email'))) {
            $form_state->setErrorByName('email', $this->t('Please enter your email address.'));
        }
        if ($method === 'phone' && empty($form_state->getValue('phone'))) {
            $form_state->setErrorByName('phone', $this->t('Please enter your phone number.'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state): void
    {
        $method = (string) $form_state->getValue('lookup_method');
        $query = [];

        if ($method === 'email') {
            $query['email'] = (string) $form_state->getValue('email');
        } else {
            $query['phone'] = (string) $form_state->getValue('phone');
        }

        $form_state->setRedirectUrl(Url::fromRoute('appointment.my_appointments', [], ['query' => $query]));
    }
}
