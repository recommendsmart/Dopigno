<?php

namespace Drupal\inventory_demo\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Sets up modules.
 */
abstract class InventoryDemoTestBase extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['inventory_demo'];

}
