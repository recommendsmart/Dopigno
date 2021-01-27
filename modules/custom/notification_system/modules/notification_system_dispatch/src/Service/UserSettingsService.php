<?php

namespace Drupal\notification_system_dispatch\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Session\AccountInterface;
use Drupal\notification_system_dispatch\NotificationSystemDispatcherInterface;
use Drupal\user\UserDataInterface;

/**
 * Handle storing of user settings.
 */
class UserSettingsService {

  /**
   * The UserData service.
   *
   * @var \Drupal\user\UserDataInterface
   */
  protected $userData;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The modules settings.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * Constructs a UserSettingsService instance.
   *
   * @param \Drupal\user\UserDataInterface $userData
   *   The UserData service.
   * @param \Drupal\Core\Session\AccountInterface $currentUser
   *   The current user.
   */
  public function __construct(UserDataInterface $userData, AccountInterface $currentUser, ConfigFactoryInterface $configFactory) {
    $this->userData = $userData;
    $this->currentUser = $currentUser;
    $this->config = $configFactory->get('notification_system_dispatch.settings');
  }

  /**
   * Check if a user has enabled a dispatcher.
   *
   * @param string $dispatcherId
   *   The id of the dispatcher plugin.
   * @param int $userId
   *   The id of the user. If none given, it will be the current user.
   *
   * @return bool
   *   A boolean indicating if the dispatcher is enabled for this user.
   */
  public function dispatcherEnabled($dispatcherId, $userId = NULL) {
    if ($userId == NULL) {
      $userId = $this->currentUser->id();
    }

    $data = $this->userData->get('notification_system_dispatcher', $userId, 'dispatcher_enabled_' . $dispatcherId);

    // Check if the dispatcher should be enabled by default.
    if ($data === NULL) {
      $defaultDispatchers = $this->config->get('default_enabled_dispatchers');
      return is_array($defaultDispatchers) && in_array($dispatcherId, $defaultDispatchers);
    }

    return $data === '1';
  }

  /**
   * Set the status of a dispatcher.
   *
   * Indicates if the user want to receive notifications via this dispatcher.
   *
   * @param string $dispatcherId
   *   The id of the dispatcher.
   * @param bool $enabled
   *   The status.
   * @param int $userId
   *   The id of the user. If none given, it will be the current user.
   */
  public function setDispatcherEnabled($dispatcherId, $enabled, $userId = NULL) {
    if ($userId == NULL) {
      $userId = $this->currentUser->id();
    }

    $this->userData->set('notification_system_dispatcher', $userId, 'dispatcher_enabled_' . $dispatcherId, $enabled);
  }

  /**
   * Return the send mode, the current user has set.
   *
   * @param int $userId
   *   The id of the user. If none given, it will be the current user.
   *
   * @return int
   *   The send mode the user has configured.
   *   See NotificationSystemDispatcherInterface.
   */
  public function getSendMode($userId = NULL) {
    if ($userId == NULL) {
      $userId = $this->currentUser->id();
    }

    $sendMode = $this->userData->get('notification_system_dispatcher', $userId, 'send_mode');

    // Default send mode if user has nothing other configured.
    if ($sendMode === NULL || $sendMode < 1 || $sendMode > 3) {
      return NotificationSystemDispatcherInterface::SEND_MODE_IMMEDIATELY;
    }

    return (int) $sendMode;
  }

  /**
   * Set the send mode.
   *
   * Indicates how often the user should be notified.
   *
   * @param int $sendMode
   *   The send mode. See NotificationSystemDispatcherInterface.
   * @param int $userId
   *   The id of the user. If none given, it will be the current user.
   *
   * @throws \LogicException
   *   Throws a LogicException if a invalid send mode was given.
   */
  public function setSendMode($sendMode, $userId = NULL) {
    if ($userId == NULL) {
      $userId = $this->currentUser->id();
    }

    if ((int) $sendMode < 1 || (int) $sendMode > 3) {
      throw new \LogicException('The send mode "' . $sendMode . '" is invalid.');
    }

    $this->userData->set('notification_system_dispatcher', $userId, 'send_mode', (int) $sendMode);
  }

  /**
   * Get the time, when bundled notifications were last dispatched.
   *
   * If notifications were never dispatched, it will return today at midnight.
   *
   * @param int $userId
   *   The id of the user. If none given, it will be the current user.
   *
   * @return int
   *   The timestamp.
   */
  public function getLastDispatchTimestamp($userId = NULL) {
    if ($userId == NULL) {
      $userId = $this->currentUser->id();
    }

    $timestamp = $this->userData->get('notification_system_dispatcher', $userId, 'last_dispatch_timestamp');

    // Default value: Today at midnight.
    if (!$timestamp || !is_int($timestamp)) {
      $defaultValue = new DrupalDateTime('now');
      $defaultValue->setTime(0, 0, 0);

      $timestamp = $defaultValue->getTimestamp();
    }

    return $timestamp;
  }

  /**
   * Set the time, when bundled notifications were last dispatched.
   *
   * @param int $timestamp
   *   Unix timestamp.
   * @param int $userId
   *   The id of the user. If none given, it will be the current user.
   */
  public function setLastDispatchTimestamp($timestamp, $userId = NULL) {
    if ($userId == NULL) {
      $userId = $this->currentUser->id();
    }

    $this->userData->set('notification_system_dispatcher', $userId, 'last_dispatch_timestamp', (int) $timestamp);
  }

}
