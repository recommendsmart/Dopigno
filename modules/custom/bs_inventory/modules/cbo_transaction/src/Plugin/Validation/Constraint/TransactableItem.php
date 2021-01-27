<?php

namespace Drupal\cbo_transaction\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Checks if the item is a transactable item.
 *
 * @Constraint(
 *   id = "TransactableItem",
 *   label = @Translation("Item must be transactable")
 * )
 */
class TransactableItem extends Constraint implements ConstraintValidatorInterface {

  public $message = '@name is not a transactable item.';

  /**
   * @var \Symfony\Component\Validator\ExecutionContextInterface
   */
  protected $context;

  /**
   * {@inheritdoc}
   */
  public function initialize(ExecutionContextInterface $context) {
    $this->context = $context;
  }

  /**
   * {@inheritdoc}
   */
  public function validatedBy() {
    return get_class($this);
  }

  /**
   * {@inheritdoc}
   */
  public function validate($items, Constraint $constraint) {
    /** @var \Drupal\Core\Field\FieldItemListInterface $items */
    /** @var \Drupal\cbo_item\ItemInterface $item */
    $item = $items->first()->entity;
    if (!$item->get('transactable')->value) {
      $this->context->addViolation($this->message, ['@name' => $item->label()]);
    }
  }

}
