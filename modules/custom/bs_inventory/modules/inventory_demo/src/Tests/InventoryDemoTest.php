<?php

namespace Drupal\inventory_demo\Tests;

/**
 * Tests inventory_demo module.
 *
 * @group inventory_demo
 */
class InventoryDemoTest extends InventoryDemoTestBase {

  /**
   * A user with inventory_demo admin permission.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = ['block', 'views'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser([
      'administer item categories',
      'administer items',
      'access item',
    ]);
  }

  /**
   * Tests the data import.
   */
  public function testDataImport() {
    $this->drupalLogin($this->adminUser);

    $this->drupalGet('admin/item/category');
    $this->assertText('Hardware', 'Item category imported');

    $this->drupalGet('admin/item');
    $this->assertLink('CM2345', 0, 'Item imported');

    $this->drupalGet('admin/locator');
    $this->assertLink('1.2.1', 0, 'Locator imported');
  }

}
