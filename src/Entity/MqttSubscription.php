<?php

namespace Drupal\mqtt_subscribe\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the MQTT Subscription entity.
 *
 * @ingroup mqtt_subscribe
 *
 * @ContentEntityType(
 *   id = "mqtt_subscription",
 *   label = @Translation("Subscription"),
 *   handlers = {
 *     "storage" = "Drupal\mqtt_subscribe\MqttSubscriptionStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\mqtt_subscribe\MqttSubscriptionListBuilder",
 *     "views_data" = "Drupal\mqtt_subscribe\Entity\MqttSubscriptionViewsData",
 *     "translation" = "Drupal\mqtt_subscribe\MqttSubscriptionTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\mqtt_subscribe\Form\MqttSubscriptionForm",
 *       "add" = "Drupal\mqtt_subscribe\Form\MqttSubscriptionForm",
 *       "edit" = "Drupal\mqtt_subscribe\Form\MqttSubscriptionForm",
 *       "delete" = "Drupal\mqtt_subscribe\Form\MqttSubscriptionDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\mqtt_subscribe\MqttSubscriptionHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\mqtt_subscribe\MqttSubscriptionAccessControlHandler",
 *   },
 *   base_table = "mqtt_subscription",
 *   data_table = "mqtt_subscription_field_data",
 *   revision_table = "mqtt_subscription_revision",
 *   revision_data_table = "mqtt_subscription_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer mqtt subscription entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/content/mqtt/subscriptions/{mqtt_subscription}",
 *     "add-form" = "/admin/content/mqtt/subscriptions/add",
 *     "edit-form" = "/admin/content/mqtt/subscriptions/{mqtt_subscription}/edit",
 *     "delete-form" = "/admin/content/mqtt/subscriptions/{mqtt_subscription}/delete",
 *     "version-history" = "/admin/content/mqtt/subscriptions/{mqtt_subscription}/revisions",
 *     "revision" = "/admin/content/mqtt/subscriptions/{mqtt_subscription}/revisions/{mqtt_subscription_revision}/view",
 *     "revision_revert" = "/admin/content/mqtt/subscriptions/{mqtt_subscription}/revisions/{mqtt_subscription_revision}/revert",
 *     "revision_delete" = "/admin/content/mqtt/subscriptions/{mqtt_subscription}/revisions/{mqtt_subscription_revision}/delete",
 *     "translation_revert" = "/admin/content/mqtt/subscriptions/{mqtt_subscription}/revisions/{mqtt_subscription_revision}/revert/{langcode}",
 *     "collection" = "/admin/content/mqtt/subscriptions",
 *   },
 *   field_ui_base_route = "mqtt_subscription.settings"
 * )
 */
class MqttSubscription extends EditorialContentEntityBase implements MqttSubscriptionInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);

    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly,
    // make the mqtt_subscription owner the revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the MQTT Subscription entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Subscription'))
      ->setDescription(t('The name of the MQTT Subscription entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['mqtt_broker'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Broker'))
      ->setDescription(t('MQTT Broker for this subscription'))
      ->setSetting('target_type', 'mqtt_broker')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', array(
        'label'  => 'hidden',
        'type'   => 'mqtt_broker',
        'weight' => -10,
      ))
      ->setDisplayOptions('form', array(
        'type'     => 'options_select',
        'weight'   => 5,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', FALSE)
      ->setRequired(TRUE);

    $fields['csv_data'] = BaseFieldDefinition::create('file')
      ->setLabel(t('CSV Data'))
      ->setDescription(t('CSV Data.'))
      ->setSetting('file_extensions', 'csv')
      ->setSetting('file_directory', 'mqtt-subscriptions')
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'file',
        'weight' => -3,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'file',
        'weight' => -3,
      ))
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'mqtt_subscription_data',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE)
    ->setRequired(FALSE);

    $fields['status']->setDescription(t('A boolean indicating whether the MQTT Subscription is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }
}
