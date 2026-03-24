<?php

declare(strict_types=1);

namespace Drupal\appointment\Controller;

use Drupal\appointment\Entity\AppointmentEntity;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Admin controller for appointment management.
 */
class AppointmentAdminController extends ControllerBase
{

    /**
     * Constructor.
     */
    public function __construct(
        protected RequestStack $requestStack,
    ) {}

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container): static
    {
        return new static(
            $container->get('request_stack'),
        );
    }

    /**
     * Admin listing page for all appointments.
     */
    public function listAppointments(): array
    {
        $request = $this->requestStack->getCurrentRequest();

        $query = $this->entityTypeManager()
            ->getStorage('appointment')
            ->getQuery()
            ->accessCheck(FALSE)
            ->sort('start_time', 'DESC');

        $count_query = clone $query;
        $total = count($count_query->execute());

        $page = (int) ($request?->query->get('page') ?? 0);
        $limit = 25;
        $query->range($page * $limit, $limit);
        $ids = $query->execute();
        $appointments = $ids ? $this->entityTypeManager()->getStorage('appointment')->loadMultiple($ids) : [];

        $rows = [];
        /** @var \Drupal\appointment\Entity\AppointmentEntity $appt */
        foreach ($appointments as $appt) {
            $rows[] = [
                'id' => (int) $appt->id(),
                'client_name' => (string) $appt->get('client_name')->value,
                'client_email' => (string) $appt->get('client_email')->value,
                'client_phone' => (string) $appt->get('client_phone')->value,
                'agency' => $appt->get('agency')->entity?->label() ?? $this->t('N/A'),
                'adviser' => (string) $appt->get('adviser_name')->value,
                'type' => $appt->get('appointment_type')->entity?->label() ?? $this->t('N/A'),
                'start_time' => date('Y-m-d H:i', (int) $appt->get('start_time')->value),
                'end_time' => date('Y-m-d H:i', (int) $appt->get('end_time')->value),
                'status' => (string) $appt->get('status')->value,
                'created' => date('Y-m-d H:i', (int) $appt->get('created')->value),
                'edit_url' => Url::fromRoute('appointment.edit', ['appointment' => $appt->id()])->toString(),
                'cancel_url' => Url::fromRoute('appointment.cancel', ['appointment' => $appt->id()])->toString(),
            ];
        }

        $header = [
            $this->t('ID'),
            $this->t('Client'),
            $this->t('Email'),
            $this->t('Phone'),
            $this->t('Agency'),
            $this->t('Adviser'),
            $this->t('Type'),
            $this->t('Date/Time'),
            $this->t('Status'),
            $this->t('Actions'),
        ];

        $table_rows = [];
        foreach ($rows as $row) {
            $actions = [];
            if ($row['status'] === 'pending') {
                $actions[] = [
                    '#type' => 'link',
                    '#title' => $this->t('Confirm'),
                    '#url' => Url::fromRoute('appointment.admin_confirm', ['appointment' => $row['id']]),
                    '#attributes' => ['class' => ['button', 'button--small', 'button--primary']],
                ];
            }
            $actions[] = [
                '#type' => 'link',
                '#title' => $this->t('Edit'),
                '#url' => Url::fromRoute('appointment.edit', ['appointment' => $row['id']]),
                '#attributes' => ['class' => ['button', 'button--small']],
            ];
            if ($row['status'] !== 'cancelled') {
                $actions[] = [
                    '#type' => 'link',
                    '#title' => $this->t('Cancel'),
                    '#url' => Url::fromRoute('appointment.cancel', ['appointment' => $row['id']]),
                    '#attributes' => ['class' => ['button', 'button--small', 'button--danger']],
                ];
            }

            $table_rows[] = [
                $row['id'],
                $row['client_name'],
                $row['client_email'],
                $row['client_phone'],
                $row['agency'],
                $row['adviser'],
                $row['type'],
                $row['start_time'],
                ['data' => ['#markup' => '<span class="appointment-status appointment-status--' . $row['status'] . '">' . ucfirst($row['status']) . '</span>']],
                ['data' => ['#type' => 'container', 'actions' => $actions]],
            ];
        }

        $build = [];

        // Actions bar.
        $build['actions_bar'] = [
            '#type' => 'container',
            '#attributes' => ['class' => ['appointment-admin-actions']],
            'export' => [
                '#type' => 'link',
                '#title' => $this->t('Export CSV'),
                '#url' => Url::fromRoute('appointment.admin_export_csv'),
                '#attributes' => ['class' => ['button', 'button--primary']],
            ],
            'count' => [
                '#type' => 'markup',
                '#markup' => '<span class="appointment-count">' . $this->t('@count appointments found', ['@count' => $total]) . '</span>',
            ],
        ];

        // Table.
        $build['table'] = [
            '#type' => 'table',
            '#header' => $header,
            '#rows' => $table_rows,
            '#empty' => $this->t('No appointments found.'),
            '#attributes' => ['class' => ['appointment-admin-table']],
        ];

        // Pager.
        if ($total > $limit) {
            $build['pager'] = ['#type' => 'pager'];
        }

        $build['#attached'] = [
            'library' => ['appointment/appointment.admin'],
        ];

        return $build;
    }

    /**
     * Confirms an appointment and sends the confirmation email.
     */
    public function confirmAppointment(AppointmentEntity $appointment): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        if ($appointment->get('status')->value === 'pending') {
            $appointment->set('status', 'confirmed');
            $appointment->save();
            \Drupal::service('appointment.email_service')->sendAppointmentEmail($appointment, 'confirmation');
            $this->messenger()->addStatus($this->t('Appointment #@id has been confirmed and the client has been notified by email.', ['@id' => $appointment->id()]));
        } else {
            $this->messenger()->addWarning($this->t('Appointment #@id cannot be confirmed (current status: @status).', [
                '@id' => $appointment->id(),
                '@status' => $appointment->get('status')->value,
            ]));
        }

        return $this->redirect('appointment.admin_list');
    }

    /**
     * Admin dashboard overview page.
     */
    public function dashboard(): array
    {
        $storage = $this->entityTypeManager()->getStorage('appointment');

        $total = count($storage->getQuery()->accessCheck(FALSE)->execute());
        $pending = count($storage->getQuery()->accessCheck(FALSE)->condition('status', 'pending')->execute());
        $confirmed = count($storage->getQuery()->accessCheck(FALSE)->condition('status', 'confirmed')->execute());
        $cancelled = count($storage->getQuery()->accessCheck(FALSE)->condition('status', 'cancelled')->execute());

        $today_start = strtotime('today');
        $today_end = strtotime('tomorrow');
        $today_count = count($storage->getQuery()->accessCheck(FALSE)
            ->condition('start_time', $today_start, '>=')
            ->condition('start_time', $today_end, '<')
            ->condition('status', 'cancelled', '<>')
            ->execute());

        return [
            '#type' => 'container',
            '#attributes' => ['class' => ['appointment-dashboard']],
            'stats' => [
                '#type' => 'markup',
                '#markup' => '<div class="appointment-stats">'
                    . '<div class="stat-card"><h3>' . $total . '</h3><p>' . $this->t('Total Appointments') . '</p></div>'
                    . '<div class="stat-card stat-pending"><h3>' . $pending . '</h3><p>' . $this->t('Pending') . '</p></div>'
                    . '<div class="stat-card stat-confirmed"><h3>' . $confirmed . '</h3><p>' . $this->t('Confirmed') . '</p></div>'
                    . '<div class="stat-card stat-cancelled"><h3>' . $cancelled . '</h3><p>' . $this->t('Cancelled') . '</p></div>'
                    . '<div class="stat-card stat-today"><h3>' . $today_count . '</h3><p>' . $this->t('Today') . '</p></div>'
                    . '</div>',
            ],
            'links' => [
                '#type' => 'markup',
                '#markup' => '<div class="appointment-admin-links">'
                    . '<a href="' . Url::fromRoute('appointment.admin_list')->toString() . '" class="button button--primary">' . $this->t('View All Appointments') . '</a> '
                    . '<a href="' . Url::fromRoute('appointment.admin_export_csv')->toString() . '" class="button">' . $this->t('Export CSV') . '</a> '
                    . '<a href="' . Url::fromRoute('appointment.admin_settings')->toString() . '" class="button">' . $this->t('Settings') . '</a>'
                    . '</div>',
            ],
            '#attached' => [
                'library' => ['appointment/appointment.admin'],
            ],
        ];
    }

    /**
     * Serves the generated CSV file for download.
     */
    public function downloadCsv(): BinaryFileResponse
    {
        $session = $this->requestStack->getCurrentRequest()?->getSession();
        $filepath = $session?->get('appointment_csv_download', '');

        if (!$filepath || !file_exists($filepath)) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }

        $session->remove('appointment_csv_download');

        $response = new BinaryFileResponse($filepath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'appointments_' . date('Y-m-d_His') . '.csv'
        );
        $response->headers->set('Content-Type', 'text/csv');
        $response->deleteFileAfterSend(TRUE);

        return $response;
    }
}
