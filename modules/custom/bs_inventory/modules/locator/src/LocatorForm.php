<?php

namespace Drupal\locator;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form handler for the locator edit forms.
 */
class LocatorForm extends ContentEntityForm {

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
      $this->logger('locator')->notice('Locator: added %title.', $context);
      drupal_set_message($this->t('Locator %title has been created.', $t_args));
    }
    else {
      $this->logger('locator')->notice('Locator: updated %title.', $context);
      drupal_set_message($this->t('Locator %title has been updated.', $t_args));
    }
  }

}
