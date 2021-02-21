<?php

namespace Drupal\mqtt_subscribe;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of MQTT Broker entities.
 *
 * @ingroup mqtt_subscribe
 */
class MqttBrokerListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('MQTT Broker ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\mqtt_subscribe\Entity\MqttBroker $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.mqtt_broker.canonical',
      ['mqtt_broker' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
