<?php

namespace Drupal\move_order;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the move_order entity type.
 *
 * @see \Drupal\move_order\Entity\MoveOrder
 */
class MoveOrderAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $move_order, $operation, AccountInterface $account) {
    if ($operation == 'view') {
      return AccessResult::allowedIfHasPermission($account, 'access move order');
    }
    else {
      return parent::checkAccess($move_order, $operation, $account);
    }
  }

}
