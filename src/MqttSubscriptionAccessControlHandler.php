<?php

namespace Drupal\mqtt;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the MQTT Subscription entity.
 *
 * @see \Drupal\mqtt\Entity\MqttSubscription.
 */
class MqttSubscriptionAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\mqtt\Entity\MqttSubscriptionInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished mqtt subscription entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published mqtt subscription entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit mqtt subscription entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete mqtt subscription entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add mqtt subscription entities');
  }


}
