<?php

namespace Drupal\bs_inventory\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 * Provides a block to display implementation status.
 *
 * @Block(
 *   id="inventory_implementation_status",
 *   admin_label = @Translation("Inventory implementation status")
 * )
 */
class ImplementationStatus extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => $this->t('Please check the <a href=":url">Inventory Implementation Status</a> and finish the system implementation.', [
        ':url' => Url::fromRoute('inventory.implementation_status')->toString(),
      ]),
      '#prefix' => '<div class="messages messages--warning">',
      '#suffix' => '</div>',
    ];
  }

}
