<?php

namespace Drupal\mqtt_subscribe\Event;

use Drupal\mqtt_subscribe\Entity\MqttSubscription;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event that is fired when mqtt subscriptions are retrieved.
 *
 * @see mqtt_subscribe_cron()
 */
class MqttSubscriptionCheckEvent extends Event {

  const EVENT_NAME = 'mqtt_subscription_check';

  /**
   * @var \Drupal\mqtt_subscribe\Entity\MqttSubscription
   */
  public $subscription;

  /**
   * Constructs the object.
   *
   * @param \Drupal\mqtt_subscribe\Entity\MqttSubscription $subscription
   *   The account of the user logged in.
   */
  public function __construct(MqttSubscription $subscription) {
    $this->subscription = $subscription;
  }

}
