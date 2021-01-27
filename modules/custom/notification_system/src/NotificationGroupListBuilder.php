<?php

namespace Drupal\notification_system;

use Drupal\Core\Config\Entity\DraggableListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a listing of Notification Group entities.
 */
class NotificationGroupListBuilder extends DraggableListBuilder {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'notification_system_admin_notification_group_form';
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();

    $myBuild['info'] = [
      '#markup' => '<p>' . $this->t('Notification Groups are used to group different types of notifications together. These groups will be used for display.') . '</p>',
    ];

    return array_merge($myBuild, $build);
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Notification Group');
    $header['id'] = $this->t('Machine name');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['id']['#markup'] = $entity->id();

    return $row + parent::buildRow($entity);
  }

}
