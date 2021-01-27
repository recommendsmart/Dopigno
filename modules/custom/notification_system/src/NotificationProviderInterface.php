<?php

namespace Drupal\notification_system;

use Drupal\Core\Session\AccountInterface;

/**
 * Interface for notification_provider plugins.
 */
interface NotificationProviderInterface {

  /**
   * Returns the translated plugin label.
   *
   * @return string
   *   The translated title.
   */
  public function label();

  /**
   * Get the id of the notification provider.
   *
   * @return string
   *   The plugin id.
   */
  public function id();

  /**
   * Get a list of notification types that this provider creates.
   *
   * @return string[]
   *   The notification types.
   */
  public function getTypes();

  /**
   * Load all unread notifications for a given user.
   *
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The user which to get the notifications for.
   * @param bool $includeReadNotifications
   *   When true, read notifications will also be returned.
   *
   * @return \Drupal\notification_system\model\NotificationInterface[]
   *   A list of notifications.
   */
  public function getNotifications(AccountInterface $user, $includeReadNotifications = FALSE);

  /**
   * Mark a notification as read.
   *
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The account of the user who has read the notification.
   * @param string $notificationId
   *   The id of the notification.
   *
   * @return bool|string
   *   Returns true if it was successful. If not, returns a error message.
   */
  public function markAsRead(AccountInterface $user, string $notificationId);

  /**
   * Load a notification by it's id.
   *
   * @param string $notificationId
   *   The provider specific id of the notification.
   *
   * @return \Drupal\notification_system\model\NotificationInterface|bool
   *   Return the notification or FALSE if provider doesn't support loading.
   */
  public function load($notificationId);

}
