<?php

/**
 * @file
 * Contains mqtt_broker.page.inc.
 *
 * Page callback for MQTT Broker entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for MQTT Broker templates.
 *
 * Default template: mqtt_broker.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_mqtt_broker(array &$variables) {
  // Fetch MqttBroker Entity Object.
  $mqtt_broker = $variables['elements']['#mqtt_broker'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
