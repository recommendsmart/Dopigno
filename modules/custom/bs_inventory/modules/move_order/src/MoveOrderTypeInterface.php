<?php

namespace Drupal\move_order;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityDescriptionInterface;

/**
 * Provides an interface defining a move order type entity.
 */
interface MoveOrderTypeInterface extends ConfigEntityInterface, EntityDescriptionInterface {

  /**
   * Determines whether the move order type is locked.
   *
   * @return string|false
   *   The module name that locks the type or FALSE.
   */
  public function isLocked();

}
