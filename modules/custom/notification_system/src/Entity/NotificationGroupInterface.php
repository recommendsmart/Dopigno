<?php

namespace Drupal\notification_system\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Notification Group entities.
 */
interface NotificationGroupInterface extends ConfigEntityInterface {

  /**
   * Returns the weight.
   *
   * @return int
   *   The weight of this role.
   */
  public function getWeight();

  /**
   * Sets the weight to the given value.
   *
   * @param int $weight
   *   The desired weight.
   *
   * @return $this
   */
  public function setWeight($weight);

  /**
   * Get the description of the group.
   *
   * @return array
   *   An array keyed with 'value' and 'format'.
   */
  public function getDescription();

}
