<?php

namespace Drupal\notification_system\Event;

use Drupal\notification_system\model\NotificationInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event that is fired when a new notification was added.

 * Can be a database notification or any other kind of notification.
 */
class NewNotificationEvent extends Event {
  const EVENT_NAME = 'notification_system_new_notification';

  /**
   * The new notification.
   *
   * @var \Drupal\notification_system\model\NotificationInterface
   */
  public $notification;

  /**
   * NewNotificationEvent constructor.
   *
   * @param \Drupal\notification_system\model\NotificationInterface $notification
   *   The new notification.
   */
  public function __construct(NotificationInterface $notification) {
    $this->notification = $notification;
  }

}
