<?php

namespace Drupal\mqtt\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for MQTT Subscription entities.
 */
class MqttSubscriptionViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
