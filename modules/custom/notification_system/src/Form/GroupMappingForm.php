<?php

namespace Drupal\notification_system\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ConfigManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\notification_system\Service\NotificationSystem;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Notification System settings for this site.
 */
class GroupMappingForm extends ConfigFormBase {

  /**
   * The notification system service.
   *
   * @var \Drupal\notification_system\Service\NotificationSystem
   */
  protected $notificationSystem;

  /**
   * The EntityTypeManager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManger;

  /**
   * Creates a GroupMappingForm.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\notification_system\Service\NotificationSystem $notificationSystem
   *   The notification system.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The EntityTypeManager.
   */
  public function __construct(ConfigFactoryInterface $config_factory, NotificationSystem $notificationSystem, EntityTypeManagerInterface $entityTypeManager) {
    parent::__construct($config_factory);

    $this->notificationSystem = $notificationSystem;
    $this->entityTypeManger = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('notification_system'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'notification_system_group_mapping';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['notification_system.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $types = $this->notificationSystem->getTypes();

    // TODO: Display message if no types found.

    // TODO: Display info text what this mapping stuff means.

    $options = [
      '' => t('- None -'),
    ];

    /** @var \Drupal\Core\Config\Entity\ConfigEntityStorageInterface $notificationGroupStorage */
    $notificationGroupStorage = \Drupal::entityTypeManager()->getStorage('notification_group');
    /** @var \Drupal\notification_system\Entity\NotificationGroupInterface[] $notificationGroups */
    $notificationGroups = $notificationGroupStorage->loadMultiple();

    foreach ($notificationGroups as $notificationGroup) {
      $options[$notificationGroup->id()] = $notificationGroup->label();
    }

    foreach ($types as $type) {
      $form['type-' . $type] = [
        '#type' => 'select',
        '#title' => $type,
        '#options' => $options,
        '#default_value' => $this->getDefaultValue($type),
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $mappings = [];

    $types = $this->notificationSystem->getTypes();

    foreach ($types as $type) {
      $mappings[] = [
        'notification_type' => $type,
        'notification_group' => $form_state->getValue('type-' . $type),
      ];
    }

    $this->config('notification_system.settings')
      ->set('group_mappings', $mappings)
      ->save();
    parent::submitForm($form, $form_state);
  }

  /**
   * Get the default value for a type selector field.
   *
   * @param string $type
   *   The notification type id.
   *
   * @return string
   *   Either an empty string if there is not value set
   *   or the notification group id.
   */
  protected function getDefaultValue(string $type) {
    $config = $this->config('notification_system.settings');
    $groupMappings = $config->get('group_mappings');

    if (!is_array($groupMappings)) {
      return '';
    }

    foreach ($groupMappings as $mapping) {
      if ($mapping['notification_type'] === $type) {
        return $mapping['notification_group'];
      }
    }

    return '';
  }

}
