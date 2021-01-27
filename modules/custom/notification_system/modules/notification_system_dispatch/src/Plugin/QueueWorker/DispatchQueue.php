<?php

namespace Drupal\notification_system_dispatch\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;

/**
 * Processes dispatching of notifications.
 *
 * @QueueWorker(
 *   id = "notification_system_dispatch",
 *   title = @Translation("Notification System Dispatch Worker"),
 *   cron = {"time" = 30}
 * )
 */
class DispatchQueue extends QueueWorkerBase {

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    /** @var \Drupal\notification_system\Service\NotificationSystem $notificationSystem */
    $notificationSystem = \Drupal::service('notification_system');

    /** @var \Drupal\notification_system_dispatch\NotificationSystemDispatcherPluginManager $notificationSystemDispatcherPluginManager */
    $notificationSystemDispatcherPluginManager = \Drupal::service('plugin.manager.notification_system_dispatcher');

    /** @var \Drupal\user\UserStorageInterface $userStorage */
    $userStorage = \Drupal::entityTypeManager()->getStorage('user');

    $dispatcherId = $data->dispatcher;
    $userId = $data->user;

    $notifications = [];

    foreach ($data->notifications as $dat) {
      $notificationProviderId = $dat->notification_provider;
      $notificationId = $dat->notification_id;

      $notification = $notificationSystem->loadNotification($notificationProviderId, $notificationId);

      if ($notification) {
        $notifications[] = $notification;
      }
    }

    /** @var \Drupal\notification_system_dispatch\NotificationSystemDispatcherInterface $dispatcher */
    $dispatcher = $notificationSystemDispatcherPluginManager->createInstance($dispatcherId);

    /** @var \Drupal\user\UserInterface $user */
    $user = $userStorage->load($userId);

    if ($dispatcher && $user && count($notifications) > 0) {
      $dispatcher->dispatch($user, $notifications);
    }

    // TODO: Error handling. Delete the item if not all variables are correct.
    // Maybe do some research on what happens when an exception is thrown in
    // this function.
  }

}
