<?php

namespace Drupal\mqtt_subscribe\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a MQTT Broker revision.
 *
 * @ingroup mqtt_subscribe
 */
class MqttBrokerRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The MQTT Broker revision.
   *
   * @var \Drupal\mqtt_subscribe\Entity\MqttBrokerInterface
   */
  protected $revision;

  /**
   * The MQTT Broker storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $mqttBrokerStorage;

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
    $instance->mqttBrokerStorage = $container->get('entity_type.manager')->getStorage('mqtt_broker');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mqtt_broker_revision_delete_confirm';
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
    return new Url('entity.mqtt_broker.version_history', ['mqtt_broker' => $this->revision->id()]);
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
  public function buildForm(array $form, FormStateInterface $form_state, $mqtt_broker_revision = NULL) {
    $this->revision = $this->MqttBrokerStorage->loadRevision($mqtt_broker_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->MqttBrokerStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('MQTT Broker: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of MQTT Broker %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.mqtt_broker.canonical',
       ['mqtt_broker' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {mqtt_broker_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.mqtt_broker.version_history',
         ['mqtt_broker' => $this->revision->id()]
      );
    }
  }

}
