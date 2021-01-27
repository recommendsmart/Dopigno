<?php

namespace Drupal\notification_system_database;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Provides an interface defining a notification entity type.
 */
interface NotificationInterface extends ContentEntityInterface {

  /**
   * Gets the notification title.
   *
   * @return string
   *   Title of the notification.
   */
  public function getTitle();

  /**
   * Sets the notification title.
   *
   * @param string $title
   *   The notification title.
   *
   * @return \Drupal\notification_system_database\NotificationInterface
   *   The called notification entity.
   */
  public function setTitle($title);

  /**
   * Gets the notification creation timestamp.
   *
   * @return int
   *   Creation timestamp of the notification.
   */
  public function getCreatedTime();

  /**
   * Sets the notification creation timestamp.
   *
   * @param int $timestamp
   *   The notification creation timestamp.
   *
   * @return \Drupal\notification_system_database\NotificationInterface
   *   The called notification entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Converts the entity to a notification model.
   *
   * @return \Drupal\notification_system\model\Notification
   *   The notification model.
   */
  public function toNotificationModel();

}
