<?php

namespace Drupal\cbo_transaction\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;

/**
 * Returns responses for Transaction routes.
 */
class TransactionController extends ControllerBase {

  /**
   * Displays add links for the available receipt bundles.
   */
  public function receiptAddPage() {
    $build = [
      '#theme' => 'entity_add_list',
      '#bundles' => [],
    ];

    $storage = $this->entityTypeManager()->getStorage('transaction_type');
    $ids = $storage->getQuery()
      ->condition('action', 'receipt_into_stores')
      ->execute();
    $bundles = $storage->loadMultiple($ids);
    if (count($bundles) == 1) {
      $bundle_names = array_keys($bundles);
      $bundle_name = reset($bundle_names);
      return $this->redirect('entity.transaction.add_form', [
        'transaction_type' => $bundle_name,
      ]);
    }
    foreach ($bundles as $bundle_name => $bundle_info) {
      $build['#bundles'][$bundle_name] = [
        'label' => $bundle_info['label'],
        'description' => isset($bundle_info['description']) ? $bundle_info['description'] : '',
        'add_link' => Link::createFromRoute($bundle_info['label'], 'entity.transaction.add_form', [
          'transaction_type' => $bundle_name,
        ]),
      ];
    }

    return $build;
  }

}
