<?php

namespace Drupal\move_order\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\move_order\MoveOrderLineInterface;

/**
 * Defines the move order line entity class.
 *
 * @ContentEntityType(
 *   id = "move_order_line",
 *   label = @Translation("Move order line"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "access" = "Drupal\move_order\MoveOrderLineAccessControlHandler",
 *     "views_data" = "Drupal\move_order\MoveOrderLineViewsData",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "inline_form" = "Drupal\move_order\Form\MoveOrderLineInlineForm",
 *   },
 *   base_table = "move_order_line",
 *   entity_keys = {
 *     "id" = "lid",
 *     "uuid" = "uuid",
 *   },
 *   admin_permission = "administer move orders",
 *   links = {
 *     "add-form" = "/admin/move_order_line/add",
 *     "canonical" = "/admin/move_order_line/{move_order_line}",
 *     "edit-form" = "/admin/move_order_line/{move_order_line}/edit",
 *     "delete-form" = "/admin/move_order_line/{move_order_line}/delete",
 *     "collection" = "/admin/move_order_line",
 *   },
 * )
 */
class MoveOrderLine extends ContentEntityBase implements MoveOrderLineInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['item'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Item'))
      ->setRequired(TRUE)
      ->setSetting('target_type', 'item')
      ->addConstraint('TransactableItem')
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => -3,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -3,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '16',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['transaction_type'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Transaction Type'))
      ->setSetting('target_type', 'transaction_type')
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['source_subinventory'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Source Subinventory'))
      ->setSetting('target_type', 'subinventory')
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['destination_subinventory'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Destination Subinventory'))
      ->setSetting('target_type', 'subinventory')
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['date_required'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Date Required'))
      ->setDescription(t('The date the items are required to be transferred.'))
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

    $fields['uom'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('UOM'))
      ->setRequired(TRUE)
      ->setSetting('target_type', 'uom')
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => -3,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -3,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '16',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['quantity'] = BaseFieldDefinition::create('float')
      ->setLabel(t('Quantity'))
      ->setRequired(TRUE)
      ->setDefaultValue(1)
      ->setDisplayOptions('view', [
        'type' => 'number_decimal',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'number',
        'weight' => 0,
        'settings' => [
          'size' => '16',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The timestamp that the move order line was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The timestamp that the move order line was last changed.'));

    return $fields;
  }

}
