<?php

namespace Drupal\cycle_count\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\cycle_count\CycleCountEntryInterface;

/**
 * Defines the cycle_count_entry entity class.
 *
 * @ContentEntityType(
 *   id = "cycle_count_entry",
 *   label = @Translation("Cycle count entry"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "access" = "Drupal\cycle_count\CycleCountEntryAccessControlHandler",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\cycle_count\CycleCountEntryForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *   },
 *   base_table = "cycle_count_entry",
 *   entity_keys = {
 *     "id" = "eid",
 *     "uuid" = "uuid",
 *     "status" = "status",
 *   },
 *   admin_permission = "administer cycle count entries",
 *   links = {
 *     "add-form" = "/admin/cycle_count_entry/add",
 *     "canonical" = "/admin/cycle_count_entry/{cycle_count_entry}",
 *     "edit-form" = "/admin/cycle_count_entry/{cycle_count_entry}/edit",
 *     "delete-form" = "/admin/cycle_count_entry/{cycle_count_entry}/delete",
 *     "collection" = "/admin/cycle_count_entry",
 *   }
 * )
 */
class CycleCountEntry extends ContentEntityBase implements CycleCountEntryInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->get('description')->value;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['cycle_count'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Cycle Count'))
      ->setRequired(TRUE)
      ->setSetting('target_type', 'cycle_count')
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

    $fields['date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Date'))
      ->setSetting('datetime_type', 'date')
      ->setDisplayOptions('view', [
        'type' => 'datetime_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['user'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Counted By'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback('Drupal\cycle_count\Entity\CycleCountEntry::getCurrentUserId')
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 0,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '16',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['lines'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Items'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'cycle_count_entry_line')
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'inline_entity_form_complex',
        'weight' => 0,
        'settings' => [
          'form_mode' => 'default',
          'allow_new' => TRUE,
          'allow_existing' => FALSE,
          'match_operator' => 'CONTAINS',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The timestamp that the cycle count entry was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The timestamp that the cycle count entry was last changed.'));

    return $fields;
  }

  /**
   * Default value callback for 'user' base field definition.
   *
   * @return array
   *   An array of default values.
   */
  public static function getCurrentUserId() {
    return [\Drupal::currentUser()->id()];
  }

}
