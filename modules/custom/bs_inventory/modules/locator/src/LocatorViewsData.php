<?php

namespace Drupal\locator;

use Drupal\views\EntityViewsData;

/**
 * Provides the views data for the locator entity types.
 */
class LocatorViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['locator']['subinventory']['filter']['id'] = 'subinventory';

    return $data;
  }

}
