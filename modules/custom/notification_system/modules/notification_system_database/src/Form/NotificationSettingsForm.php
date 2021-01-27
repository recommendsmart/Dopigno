<?php

namespace Drupal\notification_system_database\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configuration form for a notification entity type.
 */
class NotificationSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'notification_settings';
  }

  /**
   * {@inheritDoc}
   */
  protected function getEditableConfigNames() {
    return ['notification_system_database.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['settings'] = [
      '#markup' => $this->t('Settings form for a notification entity type.'),
    ];

    $form['read_purge_days'] = [
      '#type' => 'number',
      '#title' => $this->t('Delete read notifications after (days)'),
      '#default_value' => $this->config('notification_system_database.settings')->get('read_purge_days') ?: 0,
      '#description' => $this->t('Notification entities will be deleted the specified number of days after all users have read it. Enter 0 to never delete read notifications.'),
    ];


    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('notification_system_database.settings')
      ->set('read_purge_days', $form_state->getValue('read_purge_days'))
      ->save();

    $this->messenger()->addStatus($this->t('The configuration has been updated.'));

    parent::submitForm($form, $form_state);
  }

}
