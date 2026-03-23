<?php

declare(strict_types=1);

namespace Drupal\appointment\Controller;

use Drupal\appointment\Entity\AppointmentEntity;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

/**
 * Controller for appointment pages.
 */
class AppointmentController extends ControllerBase
{

    /**
     * Confirmation page.
     */
    public function confirmation(AppointmentEntity $appointment): array
    {
        return [
            '#theme' => 'appointment_confirmation',
            '#appointment' => $appointment,
            '#edit_url' => Url::fromRoute('appointment.edit', ['appointment' => $appointment->id()])->toString(),
            '#cancel_url' => Url::fromRoute('appointment.cancel', ['appointment' => $appointment->id()])->toString(),
            '#attached' => [
                'library' => ['appointment/appointment.frontend'],
            ],
        ];
    }

    /**
     * List appointments for current user or email query.
     */
    public function myAppointments(): array
    {
        $account = $this->currentUser();
        $email = (string) $this->getRequest()->query->get('email', '');
        $type_tid = (int) $this->getRequest()->query->get('type', 0);

        $query = $this->entityTypeManager()->getStorage('appointment')->getQuery()->accessCheck(FALSE)->sort('start_time', 'DESC');
        if ($account->isAuthenticated()) {
            $query->condition('uid', (int) $account->id());
        } elseif ($email !== '') {
            $query->condition('client_email', $email);
        } else {
            return [
                '#markup' => (string) $this->t('For anonymous users, open the lookup form to view appointments.'),
            ];
        }

        if ($type_tid > 0) {
            $query->condition('appointment_type', $type_tid);
        }

        $ids = $query->execute();
        $appointments = $ids ? $this->entityTypeManager()->getStorage('appointment')->loadMultiple($ids) : [];

        $rows = [];
        /** @var \Drupal\appointment\Entity\AppointmentEntity $appointment */
        foreach ($appointments as $appointment) {
            $type_label = $appointment->get('appointment_type')->entity?->label() ?? (string) $this->t('N/A');
            $rows[] = [
                'id' => (int) $appointment->id(),
                'client_name' => (string) $appointment->get('client_name')->value,
                'client_email' => (string) $appointment->get('client_email')->value,
                'agency' => $appointment->get('agency')->entity?->label() ?? (string) $this->t('N/A'),
                'adviser' => (string) $appointment->get('adviser_name')->value,
                'type' => $type_label,
                'start_time' => date('Y-m-d H:i', (int) $appointment->get('start_time')->value),
                'status' => (string) $appointment->get('status')->value,
                'edit_url' => Url::fromRoute('appointment.edit', ['appointment' => $appointment->id()])->toString(),
                'cancel_url' => Url::fromRoute('appointment.cancel', ['appointment' => $appointment->id()])->toString(),
            ];
        }

        $filters = [
            'email' => $email,
            'type' => $type_tid,
            'type_options' => $this->loadTypeOptions(),
        ];

        return [
            '#theme' => 'appointment_list',
            '#rows' => $rows,
            '#filters' => $filters,
            '#lookup_url' => Url::fromRoute('appointment.lookup')->toString(),
            '#attached' => [
                'library' => ['appointment/appointment.frontend'],
            ],
        ];
    }

    /**
     * Returns the current request object.
     */
    protected function getRequest()
    {
        return \Drupal::request();
    }

    /**
     * Loads appointment type options.
     */
    protected function loadTypeOptions(): array
    {
        $options = [0 => (string) $this->t('- All types -')];
        $ids = $this->entityTypeManager()->getStorage('taxonomy_term')->getQuery()->accessCheck(FALSE)->condition('vid', 'appointment_type')->sort('name')->execute();
        if (!$ids) {
            return $options;
        }

        foreach ($this->entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($ids) as $term) {
            $options[(int) $term->id()] = $term->label();
        }

        return $options;
    }
}
