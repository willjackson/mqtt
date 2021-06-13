<?php

namespace Drupal\mqtt;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\mqtt\Entity\MqttBrokerInterface;

/**
 * Defines the storage handler class for MQTT Broker entities.
 *
 * This extends the base storage class, adding required special handling for
 * MQTT Broker entities.
 *
 * @ingroup mqtt
 */
class MqttBrokerStorage extends SqlContentEntityStorage implements MqttBrokerStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(MqttBrokerInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {mqtt_broker_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {mqtt_broker_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(MqttBrokerInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {mqtt_broker_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('mqtt_broker_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
