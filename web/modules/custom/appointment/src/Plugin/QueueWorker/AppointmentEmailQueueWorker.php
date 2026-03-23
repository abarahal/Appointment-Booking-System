<?php

declare(strict_types=1);

namespace Drupal\appointment\Plugin\QueueWorker;

use Drupal\appointment\Entity\AppointmentEntity;
use Drupal\appointment\Service\EmailService;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Processes queued appointment emails.
 *
 * @QueueWorker(
 *   id = "appointment_email",
 *   title = @Translation("Appointment email queue"),
 *   cron = {"time" = 30}
 * )
 */
class AppointmentEmailQueueWorker extends QueueWorkerBase implements ContainerFactoryPluginInterface
{

    public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        protected EmailService $emailService,
        protected EntityTypeManagerInterface $entityTypeManager,
        protected LoggerInterface $logger,
    ) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static
    {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('appointment.email_service'),
            $container->get('entity_type.manager'),
            $container->get('logger.factory')->get('appointment'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function processItem($data): void
    {
        if (!is_array($data) || empty($data['appointment_id']) || empty($data['mail_key'])) {
            return;
        }

        $appointment = $this->entityTypeManager
            ->getStorage('appointment')
            ->load($data['appointment_id']);

        if (!$appointment instanceof AppointmentEntity) {
            $this->logger->warning('Queue: appointment @id not found, skipping email.', [
                '@id' => $data['appointment_id'],
            ]);
            return;
        }

        $extra_params = $data['extra_params'] ?? [];
        $this->emailService->sendAppointmentEmail($appointment, $data['mail_key'], $extra_params);
    }
}
