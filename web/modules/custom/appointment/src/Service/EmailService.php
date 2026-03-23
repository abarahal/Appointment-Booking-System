<?php

declare(strict_types=1);

namespace Drupal\appointment\Service;

use Drupal\appointment\Entity\AppointmentEntity;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Mail\MailManagerInterface;

/**
 * Sends appointment transactional emails.
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
    ) {}

    /**
     * Sends an appointment email.
     */
    public function sendAppointmentEmail(AppointmentEntity $appointment, string $mail_key): void
    {
        $to = (string) $appointment->get('client_email')->value;
        if ($to === '') {
            return;
        }

        $langcode = $this->languageManager->getDefaultLanguage()->getId();
        $manage_link = '/appointment/' . $appointment->id() . '/confirmation';

        $result = $this->mailManager->mail(
            'appointment',
            $mail_key,
            $to,
            $langcode,
            [
                'appointment' => $appointment,
                'manage_link' => $manage_link,
            ],
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
}
