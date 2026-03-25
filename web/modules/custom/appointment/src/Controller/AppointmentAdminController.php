<?php

declare(strict_types=1);

namespace Drupal\appointment\Controller;

use Drupal\appointment\Entity\AppointmentEntity;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\user\UserInterface;
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
     * Admin listing page for all appointments with filters and pagination.
     */
    public function listAppointments(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        $limit = 10;

        // Read filter values from query string.
        $filter_status = (string) ($request?->query->get('status') ?? '');
        $filter_agency = (int) ($request?->query->get('agency') ?? 0);
        $filter_adviser = (int) ($request?->query->get('adviser') ?? 0);
        $filter_date_from = (string) ($request?->query->get('date_from') ?? '');
        $filter_date_to = (string) ($request?->query->get('date_to') ?? '');

        // Base query: exclude deleted appointments.
        $query = $this->entityTypeManager()
            ->getStorage('appointment')
            ->getQuery()
            ->accessCheck(FALSE)
            ->condition('status', 'deleted', '<>')
            ->sort('start_time', 'DESC');

        // Apply filters.
        if ($filter_status !== '' && in_array($filter_status, ['pending', 'confirmed', 'cancelled', 'deleted'], TRUE)) {
            $query->condition('status', $filter_status);
        }
        if ($filter_agency > 0) {
            $query->condition('agency', $filter_agency);
        }
        if ($filter_adviser > 0) {
            $query->condition('adviser', $filter_adviser);
        }
        if ($filter_date_from !== '' && strtotime($filter_date_from)) {
            $query->condition('start_time', strtotime($filter_date_from), '>=');
        }
        if ($filter_date_to !== '' && strtotime($filter_date_to . ' 23:59:59')) {
            $query->condition('start_time', strtotime($filter_date_to . ' 23:59:59'), '<=');
        }

        // Count total for pager (use a separate count query).
        $count_query = clone $query;
        $total = (int) $count_query->count()->execute();

        // Apply Drupal pager.
        $query->pager($limit);
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
                'adviser' => $appt->get('adviser')->entity ? $appt->get('adviser')->entity->getDisplayName() : ((string) $appt->get('adviser_name')->value),
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
            if ($row['status'] !== 'deleted') {
                $actions[] = [
                    '#type' => 'link',
                    '#title' => $this->t('Delete'),
                    '#url' => Url::fromRoute('appointment.admin_delete', ['appointment' => $row['id']]),
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

        // Filters form.
        $build['filters'] = $this->buildFilterForm($filter_status, $filter_agency, $filter_adviser, $filter_date_from, $filter_date_to);

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

        // Pager 
        $build['pager'] = ['#type' => 'pager'];

        $build['#attached'] = [
            'library' => ['appointment/appointment.admin'],
        ];

        return $build;
    }

    /**
     * Builds the admin filter form as a render array.
     */
    protected function buildFilterForm(string $status, int $agency, int $adviser, string $date_from, string $date_to): array
    {
        $agency_options = ['' => $this->t('- All agencies -')];
        $agency_ids = $this->entityTypeManager()->getStorage('agency')->getQuery()->accessCheck(FALSE)->condition('status', 1)->sort('name')->execute();
        foreach ($this->entityTypeManager()->getStorage('agency')->loadMultiple($agency_ids) as $ag) {
            $agency_options[$ag->id()] = $ag->label();
        }

        $adviser_options = ['' => $this->t('- All advisers -')];
        $adviser_ids = $this->entityTypeManager()->getStorage('user')->getQuery()->accessCheck(FALSE)->condition('roles', 'adviser')->condition('status', 1)->sort('name')->execute();
        foreach ($this->entityTypeManager()->getStorage('user')->loadMultiple($adviser_ids) as $adv) {
            assert($adv instanceof UserInterface);
            $adviser_options[$adv->id()] = $adv->getDisplayName();
        }

        $status_options = [
            '' => $this->t('- All statuses -'),
            'pending' => $this->t('Pending'),
            'confirmed' => $this->t('Confirmed'),
            'cancelled' => $this->t('Cancelled'),
            'deleted' => $this->t('Deleted'),
        ];

        $list_url = Url::fromRoute('appointment.admin_list')->toString();

        return [
            '#type' => 'inline_template',
            '#template' => '
                <div class="appointment-admin-filters">
                  <form method="get" action="{{ list_url }}">
                    <div class="appointment-filters-inline">
                      <label>{{ "Status"|t }}
                        <select name="status">
                          {% for val, label in status_options %}
                            <option value="{{ val }}"{{ val == current_status ? " selected" : "" }}>{{ label }}</option>
                          {% endfor %}
                        </select>
                      </label>
                      <label>{{ "Agency"|t }}
                        <select name="agency">
                          {% for val, label in agency_options %}
                            <option value="{{ val }}"{{ val == current_agency ? " selected" : "" }}>{{ label }}</option>
                          {% endfor %}
                        </select>
                      </label>
                      <label>{{ "Adviser"|t }}
                        <select name="adviser">
                          {% for val, label in adviser_options %}
                            <option value="{{ val }}"{{ val == current_adviser ? " selected" : "" }}>{{ label }}</option>
                          {% endfor %}
                        </select>
                      </label>
                      <label>{{ "From"|t }}
                        <input type="date" name="date_from" value="{{ date_from }}">
                      </label>
                      <label>{{ "To"|t }}
                        <input type="date" name="date_to" value="{{ date_to }}">
                      </label>
                      <button type="submit" class="button button--primary">{{ "Filter"|t }}</button>
                      <a href="{{ list_url }}" class="button">{{ "Reset"|t }}</a>
                    </div>
                  </form>
                </div>',
            '#context' => [
                'list_url' => $list_url,
                'status_options' => $status_options,
                'agency_options' => $agency_options,
                'adviser_options' => $adviser_options,
                'current_status' => $status,
                'current_agency' => (string) $agency,
                'current_adviser' => (string) $adviser,
                'date_from' => $date_from,
                'date_to' => $date_to,
            ],
        ];
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
     * Soft-deletes an appointment from the admin interface.
     */
    public function deleteAppointment(AppointmentEntity $appointment): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        if ($appointment->get('status')->value !== 'deleted') {
            $appointment->set('status', 'deleted');
            $appointment->save();
            $this->messenger()->addStatus($this->t('Appointment #@id has been deleted.', ['@id' => $appointment->id()]));
        } else {
            $this->messenger()->addWarning($this->t('Appointment #@id is already deleted.', ['@id' => $appointment->id()]));
        }

        return $this->redirect('appointment.admin_list');
    }

    /**
     * Admin dashboard overview page.
     */
    public function dashboard(): array
    {
        $storage = $this->entityTypeManager()->getStorage('appointment');

        $total = count($storage->getQuery()->accessCheck(FALSE)->condition('deleted', 0)->execute());
        $pending = count($storage->getQuery()->accessCheck(FALSE)->condition('deleted', 0)->condition('status', 'pending')->execute());
        $confirmed = count($storage->getQuery()->accessCheck(FALSE)->condition('deleted', 0)->condition('status', 'confirmed')->execute());
        $cancelled = count($storage->getQuery()->accessCheck(FALSE)->condition('deleted', 0)->condition('status', 'cancelled')->execute());

        $today_start = strtotime('today');
        $today_end = strtotime('tomorrow');
        $today_count = count($storage->getQuery()->accessCheck(FALSE)
            ->condition('deleted', 0)
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
