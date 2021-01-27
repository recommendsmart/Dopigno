<?php

namespace Drupal\notification_system_database\Plugin\NotificationProvider;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\notification_system\NotificationProviderPluginBase;
use Drupal\notification_system_database\DbNotificationProviderPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the notification_provider.
 *
 * @NotificationProvider(
 *   id = "database",
 *   label = @Translation("Database Notification Provider"),
 *   description = @Translation("Loads notification entities from the database.")
 * )
 */
class DatabaseNotificationProvider extends NotificationProviderPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManger;

  /**
   * The notification provider plugin manager.
   *
   * @var \Drupal\notification_system_database\DbNotificationProviderPluginManager
   */
  protected $dbNotificationProviderPluginManager;

  /**
   * The notification storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $notificationStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entityTypeManager, DbNotificationProviderPluginManager $db_notification_provider_plugin_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManger = $entityTypeManager;
    $this->dbNotificationProviderPluginManager = $db_notification_provider_plugin_manager;

    /** @var \Drupal\Core\Entity\EntityStorageInterface $notificationStorage */
    $this->notificationStorage = $this->entityTypeManger->getStorage('notification');
  }

  /**
   * Creates an instance of the plugin.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container to pull out services used in the plugin.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @return static
   *   Returns an instance of this plugin.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.db_notification_provider')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getTypes() {
    $pluginDefinitions = $this->dbNotificationProviderPluginManager->getDefinitions();

    $types = [];

    foreach ($pluginDefinitions as $pluginDefinition) {
      try {
        /** @var \Drupal\notification_system\NotificationProviderInterface $provider */
        $provider = $this->dbNotificationProviderPluginManager->createInstance($pluginDefinition['id']);

        $typesOfProvider = $provider->getTypes();

        $types = array_merge($types, $typesOfProvider);
      }
      catch (PluginException $e) {
        // TODO: Handle plugin exceptions. They occur for example if a plugin
        // was removed and cache was not cleared.
      }

      $types = array_unique($types);
    }

    return $types;
  }

  /**
   * {@inheritdoc}
   */
  public function getNotifications(AccountInterface $user, $includeReadNotifications = FALSE) {
    $query = $this->notificationStorage->getQuery();
    $query->condition('user_id', $user->id());
    $results = $query->execute();

    /** @var \Drupal\notification_system_database\Entity\Notification[] $notificationEntities */
    $notificationEntities = $this->notificationStorage->loadMultiple($results);

    $notifications = [];

    foreach ($notificationEntities as $notificationEntity) {
      $notification = $notificationEntity->toNotificationModel();

      // Filter out read notifications.
      if (!$includeReadNotifications) {
        if ($notification->isReadBy($user->id())) {
          continue;
        }
      }

      $notifications[] = $notification;
    }

    return $notifications;
  }

  /**
   * {@inheritdoc}
   */
  public function markAsRead(AccountInterface $user, string $notificationId) {
    $notification = $this->notificationStorage->load($notificationId);

    if (!$notification) {
      return 'No notification with this id was found!';
    }

    $found = FALSE;
    foreach ($notification->get('user_id') as $fieldItem) {
      if ($fieldItem->target_id == $user->id()) {
        $found = TRUE;
      }
    }
    if (!$found) {
      return 'This notification is of another user.';
    }

    if ($notification->get('sticky')->value == TRUE) {
      return 'This notification cannot be marked as read because it is sticky.';
    }

    $count = \Drupal::database()->select('notification_system_database_read')
      ->condition('uid', $user->id())
      ->condition('entity_id', $notification->id())
      ->countQuery()->execute()->fetchField();

    if ($count == 0) {
      \Drupal::database()->insert('notification_system_database_read')
        ->fields([
          'uid' => $user->id(),
          'entity_id' => $notification->id(),
          'timestamp' => \Drupal::time()->getRequestTime(),
        ])->execute();
    }

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function load($notificationId) {
    /** @var \Drupal\notification_system_database\NotificationInterface $notificationEntity */
    $notificationEntity = $this->notificationStorage->load($notificationId);

    if ($notificationEntity) {
      return $notificationEntity->toNotificationModel();
    }

    return FALSE;
  }

}
