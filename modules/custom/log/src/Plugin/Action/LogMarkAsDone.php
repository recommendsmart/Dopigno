<?php

namespace Drupal\log\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\log\Entity\LogInterface;

/**
 * Action that marks a log as done.
 *
 * @Action(
 *   id = "log_mark_as_done_action",
 *   label = @Translation("Sets a Log as done"),
 *   type = "log"
 * )
 */
class LogMarkAsDone extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute(LogInterface $log = NULL) {
    if ($log) {
      $log->get('status')->first()->applyTransitionById('done');
      $log->setNewRevision(TRUE);
      $log->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    /** @var \Drupal\log\Entity\LogInterface $object */
    $result = $object->get('status')->access('edit', $account, TRUE)
      ->andIf($object->access('update', $account, TRUE));

    return $return_as_object ? $result : $result->isAllowed();
  }

}
