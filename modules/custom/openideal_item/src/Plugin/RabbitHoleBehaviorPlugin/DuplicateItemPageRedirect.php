<?php

namespace Drupal\openideal_item\Plugin\RabbitHoleBehaviorPlugin;

use Drupal\Core\Entity\EntityInterface;
use Drupal\rabbit_hole\Plugin\RabbitHoleBehaviorPlugin\PageRedirect;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirects to another page.
 *
 * @RabbitHoleBehaviorPlugin(
 *   id = "duplicate_item_page_redirect",
 *   label = @Translation("Duplicate item page redirect")
 * )
 */
class DuplicateItemPageRedirect extends PageRedirect {

  /**
   * {@inheritdoc}
   */
  public function getActionTarget(EntityInterface $entity) {
    if ($entity->bundle() !== 'item' || $entity->get('field_duplicate_of')->isEmpty()) {
      return FALSE;
    }
    return $entity->get('field_duplicate_of')->entity->toUrl()->toString();
  }

  /**
   * {@inheritdoc}
   */
  public function performAction(EntityInterface $entity, Response $current_response = NULL) {
    $target = $this->getActionTarget($entity);
    if (!$target) {
      return;
    }
    return parent::performAction($entity, $current_response);
  }

}
