<?php

namespace Drupal\notification_system\model;

/**
 * Functionality if a notification can handle marking notifications as read.
 */
interface ReadableNotificationInterface {

  /**
   * Is this notification read by a specific user?
   *
   * @param int $user
   *   ID of the user to check.
   *
   * @return bool
   *   Read status.
   */
  public function isReadBy(int $user);

}
