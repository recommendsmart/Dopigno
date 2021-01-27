<?php

namespace Drupal\move_order;

use Drupal\views\EntityViewsData;

/**
 * Provides the views data for the move_order entity type.
 */
class MoveOrderViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['move_order']['table']['base']['access query tag'] = 'move_order_access';

    $data['move_order']['bulk_form'] = [
      'title' => $this->t('Operations bulk form'),
      'help' => $this->t('Add a form element that lets you run operations on multiple move orders.'),
      'field' => [
        'id' => 'bulk_form',
      ],
    ];

    return $data;
  }

}
