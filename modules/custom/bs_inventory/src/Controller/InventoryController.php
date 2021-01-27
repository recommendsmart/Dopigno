<?php

namespace Drupal\bs_inventory\Controller;

use Drupal\cbs\Controller\CbsControllerBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;

/**
 * Returns response for Inventory routes.
 */
class InventoryController extends CbsControllerBase {

  /**
   * Display the implementation status.
   */
  public function implementationStatus() {
    $items = [];

    $items[] = $this->checkEntity(count($items) + 1, $this->t('Define Your Locations'), 'location');
    $items[] = $this->checkEntity(count($items) + 1, $this->t('Define Your Organizations'), 'organization');
    $items[] = $this->checkEntity(count($items) + 1, $this->t('Define Your Units of Measure'), 'uom');
    $items[] = $this->checkEntity(count($items) + 1, $this->t('Define Your Subinventories'), 'subinventory');
    $items[] = $this->checkEntity(count($items) + 1, $this->t('Define Your Stock Locators'), 'locator');
    $items[] = $this->checkEntity(count($items) + 1, $this->t('Define Your Categories'), 'item_category');
    $items[] = $this->checkEntity(count($items) + 1, $this->t('Define Your Item Types'), 'item_type');
    $items[] = $this->checkEntity(count($items) + 1, $this->t('Define Items'), 'item');
    $items[] = $this->checkEntity(count($items) + 1, $this->t('Define Your Transaction Source Types'), 'transaction_source_type');
    $items[] = $this->checkEntity(count($items) + 1, $this->t('Define Your Transaction Types'), 'transaction_type');

    $items[] = [
      '#markup' => $this->t('Step @step <a href=":url">Remove the implementation status block</a>', [
        '@step' => count($items) + 1,
        ':url' => Url::fromRoute('block.admin_display')->toString(),
      ]),
      '#wrapper_attributes' => [
        'class' => [
          'not-finished',
        ],
      ],
    ];

    $output = parent::_implementationStatus($items);

    $tags = $this->entityTypeManager()->getDefinition('location')
      ->getListCacheTags();
    $tags = Cache::mergeTags($tags,
      $this->entityTypeManager()->getDefinition('organization')
      ->getListCacheTags());
    $tags = Cache::mergeTags($tags,
      $this->entityTypeManager->getDefinition('uom')
        ->getListCacheTags());
    $tags = Cache::mergeTags($tags,
      $this->entityTypeManager->getDefinition('subinventory')
        ->getListCacheTags());
    $tags = Cache::mergeTags($tags,
      $this->entityTypeManager->getDefinition('item_category')
        ->getListCacheTags());
    $tags = Cache::mergeTags($tags,
      $this->entityTypeManager->getDefinition('item_type')
        ->getListCacheTags());
    $tags = Cache::mergeTags($tags,
      $this->entityTypeManager->getDefinition('item')
        ->getListCacheTags());
    $tags = Cache::mergeTags($tags,
      $this->entityTypeManager->getDefinition('transaction_source_type')
        ->getListCacheTags());
    $tags = Cache::mergeTags($tags,
      $this->entityTypeManager->getDefinition('transaction_type')
        ->getListCacheTags());
    $output['#cache']['tags'] = $tags;

    return $output;
  }

}
