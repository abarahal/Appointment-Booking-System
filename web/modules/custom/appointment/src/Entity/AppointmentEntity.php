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

        $fields['adviser'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(new TranslatableMarkup('Adviser'))
            ->setRequired(FALSE)
            ->setSetting('target_type', 'user');

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
            ->setSettings(['allowed_values' => [
                'pending' => 'Pending',
                'confirmed' => 'Confirmed',
                'cancelled' => 'Cancelled',
                'deleted' => 'Deleted',
            ]])
            ->setDefaultValue('pending');

        $fields['access_token'] = BaseFieldDefinition::create('string')
            ->setLabel(new TranslatableMarkup('Access token'))
            ->setSettings(['max_length' => 64])
            ->setRequired(TRUE);

        $fields['created'] = BaseFieldDefinition::create('created')
            ->setLabel(new TranslatableMarkup('Created'));

        $fields['changed'] = BaseFieldDefinition::create('changed')
            ->setLabel(new TranslatableMarkup('Changed'));

        $fields['reminder_sent'] = BaseFieldDefinition::create('boolean')
            ->setLabel(new TranslatableMarkup('Reminder sent'))
            ->setDefaultValue(FALSE);

        $fields['deleted'] = BaseFieldDefinition::create('boolean')
            ->setLabel(new TranslatableMarkup('Deleted'))
            ->setDescription(new TranslatableMarkup('Soft-delete flag. TRUE means the appointment is logically removed.'))
            ->setDefaultValue(FALSE);

        return $fields;
    }

    /**
     * Marks this appointment as soft-deleted.
     */
    public function softDelete(): void
    {
        $this->set('deleted', TRUE);
        $this->save();
    }

    /**
     * Whether this appointment is soft-deleted.
     */
    public function isDeleted(): bool
    {
        return (bool) $this->get('deleted')->value;
    }
}
