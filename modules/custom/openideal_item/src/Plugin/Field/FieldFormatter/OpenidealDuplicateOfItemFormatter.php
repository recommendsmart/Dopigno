<?php

namespace Drupal\openideal_item\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'openideal_duplicate_of_item' formatter.
 *
 * @FieldFormatter(
 *   id = "openideal_duplicate_of_item",
 *   label = @Translation("Openideal Duplicate Item"),
 *   description = @Translation("Display the link for a duplicate item."),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class OpenidealDuplicateOfItemFormatter extends EntityReferenceFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    // @codingStandardsIgnoreLine
    if (\Drupal::routeMatch()->getRouteName() != 'layout_builder.defaults.node.view') {
      foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {
        if ($entity) {
          $field_text = $this->t('This item was merged with <a href="@link">@title</a> item.', [
            '@link' => $entity->toUrl()->toString(),
            '@title' => $entity->label(),
          ]);
          // @TODO: add css class to the markup.
          $elements[$delta] = [
            '#type' => 'markup',
            '#markup' => '<div>' . $field_text . '</div>',
          ];

          $elements[$delta]['#cache']['tags'] = $entity->getCacheTags();
        }
      }
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    // This formatter is only available for item content type nodes.
    $target_type = $field_definition->getFieldStorageDefinition()
      ->getSetting('target_type');
    return $target_type == 'node' && $field_definition->get('bundle') == 'item';
  }

}
