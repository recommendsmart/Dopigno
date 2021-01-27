<?php

namespace Drupal\notification_system_dispatch\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Notification Dispatch Bundle entities.
 *
 * @ingroup notification_system_dispatch
 */
interface NotificationDispatchBundleInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Notification Dispatch Bundle name.
   *
   * @return string
   *   Name of the Notification Dispatch Bundle.
   */
  public function getName();

  /**
   * Sets the Notification Dispatch Bundle name.
   *
   * @param string $name
   *   The Notification Dispatch Bundle name.
   *
   * @return \Drupal\notification_system_dispatch\Entity\NotificationDispatchBundleInterface
   *   The called Notification Dispatch Bundle entity.
   */
  public function setName($name);

  /**
   * Gets the Notification Dispatch Bundle creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Notification Dispatch Bundle.
   */
  public function getCreatedTime();

  /**
   * Sets the Notification Dispatch Bundle creation timestamp.
   *
   * @param int $timestamp
   *   The Notification Dispatch Bundle creation timestamp.
   *
   * @return \Drupal\notification_system_dispatch\Entity\NotificationDispatchBundleInterface
   *   The called Notification Dispatch Bundle entity.
   */
  public function setCreatedTime($timestamp);

}
