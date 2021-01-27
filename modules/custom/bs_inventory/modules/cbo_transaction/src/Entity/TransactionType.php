<?php

namespace Drupal\cbo_transaction\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\cbo_transaction\TransactionTypeInterface;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Defines the Transaction Type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "transaction_type",
 *   label = @Translation("Transaction Type"),
 *   handlers = {
 *     "access" = "Drupal\Core\Entity\EntityAccessControlHandler",
 *     "form" = {
 *       "default" = "Drupal\cbo_transaction\TransactionTypeForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *     "list_builder" = "Drupal\cbo\CboConfigEntityListBuilder",
 *   },
 *   admin_permission = "administer transaction types",
 *   config_prefix = "type",
 *   bundle_of = "transaction",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "add-form" = "/admin/transaction/type/add",
 *     "edit-form" = "/admin/transaction/type/{transaction_type}/edit",
 *     "delete-form" = "/admin/transaction/type/{transaction_type}/delete",
 *     "collection" = "/admin/transaction/type",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "source_type",
 *     "action",
 *   }
 * )
 */
class TransactionType extends ConfigEntityBundleBase implements TransactionTypeInterface {

  /**
   * The machine name of this Transaction Type.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable name of the Transaction Type.
   *
   * @var string
   */
  protected $label;

  /**
   * The transaction source type.
   *
   * @var string
   */
  protected $source_type;

  /**
   * The transaction action.
   *
   * @var string
   */
  protected $action;

  /**
   * A brief description of this Transaction Type.
   *
   * @var string
   */
  protected $description;

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);

    if (!$update) {
      if ($this->action == 'direct_organization_transfer') {
        cbo_transaction_add_to_organization_field($this);
      }
    }
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

  /**
   * {@inheritdoc}
   */
  public function getSourceType() {
    return $this->source_type;
  }

  /**
   * {@inheritdoc}
   */
  public function getAction() {
    return $this->action;
  }

}
