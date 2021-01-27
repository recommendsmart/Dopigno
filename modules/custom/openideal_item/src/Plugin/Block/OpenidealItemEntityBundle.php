<?php

namespace Drupal\openideal_item\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\openideal_challenge\OpenidealContextEntityTrait;

/**
 * Provides a OpenidealItemEntityBundle class.
 *
 * @Block(
 *   id = "openidel_item_node_bundle",
 *   admin_label = @Translation("Entity bundle"),
 *   category = @Translation("Openideal"),
 *   context = {
 *      "entity" = @ContextDefinition(
 *       "entity",
 *       label = @Translation("Current Node"),
 *       required = FALSE,
 *     )
 *   }
 * )
 */
class OpenidealItemEntityBundle extends BlockBase {

  use OpenidealContextEntityTrait;

  /**
   * {@inheritDoc}
   */
  public function build() {
    $build = [];

    // If displayed in layout builder node isn't presented.
    if ($entity = $this->getEntity($this->getContexts(), 'entity')) {
      $build['content_type'] = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#attributes' => ['class' => ['entity_bundle_label', 'entity_bundle_label--' . $entity->bundle()]],
        '#value' => $entity->bundle() == 'article' ? $this->t('News') : $entity->bundle(),
      ];
    }

    return $build;
  }

}
