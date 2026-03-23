<?php

declare(strict_types=1);

namespace Drupal\appointment\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the agency entity.
 *
 * @ContentEntityType(
 *   id = "agency",
 *   label = @Translation("Agency"),
 *   base_table = "agency",
 *   admin_permission = "access content",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "label" = "name"
 *   },
 *   links = {
 *     "canonical" = "/agency/{agency}"
 *   }
 * )
 */
class AgencyEntity extends ContentEntityBase
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

        $fields['address'] = BaseFieldDefinition::create('string_long')
            ->setLabel(new TranslatableMarkup('Address'))
            ->setRequired(FALSE);

        $fields['advisers'] = BaseFieldDefinition::create('string_long')
            ->setLabel(new TranslatableMarkup('Advisers JSON'))
            ->setDescription(new TranslatableMarkup('JSON array of advisers with name and email.'))
            ->setRequired(FALSE);

        $fields['status'] = BaseFieldDefinition::create('boolean')
            ->setLabel(new TranslatableMarkup('Published'))
            ->setDefaultValue(TRUE);

        $fields['created'] = BaseFieldDefinition::create('created')
            ->setLabel(new TranslatableMarkup('Created'));

        $fields['changed'] = BaseFieldDefinition::create('changed')
            ->setLabel(new TranslatableMarkup('Changed'));

        return $fields;
    }
}
