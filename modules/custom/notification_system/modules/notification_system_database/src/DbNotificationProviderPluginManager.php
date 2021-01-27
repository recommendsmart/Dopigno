<?php

namespace Drupal\notification_system_database;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * DbNotificationProvider plugin manager.
 */
class DbNotificationProviderPluginManager extends DefaultPluginManager {

  /**
   * Constructs DbNotificationProviderPluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/DbNotificationProvider',
      $namespaces,
      $module_handler,
      'Drupal\notification_system_database\DbNotificationProviderInterface',
      'Drupal\notification_system_database\Annotation\DbNotificationProvider'
    );
    $this->alterInfo('db_notification_provider_info');
    $this->setCacheBackend($cache_backend, 'db_notification_provider_plugins');
  }

}
