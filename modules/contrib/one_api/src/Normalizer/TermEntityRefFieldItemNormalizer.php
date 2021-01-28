<?php
namespace Drupal\one_api\Normalizer;

use Drupal\serialization\Normalizer\EntityReferenceFieldItemNormalizer;
use Drupal\taxonomy\Entity\Term;

/**
 * Converts the Drupal entity object structures to a normalized array.
 */
class TermEntityRefFieldItemNormalizer extends EntityReferenceFieldItemNormalizer {
  
  /**
   * {@inheritdoc}
   */
  public function supportsNormalization($data, $format = NULL) {
    if(is_object($data) && (get_class($data) == "Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem")){
      return TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize($field_item, $format = NULL, array $context = []) {
    $values = parent::normalize($field_item, $format, $context);
    if (!empty($values['target_type']) && "taxonomy_term" == $values['target_type']) {
      $term = Term::load($values['target_id']);
      $name = $term->getName();
      $values['name'] = $name;
    }
    return $values;
  }
}
