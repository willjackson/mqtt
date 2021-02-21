<?php

namespace Drupal\mqtt_subscribe\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class MqttSubscriptionSettingsForm.
 *
 * @ingroup mqtt_subscribe
 */
class MqttSubscriptionSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'mqtt_subscribe.mqttsubscriptionsettingsform',
    ];
  }

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'mqttsubscription_settings';
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
    $this->config('mqtt_subscribe.mqttsubscriptionsettingsform')
      ->set('mqtt_polling_interval', $form_state->getValue('mqtt_polling_interval'))
      ->save();
  }

  /**
   * Defines the settings form for MQTT Subscription entities.
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

    $config = $this->config('mqtt_subscribe.mqttsubscriptionsettingsform');


    $form['mqtt_polling_interval'] = [
      '#type' => 'select',
      '#title' => $this->t('MQTT Polling Interval'),
      '#description' => $this->t('Select the polling interval for MQTT subscriptions.'),
      '#options' => [
        '0' => t('Every time cron runs'),
        '5' => t('5 minutes'),
        '10' => t('10 minutes'),
        '15' => t('15 minutes'),
        '30' => t('30 minutes'),
        '60' => t('1 hour')
      ],
      '#multiple' => FALSE,
      '#default_value' => $config->get('mqtt_polling_interval'),
    ];


    return parent::buildForm($form, $form_state);
  }

}
