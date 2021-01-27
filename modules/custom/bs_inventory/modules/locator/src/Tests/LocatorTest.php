<?php

namespace Drupal\locator\Tests;

/**
 * Tests locator entities.
 *
 * @group locator
 */
class LocatorTest extends LocatorTestBase {

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
      'administer locators',
      'access locator',
      'access subinventory',
    ]);
    $this->adminUser->people->target_id = $this->people->id();
    $this->adminUser->save();
  }

  /**
   * Tests the list, add, save.
   */
  public function testList() {
    $this->drupalPlaceBlock('local_tasks_block');
    $this->drupalPlaceBlock('local_actions_block');

    $this->drupalLogin($this->adminUser);

    $this->drupalGet('admin/locator');
    $this->assertResponse(200);
    $this->assertLinkByHref('admin/locator/add');

    $this->clickLink(t('Add Locator'));
    $this->assertResponse(200);

    $edit = [
      'title[0][value]' => $this->randomMachineName(8),
      'description[0][value]' => $this->randomMachineName(8),
      'subinventory' => $this->subinventory->id(),
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

    $this->drupalGet('admin/locator/' . $this->locator->id());
    $this->assertResponse(200);
    $this->assertLinkByHref('admin/locator/' . $this->locator->id() . '/edit');

    $this->clickLink(t('Edit'));
    $this->assertResponse(200);

    $edit = [
      'title[0][value]' => $this->randomMachineName(8),
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->assertResponse(200);
    $this->assertText($edit['title[0][value]']);
  }

}
