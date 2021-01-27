<?php

namespace Drupal\notification_system_database\model;

use Drupal\notification_system\model\Notification;
use Drupal\notification_system\model\ReadableNotificationInterface;

/**
 * Subclass of the notification model.
 *
 * It is primarily used to improve the speed of getUsers().
 *
 * @package Drupal\notification_system_database\model
 */
class DatabaseNotification extends Notification implements ReadableNotificationInterface {

  /**
   * ID of the notification entity.
   *
   * @var int
   */
  protected $entityId;

  /**
   * Sets the entity id.
   *
   * @param int $id
   *   The id.
   */
  public function setEntityId($id) {
    $this->entityId = $id;
  }

  /**
   * Get the id of the notification entity.
   *
   * @return int
   *   ID of the notification entity.
   */
  public function getEntityId() {
    return $this->entityId;
  }

  /**
   * Place to cache read state.
   *
   * Associative array where the key is the user id and the value is a boolean
   * which indicates if the user with this id has read the notification.
   *
   * @var int[]
   */
  protected $isReadByCache = [];

  /**
   * {@inheritdoc}
   *
   * @throws \Exception
   */
  public function getUsers(): array {
    if (!$this->entityId) {
      throw new \Exception('Entity ID for this notification was not set. Use ->setEntityId() when creating the notification model!');
    }

    /** @var \Drupal\Core\Entity\EntityStorageInterface $notificationStorage */
    $notificationStorage = \Drupal::entityTypeManager()->getStorage('notification');
    $notification = $notificationStorage->load($this->entityId);

    $userIds = [];
    // TODO: Filter users that were deleted. maybe.
    foreach ($notification->get('user_id') as $fieldItem) {
      $userIds[] = $fieldItem->target_id;
    }

    return $userIds;
  }

  /**
   * {@inheritDoc}
   */
  public function isReadBy(int $user) {
    if (!$this->entityId) {
      throw new \Exception('Entity ID for this notification was not set. Use ->setEntityId() when creating the notification model!');
    }

    if (!array_key_exists($user, $this->isReadByCache)) {
      $query = \Drupal::database()->select('notification_system_database_read');
      $query->condition('uid', $user);
      $query->condition('entity_id', $this->entityId);
      $result_count = $query->countQuery()->execute()->fetchField();

      $this->isReadByCache[$user] = $result_count > 0;
    }

    return $this->isReadByCache[$user];
  }

}
