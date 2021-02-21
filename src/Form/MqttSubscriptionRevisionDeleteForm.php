<?php

namespace Drupal\mqtt_subscribe\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a MQTT Subscription revision.
 *
 * @ingroup mqtt_subscribe
 */
class MqttSubscriptionRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The MQTT Subscription revision.
   *
   * @var \Drupal\mqtt_subscribe\Entity\MqttSubscriptionInterface
   */
  protected $revision;

  /**
   * The MQTT Subscription storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $mqttSubscriptionStorage;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->mqttSubscriptionStorage = $container->get('entity_type.manager')->getStorage('mqtt_subscription');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mqtt_subscription_revision_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the revision from %revision-date?', [
      '%revision-date' => format_date($this->revision->getRevisionCreationTime()),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.mqtt_subscription.version_history', ['mqtt_subscription' => $this->revision->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $mqtt_subscription_revision = NULL) {
    $this->revision = $this->MqttSubscriptionStorage->loadRevision($mqtt_subscription_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->MqttSubscriptionStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('MQTT Subscription: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of MQTT Subscription %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.mqtt_subscription.canonical',
       ['mqtt_subscription' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {mqtt_subscription_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.mqtt_subscription.version_history',
         ['mqtt_subscription' => $this->revision->id()]
      );
    }
  }

}
