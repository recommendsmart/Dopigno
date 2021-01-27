<?php

namespace Drupal\notification_system_dispatch\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\notification_system_dispatch\NotificationSystemDispatcherInterface;

/**
 * Provides a Notification System Dispatch form.
 */
class UserSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'notification_system_dispatch_usersettings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\notification_system_dispatch\Service\UserSettingsService $userSettingsService */
    $userSettingsService = \Drupal::service('notification_system_dispatch.user_settings');

    /** @var \Drupal\notification_system_dispatch\NotificationSystemDispatcherPluginManager $notificationSystemDispatcherPluginManager */
    $notificationSystemDispatcherPluginManager = \Drupal::service('plugin.manager.notification_system_dispatcher');

    $pluginDefinitions = $notificationSystemDispatcherPluginManager->getDefinitions();

    foreach ($pluginDefinitions as $definition) {
      /** @var \Drupal\notification_system_dispatch\NotificationSystemDispatcherInterface $dispatcher */
      $dispatcher = $notificationSystemDispatcherPluginManager->createInstance($definition['id']);

      $form['dispatcher_' . $dispatcher->id()] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Receive notifications via @dispatcher', [
          '@dispatcher' => $dispatcher->label(),
        ]),
        '#default_value' => $userSettingsService->dispatcherEnabled($dispatcher->id()),
      ];
    }

    $enableBundling = $this->config('notification_system_dispatch.settings')->get('enable_bundling');

    if ($enableBundling) {
      $form['send_mode'] = [
        '#type' => 'select',
        '#title' => $this->t('When do you want to receive the notifications?'),
        '#options' => [
          NotificationSystemDispatcherInterface::SEND_MODE_IMMEDIATELY => $this->t('Immediately'),
          NotificationSystemDispatcherInterface::SEND_MODE_DAILY => $this->t('Daily summary'),
          NotificationSystemDispatcherInterface::SEND_MODE_WEEKLY => $this->t('Weekly summary'),
        ],
        '#default_value' => $userSettingsService->getSendMode(),
      ];
    }

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
    /** @var \Drupal\notification_system_dispatch\Service\UserSettingsService $userSettingsService */
    $userSettingsService = \Drupal::service('notification_system_dispatch.user_settings');

    /** @var \Drupal\notification_system_dispatch\NotificationSystemDispatcherPluginManager $notificationSystemDispatcherPluginManager */
    $notificationSystemDispatcherPluginManager = \Drupal::service('plugin.manager.notification_system_dispatcher');

    $pluginDefinitions = $notificationSystemDispatcherPluginManager->getDefinitions();

    foreach ($pluginDefinitions as $definition) {
      /** @var \Drupal\notification_system_dispatch\NotificationSystemDispatcherInterface $dispatcher */
      $dispatcher = $notificationSystemDispatcherPluginManager->createInstance($definition['id']);

      $enabled = $form_state->getValue('dispatcher_' . $dispatcher->id());

      $userSettingsService->setDispatcherEnabled($dispatcher->id(), $enabled);

      $enableBundling = $this->config('notification_system_dispatch.settings')->get('enable_bundling');

      if ($enableBundling) {
        $sendMode = $form_state->getValue('send_mode');
        $userSettingsService->setSendMode($sendMode);
      }
    }

    $this->messenger()->addStatus($this->t('Your preferences have been saved.'));
  }

}
