<?php

namespace Drupal\cbo_transaction\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\cbo_transaction\TransactionLineInterface;

/**
 * Defines the transaction line entity class.
 *
 * @ContentEntityType(
 *   id = "transaction_line",
 *   label = @Translation("Transaction Line"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "access" = "Drupal\cbo_transaction\TransactionLineAccessControlHandler",
 *     "views_data" = "Drupal\cbo_transaction\TransactionLineViewsData",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "inline_form" = "Drupal\cbo_transaction\Form\TransactionLineInlineForm",
 *   },
 *   base_table = "transaction_line",
 *   entity_keys = {
 *     "id" = "lid",
 *     "uuid" = "uuid",
 *   },
 *   admin_permission = "administer transactions",
 *   links = {
 *     "add-form" = "/admin/transaction_line/add",
 *     "canonical" = "/admin/transaction_line/{transaction_line}",
 *     "edit-form" = "/admin/transaction_line/{transaction_line}/edit",
 *     "delete-form" = "/admin/transaction_line/{transaction_line}/delete",
 *     "collection" = "/admin/transaction_line",
 *   },
 * )
 */
class TransactionLine extends ContentEntityBase implements TransactionLineInterface {

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

    $fields['subinventory'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Subinventory'))
      ->setRequired(TRUE)
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

    $fields['locator'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Locator'))
      ->setSetting('target_type', 'locator')
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

    $fields['lot'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Lot'))
      ->setSetting('target_type', 'item_lot')
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

    $fields['serial_number'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Serial Number'))
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', [
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
        'settings' => [
          'size' => '16',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['uom'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('UOM'))
      ->setRequired(TRUE)
      ->setSetting('target_type', 'uom')
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
      ->setDescription(t('The timestamp that the transaction line was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The timestamp that the transaction line was last changed.'));

    return $fields;
  }

}
