<?php

namespace Drupal\commerce_invoice\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\commerce_invoice\InvoicePrintBuilderInterface;
use Drupal\entity_print\Plugin\EntityPrintPluginManagerInterface;
use Drupal\entity_print\PrintEngineException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Provides the invoice download route.
 */
class InvoiceController implements ContainerInjectionInterface {

  use DependencySerializationTrait;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The Entity print plugin manager.
   *
   * @var \Drupal\entity_print\Plugin\EntityPrintPluginManagerInterface
   */
  protected $pluginManager;

  /**
   * The print builder.
   *
   * @var \Drupal\commerce_invoice\InvoicePrintBuilderInterface
   */
  protected $printBuilder;

  /**
   * Constructs a new InvoiceController object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\entity_print\Plugin\EntityPrintPluginManagerInterface $plugin_manager
   *   The Entity print plugin manager.
   * @param \Drupal\commerce_invoice\InvoicePrintBuilderInterface $print_builder
   *   The print builder.
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityPrintPluginManagerInterface $plugin_manager, InvoicePrintBuilderInterface $print_builder) {
    $this->configFactory = $config_factory;
    $this->pluginManager = $plugin_manager;
    $this->printBuilder = $print_builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('plugin.manager.entity_print.print_engine'),
      $container->get('commerce_invoice.print_builder')
    );
  }

  /**
   * Download an invoice.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   *   Thrown when the file was not found.
   */
  public function download(RouteMatchInterface $route_match) {
    /** @var \Drupal\commerce_invoice\Entity\InvoiceInterface $invoice */
    $invoice = $route_match->getParameter('commerce_invoice');

    try {
      /** @var \Drupal\entity_print\Plugin\PrintEngineInterface $print_engine */
      $print_engine = $this->pluginManager->createSelectedInstance('pdf');
    }
    catch (PrintEngineException $e) {
      watchdog_exception('commerce_invoice', $e);
      throw new NotFoundHttpException();
    }
    $file = $this->printBuilder->savePrintable($invoice, $print_engine);

    if (!$file) {
      throw new NotFoundHttpException();
    }

    $config = $this->configFactory->get('entity_print.settings');
    // Check whether we need to force the download.
    $content_disposition = $config->get('force_download') ? 'attachment' : NULL;
    $headers = file_get_content_headers($file);
    return new BinaryFileResponse($file->getFileUri(), 200, $headers, FALSE, $content_disposition);
  }

}
