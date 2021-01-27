<?php

namespace Drupal\notification_system_database;

use Drupal\Component\Plugin\PluginBase;

/**
 * Base class for db_notification_provider plugins.
 */
abstract class DbNotificationProviderPluginBase extends PluginBase implements DbNotificationProviderInterface {

  /**
   * {@inheritdoc}
   */
  public function label() {
    // Cast the label to a string since it is a TranslatableMarkup object.
    return (string) $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function id() {
    return (string) $this->pluginDefinition['id'];
  }

}
