<?php

namespace Drupal\cbo_transaction;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityDescriptionInterface;

/**
 * Provides an interface defining a transaction type entity.
 */
interface TransactionTypeInterface extends ConfigEntityInterface, EntityDescriptionInterface {

  /**
   * Gets the transaction source type.
   *
   * @return string
   *   The transaction source type.
   */
  public function getSourceType();

  /**
   * Gets the transaction action.
   *
   * @return string
   *   The transaction action.
   */
  public function getAction();

}
