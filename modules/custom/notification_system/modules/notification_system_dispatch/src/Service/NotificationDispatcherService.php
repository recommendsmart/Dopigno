<?php

namespace Drupal\notification_system_dispatch\Service;

use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\State\StateInterface;
use Drupal\notification_system\model\NotificationInterface;
use Drupal\notification_system_dispatch\NotificationSystemDispatcherPluginManager;

/**
 * Handle queueing of notifications.
 */
class NotificationDispatcherService {

  /**
   * The user settings service.
   *
   * @var \Drupal\notification_system_dispatch\Service\UserSettingsService
   */
  protected $userSettingsService;

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The queue which holds the dispatch jobs.
   *
   * @var \Drupal\Core\Queue\QueueInterface
   */
  protected $queue;

  /**
   * A list of all available dispatchers.
   *
   * @var \Drupal\notification_system_dispatch\NotificationSystemDispatcherInterface[]
   */
  protected $dispatchers;

  /**
   * Constructs a NotificationDispatcherService instance.
   *
   * @param \Drupal\notification_system_dispatch\Service\UserSettingsService $userSettingsService
   *   The user settings service.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   * @param \Drupal\Core\Queue\QueueFactory $queueFactory
   *   The QueueFactory service.
   * @param \Drupal\notification_system_dispatch\NotificationSystemDispatcherPluginManager $notificationSystemDispatcherPluginManager
   *   The NotificationSystemDispatcherPluginManager.
   */
  public function __construct(UserSettingsService $userSettingsService, StateInterface $state, QueueFactory $queueFactory, NotificationSystemDispatcherPluginManager $notificationSystemDispatcherPluginManager) {
    $this->userSettingsService = $userSettingsService;
    $this->state = $state;
    $this->dispatchers = $notificationSystemDispatcherPluginManager->getDefinitions();
    $this->queue = $queueFactory->get('notification_system_dispatch');
    $this->queue->createQueue();
  }

  /**
   * Create notification dispatch jobs.
   *
   * One item in the queue for each dispatcher that the user has enabled.
   * Also checks if a user is whitelisted.
   *
   * @param \Drupal\notification_system\model\NotificationInterface[] $notifications
   *   The notifications that should be dispatched.
   * @param int $userId
   *   The id of the user.
   */
  public function queue(array $notifications, $userId) {
    $whitelistEnabled = $this->state->get('notification_system_dispatch.enable_whitelist');
    $whitelist = $this->state->get('notification_system_dispatch.whitelist');

    // If whitelist mode is enabled, allow only users of the whitelist.
    if ($whitelistEnabled === 1) {
      if (!is_array($whitelist)) {
        return;
      }

      if (!in_array($userId, $whitelist)) {
        return;
      }
    }

    foreach ($this->dispatchers as $dispatcher) {
      // If a user has disabled a dispatcher, don't create queue items.
      if (!$this->userSettingsService->dispatcherEnabled($dispatcher['id'], $userId)) {
        continue;
      }

      // Add an item to the dispatcher queue.
      $item = new \stdClass();
      $item->user = $userId;
      $item->dispatcher = $dispatcher['id'];
      $item->notifications = [];

      foreach ($notifications as $notification) {
        $data = new \stdClass();
        $data->notification_provider = $notification->getProvider();
        $data->notification_id = $notification->getId();
        $item->notifications[] = $data;
      }

      $this->queue->createItem($item);
    }
  }

}
