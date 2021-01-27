<?php

namespace Drupal\bs_inventory\Tests;

use Drupal\cbo_item\Tests\ItemTestBase;

/**
 * Provides tool functions.
 */
abstract class InventoryTestBase extends ItemTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['bs_inventory'];

}
