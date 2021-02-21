<?php

/**
 * @file
 * Contains mqtt_subscription.page.inc.
 *
 * Page callback for MQTT Subscription entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for MQTT Subscription templates.
 *
 * Default template: mqtt_subscription.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_mqtt_subscription(array &$variables) {
  // Fetch MqttSubscription Entity Object.
  $mqtt_subscription = $variables['elements']['#mqtt_subscription'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
