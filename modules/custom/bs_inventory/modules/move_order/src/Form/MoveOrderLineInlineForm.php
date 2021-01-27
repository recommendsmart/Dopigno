<?php

namespace Drupal\move_order\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\inline_entity_form\Form\EntityInlineForm;

/**
 * MoveOrder line inline form handler.
 */
class MoveOrderLineInlineForm extends EntityInlineForm {

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
    $fields['uom'] = [
      'type' => 'field',
      'label' => 'UOM',
      'weight' => 30,
      'display_options' => [
        'type' => 'entity_reference_label',
      ],
    ];
    $fields['quantity'] = [
      'type' => 'field',
      'label' => 'Quantity',
      'weight' => 40,
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
    $form['container']['uom'] = $form['uom'];
    unset($form['uom']);
    $form['container']['quantity'] = $form['quantity'];
    unset($form['quantity']);

    $form['container']['uom']['#prefix'] = '<div id="uom-wrapper">';
    $form['container']['uom']['#suffix'] = '</div>';
    $form['container']['item']['widget'][0]['target_id']['#ajax'] = [
      'callback' => '::itemSwitch',
      'wrapper' => 'uom-wrapper',
    ];

    return $form;
  }

  /**
   * Handles switching the uom based on the selected item.
   */
  public function itemSwitch($form, FormStateInterface $form_state) {
    if ($item = $form_state->getValue('item')) {
      if ($item = $this->entityTypeManager->getStorage('item')->load($item)) {
        $form['container']['uom']['#default_value'] = $item->get('uom')->target_id;
      }
    }

    return $form['container']['uom'];
  }

}
