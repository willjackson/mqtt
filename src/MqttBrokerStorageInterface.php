<?php

namespace Drupal\mqtt;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface MqttBrokerStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of MQTT Broker revision IDs for a specific MQTT Broker.
   *
   * @param \Drupal\mqtt\Entity\MqttBrokerInterface $entity
   *   The MQTT Broker entity.
   *
   * @return int[]
   *   MQTT Broker revision IDs (in ascending order).
   */
  public function revisionIds(MqttBrokerInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as MQTT Broker author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   MQTT Broker revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\mqtt\Entity\MqttBrokerInterface $entity
   *   The MQTT Broker entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(MqttBrokerInterface $entity);

  /**
   * Unsets the language for all MQTT Broker with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
