<?php

namespace Drupal\bs_inventory\Tests;

/**
 * Tests the bs_inventory module.
 *
 * @group bs_inventory
 */
class InventoryTest extends InventoryTestBase {

  /**
   * A user with project admin permission.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = ['block'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser([
      'access administration pages',
    ]);
  }

  /**
   * Tests the implementation status block.
   */
  public function testInventory() {
    $this->drupalPlaceBlock('inventory_implementation_status');

    $this->drupalLogin($this->adminUser);

    $this->drupalGet('<front>');
    $this->assertResponse(200);
    $this->assertLinkByHref('admin/inventory/implementation_status');

    $this->clickLink(t('Inventory Implementation Status'));
    $this->assertResponse(200);
  }

}
