<?php

namespace Drupal\notification_system\Service;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\notification_system\model\NotificationInterface;
use Drupal\notification_system\model\ReadableNotificationInterface;
use Drupal\notification_system\NotificationProviderPluginManager;

/**
 * NotificationSystem service.
 */
class NotificationSystem {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The notification provider plugin manager.
   *
   * @var \Drupal\notification_system\NotificationProviderPluginManager
   */
  protected $notificationProviderPluginManager;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a NotificationSystem object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\notification_system\NotificationProviderPluginManager $notificationProviderPluginManager
   *   The notification provider plugin manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, NotificationProviderPluginManager $notificationProviderPluginManager, ConfigFactoryInterface $configFactory) {
    $this->entityTypeManager = $entity_type_manager;
    $this->notificationProviderPluginManager = $notificationProviderPluginManager;
    $this->configFactory = $configFactory;
  }

  /**
   * Gets all available notification providers.
   *
   * @return \Drupal\notification_system\NotificationProviderInterface[]
   *   An array of notification providers.
   */
  public function getProviders() {
    $pluginDefinitions = $this->notificationProviderPluginManager->getDefinitions();

    $providers = [];

    foreach ($pluginDefinitions as $pluginDefinition) {
      try {
        /** @var \Drupal\notification_system\NotificationProviderInterface $provider */
        $provider = $this->notificationProviderPluginManager->createInstance($pluginDefinition['id']);
        $providers[] = $provider;
      }
      catch (PluginException $e) {
        // TODO: Handle plugin exceptions. They occur for example if a plugin
        // was removed and cache was not cleared.
        \Drupal::messenger()->addError($e->getMessage());
      }
    }

    return $providers;
  }

  /**
   * Get a list of unread notifications for a given user from all providers.
   *
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The user to get the notifications for.
   * @param bool $bundled
   *   Indicates if the notifications should be bundled by NotificationGroup.
   * @param bool $includeReadNotifications
   *   Include read notifications.
   *
   * @return array|\Drupal\notification_system\model\NotificationInterface[]
   *   A list of notifications.
   */
  public function getNotifications(AccountInterface $user, $bundled = FALSE, $includeReadNotifications = FALSE) {
    /** @var \Drupal\notification_system\model\NotificationInterface[] $notifications */
    $notifications = [];

    $providers = $this->getProviders();

    foreach ($providers as $provider) {
      $notificationsOfProvider = $provider->getNotifications($user, $includeReadNotifications);

      $notifications = array_merge($notifications, $notificationsOfProvider);
    }


    // Sort notifications by priority DESC, timestamp DESC.
    usort($notifications, function (NotificationInterface $a, NotificationInterface $b) {
      if ($a->getPriority() == $b->getPriority()) {
        return $b->getTimestamp() - $a->getTimestamp();
      }

      return $b->getPriority() - $a->getPriority();
    });


    // Sort out read notifications and move them to the end.
    if ($includeReadNotifications) {
      $readNotifications = [];

      foreach ($notifications as $index => $notification) {
        if ($notification instanceof ReadableNotificationInterface) {
          if ($notification->isReadBy($user->id())) {
            $readNotifications[] = $notification;
            unset($notifications[$index]);
          }
        }
      }

      $notifications = array_merge($notifications, $readNotifications);
    }


    // Bundle notifications.
    if ($bundled) {
      $notifications = $this->bundleNotifications($notifications);
    }

    return $notifications;
  }

  /**
   * Gets all notification types of all providers.
   *
   * @return string[]
   *   An array of notification types.
   */
  public function getTypes() {
    $providers = $this->getProviders();

    $types = [];

    foreach ($providers as $provider) {
      $typesOfProvider = $provider->getTypes();
      $types = array_merge($types, $typesOfProvider);
    }

    $types = array_unique($types);

    sort($types);

    return $types;
  }

  /**
   * Loads the current mapping configuration.
   *
   * @return array
   *   An array which keys are the notification types and the values
   *   the notification group ids.
   */
  protected function getTypeToGroupMappings() {
    $mappings = [];

    $config = $this->configFactory->get('notification_system.settings');
    $configItems = $config->get('group_mappings');

    foreach ($configItems as $configItem) {
      $mappings[$configItem['notification_type']] = $configItem['notification_group'];
    }

    return $mappings;
  }

  /**
   * Marks a notification as read.
   *
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The user who has read the notification.
   * @param string $notificationProviderId
   *   The id of the notification provider, that holds the notification.
   * @param string $notificationId
   *   The id of the notification.
   *
   * @return bool|string
   *   Returns true if it was successful. If not, returns a error message.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function markAsRead(AccountInterface $user, string $notificationProviderId, string $notificationId) {
    /** @var \Drupal\notification_system\NotificationProviderInterface $provider */
    try {
      $provider = $this->notificationProviderPluginManager->createInstance($notificationProviderId);
      return $provider->markAsRead($user, $notificationId);
    }
    catch (PluginNotFoundException $e) {
      return $e->getMessage();
    }
  }

  /**
   * Bundles notifications by the NotificationGroup they belong to.
   *
   * @param \Drupal\notification_system\model\Notification[] $notifications
   *   A list of notifications to bundle.
   *
   * @return array
   *   An array of notification arrays, keyed by the NotificationGroup id.
   *   Ungrouped notifications will be under the key 'NONE'
   */
  public function bundleNotifications(array $notifications) {
    $mappings = $this->getTypeToGroupMappings();

    $groupedNotifications = [];

    foreach ($notifications as $notification) {
      if (!array_key_exists($notification->getType(), $mappings)) {
        $group = 'NONE';
      }
      else {
        $group = $mappings[$notification->getType()];

        if (!$group || $group === '') {
          $group = 'NONE';
        }
      }

      $groupedNotifications[$group][] = $notification;
    }

    return $groupedNotifications;
  }

  /**
   * Load a notification from its provider.
   *
   * @param string $providerId
   *   The id of the notification provider.
   * @param string $notificationId
   *   The provider specific id of the notification.
   *
   * @return \Drupal\notification_system\model\NotificationInterface|bool
   *   Returns the notification of FALSE if not found.
   */
  public function loadNotification($providerId, $notificationId) {
    try {
      /** @var \Drupal\notification_system\NotificationProviderInterface $provider */
      $provider = $this->notificationProviderPluginManager->createInstance($providerId);
      return $provider->load($notificationId);
    }
    catch (PluginException $e) {
      // TODO: Handle exception.
      return FALSE;
    }
  }

}
