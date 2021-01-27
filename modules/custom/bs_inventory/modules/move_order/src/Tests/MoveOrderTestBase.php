<?php

namespace Drupal\move_order\Tests;

use Drupal\cbo_transaction\Tests\TransactionTestBase;
use Drupal\move_order\Entity\MoveOrder;

/**
 * Provides helper functions for move_order module tests.
 */
abstract class MoveOrderTestBase extends TransactionTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['move_order'];

  /**
   * A move_order.
   *
   * @var \Drupal\move_order\MoveOrderInterface
   */
  protected $moveOrder;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->moveOrder = $this->createMoveOrder();
  }

  /**
   * Creates a move_order based on default settings.
   */
  protected function createMoveOrder(array $settings = []) {
    // Populate defaults array.
    $settings += [
      'type' => 'requisition',
      'title' => $this->randomMachineName(8),
      'description' => $this->randomMachineName(8),
    ];
    $entity = MoveOrder::create($settings);
    $entity->save();

    return $entity;
  }

}
