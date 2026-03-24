<?php

declare(strict_types=1);

namespace Drupal\appointment\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the adviser entity.
 *
 * @ContentEntityType(
 *   id = "adviser",
 *   label = @Translation("Adviser"),
 *   base_table = "adviser",
 *   admin_permission = "administer appointments",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "label" = "name"
 *   },
 *   links = {
 *     "canonical" = "/adviser/{adviser}"
 *   }
 * )
 */
class AdviserEntity extends ContentEntityBase
{

    use EntityChangedTrait;

    /**
     * {@inheritdoc}
     */
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array
    {
        $fields = parent::baseFieldDefinitions($entity_type);

        $fields['name'] = BaseFieldDefinition::create('string')
            ->setLabel(new TranslatableMarkup('Name'))
            ->setRequired(TRUE)
            ->setSettings(['max_length' => 255]);

        $fields['email'] = BaseFieldDefinition::create('email')
            ->setLabel(new TranslatableMarkup('Email'))
            ->setRequired(TRUE);

        $fields['agency'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(new TranslatableMarkup('Agency'))
            ->setRequired(TRUE)
            ->setSetting('target_type', 'agency');

        $fields['working_hours'] = BaseFieldDefinition::create('string_long')
            ->setLabel(new TranslatableMarkup('Working hours'))
            ->setDescription(new TranslatableMarkup('Working hours schedule for this adviser.'))
            ->setRequired(FALSE);

        $fields['specializations'] = BaseFieldDefinition::create('string_long')
            ->setLabel(new TranslatableMarkup('Specializations'))
            ->setDescription(new TranslatableMarkup('Areas of specialization for this adviser.'))
            ->setRequired(FALSE);

        $fields['status'] = BaseFieldDefinition::create('boolean')
            ->setLabel(new TranslatableMarkup('Active'))
            ->setDefaultValue(TRUE);

        $fields['created'] = BaseFieldDefinition::create('created')
            ->setLabel(new TranslatableMarkup('Created'));

        $fields['changed'] = BaseFieldDefinition::create('changed')
            ->setLabel(new TranslatableMarkup('Changed'));

        return $fields;
    }
}
