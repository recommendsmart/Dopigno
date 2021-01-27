<?php

namespace Drupal\notification_system_dispatch\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Notification System Dispatch settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'notification_system_dispatch_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'notification_system_dispatch.settings'
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\Core\State\StateInterface $state */
    $state = \Drupal::service('state');

    /** @var \Drupal\user\UserStorageInterface $userStorage */
    $userStorage = \Drupal::service('entity_type.manager')->getStorage('user');

    /** @var \Drupal\notification_system_dispatch\NotificationSystemDispatcherPluginManager $notificationSystemDispatcherPluginManager */
    $notificationSystemDispatcherPluginManager = \Drupal::service('plugin.manager.notification_system_dispatcher');


    $form['enable_bundling'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable bundling'),
      '#description' => $this->t('If enabled, users can select if they want to receive their notifications immediately, daily or weekly.'),
      '#default_value' => $this->config('notification_system_dispatch.settings')->get('enable_bundling') ?: FALSE,
    ];


    $form['enable_whitelist'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable whitelist'),
      '#description' => $this->t('For testing purposes you can enable a whitelist. Then notifications will only be sent for a given list of users.'),
      '#default_value' => $state->get('notification_system_dispatch.enable_whitelist'),
    ];

    $users = [];
    $whitelist = $state->get('notification_system_dispatch.whitelist');
    if ($whitelist) {
      $users = $userStorage->loadMultiple($whitelist);
    }

    $form['whitelist'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Allowed users'),
      '#description' => $this->t('Enter a comma separated list uf users who should receive notifications.'),
      '#target_type' => 'user',
      '#tags' => TRUE,
      '#default_value' => $users,
      '#states' => [
        'visible' => [
          ':input[name="enable_whitelist"]' => [
            'checked' => TRUE,
          ],
        ],
      ],
    ];


    $dispatchers = $notificationSystemDispatcherPluginManager->getDefinitions();

    $options = [];

    foreach ($dispatchers as $dispatcher) {
      $options[$dispatcher['id']] = $dispatcher['label'];
    }

    $form['default_enabled_dispatchers'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Default Dispatchers'),
      '#description' => $this->t('Select the dispatchers that should be enabled by default.'),
      '#default_value' => $this->config('notification_system_dispatch.settings')->get('default_enabled_dispatchers') ?: [],
      '#options' => $options,
    ];


    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\Core\State\StateInterface $state */
    $state = \Drupal::service('state');

    $userIds = [];
    if (is_array($form_state->getValue('whitelist'))) {
      foreach ($form_state->getValue('whitelist') as $item) {
        $userIds[] = $item['target_id'];
      }
    }

    $state->set('notification_system_dispatch.enable_whitelist', $form_state->getValue('enable_whitelist'));
    $state->set('notification_system_dispatch.whitelist', $userIds);

    $defaultEnabledDispatchers = [];
    foreach ($form_state->getValue('default_enabled_dispatchers') as $key => $value) {
      if ($value !== 0) {
        $defaultEnabledDispatchers[] = $key;
      }
    }

    $this->config('notification_system_dispatch.settings')
      ->set('enable_bundling', $form_state->getValue('enable_bundling'))
      ->set('default_enabled_dispatchers', $defaultEnabledDispatchers)
      ->save();

    parent::submitForm($form, $form_state);
  }

}
