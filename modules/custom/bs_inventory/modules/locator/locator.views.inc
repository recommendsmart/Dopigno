<?php

/**
 * @file
 * Provide views data for locator.module.
 */

/**
 * Implements hook_views_data().
 */
function locator_views_data() {
  $data['locator_quantity']['table']['group'] = t('Locator quantities');

  $data['locator_quantity']['table']['base'] = [
    'title' => t('Locator quantities'),
  ];

  $data['locator_quantity']['lid'] = [
    'title' => t('Locator'),
    'field' => [
      'id' => 'numeric',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
    'argument' => [
      'id' => 'numeric',
    ],
    'relationship' => [
      'title' => t('Locator'),
      'base' => 'locator',
      'base field' => 'lid',
      'id' => 'standard',
    ],
  ];

  $data['locator_quantity']['iid'] = [
    'title' => t('Item'),
    'field' => [
      'id' => 'numeric',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
    'argument' => [
      'id' => 'numeric',
    ],
    'relationship' => [
      'title' => t('Item'),
      'base' => 'item',
      'base field' => 'iid',
      'id' => 'standard',
    ],
  ];

  $data['locator_quantity']['table']['join'] = [
    'locator' => [
      'left_field' => 'lid',
      'field' => 'lid',
    ],
    'item' => [
      'left_field' => 'iid',
      'field' => 'iid',
    ],
  ];

  $data['locator_quantity']['quantity'] = [
    'title' => t('Quantity'),
    'field' => [
      'id' => 'numeric',
      'float' => TRUE,
      'click sortable' => TRUE,
    ],
  ];

  return $data;
}
