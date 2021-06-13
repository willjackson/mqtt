<?php

namespace Drupal\mqtt;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of MQTT Subscription entities.
 *
 * @ingroup mqtt
 */
class MqttSubscriptionListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('ID');
    $header['name'] = $this->t('Subscription');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\mqtt\Entity\MqttSubscription $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.mqtt_subscription.canonical',
      ['mqtt_subscription' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
