<?php

declare(strict_types=1);

namespace Drupal\appointment\Controller;

use Drupal\appointment\Entity\AppointmentEntity;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        ];
    }

    /**
     * List appointments for current user or email/phone query.
     */
    public function myAppointments(): array
    {
        $account = $this->currentUser();
        $email = (string) $this->getRequest()->query->get('email', '');
        $phone = (string) $this->getRequest()->query->get('phone', '');
        $type_tid = (int) $this->getRequest()->query->get('type', 0);

        $query = $this->entityTypeManager()->getStorage('appointment')->getQuery()->accessCheck(FALSE)->condition('deleted', 0)->sort('start_time', 'DESC');
        if ($account->isAuthenticated()) {
            $query->condition('uid', (int) $account->id());
        } elseif ($email !== '') {
            $query->condition('client_email', $email);
        } elseif ($phone !== '') {
            $query->condition('client_phone', $phone);
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
                'adviser' => $appointment->get('adviser')->entity ? $appointment->get('adviser')->entity->getDisplayName() : ((string) $appointment->get('adviser_name')->value),
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
        ];
    }

    /**
     * Returns booked slots for an adviser as JSON.
     */
    public function bookedSlots(): JsonResponse
    {
        $request = $this->getRequest();
        $adviser_id = (int) $request->query->get('adviser_id', 0);
        // Fallback: also accept adviser_email for backward compatibility.
        $adviser_email = (string) $request->query->get('adviser_email', '');
        $range_start = (string) $request->query->get('start', '');
        $range_end = (string) $request->query->get('end', '');
        $exclude_id = (int) $request->query->get('exclude_id', 0);

        if (($adviser_id === 0 && $adviser_email === '') || $range_start === '' || $range_end === '') {
            return new JsonResponse([]);
        }

        $start_ts = strtotime($range_start);
        $end_ts = strtotime($range_end);
        if (!$start_ts || !$end_ts) {
            return new JsonResponse([]);
        }

        $query = $this->entityTypeManager()->getStorage('appointment')->getQuery()
            ->accessCheck(FALSE)
            ->condition('status', ['pending', 'confirmed'], 'IN')
            ->condition('deleted', 0)
            ->condition('start_time', $start_ts, '>=')
            ->condition('end_time', $end_ts, '<=');

        if ($adviser_id > 0) {
            $query->condition('adviser', $adviser_id);
        } else {
            $query->condition('adviser_email', $adviser_email);
        }

        if ($exclude_id > 0) {
            $query->condition('id', $exclude_id, '<>');
        }

        $ids = $query->execute();
        $slots = [];

        if ($ids) {
            /** @var \Drupal\appointment\Entity\AppointmentEntity $appt */
            foreach ($this->entityTypeManager()->getStorage('appointment')->loadMultiple($ids) as $appt) {
                $slots[] = [
                    'start' => date('c', (int) $appt->get('start_time')->value),
                    'end' => date('c', (int) $appt->get('end_time')->value),
                ];
            }
        }

        return new JsonResponse($slots);
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
