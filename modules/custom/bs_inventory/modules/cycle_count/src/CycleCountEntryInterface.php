<?php

namespace Drupal\cycle_count;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a cycle_count_entry entity.
 */
interface CycleCountEntryInterface extends ContentEntityInterface, EntityChangedInterface {

}
