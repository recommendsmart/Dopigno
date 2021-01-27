<?php

namespace Drupal\notification_system\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Defines the Notification Group entity.
 *
 * @ConfigEntityType(
 *   id = "notification_group",
 *   label = @Translation("Notification Group"),
 *   label_collection = @Translation("Notification Groups"),
 *   label_singular = @Translation("notification group"),
 *   label_plural = @Translation("notification groups"),
 *   label_count = @PluralTranslation(
 *     singular = "@count notification group",
 *     plural = "@count notificatoin groups",
 *   ),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\notification_system\NotificationGroupListBuilder",
 *     "form" = {
 *       "add" = "Drupal\notification_system\Form\NotificationGroupForm",
 *       "edit" = "Drupal\notification_system\Form\NotificationGroupForm",
 *       "delete" = "Drupal\notification_system\Form\NotificationGroupDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\notification_system\NotificationGroupHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "notification_group",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "weight" = "weight",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/notification-group/{notification_group}",
 *     "add-form" = "/admin/structure/notification-group/add",
 *     "edit-form" = "/admin/structure/notification-group/{notification_group}/edit",
 *     "delete-form" = "/admin/structure/notification-group/{notification_group}/delete",
 *     "collection" = "/admin/structure/notification-group"
 *   }
 * )
 */
class NotificationGroup extends ConfigEntityBase implements NotificationGroupInterface {

  /**
   * The Notification Group ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Notification Group label.
   *
   * @var string
   */
  protected $label;

  /**
   * The weight of this group. Used for sorting.
   *
   * @var int
   */
  protected $weight;

  /**
   * The description of this group. Displayed in the Group dropdown.
   *
   * @var array
   */
  protected $description;

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return $this->get('weight');
  }

  /**
   * {@inheritdoc}
   */
  public function setWeight($weight) {
    $this->set('weight', $weight);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    $value = $this->get('description');

    if (!$value) {
      return [
        'value' => '',
        'format' => 'full_html',
      ];
    }

    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public static function postLoad(EntityStorageInterface $storage, array &$entities) {
    parent::postLoad($storage, $entities);
    // Sort the queried roles by their weight.
    // See \Drupal\Core\Config\Entity\ConfigEntityBase::sort().
    uasort($entities, 'static::sort');
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    if (!isset($this->weight) && ($roles = $storage->loadMultiple())) {
      // Set a group weight to make this new role last.
      $max = array_reduce($roles, function ($max, $role) {
        return $max > $role->weight ? $max : $role->weight;
      });
      $this->weight = $max + 1;
    }
  }

}
