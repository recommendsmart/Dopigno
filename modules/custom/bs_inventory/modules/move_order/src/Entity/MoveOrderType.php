<?php

namespace Drupal\move_order\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\move_order\MoveOrderTypeInterface;

/**
 * Defines the Move order type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "move_order_type",
 *   label = @Translation("Move order type"),
 *   handlers = {
 *     "access" = "Drupal\Core\Entity\EntityAccessControlHandler",
 *     "form" = {
 *       "default" = "Drupal\move_order\MoveOrderTypeForm",
 *       "delete" = "Drupal\move_order\Form\MoveOrderTypeDeleteConfirm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *     "list_builder" = "Drupal\cbo\CboConfigEntityListBuilder",
 *   },
 *   admin_permission = "administer move order types",
 *   config_prefix = "type",
 *   bundle_of = "move_order",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "add-form" = "/admin/move_order/type/add",
 *     "edit-form" = "/admin/move_order/type/{move_order_type}",
 *     "delete-form" = "/admin/move_order/type/{move_order_type}/delete",
 *     "collection" = "/admin/move_order/type",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *   }
 * )
 */
class MoveOrderType extends ConfigEntityBundleBase implements MoveOrderTypeInterface {

  /**
   * The machine name of this MoveOrder type.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable name of the MoveOrder type.
   *
   * @var string
   */
  protected $label;

  /**
   * A brief description of this MoveOrder type.
   *
   * @var string
   */
  protected $description;

  /**
   * {@inheritdoc}
   */
  public function isLocked() {
    $locked = \Drupal::state()->get('move_order.type.locked');
    return isset($locked[$this->id()]) ? $locked[$this->id()] : FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($description) {
    $this->description = $description;
    return $this;
  }

}
