<?php

namespace Drupal\commerce_funds\Controller;

use Drupal\Core\Url;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\commerce_funds\WithdrawalMethodManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * WithdrawalMethods controller.
 */
class WithdrawalMethods extends ControllerBase {

  /**
   * Defines variables to be used later.
   *
   * @var \Drupal\commerce_funds\WithdrawalMethodManager
   */
  protected $withdrawalMethodManager;

  /**
   * Class constructor.
   */
  public function __construct(WithdrawalMethodManager $withdrawal_method_manager) {
    $this->withdrawalMethodManager = $withdrawal_method_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.withdrawal_method')
    );
  }

  /**
   * Display the list of available Withdrawal methods.
   *
   * @see WithdrawalMethodsManager
   *
   * @return array
   *   Return a renderable array.
   */
  public function content($method) {
    $methods = $this->withdrawalMethodManager->getDefinitions();
    $enabled_methods = $this->config('commerce_funds.settings')->get('withdrawal_methods')['methods'];
    // Build the list page (route parameter = default).
    if ($method == 'list') {
      // Prepares the link for the list.
      $base_url = Url::fromRoute('commerce_funds.withdrawal_methods')->toString();
      $items = [];
      foreach ($enabled_methods as $method => $name) {
        if (!is_numeric($name)) {
          $items[$name] = [
            '#markup' => '<a href="' . $base_url . '/' . $methods[$name]['id'] . '">' . ucfirst($name) . '</a>',
            '#wrapper_attributes' => [
              'class' => [
                'method-link',
              ],
            ],
          ];
        }
      }
      // Build the items list.
      $build['item_list'] = [
        '#theme' => 'item_list',
        '#list_type' => 'ul',
        '#wrapper_attributes' => [
          'class' => [
            'method-list',
          ],
        ],
        '#attributes' => [
          'class' => [
            'method-links',
          ],
        ],
        '#items' => $items,
      ];
    }
    // Load the plugin (route parameter = plugin id).
    elseif (in_array($method, array_keys($methods))) {
      $class = $this->withdrawalMethodManager->getDefinition($method)['class'];
      $build = $this->formBuilder()->getForm($class);
    }
    // Make sure people reach page not found on other route paramater.
    else {
      throw new NotFoundHttpException();
    }

    return $build;
  }

}
