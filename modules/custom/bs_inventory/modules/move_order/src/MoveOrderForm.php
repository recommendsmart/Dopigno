<?php

namespace Drupal\move_order;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form handler for the move_order edit forms.
 */
class MoveOrderForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $insert = $entity->isNew();
    $entity->save();
    $entity_link = $entity->link($this->t('View'));
    $context = ['%title' => $entity->label(), 'link' => $entity_link];
    $t_args = ['%title' => $entity->link($entity->label())];

    if ($insert) {
      $this->logger('move_order')->notice('Move order: added %title.', $context);
      drupal_set_message($this->t('Move order %title has been created.', $t_args));
    }
    else {
      $this->logger('move_order')->notice('Move order: updated %title.', $context);
      drupal_set_message($this->t('Move order %title has been updated.', $t_args));
    }
  }

}
