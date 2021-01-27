<?php

namespace Drupal\notification_system_dispatch;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Notification Dispatch Bundle entities.
 *
 * @ingroup notification_system_dispatch
 */
class NotificationDispatchBundleListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Notification Dispatch Bundle ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\notification_system_dispatch\Entity\NotificationDispatchBundle $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.notification_dispatch_bundle.edit_form',
      ['notification_dispatch_bundle' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
