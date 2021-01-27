<?php

namespace Drupal\cycle_count\Tests;

use Drupal\cbo_inventory\Tests\InventoryTestBase;
use Drupal\cycle_count\Entity\CycleCount;
use Drupal\cycle_count\Entity\CycleCountEntry;

/**
 * Provides tool functions.
 */
abstract class CycleCountTestBase extends InventoryTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['cycle_count'];

  /**
   * A cycle_count.
   *
   * @var \Drupal\cycle_count\CycleCountInterface
   */
  protected $cycleCount;

  /**
   * A cycle_count_entry.
   *
   * @var \Drupal\cycle_count\CycleCountEntryInterface
   */
  protected $cycleCountEntry;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->cycleCount = $this->createCycleCount();
    $this->cycleCountEntry = $this->createCycleCountEntry();
  }

  /**
   * Creates a cycle_count based on default settings.
   */
  protected function createCycleCount(array $settings = []) {
    // Populate defaults array.
    $settings += [
      'title' => $this->randomMachineName(8),
      'description' => $this->randomMachineName(8),
    ];
    $entity = CycleCount::create($settings);
    $entity->save();

    return $entity;
  }

  /**
   * Creates a cycle_count based on default settings.
   */
  protected function createCycleCountEntry(array $settings = []) {
    // Populate defaults array.
    $settings += [
      'cycle_count' => $this->cycleCount,
    ];
    $entity = CycleCountEntry::create($settings);
    $entity->save();

    return $entity;
  }

}
