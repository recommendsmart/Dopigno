<?php

namespace Drupal\cbo_transaction;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a transaction entity.
 */
interface TransactionInterface extends ContentEntityInterface, EntityChangedInterface {

}
