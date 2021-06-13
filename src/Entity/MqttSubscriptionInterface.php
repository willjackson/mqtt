<?php

namespace Drupal\mqtt\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining MQTT Subscription entities.
 *
 * @ingroup mqtt
 */
interface MqttSubscriptionInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the MQTT Subscription name.
   *
   * @return string
   *   Name of the MQTT Subscription.
   */
  public function getName();

  /**
   * Sets the MQTT Subscription name.
   *
   * @param string $name
   *   The MQTT Subscription name.
   *
   * @return \Drupal\mqtt\Entity\MqttSubscriptionInterface
   *   The called MQTT Subscription entity.
   */
  public function setName($name);

  /**
   * Gets the MQTT Subscription creation timestamp.
   *
   * @return int
   *   Creation timestamp of the MQTT Subscription.
   */
  public function getCreatedTime();

  /**
   * Sets the MQTT Subscription creation timestamp.
   *
   * @param int $timestamp
   *   The MQTT Subscription creation timestamp.
   *
   * @return \Drupal\mqtt\Entity\MqttSubscriptionInterface
   *   The called MQTT Subscription entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the MQTT Subscription revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the MQTT Subscription revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\mqtt\Entity\MqttSubscriptionInterface
   *   The called MQTT Subscription entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the MQTT Subscription revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the MQTT Subscription revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\mqtt\Entity\MqttSubscriptionInterface
   *   The called MQTT Subscription entity.
   */
  public function setRevisionUserId($uid);

}
