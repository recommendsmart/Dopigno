<?php

namespace Drupal\cycle_count;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a cycle count entity.
 */
interface CycleCountInterface extends ContentEntityInterface, EntityChangedInterface {

}
