<?php

namespace Drupal\locator\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\locator\LocatorInterface;

/**
 * Defines the locator entity class.
 *
 * @ContentEntityType(
 *   id = "locator",
 *   label = @Translation("Locator"),
 *   handlers = {
 *     "access" = "Drupal\locator\LocatorAccessControlHandler",
 *     "views_data" = "Drupal\locator\LocatorViewsData",
 *     "form" = {
 *       "default" = "Drupal\locator\LocatorForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *   },
 *   base_table = "locator",
 *   entity_keys = {
 *     "id" = "lid",
 *     "label" = "title",
 *     "uuid" = "uuid",
 *   },
 *   admin_permission = "administer locators",
 *   links = {
 *     "add-form" = "/admin/locator/add",
 *     "canonical" = "/admin/locator/{locator}",
 *     "edit-form" = "/admin/locator/{locator}/edit",
 *     "delete-form" = "/admin/locator/{locator}/delete",
 *     "collection" = "/admin/locator",
 *   },
 * )
 */
class Locator extends ContentEntityBase implements LocatorInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['description'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Description'))
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', [
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['subinventory'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Subinventory'))
      ->setRequired(TRUE)
      ->setSetting('target_type', 'subinventory')
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => -3,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => -3,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['picking_order'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Picking Order'))
      ->setDescription(t('Picking order value for sequencing picking tasks.'))
      ->setDisplayOptions('view', [
        'type' => 'number_integer',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'number',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['dropping_order'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Dropping Order'))
      ->setDescription(t('Numeric dropping order value.'))
      ->setDisplayOptions('view', [
        'type' => 'number_integer',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'number',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The timestamp that the locator was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The timestamp that the locator was last changed.'));

    return $fields;
  }

}
