<?php

namespace Drupal\cycle_count;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form handler for the cycle_count_entry edit forms.
 */
class CycleCountEntryForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $form['date']['widget'][0]['value']['#default_value'] = DrupalDateTime::createFromTimestamp(time());

    $form['container'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['container-inline'],
      ],
      '#weight' => -5,
    ];
    $form['container']['cycle_count'] = $form['cycle_count'];
    unset($form['cycle_count']);
    $form['container']['date'] = $form['date'];
    unset($form['date']);
    $form['container']['user'] = $form['user'];
    unset($form['user']);

    return $form;
  }

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
      $this->logger('cycle_count')->notice('CycleCountEntry: added %title.', $context);
      drupal_set_message($this->t('CycleCountEntry %title has been created.', $t_args));
    }
    else {
      $this->logger('cycle_count')->notice('CycleCountEntry: updated %title.', $context);
      drupal_set_message($this->t('CycleCountEntry %title has been updated.', $t_args));
    }
  }

}
