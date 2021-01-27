<?php

namespace Drupal\cycle_count\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\cycle_count\CycleCountInterface;

/**
 * Defines the cycle_count entity class.
 *
 * @ContentEntityType(
 *   id = "cycle_count",
 *   label = @Translation("Cycle count"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "access" = "Drupal\cycle_count\CycleCountAccessControlHandler",
 *     "views_data" = "Drupal\cycle_count\CycleCountViewsData",
 *     "form" = {
 *       "default" = "Drupal\cycle_count\CycleCountForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *   },
 *   base_table = "cycle_count",
 *   entity_keys = {
 *     "id" = "cid",
 *     "label" = "title",
 *     "uuid" = "uuid",
 *     "status" = "status",
 *   },
 *   admin_permission = "administer cycle counts",
 *   links = {
 *     "add-form" = "/admin/cycle_count/add",
 *     "canonical" = "/admin/cycle_count/{cycle_count}",
 *     "edit-form" = "/admin/cycle_count/{cycle_count}/edit",
 *     "delete-form" = "/admin/cycle_count/{cycle_count}/delete",
 *     "collection" = "/admin/cycle_count",
 *   }
 * )
 */
class CycleCount extends ContentEntityBase implements CycleCountInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->get('description')->value;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', [
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['description'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Description'))
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', [
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The timestamp that the bom was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The timestamp that the bom was last changed.'));

    return $fields;
  }

}
