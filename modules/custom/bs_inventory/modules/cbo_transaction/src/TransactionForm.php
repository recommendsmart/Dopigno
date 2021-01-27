<?php

namespace Drupal\cbo_transaction;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form handler for the transaction edit forms.
 */
class TransactionForm extends ContentEntityForm {

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
    $form['container']['number'] = $form['number'];
    unset($form['number']);
    $form['container']['user'] = $form['user'];
    unset($form['user']);
    $form['container']['date'] = $form['date'];
    unset($form['date']);

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
      $this->logger('cbo_transaction')->notice('Transaction: added %title.', $context);
      drupal_set_message($this->t('Transaction %title has been created.', $t_args));
    }
    else {
      $this->logger('cbo_transaction')->notice('Transaction: updated %title.', $context);
      drupal_set_message($this->t('Transaction %title has been updated.', $t_args));
    }
  }

}
