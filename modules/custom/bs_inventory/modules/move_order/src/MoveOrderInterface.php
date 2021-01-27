<?php

namespace Drupal\move_order;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a move_order entity.
 */
interface MoveOrderInterface extends ContentEntityInterface, EntityChangedInterface {

  /**
   * Gets the move_order description.
   *
   * @return string
   *   Description of the move_order.
   */
  public function getDescription();

}
