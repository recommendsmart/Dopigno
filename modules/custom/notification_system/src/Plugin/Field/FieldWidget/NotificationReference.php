<?php

namespace Drupal\notification_system\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'notification_reference' widget.
 *
 * @FieldWidget(
 *   id = "notification_reference",
 *   module = "notification_system",
 *   label = @Translation("Notification Reference"),
 *   field_types = {
 *     "notification_reference"
 *   }
 * )
 */
class NotificationReference extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['wrapper'] = $element + [
      '#type' => 'fieldset',
    ];

    /** @var \Drupal\notification_system\Service\NotificationSystem $notificationSystem */
    $notificationSystem = \Drupal::service('notification_system');
    $providers = $notificationSystem->getProviders();

    $options = [];
    foreach ($providers as $provider) {
      $options[$provider->id()] = $provider->label();
    }

    $element['wrapper']['provider'] = [
      '#type' => 'radios',
      '#title' => $this->t('Notification Provider'),
      '#default_value' => isset($items[$delta]->provider) ? $items[$delta]->provider : NULL,
      '#required' => $element['#required'],
      '#options' => $options,
    ];

    $element['wrapper']['notification_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Notification ID'),
      '#default_value' => isset($items[$delta]->id) ? $items[$delta]->id : NULL,
      '#required' => $element['#required'],
    ];

    return $element;
  }

  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    $output = [];

    foreach ($values as $key => $value) {
      $output[$key] = [
        'provider' => $value['wrapper']['provider'],
        'notification_id' => $value['wrapper']['notification_id'],
      ];
    }

    return $output;
  }


}
