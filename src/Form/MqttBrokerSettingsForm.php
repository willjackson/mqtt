<?php

namespace Drupal\mqtt_subscribe\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class MqttBrokerSettingsForm.
 *
 * @ingroup mqtt_subscribe
 */
class MqttBrokerSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'mqtt_subscribe.mqttbrokersettingsform',
    ];
  }

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'mqttbroker_settings';
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('mqtt_subscribe.mqttbrokersettingsform')
      ->set('mqtt_id', $form_state->getValue('mqtt_id'))
      ->save();
  }

  /**
   * Defines the settings form for MQTT Broker entities.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   Form definition array.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('mqtt_subscribe.mqttbrokersettingsform');

    $form['mqtt_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('MQTT ID'),
      '#description' => $this->t('Provide an id to use when polling MQTT broker'),
      '#default_value' => $config->get('mqtt_id'),
    ];
    return parent::buildForm($form, $form_state);
  }

}
