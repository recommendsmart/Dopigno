<?php

namespace Drupal\notification_system_database\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines db_notification_provider annotation object.
 *
 * @Annotation
 */
class DbNotificationProvider extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $title;

  /**
   * The description of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $description;

}
