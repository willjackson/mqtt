<?php

namespace Drupal\mqtt;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\mqtt\Entity\MqttSubscriptionInterface;

/**
 * Defines the storage handler class for MQTT Subscription entities.
 *
 * This extends the base storage class, adding required special handling for
 * MQTT Subscription entities.
 *
 * @ingroup mqtt
 */
interface MqttSubscriptionStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of MQTT Subscription revision IDs for a specific MQTT Subscription.
   *
   * @param \Drupal\mqtt\Entity\MqttSubscriptionInterface $entity
   *   The MQTT Subscription entity.
   *
   * @return int[]
   *   MQTT Subscription revision IDs (in ascending order).
   */
  public function revisionIds(MqttSubscriptionInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as MQTT Subscription author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   MQTT Subscription revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\mqtt\Entity\MqttSubscriptionInterface $entity
   *   The MQTT Subscription entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(MqttSubscriptionInterface $entity);

  /**
   * Unsets the language for all MQTT Subscription with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
