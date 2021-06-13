<?php

namespace Drupal\mqtt\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining MQTT Broker entities.
 *
 * @ingroup mqtt
 */
interface MqttBrokerInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the MQTT Broker name.
   *
   * @return string
   *   Name of the MQTT Broker.
   */
  public function getName();

  /**
   * Sets the MQTT Broker name.
   *
   * @param string $name
   *   The MQTT Broker name.
   *
   * @return \Drupal\mqtt\Entity\MqttBrokerInterface
   *   The called MQTT Broker entity.
   */
  public function setName($name);

  /**
   * Gets the MQTT Broker creation timestamp.
   *
   * @return int
   *   Creation timestamp of the MQTT Broker.
   */
  public function getCreatedTime();

  /**
   * Sets the MQTT Broker creation timestamp.
   *
   * @param int $timestamp
   *   The MQTT Broker creation timestamp.
   *
   * @return \Drupal\mqtt\Entity\MqttBrokerInterface
   *   The called MQTT Broker entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the MQTT Broker revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the MQTT Broker revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\mqtt\Entity\MqttBrokerInterface
   *   The called MQTT Broker entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the MQTT Broker revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the MQTT Broker revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\mqtt\Entity\MqttBrokerInterface
   *   The called MQTT Broker entity.
   */
  public function setRevisionUserId($uid);

}
