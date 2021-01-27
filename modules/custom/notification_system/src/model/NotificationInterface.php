<?php

namespace Drupal\notification_system\model;

use Drupal\Core\Url;
use Drupal\notification_system\NotificationProviderInterface;

/**
 * Interface for notification models.
 */
interface NotificationInterface {
  const PRIORITY_LOWEST = 1;
  const PRIORITY_LOW = 2;
  const PRIORITY_MEDIUM = 3;
  const PRIORITY_HIGH = 4;
  const PRIORITY_HIGHEST = 5;

  /**
   * NotificationInterface constructor.
   *
   * @param string $provider
   *   The id of the provider that generated the notification.
   * @param string $id
   *   An identifier for this notification that is unique in the context of
   *   the notification provider.
   * @param string $type
   *   A key which indicates the type of the notification.
   * @param int[] $users
   *   An array of user ids who this notification is for.
   * @param int $timestamp
   *   The unix timestamp when the notification was created.
   * @param string $title
   *   The title of the notification.
   * @param string|null $body
   *   Additional text of the notification.
   * @param \Drupal\Core\Url|null $link
   *   A link that provides more information about the notification.
   * @param bool $sticky
   *   Indicates if the notification can be marked as read.
   * @param int $priority
   *   Indicates how important the notification is.
   */
  public function __construct($provider, $id, $type, array $users, $timestamp, $title, $body = NULL, Url $link = NULL, $sticky = FALSE, $priority = self::PRIORITY_MEDIUM);

  /**
   * Get the provider that generated the notification.
   *
   * @return string
   *   The id of the provider.
   */
  public function getProvider();

  /**
   * Get the identifier for this notification.
   *
   * It is unique in the context of the notification provider.
   *
   * @return string
   *   The id.
   */
  public function getId(): string;

  /**
   * Get a key which indicates the type of the notification.
   *
   * @return string
   *   The type.
   */
  public function getType(): string;

  /**
   * Get the users which the notification is for.
   *
   * @return int[]
   *   An array of user ids.
   */
  public function getUsers(): array;

  /**
   * Get the timestamp of the notification.
   *
   * @return int
   *   The timestamp.
   */
  public function getTimestamp(): int;

  /**
   * Get the title of the notification.
   *
   * @return string
   *   The title.
   */
  public function getTitle(): string;

  /**
   * Get the body of the notification.
   *
   * @return string|null
   *   The body.
   */
  public function getBody();

  /**
   * Get the link of the notification.
   *
   * @return \Drupal\Core\Url|null
   *   The link.
   */
  public function getLink();

  /**
   * Get the info if the notification is sticky.
   *
   * @return bool
   *   True if it is sticky.
   */
  public function isSticky(): bool;

  /**
   * Get the priority of the notification.
   *
   * @return int
   *   The notification.
   */
  public function getPriority(): int;

  /**
   * Set the provider of the notification.
   *
   * @param string $provider
   *   The id of the provider that generated the notification.
   */
  public function setProvider($provider);

  /**
   * Set the id of the notification.
   *
   * @param string $id
   *   An identifier for this notification that is unique in the context of
   *   the notification provider.
   */
  public function setId(string $id);

  /**
   * Set the type of the notification.
   *
   * @param string $type
   *   A key which indicates the type of the notification.
   */
  public function setType(string $type);

  /**
   * Set the users the notification is for.
   *
   * @param int[] $users
   *   An array of users ids.
   */
  public function setUsers(array $users);

  /**
   * Add a user to the users property.
   *
   * @param int $userId
   *   The user id of the user.
   */
  public function addUser(int $userId);

  /**
   * Remove a user from the users property.
   *
   * @param int $userId
   *   The user id of the user.
   */
  public function removeUser(int $userId);

  /**
   * Set the timestamp of the notification.
   *
   * @param int $timestamp
   *   The unix timestamp when the notification was created.
   */
  public function setTimestamp(int $timestamp);

  /**
   * Set the title of the notification.
   *
   * @param string $title
   *   The unix timestamp when the notification was created.
   */
  public function setTitle(string $title);

  /**
   * Set the body of the notification.
   *
   * @param string $body
   *   Additional text of the notification.
   */
  public function setBody(string $body);

  /**
   * Set the link of the notification.
   *
   * @param \Drupal\Core\Url $link
   *   A link that provides more information about the notification.
   */
  public function setLink(Url $link);

  /**
   * Set if the notification is sticky.
   *
   * @param bool $sticky
   *   Indicates if the notification can be deleted.
   */
  public function setSticky(bool $sticky);

  /**
   * Set the priority of the notification.
   *
   * @param int $priority
   *   Indicates how important the notification is.
   */
  public function setPriority(int $priority);

}
