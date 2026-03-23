<?php

declare(strict_types=1);

namespace Drupal\appointment\Service;

use Drupal\appointment\Entity\AppointmentEntity;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Queue\QueueFactory;

/**
 * Sends appointment transactional emails (direct or queued).
 */
class EmailService
{

    /**
     * Constructor.
     */
    public function __construct(
        protected MailManagerInterface $mailManager,
        protected LoggerChannelFactoryInterface $loggerFactory,
        protected LanguageManagerInterface $languageManager,
        protected QueueFactory $queueFactory,
    ) {}

    /**
     * Sends an appointment email immediately.
     */
    public function sendAppointmentEmail(AppointmentEntity $appointment, string $mail_key, array $extra_params = []): void
    {
        $to = (string) $appointment->get('client_email')->value;
        if ($to === '') {
            return;
        }

        $langcode = $this->languageManager->getDefaultLanguage()->getId();
        $manage_link = '/appointment/' . $appointment->id() . '/confirmation';

        $params = [
            'appointment' => $appointment,
            'manage_link' => $manage_link,
        ] + $extra_params;

        $result = $this->mailManager->mail(
            'appointment',
            $mail_key,
            $to,
            $langcode,
            $params,
            NULL,
            TRUE
        );

        if (empty($result['result'])) {
            $this->loggerFactory->get('appointment')->error('Unable to send @key email for appointment @id', [
                '@key' => $mail_key,
                '@id' => $appointment->id(),
            ]);
        }
    }

    /**
     * Queues an appointment email for later processing.
     */
    public function queueAppointmentEmail(int $appointment_id, string $mail_key, array $extra_params = []): void
    {
        $queue = $this->queueFactory->get('appointment_email');
        $queue->createItem([
            'appointment_id' => $appointment_id,
            'mail_key' => $mail_key,
            'extra_params' => $extra_params,
        ]);
    }
}
