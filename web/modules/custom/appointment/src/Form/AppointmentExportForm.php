<?php

declare(strict_types=1);

namespace Drupal\appointment\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Batch\BatchBuilder;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Messenger\MessengerInterface;

/**
 * Form that triggers a batch CSV export of appointments.
 */
class AppointmentExportForm extends FormBase
{

    /**
     * Constructor.
     */
    public function __construct(
        protected EntityTypeManagerInterface $entityTypeManager,
        protected FileSystemInterface $fileSystem,
    ) {}

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container): static
    {
        return new static(
            $container->get('entity_type.manager'),
            $container->get('file_system'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId(): string
    {
        return 'appointment_export_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state): array
    {
        $request = \Drupal::request();

        $form['info'] = [
            '#type' => 'markup',
            '#markup' => '<p>' . $this->t('Export appointments to CSV using batch processing. Apply filters to export a subset.') . '</p>',
        ];

        $form['status'] = [
            '#type' => 'select',
            '#title' => $this->t('Status'),
            '#options' => [
                '' => $this->t('- All -'),
                'booked' => $this->t('Booked'),
                'confirmed' => $this->t('Confirmed'),
                'cancelled' => $this->t('Cancelled'),
            ],
            '#default_value' => (string) $request->query->get('status', ''),
        ];

        $agency_options = ['' => $this->t('- All agencies -')];
        $agency_ids = $this->entityTypeManager->getStorage('agency')
            ->getQuery()->accessCheck(FALSE)->condition('status', 1)->sort('name')->execute();
        if ($agency_ids) {
            foreach ($this->entityTypeManager->getStorage('agency')->loadMultiple($agency_ids) as $agency) {
                $agency_options[$agency->id()] = $agency->label();
            }
        }
        $form['agency'] = [
            '#type' => 'select',
            '#title' => $this->t('Agency'),
            '#options' => $agency_options,
            '#default_value' => (string) $request->query->get('agency', ''),
        ];

        $form['date_from'] = [
            '#type' => 'date',
            '#title' => $this->t('Date from'),
            '#default_value' => (string) $request->query->get('date_from', ''),
        ];

        $form['date_to'] = [
            '#type' => 'date',
            '#title' => $this->t('Date to'),
            '#default_value' => (string) $request->query->get('date_to', ''),
        ];

        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Export CSV'),
            '#button_type' => 'primary',
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state): void
    {
        $filters = [
            'status' => (string) $form_state->getValue('status'),
            'agency' => (int) $form_state->getValue('agency'),
            'date_from' => (string) $form_state->getValue('date_from'),
            'date_to' => (string) $form_state->getValue('date_to'),
        ];

        // Collect matching IDs.
        $query = $this->entityTypeManager->getStorage('appointment')
            ->getQuery()
            ->accessCheck(FALSE)
            ->sort('start_time', 'DESC');

        if ($filters['status'] !== '') {
            $query->condition('status', $filters['status']);
        }
        if ($filters['agency'] > 0) {
            $query->condition('agency', $filters['agency']);
        }
        if ($filters['date_from'] !== '') {
            $ts = strtotime($filters['date_from']);
            if ($ts) {
                $query->condition('start_time', $ts, '>=');
            }
        }
        if ($filters['date_to'] !== '') {
            $ts = strtotime($filters['date_to'] . ' 23:59:59');
            if ($ts) {
                $query->condition('start_time', $ts, '<=');
            }
        }

        $ids = $query->execute();
        if (empty($ids)) {
            $this->messenger()->addWarning($this->t('No appointments match the selected filters.'));
            return;
        }

        $config = \Drupal::config('appointment.settings');
        $batch_size = (int) ($config->get('csv_batch_size') ?? 100);

        // Prepare tmp file path.
        $tmp_dir = $this->fileSystem->getTempDirectory();
        $filepath = $tmp_dir . '/appointment_export_' . time() . '.csv';

        $batches = array_chunk(array_values($ids), $batch_size);

        $batch = new BatchBuilder();
        $batch->setTitle($this->t('Exporting appointments to CSV'));
        $batch->setFinishCallback([static::class, 'batchFinished']);

        foreach ($batches as $index => $chunk_ids) {
            $batch->addOperation(
                [static::class, 'processBatch'],
                [$chunk_ids, $filepath, $index === 0],
            );
        }

        $batchArray = $batch->toArray();
        $setBatch = 'batch_set';
        $setBatch($batchArray);
    }

    /**
     * Batch callback: writes a chunk of appointments to CSV.
     */
    public static function processBatch(array $ids, string $filepath, bool $write_header, array &$context): void
    {
        $storage = \Drupal::entityTypeManager()->getStorage('appointment');
        $appointments = $storage->loadMultiple($ids);

        $mode = $write_header ? 'w' : 'a';
        $handle = fopen($filepath, $mode);
        if (!$handle) {
            $context['results']['error'] = TRUE;
            return;
        }

        if ($write_header) {
            fputcsv($handle, [
                'ID',
                'Client Name',
                'Client Email',
                'Client Phone',
                'Agency',
                'Adviser',
                'Adviser Email',
                'Type',
                'Start Time',
                'End Time',
                'Status',
                'Notes',
                'Created',
            ]);
        }

        /** @var \Drupal\appointment\Entity\AppointmentEntity $appt */
        foreach ($appointments as $appt) {
            fputcsv($handle, [
                $appt->id(),
                $appt->get('client_name')->value,
                $appt->get('client_email')->value,
                $appt->get('client_phone')->value,
                $appt->get('agency')->entity?->label() ?? '',
                $appt->get('adviser_name')->value,
                $appt->get('adviser_email')->value,
                $appt->get('appointment_type')->entity?->label() ?? '',
                date('Y-m-d H:i', (int) $appt->get('start_time')->value),
                date('Y-m-d H:i', (int) $appt->get('end_time')->value),
                $appt->get('status')->value,
                $appt->get('notes')->value ?? '',
                date('Y-m-d H:i', (int) $appt->get('created')->value),
            ]);

            if (!isset($context['results']['count'])) {
                $context['results']['count'] = 0;
            }
            $context['results']['count']++;
        }

        fclose($handle);
        $context['results']['filepath'] = $filepath;
    }

    /**
     * Batch finished callback.
     */
    public static function batchFinished(bool $success, array $results, array $operations): void
    {
        if (!$success || !empty($results['error'])) {
            \Drupal::messenger()->addError(\Drupal::translation()->translate('CSV export failed.'));
            return;
        }

        $count = $results['count'] ?? 0;
        $filepath = $results['filepath'] ?? '';

        if ($filepath && file_exists($filepath)) {
            // Store path in session so the download controller can serve it.
            \Drupal::request()->getSession()->set('appointment_csv_download', $filepath);
            \Drupal::messenger()->addStatus(\Drupal::translation()->translate('Exported @count appointments. <a href="@url">Download CSV</a>', [
                '@count' => $count,
                '@url' => Url::fromRoute('appointment.admin_download_csv')->toString(),
            ]));
        } else {
            \Drupal::messenger()->addError(\Drupal::translation()->translate('CSV file not found after export.'));
        }
    }
}
