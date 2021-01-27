<?php

namespace Drupal\notification_system_dispatch\EventSubscriber;

use Drupal\notification_system\Event\NewNotificationEvent;
use Drupal\notification_system_dispatch\Entity\NotificationDispatchBundle;
use Drupal\notification_system_dispatch\NotificationSystemDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Notification System Dispatch event subscriber.
 */
class NotificationSystemDispatchSubscriber implements EventSubscriberInterface {

  /**
   * Constructs event subscriber.
   */
  public function __construct() {
  }

  /**
   * New notification event handler.
   *
   * Adds dispatch jobs to a queue because we are working with heavy loads here.
   *
   * @param \Drupal\notification_system\Event\NewNotificationEvent $event
   *   New notification event.
   */
  public function onNewNotification(NewNotificationEvent $event) {
    /** @var \Drupal\notification_system_dispatch\Service\NotificationDispatcherService $dispatcherService */
    $dispatcherService = \Drupal::service('notification_system_dispatch');

    /** @var \Drupal\notification_system_dispatch\Service\UserSettingsService $userSettingsService */
    $userSettingsService = \Drupal::service('notification_system_dispatch.user_settings');

    /** @var \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager */
    $entityTypeManager = \Drupal::service('entity_type.manager');

    /** @var \Drupal\Core\Entity\EntityStorageInterface $dispatchBundleStorage */
    $dispatchBundleStorage = $entityTypeManager->getStorage('notification_dispatch_bundle');

    foreach ($event->notification->getUsers() as $userId) {

      // If user wants to receive notifications immediately.
      if ($userSettingsService->getSendMode($userId) == NotificationSystemDispatcherInterface::SEND_MODE_IMMEDIATELY) {
        $dispatcherService->queue([$event->notification], $userId);
      }
      // Add notification to a NotificationDispatcherBundle.
      else {
        // Check if bundle exists
        $query = $dispatchBundleStorage->getQuery();
        $query->condition('user_id', $userId);
        $query->accessCheck(FALSE);
        $result = $query->execute();

        // Create bundle if not exists. Else load existing.
        if (count($result) > 0) {
          $dispatchBundle = $dispatchBundleStorage->load(array_values($result)[0]);
        }
        else {
          $dispatchBundle = NotificationDispatchBundle::create([
            'user_id' => $userId,
          ]);
        }

        // Add notification to the bundle.
        $dispatchBundle->notifications[] = [
          'provider' => $event->notification->getProvider(),
          'notification_id' => $event->notification->getId(),
        ];

        $dispatchBundle->save();
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      NewNotificationEvent::EVENT_NAME => ['onNewNotification'],
    ];
  }

}
