<?php

namespace Drupal\notification_system;

use Drupal\Component\Plugin\PluginBase;

/**
 * Base class for notification_provider plugins.
 */
abstract class NotificationProviderPluginBase extends PluginBase implements NotificationProviderInterface {

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
