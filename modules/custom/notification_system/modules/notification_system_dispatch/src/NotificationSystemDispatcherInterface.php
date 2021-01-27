<?php

namespace Drupal\notification_system_dispatch;

use Drupal\notification_system\model\NotificationInterface;
use Drupal\user\UserInterface;

/**
 * Interface for notification_system_dispatcher plugins.
 */
interface NotificationSystemDispatcherInterface {

  public const SEND_MODE_IMMEDIATELY = 1;
  public const SEND_MODE_DAILY = 2;
  public const SEND_MODE_WEEKLY = 3;

  /**
   * Returns the translated plugin label.
   *
   * @return string
   *   The translated title.
   */
  public function label();

  /**
   * Returns the plugin id.
   *
   * @return string
   *   The dispatcher id.
   */
  public function id();

  /**
   * Send out a specific notification to one user.
   *
   * @param \Drupal\user\UserInterface $user
   *   The recipient.
   * @param \Drupal\notification_system\model\NotificationInterface[] $notifications
   *   Send the notifications.
   */
  public function dispatch(UserInterface $user, array $notifications);

}
