<?php

namespace Drupal\locator;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a locator entity.
 */
interface LocatorInterface extends ContentEntityInterface, EntityChangedInterface {

}
