<?php

namespace Drupal\move_order\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\move_order\MoveOrderInterface;

/**
 * Defines the move_order entity class.
 *
 * @ContentEntityType(
 *   id = "move_order",
 *   label = @Translation("Move order"),
 *   bundle_label = @Translation("Move order type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "access" = "Drupal\move_order\MoveOrderAccessControlHandler",
 *     "views_data" = "Drupal\move_order\MoveOrderViewsData",
 *     "form" = {
 *       "default" = "Drupal\move_order\MoveOrderForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *   },
 *   base_table = "move_order",
 *   entity_keys = {
 *     "id" = "oid",
 *     "bundle" = "type",
 *     "label" = "title",
 *     "uuid" = "uuid",
 *     "status" = "status",
 *   },
 *   bundle_entity_type = "move_order_type",
 *   field_ui_base_route = "entity.move_order_type.edit_form",
 *   admin_permission = "administer move orders",
 *   links = {
 *     "add-page" = "/admin/move_order/add",
 *     "add-form" = "/admin/move_order/add/{move_order_type}",
 *     "canonical" = "/admin/move_order/{move_order}",
 *     "edit-form" = "/admin/move_order/{move_order}/edit",
 *     "delete-form" = "/admin/move_order/{move_order}/delete",
 *     "collection" = "/admin/move_order",
 *   }
 * )
 */
class MoveOrder extends ContentEntityBase implements MoveOrderInterface {

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

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Number'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', [
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

    $fields['created_by'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Created By'))
      ->setDescription(t('The user creating the move order requisition.'))
      ->setRequired(TRUE)
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback('Drupal\move_order\Entity\MoveOrder::getCurrentUserId')
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => -3,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('state')
      ->setLabel(t('Status'))
      ->setRequired(TRUE)
      ->setSetting('workflow', 'move_order_status')
      ->setDefaultValue('incomplete')
      ->setDisplayOptions('view', [
        'type' => 'list_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['lines'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Items'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'move_order_line')
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
      ->setDescription(t('The timestamp that the bom was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The timestamp that the bom was last changed.'));

    return $fields;
  }

  /**
   * Default value callback for 'created_by' base field definition.
   *
   * @see ::baseFieldDefinitions()
   *
   * @return array
   *   An array of default values.
   */
  public static function getCurrentUserId() {
    return [\Drupal::currentUser()->id()];
  }

}
