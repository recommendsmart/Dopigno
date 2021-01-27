<?php

namespace Drupal\notification_system_dispatch_mail\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Notification System Dispatch Mail settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'notification_system_dispatch_mail_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['notification_system_dispatch_mail.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['help_text'] = [
      '#type' => 'markup',
      '#markup' => '
        <p>You can use twig to generate the mail subject and body, which allows you to use if statements and for loops for the case that multiple notifications will be sent at once (notification bundling)</p>
        <p><strong>Variables:</strong></p>
        <ul>
          <li><em>notifications</em> - A list of notifications.</li>
          <ul>
            <li><em>title</em> - The title of the notification.</li>
            <li><em>body</em> - The body of the notification.</li>
            <li><em>timestamp</em> - The date and time when the notification was created. Uses the short date format.</li>
            <li><em>link</em> - A link to the notification. When clicking, the notification will be marked as read.</li>
            <li><em>direct_link</em> - A direct link to the notification without marking it as read.</li>
          </ul>
        </ul>
      ',
    ];

    $form['subject_template'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Subject Template'),
      '#default_value' => $this->config('notification_system_dispatch_mail.settings')->get('subject_template'),
      '#rows' => 8,
      '#required' => TRUE,
    ];

    $form['body_template'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Body Template'),
      '#default_value' => $this->config('notification_system_dispatch_mail.settings')->get('body_template'),
      '#rows' => 20,
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('notification_system_dispatch_mail.settings')
      ->set('subject_template', $form_state->getValue('subject_template'))
      ->set('body_template', $form_state->getValue('body_template'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
