<?php

namespace Drupal\notification_system_dispatch\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides an example block.
 *
 * @Block(
 *   id = "notification_system_dispatch_usersettings",
 *   admin_label = @Translation("Notification Dispatch User Settings"),
 *   category = @Translation("Notification System")
 * )
 */
class UserSettingsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build['content'] = \Drupal::formBuilder()->getForm('\Drupal\notification_system_dispatch\Form\UserSettingsForm');

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIf(!$account->isAnonymous());
  }

}
