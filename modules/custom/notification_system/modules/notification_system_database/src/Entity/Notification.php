<?php

namespace Drupal\notification_system_database\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\link\Plugin\Field\FieldType\LinkItem;
use Drupal\notification_system\Event\NewNotificationEvent;
use Drupal\notification_system\model\NotificationInterface as ModelNotificationInterface;
use Drupal\notification_system_database\model\DatabaseNotification;
use Drupal\notification_system_database\NotificationInterface;

/**
 * Defines the notification entity class.
 *
 * @ContentEntityType(
 *   id = "notification",
 *   label = @Translation("Notification"),
 *   label_collection = @Translation("Notifications"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\notification_system_database\NotificationListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\notification_system_database\Form\NotificationForm",
 *       "edit" = "Drupal\notification_system_database\Form\NotificationForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "notification",
 *   data_table = "notification_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer notification",
 *   entity_keys = {
 *     "id" = "id",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "label" = "title",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "add-form" = "/admin/content/notification/add",
 *     "canonical" = "/notification/{notification}",
 *     "edit-form" = "/admin/content/notification/{notification}/edit",
 *     "delete-form" = "/admin/content/notification/{notification}/delete",
 *     "collection" = "/admin/content/notification"
 *   },
 *   field_ui_base_route = "entity.notification.settings"
 * )
 */
class Notification extends ContentEntityBase implements NotificationInterface {

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->get('title')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTitle($title) {
    $this->set('title', $title);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Users'))
      ->setDescription(t('The users who the notification is for'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setTranslatable(FALSE)
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['provider_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Provider'))
      ->setDescription(t('The id of the <strong>database</strong> notification provider that created the notification. Not a normal notification provider.'))
      ->setTranslatable(FALSE)
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['notification_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Type'))
      ->setDescription(t('The type of the notification'))
      ->setTranslatable(FALSE)
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created on'))
      ->setDescription(t('The time that the notification was created.'))
      ->setTranslatable(FALSE)
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
      ])
      ->setDisplayConfigurable('view', TRUE);


    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The title of the notification entity.'))
      ->setTranslatable(TRUE)
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['body'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Body'))
      ->setDescription(t('Additional text of the notification.'))
      ->setTranslatable(TRUE)
      ->setRequired(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'basic_string',
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['link'] = BaseFieldDefinition::create('link')
      ->setLabel(t('Link'))
      ->setDescription(t('A link that provides more information about the notification.'))
      ->setTranslatable(TRUE)
      ->setRequired(FALSE)
      ->setSetting('link_type', LinkItem::LINK_GENERIC)
      ->setSetting('title', DRUPAL_DISABLED)
      ->setDisplayOptions('form', [
        'type' => 'link_default',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'link',
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['sticky'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Sticky'))
      ->setDescription(t('Indicates if the notification can be marked as read.'))
      ->setTranslatable(FALSE)
      ->setDefaultValue(FALSE)
      ->setRequired(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'boolean',
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['priority'] = BaseFieldDefinition::create('list_integer')
      ->setLabel(t('Priority'))
      ->setDescription(t('Indicates how important the notification is.'))
      ->setTranslatable(FALSE)
      ->setDefaultValue(ModelNotificationInterface::PRIORITY_MEDIUM)
      ->setRequired(TRUE)
      ->setSetting('allowed_values', [
        ModelNotificationInterface::PRIORITY_LOWEST => t('Lowest'),
        ModelNotificationInterface::PRIORITY_LOW => t('Low'),
        ModelNotificationInterface::PRIORITY_MEDIUM => t('Medium (Default)'),
        ModelNotificationInterface::PRIORITY_HIGH => t('High'),
        ModelNotificationInterface::PRIORITY_HIGHEST => t('Highest'),
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'list_default',
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['expires'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Expires on'))
      ->setDescription(t('Specify a date when the notification is not relevant anymore.'))
      ->setTranslatable(FALSE)
      ->setRequired(FALSE)
      ->setSetting('datetime_type', DateTimeItem::DATETIME_TYPE_DATETIME)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'datetime_default',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function toNotificationModel() {
    $notification = new DatabaseNotification(
      'database',
      $this->id(),
      $this->get('notification_type')->value,
      [],
      $this->getCreatedTime(),
      $this->label(),
      NULL,
      NULL,
      $this->get('sticky')->value,
      $this->get('priority')->value
    );

    $notification->setEntityId($this->id());

    if (count($this->get('body')) > 0) {
      $notification->setBody($this->get('body')->value);
    }

    if (count($this->get('link')) > 0) {
      /** @var \Drupal\link\LinkItemInterface $linkItem */
      $linkItem = $this->get('link')[0];
      if (!$linkItem->isEmpty()) {
        $notification->setLink($linkItem->getUrl());
      }
    }

    return $notification;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    // Workaround for over long notification titles.
    $this->setTitle(substr($this->getTitle(), 0, 255));
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);

    if (!$update) {
      $notification = $this->toNotificationModel();

      $event = new NewNotificationEvent($notification);

      /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
      $eventDispatcher = \Drupal::service('event_dispatcher');

      $eventDispatcher->dispatch(NewNotificationEvent::EVENT_NAME, $event);
    }
  }

  /**
   * {@inheritDoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    parent::postDelete($storage, $entities);

    // Delete corresponding entries in the "Read-Table".
    $notificationIds = [];

    foreach ($entities as $entity) {
      $notificationIds[] = $entity->id();
    }

    \Drupal::database()->delete('notification_system_database_read')
      ->condition('entity_id', $notificationIds, 'IN')
      ->execute();

  }

}
