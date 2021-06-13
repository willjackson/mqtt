<?php

namespace Drupal\mqtt\Event;

use Drupal\mqtt\Entity\MqttSubscription;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event that is fired when mqtt subscriptions are retrieved.
 *
 * @see mqtt_subscribe_cron()
 */
class MqttSubscriptionCheckEvent extends Event {

  const EVENT_NAME = 'mqtt_subscription_check';

  /**
   * @var \Drupal\mqtt\Entity\MqttSubscription
   */
  public $subscription;

  /**
   * Constructs the object.
   *
   * @param \Drupal\mqtt\Entity\MqttSubscription $subscription
   *   The account of the user logged in.
   */
  public function __construct(MqttSubscription $subscription) {
    $this->subscription = $subscription;
  }

}
