<?php

namespace Drupal\cycle_count\Tests;

/**
 * Tests cycle_count_entry entities.
 *
 * @group cycle_count
 */
class CycleCountEntryTest extends CycleCountTestBase {

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
      'administer cycle count entries',
      'access cycle count entry',
    ]);
  }

  /**
   * Tests the list, add, save page.
   */
  public function testList() {
    $this->drupalPlaceBlock('local_tasks_block');
    $this->drupalPlaceBlock('local_actions_block');

    $this->drupalLogin($this->adminUser);

    $this->drupalGet('admin/cycle_count_entry');
    $this->assertResponse(200);
    $this->assertLinkByHref('admin/cycle_count_entry/add');

    $this->clickLink(t('Add cycle count entry'));
    $this->assertResponse(200);

    $edit = [
      'cycle_count[0][value]' => $this->cycleCount->label() . ' (' . $this->cycleCount->id() . ')',
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->assertResponse(200);
    // $this->assertText($edit['number[0][value]']);
  }

  /**
   * Tests the edit form.
   */
  public function testEdit() {
    $this->drupalPlaceBlock('local_tasks_block');

    $this->drupalLogin($this->adminUser);

    $this->drupalGet('admin/cycle_count_entry/' . $this->cycleCountEntry->id());
    $this->assertResponse(200);
    $this->assertLinkByHref('admin/cycle_count_entry/' . $this->cycleCountEntry->id() . '/edit');

    $this->clickLink(t('Edit'));
    $this->assertResponse(200);

    $edit = [
      'cycle_count[0][value]' => $this->cycleCount->label() . ' (' . $this->cycleCount->id() . ')',
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->assertResponse(200);
    // $this->assertText($edit['number[0][value]']);
  }

}
