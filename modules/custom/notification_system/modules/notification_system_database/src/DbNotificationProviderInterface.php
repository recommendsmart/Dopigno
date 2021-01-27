<?php

namespace Drupal\notification_system_database;

/**
 * Interface for db_notification_provider plugins.
 */
interface DbNotificationProviderInterface {

  /**
   * Returns the translated plugin label.
   *
   * @return string
   *   The translated title.
   */
  public function label();

  /**
   * Returns the plugin id.
   *
   * @return string
   *   The plugin id.
   */
  public function id();

  /**
   * Get a list of notification types that this provider creates.
   *
   * @return string[]
   *   The notification types.
   */
  public function getTypes();

}
