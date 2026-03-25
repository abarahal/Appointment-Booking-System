<?php

declare(strict_types=1);

namespace Drupal\appointment\Service;

use Drupal\appointment\Entity\AgencyEntity;
use Drupal\appointment\Entity\AppointmentEntity;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\user\UserInterface;

/**
 * Business logic for appointment booking operations.
 */
class AppointmentManagerService
{

    /**
     * Constructor.
     */
    public function __construct(
        protected EntityTypeManagerInterface $entityTypeManager,
        protected DateFormatterInterface $dateFormatter,
        protected TimeInterface $time,
    ) {}

    /**
     * Returns agencies as select options.
     */
    public function getAgencyOptions(): array
    {
        $storage = $this->entityTypeManager->getStorage('agency');
        $ids = $storage->getQuery()->accessCheck(FALSE)->condition('status', 1)->sort('name')->execute();
        if (!$ids) {
            return [];
        }

        $options = [];
        foreach ($storage->loadMultiple($ids) as $agency) {
            $options[$agency->id()] = $agency->label();
        }
        return $options;
    }

    /**
     * Returns advisers for one agency as key-value options.
     */
    public function getAdviserOptions(?int $agency_id): array
    {
        if (!$agency_id) {
            return [];
        }

        $ids = $this->entityTypeManager->getStorage('user')
            ->getQuery()
            ->accessCheck(FALSE)
            ->condition('roles', 'adviser')
            ->condition('field_adviser_agency', $agency_id)
            ->condition('status', 1)
            ->sort('name')
            ->execute();

        if (!$ids) {
            return [];
        }

        $options = [];
        /** @var \Drupal\user\UserInterface $user */
        foreach ($this->entityTypeManager->getStorage('user')->loadMultiple($ids) as $user) {
            $options[(int) $user->id()] = $user->getDisplayName() . ' (' . $user->getEmail() . ')';
        }

        return $options;
    }

    /**
     * Returns appointment taxonomy terms as options.
     */
    public function getAppointmentTypeOptions(): array
    {
        $storage = $this->entityTypeManager->getStorage('taxonomy_term');
        $ids = $storage->getQuery()->accessCheck(FALSE)->condition('vid', 'appointment_type')->sort('name')->execute();

        if (!$ids) {
            return [];
        }

        $options = [];
        foreach ($storage->loadMultiple($ids) as $term) {
            $options[$term->id()] = $term->label();
        }
        return $options;
    }

    /**
     * Checks adviser availability for a time range.
     */
    public function isSlotAvailable(string $adviser_email, int $start_timestamp, int $end_timestamp, ?int $exclude_id = NULL): bool
    {
        $query = $this->entityTypeManager->getStorage('appointment')->getQuery()->accessCheck(FALSE)
            ->condition('adviser_email', $adviser_email)
            ->condition('status', ['pending', 'confirmed'], 'IN')
            ->condition('deleted', 0);

        if ($exclude_id) {
            $query->condition('id', $exclude_id, '<>');
        }

        $ids = $query->execute();
        if (!$ids) {
            return TRUE;
        }

        $appointments = $this->entityTypeManager->getStorage('appointment')->loadMultiple($ids);
        /** @var AppointmentEntity $appointment */
        foreach ($appointments as $appointment) {
            $existing_start = (int) $appointment->get('start_time')->value;
            $existing_end = (int) $appointment->get('end_time')->value;
            if ($start_timestamp < $existing_end && $end_timestamp > $existing_start) {
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * Creates or updates an appointment.
     */
    public function saveAppointment(array $values, ?AppointmentEntity $appointment = NULL): AppointmentEntity
    {
        if (!$appointment) {
            $appointment = AppointmentEntity::create([
                'uid' => $values['uid'] ?? 0,
                'access_token' => hash('sha256', uniqid('appointment_', TRUE)),
            ]);
        }

        $appointment->set('uid', $values['uid'] ?? 0);
        $appointment->set('agency', $values['agency']);
        $appointment->set('adviser_name', $values['adviser_name']);
        $appointment->set('adviser_email', $values['adviser_email']);
        $appointment->set('appointment_type', $values['appointment_type']);
        $appointment->set('start_time', $values['start_time']);
        $appointment->set('end_time', $values['end_time']);
        $appointment->set('client_name', $values['client_name']);
        $appointment->set('client_email', $values['client_email']);
        $appointment->set('client_phone', $values['client_phone'] ?? '');
        $appointment->set('notes', $values['notes'] ?? '');
        $appointment->set('status', $values['status'] ?? 'pending');
        $appointment->save();

        return $appointment;
    }

    /**
     * Returns appointments for a user or for an anonymous email lookup.
     */
    public function getAppointments(?UserInterface $account = NULL, ?string $email = NULL, ?int $type_tid = NULL): array
    {
        $query = $this->entityTypeManager->getStorage('appointment')->getQuery()->accessCheck(FALSE)->condition('deleted', 0)->sort('start_time', 'DESC');

        if ($account && !$account->isAnonymous()) {
            $query->condition('uid', (int) $account->id());
        } elseif ($email) {
            $query->condition('client_email', $email);
        } else {
            return [];
        }

        if ($type_tid) {
            $query->condition('appointment_type', $type_tid);
        }

        $ids = $query->execute();
        if (!$ids) {
            return [];
        }

        return $this->entityTypeManager->getStorage('appointment')->loadMultiple($ids);
    }

    /**
     * Formats a timestamp for display.
     */
    public function formatTime(int $timestamp): string
    {
        return $this->dateFormatter->format($timestamp, 'custom', 'Y-m-d H:i');
    }
}
