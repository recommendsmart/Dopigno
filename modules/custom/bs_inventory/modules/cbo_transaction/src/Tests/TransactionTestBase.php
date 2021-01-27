<?php

namespace Drupal\cbo_transaction\Tests;

use Drupal\cbo_inventory\Tests\InventoryTestBase;
use Drupal\cbo_transaction\Entity\Transaction;

/**
 * Provides tool functions.
 */
abstract class TransactionTestBase extends InventoryTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['cbo_transaction'];

  /**
   * A transaction.
   *
   * @var \Drupal\cbo_transaction\TransactionInterface
   */
  protected $transaction;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->transaction = $this->createTransaction();
  }

  /**
   * Creates a transaction based on default settings.
   */
  protected function createTransaction(array $settings = []) {
    // Populate defaults array.
    $settings += [
      'type' => 'rma_receipt',
      'number' => $this->randomMachineName(8),
    ];
    $entity = Transaction::create($settings);
    $entity->save();

    return $entity;
  }

}
