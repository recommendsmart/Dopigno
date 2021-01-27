<?php

namespace Drupal\notification_system\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class NotificationGroupForm.
 */
class NotificationGroupForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $notification_group = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#description' => $this->t("Label for the Notification Group."),
      '#maxlength' => 255,
      '#default_value' => $notification_group->label(),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $notification_group->id(),
      '#machine_name' => [
        'exists' => '\Drupal\notification_system\Entity\NotificationGroup::load',
      ],
      '#disabled' => !$notification_group->isNew(),
    ];

    $form['description'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Description'),
      '#description' => $this->t("Text that is displayed inside the group dropdown."),
      '#default_value' => $notification_group->getDescription()['value'],
      '#format' => $notification_group->getDescription()['format'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $notification_group = $this->entity;
    $status = $notification_group->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Notification Group.', [
          '%label' => $notification_group->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Notification Group.', [
          '%label' => $notification_group->label(),
        ]));
    }
    $form_state->setRedirectUrl($notification_group->toUrl('collection'));
  }

}
