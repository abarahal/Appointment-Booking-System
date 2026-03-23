<?php

declare(strict_types=1);

namespace Drupal\appointment\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the appointment entity.
 *
 * @ContentEntityType(
 *   id = "appointment",
 *   label = @Translation("Appointment"),
 *   base_table = "appointment",
 *   admin_permission = "access content",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "label" = "client_name"
 *   },
 *   links = {
 *     "canonical" = "/appointment/{appointment}"
 *   }
 * )
 */
class AppointmentEntity extends ContentEntityBase
{

    use EntityChangedTrait;

    /**
     * {@inheritdoc}
     */
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array
    {
        $fields = parent::baseFieldDefinitions($entity_type);

        $fields['uid'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(new TranslatableMarkup('Author'))
            ->setSetting('target_type', 'user')
            ->setDefaultValue(0);

        $fields['agency'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(new TranslatableMarkup('Agency'))
            ->setRequired(TRUE)
            ->setSetting('target_type', 'agency');

        $fields['adviser_name'] = BaseFieldDefinition::create('string')
            ->setLabel(new TranslatableMarkup('Adviser name'))
            ->setRequired(TRUE)
            ->setSettings(['max_length' => 255]);

        $fields['adviser_email'] = BaseFieldDefinition::create('email')
            ->setLabel(new TranslatableMarkup('Adviser email'))
            ->setRequired(TRUE);

        $fields['appointment_type'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(new TranslatableMarkup('Appointment type'))
            ->setRequired(TRUE)
            ->setSetting('target_type', 'taxonomy_term');

        $fields['start_time'] = BaseFieldDefinition::create('timestamp')
            ->setLabel(new TranslatableMarkup('Start time'))
            ->setRequired(TRUE);

        $fields['end_time'] = BaseFieldDefinition::create('timestamp')
            ->setLabel(new TranslatableMarkup('End time'))
            ->setRequired(TRUE);

        $fields['client_name'] = BaseFieldDefinition::create('string')
            ->setLabel(new TranslatableMarkup('Client name'))
            ->setRequired(TRUE)
            ->setSettings(['max_length' => 255]);

        $fields['client_email'] = BaseFieldDefinition::create('email')
            ->setLabel(new TranslatableMarkup('Client email'))
            ->setRequired(TRUE);

        $fields['client_phone'] = BaseFieldDefinition::create('string')
            ->setLabel(new TranslatableMarkup('Client phone'))
            ->setRequired(FALSE)
            ->setSettings(['max_length' => 64]);

        $fields['notes'] = BaseFieldDefinition::create('string_long')
            ->setLabel(new TranslatableMarkup('Notes'))
            ->setRequired(FALSE);

        $fields['status'] = BaseFieldDefinition::create('list_string')
            ->setLabel(new TranslatableMarkup('Status'))
            ->setRequired(TRUE)
            ->setSettings(['allowed_values' => ['booked' => 'Booked', 'cancelled' => 'Cancelled']])
            ->setDefaultValue('booked');

        $fields['access_token'] = BaseFieldDefinition::create('string')
            ->setLabel(new TranslatableMarkup('Access token'))
            ->setSettings(['max_length' => 64])
            ->setRequired(TRUE);

        $fields['created'] = BaseFieldDefinition::create('created')
            ->setLabel(new TranslatableMarkup('Created'));

        $fields['changed'] = BaseFieldDefinition::create('changed')
            ->setLabel(new TranslatableMarkup('Changed'));

        return $fields;
    }
}
