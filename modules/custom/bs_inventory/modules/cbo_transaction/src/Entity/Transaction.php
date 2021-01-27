<?php

namespace Drupal\cbo_transaction\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\cbo_transaction\TransactionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Defines the transaction entity class.
 *
 * @ContentEntityType(
 *   id = "transaction",
 *   label = @Translation("Transaction"),
 *   bundle_label = @Translation("Transaction type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "access" = "Drupal\cbo_transaction\TransactionAccessControlHandler",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\cbo_transaction\TransactionForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *   },
 *   base_table = "transaction",
 *   entity_keys = {
 *     "id" = "tid",
 *     "bundle" = "type",
 *     "label" = "number",
 *     "uuid" = "uuid",
 *     "status" = "status",
 *   },
 *   bundle_entity_type = "transaction_type",
 *   field_ui_base_route = "entity.transaction_type.edit_form",
 *   admin_permission = "administer transactions",
 *   links = {
 *     "add-page" = "/admin/transaction/add",
 *     "add-form" = "/admin/transaction/add/{transaction_type}",
 *     "canonical" = "/admin/transaction/{transaction}",
 *     "edit-form" = "/admin/transaction/{transaction}/edit",
 *     "delete-form" = "/admin/transaction/{transaction}/delete",
 *     "collection" = "/admin/transaction",
 *   }
 * )
 */
class Transaction extends ContentEntityBase implements TransactionInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['number'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Number'))
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
        'settings' => [
          'size' => '16',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['user'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('By'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback('Drupal\cbo_transaction\Entity\Transaction::getCurrentUserId')
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

    $fields['date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Date'))
      ->setSetting('datetime_type', 'date')
      ->setDisplayOptions('view', [
        'type' => 'datetime_default',
        'settings' => [
          'format_type' => 'html_date',
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['lines'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Items'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'transaction_line')
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_table',
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
   * Default value callback for 'user' base field definition.
   *
   * @return array
   *   An array of default values.
   */
  public static function getCurrentUserId() {
    return [\Drupal::currentUser()->id()];
  }

}
