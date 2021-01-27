<?php

namespace Drupal\notification_system\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'notification_reference' formatter.
 *
 * @FieldFormatter(
 *   id = "notification_reference",
 *   label = @Translation("Notification Reference"),
 *   field_types = {
 *     "notification_reference"
 *   }
 * )
 */
class NotificationReference extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $this->viewValue($item)];
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    return nl2br(Html::escape($item->get('provider')->getValue() . ': ' . $item->get('notification_id')->getValue()));
  }

}
