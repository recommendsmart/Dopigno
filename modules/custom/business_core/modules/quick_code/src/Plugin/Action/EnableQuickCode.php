<?php

namespace Drupal\quick_code\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Session\AccountInterface;

/**
 * Enable a quick_code.
 *
 * @Action(
 *   id = "quick_code_enable_action",
 *   label = @Translation("Enable selected quick codes"),
 *   type = "quick_code"
 * )
 */
class EnableQuickCode extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    $today = new DrupalDateTime('now', DATETIME_STORAGE_TIMEZONE);
    $entity->effective_dates->value = $today->format(DATETIME_DATE_STORAGE_FORMAT);
    $entity->effective_dates->end_value = '';
    $entity->save();
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    $result = $object->access('update', $account, TRUE)
      ->andIf($object->effective_dates->access('edit', $account, TRUE));

    return $return_as_object ? $result : $result->isAllowed();
  }

}
