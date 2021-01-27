<?php

namespace Drupal\notification_system_dispatch;

use Drupal\Component\Plugin\PluginBase;

/**
 * Base class for notification_system_dispatcher plugins.
 */
abstract class NotificationSystemDispatcherPluginBase extends PluginBase implements NotificationSystemDispatcherInterface {

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
