<?php

namespace Drupal\commerce_shipping;

use Drupal\commerce_shipping\Entity\ShipmentInterface;
use Drupal\commerce_shipping\Event\ShippingEvents;
use Drupal\commerce_shipping\Event\ShippingRatesEvent;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ShipmentManager implements ShipmentManagerInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * The logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructs a new ShipmentManager object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EventDispatcherInterface $event_dispatcher, LoggerInterface $logger) {
    $this->entityTypeManager = $entity_type_manager;
    $this->eventDispatcher = $event_dispatcher;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateRates(ShipmentInterface $shipment) {
    $all_rates = [];
    /** @var \Drupal\commerce_shipping\ShippingMethodStorageInterface $shipping_method_storage */
    $shipping_method_storage = $this->entityTypeManager->getStorage('commerce_shipping_method');
    $shipping_methods = $shipping_method_storage->loadMultipleForShipment($shipment);
    foreach ($shipping_methods as $shipping_method) {
      $shipping_method_plugin = $shipping_method->getPlugin();
      try {
        $rates = $shipping_method_plugin->calculateRates($shipment);
      }
      catch (\Exception $exception) {
        $this->logger->error('Exception occurred when calculating rates for @name: @message', [
          '@name' => $shipping_method->getName(),
          '@message' => $exception->getMessage(),
        ]);
        continue;
      }
      // Allow the rates to be altered via code.
      $event = new ShippingRatesEvent($rates, $shipping_method, $shipment);
      $this->eventDispatcher->dispatch(ShippingEvents::SHIPPING_RATES, $event);
      $rates = $event->getRates();

      foreach ($rates as $rate) {
        // Replace the rate ID with a constructed one which contains the
        // shipping method ID. Use it to key the return array.
        $new_rate_values = [
          'id' => $shipping_method->id() . '--' . $rate->getService()->getId(),
        ] + $rate->toArray();
        $rate = ShippingRate::fromArray($new_rate_values);

        $all_rates[$rate->getId()] = $rate;
      }
    }

    return $all_rates;
  }

  /**
   * {@inheritdoc}
   */
  public function selectDefaultRate(ShipmentInterface $shipment, array $rates) {
    /** @var \Drupal\commerce_shipping\ShippingRate[] $rates */
    $default_rate = reset($rates);
    if ($shipment->getShippingMethodId() && $shipment->getShippingService()) {
      $rate_id = $shipment->getShippingMethodId() . '--' . $shipment->getShippingService();
      foreach ($rates as $rate) {
        if ($rate_id == $rate->getId()) {
          $default_rate = $rate;
          break;
        }
      }
    }

    return $default_rate;
  }

}
