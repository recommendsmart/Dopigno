<?php

namespace Drupal\move_order\Tests;

/**
 * Tests move_order entity.
 *
 * @group move_order
 */
class MoveOrderTest extends MoveOrderTestBase {

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = ['block', 'views'];

  /**
   * A user with permission to administer move_order.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser([
      'administer move orders',
      'access move order',
      'access organization',
    ]);
    $this->adminUser->people->target_id = $this->people->id();
    $this->adminUser->save();
  }

  /**
   * Test list, add, save.
   */
  public function testList() {
    $this->drupalPlaceBlock('local_actions_block');

    $this->drupalLogin($this->adminUser);

    $this->drupalGet('admin/move_order');
    $this->assertResponse(200);
    $this->assertLinkByHref('admin/move_order/add');

    $this->clickLink(t('Add move order'));
    $this->assertResponse(200);
    $this->assertLinkByHref('admin/move_order/add/requisition');

    $this->clickLink(t('Requisition'));
    $this->assertResponse(200);
    $edit = [
      'title[0][value]' => $this->randomMachineName(8),
      'description[0][value]' => $this->randomMachineName(8),
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->assertResponse(200);
    $this->assertText($edit['title[0][value]']);
  }

  /**
   * Tests edit page.
   */
  public function testEdit() {
    $this->drupalLogin($this->adminUser);

    $this->drupalGet('admin/move_order/' . $this->moveOrder->id() . '/edit');
    $this->assertResponse(200);

    $edit = [
      'title[0][value]' => $this->randomMachineName(8),
      'description[0][value]' => $this->randomMachineName(8),
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->assertResponse(200);
    $this->assertText($edit['title[0][value]']);
  }

}
