<?php

namespace Drupal\notification_system_dispatch_mail\Plugin\NotificationSystemDispatcher;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\notification_system\model\NotificationInterface;
use Drupal\notification_system_dispatch\NotificationSystemDispatcherPluginBase;
use Drupal\user\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the notification_system_dispatcher.
 *
 * @NotificationSystemDispatcher(
 *   id = "mail",
 *   label = @Translation("Mail"),
 *   description = @Translation("Send notifications via mail.")
 * )
 */
class MailDispatcher extends NotificationSystemDispatcherPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The mail manager.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * The logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MailManagerInterface $mailManager, LoggerChannelFactoryInterface $loggerChannelFactory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->mailManager = $mailManager;
    $this->logger = $loggerChannelFactory->get('notification_system_dispatch_mail');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.mail'),
      $container->get('logger.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function dispatch(UserInterface $user, array $notifications) {
    $module = 'notification_system_dispatch_mail';
    $key = 'new_notification';
    $to = $user->getEmail();
    $params['notifications'] = $notifications;
    $langcode = $user->getPreferredLangcode();
    $send = TRUE;

    if (!$to) {
      return;
    }

    $result = $this->mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);

    if ($result['result'] !== TRUE) {
      $this->logger->warning('Error while sending the notifications to ' . $to . ' via email.');
    }
  }

}
