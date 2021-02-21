<?php

namespace Drupal\mqtt_subscribe;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\mqtt_subscribe\Entity\MqttSubscriptionInterface;

/**
 * Defines the storage handler class for MQTT Subscription entities.
 *
 * This extends the base storage class, adding required special handling for
 * MQTT Subscription entities.
 *
 * @ingroup mqtt_subscribe
 */
class MqttSubscriptionStorage extends SqlContentEntityStorage implements MqttSubscriptionStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(MqttSubscriptionInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {mqtt_subscription_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {mqtt_subscription_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(MqttSubscriptionInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {mqtt_subscription_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('mqtt_subscription_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
