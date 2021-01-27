<?php

namespace Drupal\cycle_count\Form;

use Drupal\Core\Entity\Element\EntityAutocomplete;
use Drupal\Core\Form\FormStateInterface;
use Drupal\inline_entity_form\Form\EntityInlineForm;

/**
 * Cycle count entry line inline form handler.
 */
class CycleCountEntryLineInlineForm extends EntityInlineForm {

  /**
   * {@inheritdoc}
   */
  public function getTableFields($bundles) {
    $fields = parent::getTableFields($bundles);

    unset($fields['label']);

    $fields['item'] = [
      'type' => 'field',
      'label' => 'Item',
      'weight' => 0,
      'display_options' => [
        'type' => 'entity_reference_label',
      ],
    ];
    $fields['subinventory'] = [
      'type' => 'field',
      'label' => 'Subinventory',
      'weight' => 10,
      'display_options' => [
        'type' => 'entity_reference_label',
      ],
    ];
    $fields['locator'] = [
      'type' => 'field',
      'label' => 'Locator',
      'weight' => 20,
      'display_options' => [
        'type' => 'entity_reference_label',
      ],
    ];
    $fields['lot'] = [
      'type' => 'field',
      'label' => 'Lot',
      'weight' => 30,
      'display_options' => [
        'type' => 'entity_reference_label',
      ],
    ];
    $fields['serial_number'] = [
      'type' => 'field',
      'label' => 'Serial Number',
      'weight' => 40,
      'display_options' => [
        'type' => 'string',
      ],
    ];
    $fields['uom'] = [
      'type' => 'field',
      'label' => 'UOM',
      'weight' => 50,
      'display_options' => [
        'type' => 'entity_reference_label',
      ],
    ];
    $fields['quantity'] = [
      'type' => 'field',
      'label' => 'Quantity',
      'weight' => 60,
      'display_options' => [
        'type' => 'number_decimal',
      ],
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function entityForm(array $entity_form, FormStateInterface $form_state) {
    $form = parent::entityForm($entity_form, $form_state);

    $form['container'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['container-inline'],
      ],
    ];
    $form['container']['item'] = $form['item'];
    unset($form['item']);
    $form['container']['subinventory'] = $form['subinventory'];
    unset($form['subinventory']);
    $form['container']['locator'] = $form['locator'];
    unset($form['locator']);
    $form['container']['lot'] = $form['lot'];
    unset($form['lot']);
    $form['container']['serial_number'] = $form['serial_number'];
    unset($form['serial_number']);
    $form['container']['uom'] = $form['uom'];
    unset($form['uom']);
    $form['container']['quantity'] = $form['quantity'];
    unset($form['quantity']);

    $form['container']['subinventory']['#prefix'] = '<div id="wrapper-by-item">';
    $form['container']['uom']['#suffix'] = '</div>';
    $form['container']['locator']['widget'][0]['target_id']['#access'] = FALSE;
    $form['container']['lot']['widget'][0]['target_id']['#access'] = FALSE;
    $form['container']['serial_number']['widget'][0]['value']['#access'] = FALSE;
    $form['container']['item']['widget'][0]['target_id']['#ajax'] = [
      'callback' => __CLASS__ . '::itemSwitch',
      'wrapper' => 'wrapper-by-item',
      'event' => 'autocompleteclose',
    ];

    return $form;
  }

  /**
   * Handles switching the uom based on the selected item.
   */
  public function itemSwitch($form, FormStateInterface $form_state) {
    $inline_entity_form = $form['lines']['widget']['form']['inline_entity_form'];
    $item_value = $inline_entity_form['container']['item']['widget'][0]['target_id']['#value'];
    $id = EntityAutocomplete::extractEntityIdFromAutocompleteInput($item_value);
    // Do not using $this when not in object context
    if ($item = \Drupal::entityTypeManager()->getStorage('item')->load($id)) {
      // TODO: restrict subinventory
      $inline_entity_form['container']['locator']['widget'][0]['target_id']['#access'] = $item->get('locator_control')->value != 'no_control';
      $inline_entity_form['container']['lot']['widget'][0]['target_id']['#access'] = $item->get('lot_control')->value;
      $inline_entity_form['container']['serial_number']['widget'][0]['value']['#access'] = $item->get('serial_generation')->value != 'no_control';

      $uom = $item->get('uom')->entity;
      $inline_entity_form['container']['uom']['widget'][0]['target_id']['#value'] = $uom->label() . ' (' . $uom->id() . ')';
    }

    return [
      $inline_entity_form['container']['subinventory'],
      $inline_entity_form['container']['locator'],
      $inline_entity_form['container']['lot'],
      $inline_entity_form['container']['serial_number'],
      $inline_entity_form['container']['uom'],
    ];
  }

}
