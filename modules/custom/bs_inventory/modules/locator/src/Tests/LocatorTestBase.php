<?php

namespace Drupal\locator\Tests;

use Drupal\cbo_inventory\Tests\InventoryTestBase;
use Drupal\locator\Entity\Locator;

/**
 * Provides tool functions.
 */
abstract class LocatorTestBase extends InventoryTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['locator'];

  /**
   * A locator.
   *
   * @var \Drupal\locator\LocatorInterface
   */
  protected $locator;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->locator = $this->createLocator();
  }

  /**
   * Creates a locator based on default settings.
   */
  protected function createLocator(array $settings = []) {
    // Populate defaults array.
    $settings += [
      'title' => $this->randomMachineName(8),
      'description' => $this->randomMachineName(8),
      'subinventory' => $this->subinventory->id(),
    ];
    $entity = Locator::create($settings);
    $entity->save();

    return $entity;
  }

}
