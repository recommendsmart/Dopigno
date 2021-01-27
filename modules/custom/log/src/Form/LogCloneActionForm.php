<?php

namespace Drupal\log\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a log clone confirmation form.
 */
class LogCloneActionForm extends LogActionFormBase {

  /**
   * The action id.
   *
   * @var string
   */
  protected $actionId = 'log_clone_action';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'log_clone_action_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->formatPlural(count($this->logs), 'Are you sure you want to clone this log?', 'Are you sure you want to clone these logs?');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Clone');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\Core\Datetime\DrupalDateTime $new_date */
    if ($form_state->getValue('confirm') && !empty($this->logs)) {
      $new_date = $form_state->getValue('date');
      $count = count($this->logs);
      foreach ($this->logs as $log) {
        $cloned_log = $log->createDuplicate();
        $cloned_log->set('timestamp', $new_date->getTimestamp());
        $cloned_log->save();
      }
      $this->messenger()->addMessage($this->formatPlural($count, 'Cloned 1 log.', 'Cloned @count logs.'));
    }

    parent::submitForm($form, $form_state);
  }

}
