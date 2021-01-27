<?php

namespace Drupal\notification_system_dispatch\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Notification Dispatch Bundle entities.
 */
class NotificationDispatchBundleViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
