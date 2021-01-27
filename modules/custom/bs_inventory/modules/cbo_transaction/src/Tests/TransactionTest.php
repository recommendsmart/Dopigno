<?php

namespace Drupal\cbo_transaction\Tests;

/**
 * Tests transaction entities.
 *
 * @group cbo_transaction
 */
class TransactionTest extends TransactionTestBase {

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
      'administer transactions',
      'access transaction',
    ]);
  }

  /**
   * Tests the receipt page.
   */
  public function testList() {
    $this->drupalPlaceBlock('local_tasks_block');
    $this->drupalPlaceBlock('local_actions_block');

    $this->drupalLogin($this->adminUser);

    $this->drupalGet('admin/transaction/receipt');
    $this->assertResponse(200);
    $this->assertLinkByHref('admin/transaction/receipt/add');

    $this->clickLink(t('Add receipt'));
    $this->assertResponse(200);

    $edit = [
      'number[0][value]' => $this->randomMachineName(8),
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->assertResponse(200);
    $this->assertText($edit['number[0][value]']);
  }

  /**
   * Tests the edit form.
   */
  public function testEdit() {
    $this->drupalPlaceBlock('local_tasks_block');

    $this->drupalLogin($this->adminUser);

    $this->drupalGet('admin/transaction/' . $this->transaction->id());
    $this->assertResponse(200);
    $this->assertLinkByHref('admin/transaction/' . $this->transaction->id() . '/edit');

    $this->clickLink(t('Edit'));
    $this->assertResponse(200);

    $edit = [
      'number[0][value]' => $this->randomMachineName(8),
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->assertResponse(200);
    $this->assertText($edit['number[0][value]']);
  }

}
