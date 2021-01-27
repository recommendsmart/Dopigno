<?php

namespace Drupal\cbo_transaction\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\cbo_transaction\TransactionSourceTypeInterface;

/**
 * Defines the Transaction Source Type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "transaction_source_type",
 *   label = @Translation("Transaction Source Type"),
 *   handlers = {
 *     "access" = "Drupal\Core\Entity\EntityAccessControlHandler",
 *     "form" = {
 *       "default" = "Drupal\cbo_transaction\TransactionSourceTypeForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *     "list_builder" = "Drupal\cbo\CboConfigEntityListBuilder",
 *   },
 *   admin_permission = "administer transaction source_types",
 *   config_prefix = "source_type",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "add-form" = "/admin/transaction/source_type/add",
 *     "edit-form" = "/admin/transaction/source_type/{transaction_source_type}/edit",
 *     "delete-form" = "/admin/transaction/source_type/{transaction_source_type}/delete",
 *     "collection" = "/admin/transaction/source_type",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *   }
 * )
 */
class TransactionSourceType extends ConfigEntityBase implements TransactionSourceTypeInterface {

  /**
   * The machine name of this Transaction Source Type.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable name of the Transaction Source Type.
   *
   * @var string
   */
  protected $label;

  /**
   * A brief description of this Transaction Source Type.
   *
   * @var string
   */
  protected $description;

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
