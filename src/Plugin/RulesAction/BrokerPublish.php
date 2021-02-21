<?php

namespace Drupal\mqtt_subscribe\Plugin\RulesAction;

use Drupal\mqtt_subscribe\Entity\MqttBroker;
use Drupal\rules\Core\RulesActionBase;
use karpy47\PhpMqttClient\MQTTClient;

/**
 * Publish to a subscription.
 *
 * @RulesAction(
 *   id = "rules_mqtt_broker_publish",
 *   label = @Translation("Publish to a MQTT broker"),
 *   category = @Translation("Broker"),
 *   context_definitions = {
 *     "broker" = @ContextDefinition("entity_reference",
 *       label = @Translation("Broker"),
 *       description = @Translation("Specify the MQTT broker to publish to."),
 *       assignment_restriction = "selector"
 *     ),
 *     "subscription" = @ContextDefinition("string",
 *       label = @Translation("Subscription"),
 *       description = @Translation("The subscription on the MQTT Broker to publish to"),
 *     ),
 *     "message" = @ContextDefinition("string",
 *       label = @Translation("Message"),
 *       description = @Translation("The message to submit to the MQTT Broker"),
 *     ),
 *   }
 * )
 */

class BrokerPublish extends RulesActionBase {

  /**
   * Executes the action with the given context.
   *
   * @param \Drupal\mqtt_subscribe\Entity\MqttBroker $broker
   *   The node to modify.
   * @param string $subscription
   *   Message to send to broker.
   * @param string $message
   *   Message to send to broker.
   */
  protected function doExecute(MqttBroker $broker, $subscription, $message) {
    // Process item operations.
    $broker_config = \Drupal::config('mqtt_subscribe.mqttbrokersettingsform');
    $subscription_broker_id = $broker_config->get('mqtt_id');

    $broker_host = $broker->get('broker_address')->getValue()[0]['value'];
    $broker_host = (!empty($broker_host)) ? $broker_host : NULL;
    $broker_port = $broker->get('broker_port')->getValue()[0]["value"];
    $broker_user = !empty($broker->get('broker_username')
      ->getValue()) ? $broker->get('broker_username')
      ->getValue()[0]["value"] : NULL;
    $broker_password = !empty($broker->get('broker_password')
      ->getValue()) ? $broker->get('broker_password')
      ->getValue()[0]["value"] : NULL;

    $client = new MQTTClient($broker_host, $broker_port);
    if (!empty($broker_host) && !empty($broker_password)) {
      $client->setAuthentication($broker_user, $broker_password);
    }

    // $client->setEncryption('cacerts.pem'); // Todo: Add support for SSL connection!
    $success = $client->sendConnect($subscription_broker_id);
    if ($success) {
      $client->sendPublish($subscription, $message);
      $client->sendDisconnect();
    }
    $client->close();
  }
}
