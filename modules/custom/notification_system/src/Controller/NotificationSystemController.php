<?php

namespace Drupal\notification_system\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\notification_system\model\ReadableNotificationInterface;
use Drupal\notification_system\Service\NotificationSystem;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Returns responses for Notification System routes.
 */
class NotificationSystemController extends ControllerBase {

  /**
   * The notification system.
   *
   * @var \Drupal\notification_system\Service\NotificationSystem
   */
  protected $notificationSystem;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The current http request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * The controller constructor.
   *
   * @param \Drupal\notification_system\Service\NotificationSystem $notification_system
   *   The notification system service.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The current http request.
   */
  public function __construct(NotificationSystem $notification_system, RendererInterface $renderer, EntityTypeManagerInterface $entity_type_manager, AccountProxyInterface $current_user, RequestStack $request_stack) {
    $this->notificationSystem = $notification_system;
    $this->renderer = $renderer;
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $current_user;
    $this->request = $request_stack->getCurrentRequest();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('notification_system'),
      $container->get('renderer'),
      $container->get('entity_type.manager'),
      $container->get('current_user'),
      $container->get('request_stack')
    );
  }

  /**
   * Builds the response.
   */
  public function build() {
    $types = $this->notificationSystem->getTypes();

    $build['types'] = [
      '#markup' => '<p>Possible types are: ' . implode(', ', $types) . '</p>',
    ];

    $notifications = $this->notificationSystem->getNotifications(\Drupal::currentUser());

    $build['table'] = [
      '#type' => 'table',
      '#header' => [
        'Provider',
        'ID',
        'Type',
        'Title',
        'Body',
        'Link',
        'Sticky',
        'Timestamp',
        'Priority',
      ],
    ];

    foreach ($notifications as $notification) {
      $build['table']['#rows'][] = [
        $notification->getProvider(),
        $notification->getId(),
        $notification->getType(),
        $notification->getTitle(),
        $notification->getBody(),
        $notification->getLink() ? $notification->getLink()->toString() : '',
        $notification->isSticky() ? 'True' : 'False',
        $notification->getTimestamp(),
        $notification->getPriority(),
      ];
    }

    $build['#cache'] = [
      'max-age' => 0,
    ];

    return $build;
  }

  /**
   * Generates the html for all unread notifications for the current user.
   *
   * @param string $display_mode
   *   If it should output bundled html or not.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Outputs only the needed html. Will cache the output for 20 seconds.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getNotifications($display_mode) {
    $build = [];

    $showRead = $this->request->query->get('showRead') !== NULL;

    $notifications = $this->notificationSystem->getNotifications($this->currentUser, $display_mode == 'bundled', $showRead);

    if ($display_mode == 'bundled') {
      $groupManager = $this->entityTypeManager->getStorage('notification_group');

      /** @var \Drupal\notification_system\Entity\NotificationGroupInterface[] $groups */
      $groups = $groupManager->loadMultiple();

      foreach ($groups as $group) {
        $build[$group->id()] = [
          '#theme' => 'notification_group',
          '#group' => $group,
          '#id' => $group->id(),
          '#label' => $group->label(),
          '#description' => [
            '#type' => 'processed_text',
            '#text' => $group->getDescription()['value'],
            '#format' => $group->getDescription()['format'],
          ],
        ];
      }

      foreach ($notifications as $groupId => $notificationList) {
        if (array_key_exists($groupId, $build)) {
          $build[$groupId]['#content'] = $this->buildNotificationList($notificationList);
        }
      }
    }
    else {
      $build = $this->buildNotificationList($notifications);
    }

    $renderedOutput = $this->renderer->renderRoot($build);

    $response = new Response($renderedOutput);

     // Cache the notifications for 20 seconds in the browser.
        $response->setMaxAge(0);
    return $response;
  }

  /**
   * Mark a notification as read.
   *
   * @param string $providerId
   *   The id of the notification provider that holds the notification.
   * @param string $notificationId
   *   The id of the notification.
   *
   * @return \Zend\Diactoros\Response\JsonResponse
   *   A JSON Response containing the status.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function markAsRead(string $providerId, string $notificationId) {
    $status = $this->notificationSystem->markAsRead($this->currentUser(), $providerId, $notificationId);

    // Check if it was successful.
    if ($status === TRUE) {
      $responseBody = [
        'status' => 'success',
      ];
    }
    else {
      $responseBody = [
        'status' => 'error',
        'message' => $status,
      ];
    }

    $statusCode = 200;
    if ($responseBody['status'] == 'error') {
      $statusCode = 400;
    }

    $response = new JsonResponse($responseBody, $statusCode);

    return $response;
  }

  /**
   * Builds a render array for a list of notifications.
   *
   * @param \Drupal\notification_system\model\NotificationInterface[] $notifications
   *   The notifications to build.
   *
   * @return array
   *   A render array.
   */
  protected function buildNotificationList(array $notifications) {
    $build = [];

    foreach ($notifications as $notification) {
      $provider = $notification->getProvider();

      $link = NULL;
      if ($notification->getLink()) {
        $link = new Link($this->t('Read more'), $notification->getLink());
      }

      $notificationBuild = [
        '#theme' => 'notification_item',
        '#notification' => $notification,
        '#provider' => $provider,
        '#id' => $notification->getId(),
        '#type' => $notification->getType(),
        '#timestamp' => $notification->getTimestamp(),
        '#title' => $notification->getTitle(),
        '#body' => $notification->getBody(),
        '#link' => $link,
        '#sticky' => $notification->isSticky(),
        '#priority' => $notification->getPriority(),
      ];

      if ($notification instanceof ReadableNotificationInterface) {
        $notificationBuild['#isRead'] = $notification->isReadBy($this->currentUser()->id());
      }

      $build[] = $notificationBuild;
    }

    return $build;
  }

}
