<?php

namespace Drupal\notification_system\Plugin\Field\FieldType;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\Exception\MissingDataException;

/**
 * Plugin implementation of the 'notification_reference' field type.
 *
 * @FieldType(
 *   id = "notification_reference",
 *   label = @Translation("Notification reference"),
 *   description = @Translation("Reference a specific notification by provider and id"),
 *   default_widget = "notification_reference",
 *   default_formatter = "notification_reference"
 * )
 */
class NotificationReference extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['provider'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Notification Provider'))
      ->setRequired(TRUE);

    $properties['notification_id'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Notification ID'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'provider' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'notification_id' => [
          'type' => 'varchar',
          'length' => 255,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $random = new Random();
    $values['provider'] = 'example';
    $values['notification_id'] = $random->string(8);
    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('provider')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * Load the notification from the provider.
   *
   * @return bool|\Drupal\notification_system\model\NotificationInterface
   *   A notification model or FALSE if the notification could not be loaded.
   */
  public function loadNotification() {
    /** @var \Drupal\notification_system\NotificationProviderPluginManager $notificationProviderPluginManager */
    $notificationProviderPluginManager = \Drupal::service('plugin.manager.notification_provider');

    try {
      $providerId = $this->get('provider')->getValue();
      $notificationId = $this->get('notification_id')->getValue();

      /** @var \Drupal\notification_system\NotificationProviderInterface $provider */
      $provider = $notificationProviderPluginManager->createInstance($providerId);

      return $provider->load($notificationId);
    }
    catch (PluginException $e) {
    }
    catch (MissingDataException $e) {
    }

    return FALSE;
  }

}
