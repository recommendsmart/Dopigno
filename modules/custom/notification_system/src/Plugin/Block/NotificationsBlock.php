<?php

namespace Drupal\notification_system\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;

/**
 * Provides a notifications block.
 *
 * @Block(
 *   id = "notification_system_notifications",
 *   admin_label = @Translation("Notifications"),
 *   category = @Translation("Notification System")
 * )
 */
class NotificationsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'display_mode' => 'simple',
      'show_read' => FALSE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['display_mode'] = [
      '#type' => 'select',
      '#title' => $this->t('Display Mode'),
      '#options' => [
        'simple' => $this->t('Simple (No bundling)'),
        'bundled' => $this->t('Bundled by notification group'),
      ],
      '#default_value' => $this->configuration['display_mode'] ?: 'simple',
    ];

    $form['show_read'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show read notifications'),
      '#description' => $this->t('Read notifications will be shown below the unread notifications'),
      '#default_value' => $this->configuration['show_read'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['display_mode'] = $form_state->getValue('display_mode');
    $this->configuration['show_read'] = $form_state->getValue('show_read');
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    // Only show the block for logged in users.
    return AccessResult::allowedIf($account->isAuthenticated());
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build['content'] = [
      '#theme' => 'notification_block',
      '#display_mode' => $this->configuration['display_mode'],
      '#show_read' => $this->configuration['show_read'],
      '#content' => [
        '#markup' => '<p>' . $this->t('Loading') . '...</p>',
      ],
      '#attached' => [
        'library' => [
          'notification_system/notifications_block',
        ],
        'drupalSettings' => [
          'notificationSystem' => [
            'getEndpointUrl' => Url::fromRoute('notification_system.getnotifications', ['display_mode' => 'DISPLAY_MODE'])->toString(),
            'markAsReadEndpointUrl' => Url::fromRoute('notification_system.markasread', ['providerId' => 'PROVIDER_ID', 'notificationId' => 'NOTIFICATION_ID'])->toString(),
          ],
        ],
      ],
    ];

    if ($this->configuration['display_mode'] == 'bundled') {
      $build['content']['#content'] = [];

      $groupManager = \Drupal::entityTypeManager()->getStorage('notification_group');

      /** @var \Drupal\notification_system\Entity\NotificationGroupInterface[] $groups */
      $groups = $groupManager->loadMultiple();

      foreach ($groups as $group) {
        $build['content']['#content'][$group->id()] = [
          '#theme' => 'notification_group',
          '#group' => $group,
          '#id' => $group->id(),
          '#label' => $group->label(),
          '#description' => [
            '#type' => 'processed_text',
            '#text' => $group->getDescription()['value'],
            '#format' => $group->getDescription()['format'],
          ],
          '#content' => [
            '#markup' => '<p>' . $this->t('Loading') . '...</p>',
          ],
        ];
      }
    }

    return $build;
  }

}
