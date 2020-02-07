<?php

namespace Drupal\Tests\commerce_invoice\Kernel;

use Drupal\Tests\commerce\Kernel\CommerceKernelTestBase;

/**
 * Provides a base class for invoice kernel tests.
 */
abstract class InvoiceKernelTestBase extends CommerceKernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'entity_print',
    'entity_reference_revisions',
    'profile',
    'state_machine',
    'commerce_order',
    'commerce_number_pattern',
    'commerce_invoice',
    'commerce_invoice_test',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('profile');
    $this->installEntitySchema('commerce_order');
    $this->installEntitySchema('commerce_order_item');
    $this->installEntitySchema('commerce_invoice_item');
    $this->installEntitySchema('commerce_invoice');
    $this->installEntitySchema('commerce_number_pattern');
    $this->installSchema('commerce_number_pattern', ['commerce_number_pattern_sequence']);
    $this->installConfig([
      'commerce_invoice',
      'entity_print',
      'commerce_order',
    ]);
  }

}
