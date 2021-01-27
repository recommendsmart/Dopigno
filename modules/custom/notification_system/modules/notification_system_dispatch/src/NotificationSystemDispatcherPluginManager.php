<?php

namespace Drupal\notification_system_dispatch;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * NotificationSystemDispatcher plugin manager.
 */
class NotificationSystemDispatcherPluginManager extends DefaultPluginManager {

  /**
   * Constructs NotificationSystemDispatcherPluginManager object.
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
      'Plugin/NotificationSystemDispatcher',
      $namespaces,
      $module_handler,
      'Drupal\notification_system_dispatch\NotificationSystemDispatcherInterface',
      'Drupal\notification_system_dispatch\Annotation\NotificationSystemDispatcher'
    );
    $this->alterInfo('notification_system_dispatcher_info');
    $this->setCacheBackend($cache_backend, 'notification_system_dispatcher_plugins');
  }

}
