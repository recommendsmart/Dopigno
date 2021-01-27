<?php

namespace Drupal\cycle_count\Tests;

/**
 * Tests cycle count entities.
 *
 * @group cycle_count
 */
class CycleCountTest extends CycleCountTestBase {

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
  public static $modules = ['block', 'views'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser([
      'administer cycle counts',
      'access cycle count',
    ]);
  }

  /**
   * Tests the list, add, save page.
   */
  public function testList() {
    $this->drupalPlaceBlock('local_tasks_block');
    $this->drupalPlaceBlock('local_actions_block');

    $this->drupalLogin($this->adminUser);

    $this->drupalGet('admin/cycle_count');
    $this->assertResponse(200);
    $this->assertLinkByHref('admin/cycle_count/add');

    $this->clickLink(t('Add cycle count'));
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
   * Tests the edit form.
   */
  public function testEdit() {
    $this->drupalPlaceBlock('local_tasks_block');

    $this->drupalLogin($this->adminUser);

    $this->drupalGet('admin/cycle_count/' . $this->cycleCount->id());
    $this->assertResponse(200);
    $this->assertLinkByHref('admin/cycle_count/' . $this->cycleCount->id() . '/edit');

    $this->clickLink(t('Edit'));
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
