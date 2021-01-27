<?php

namespace Drupal\cbo_transaction;

use Drupal\Core\Entity\BundleEntityFormBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form handler for transaction type forms.
 */
class TransactionTypeForm extends BundleEntityFormBase {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $type = $this->entity;
    if ($this->operation == 'add') {
      $form['#title'] = $this->t('Add transaction type');
    }
    else {
      $form['#title'] = $this->t('Edit %label transaction type', ['%label' => $type->label()]);
    }

    $form['label'] = [
      '#title' => t('Name'),
      '#type' => 'textfield',
      '#default_value' => $type->label(),
      '#required' => TRUE,
      '#size' => 30,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $type->id(),
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
      '#machine_name' => [
        'exists' => ['Drupal\cbo_transaction\Entity\TransactionType', 'load'],
        'source' => ['label'],
      ],
      '#description' => t('A unique machine-readable name for this transaction type. It must only contain lowercase letters, numbers, and underscores. This name will be used for constructing the URL of the %transaction-add page, in which underscores will be converted into hyphens.', [
        '%transaction-add' => t('Add transaction'),
      ]),
    ];

    $form['description'] = [
      '#title' => t('Description'),
      '#type' => 'textfield',
      '#default_value' => $type->getDescription(),
      '#size' => 30,
    ];

    $entities = $this->entityTypeManager->getStorage('transaction_source_type')->loadMultiple();
    $options = array_map(function ($entity) {
      return $entity->label();
    }, $entities);
    $form['source_type'] = [
      '#title' => t('Source Type'),
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $type->getSourceType(),
    ];

    $form['action'] = [
      '#title' => t('Action'),
      '#type' => 'select',
      '#options' => [
        'issue_from_store' => 'Issue from stores',
        'subinventory_transfer' => 'Subinventory transfer',
        'direct_organization_transfer' => 'Direct organization transfer',
        'cycle_count_adjustment' => 'Cycle count adjustment',
        'physical_inventory_adjustment' => 'Physical inventory adjustment',
        'intransit_receipt' => 'Intransit receipt',
        'intransit_shipment' => 'Intransit shipment',
        'cost_update' => 'Cost update',
        'receipt_into_stores' => 'Receipt into stores',
        'delivery_adjustments' => 'Delivery adjustments',
        'wip_assembly_scrap' => 'WIP assembly scrap',
        'assembly_completion' => 'Assembly completion',
        'assembly_return' => 'Assembly return',
        'negative_component_issue' => 'Negative component issue',
        'negative_component_return' => 'Negative component return',
        'staging_transfer' => 'Staging Rransfer',
        'ownership_transfer' => 'Ownership Transfer',
        'logical_issue' => 'Logical Issue',
        'logical_delivery_adjustment' => 'Logical Delivery Adjustment',
        'retroactive_price_adjustment' => 'Retroactive Price Adjustment',
        'logical_receipt' => 'Logical Receipt',
        'delivery_adjustment' => 'Delivery Adjustment',
        'lot_split' => 'Lot Split',
        'lot_merge' => 'Lot Merge',
        'lot_translate' => 'Lot Translate',
        'lot_update_quantity' => 'Lot Update Quantity',
        'logical_expense_requisition_receipt' => 'Logical Expense Requisition Receipt',
        'planning_transfer' => 'Planning Transfer',
        'ownership_transfer' => 'Ownership Transfer',
        'logical_intercompany_sales' => 'Logical Intercompany Sales',
        'logical_intercompany_receipt' => 'Logical Intercompany Receipt',
        'logical_intercompany_receipt_return' => 'Logical Intercompany Receipt Return',
        'logical_intercompany_sales_return' => 'Logical Intercompany Sales Return',
        'container_pack' => 'Container Pack',
        'container_unpack' => 'Container Unpack',
        'container_split' => 'Container Split',
        'cost_group_transfer' => 'Cost Group Transfer',
      ],
      '#default_value' => $type->getAction(),
    ];

    return $this->protectBundleIdElement($form);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $id = trim($form_state->getValue('id'));
    // '0' is invalid, since elsewhere we check it using empty().
    if ($id == '0') {
      $form_state->setErrorByName('id', $this->t("Invalid machine-readable name. Enter a name other than %invalid.", ['%invalid' => $id]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $type = $this->entity;
    $type->set('id', trim($type->id()));
    $type->set('label', trim($type->label()));

    $status = $type->save();

    $t_args = ['%name' => $type->label()];

    if ($status == SAVED_UPDATED) {
      drupal_set_message(t('The transaction type %name has been updated.', $t_args));
    }
    elseif ($status == SAVED_NEW) {
      drupal_set_message(t('The transaction type %name has been added.', $t_args));
      $context = array_merge($t_args, ['link' => $type->link($this->t('View'), 'collection')]);
      $this->logger('transaction')->notice('Added transaction type %name.', $context);
    }
  }

}
