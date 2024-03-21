<?php

namespace Drupal\mqtt\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\file\Entity\File;
use Drupal\mqtt\Event\MqttSubscriptionCheckEvent;
use karpy47\PhpMqttClient\MQTTClient;

/**
 * Plugin implementation of the mqtt_subscription_queue queueworker.
 *
 * @QueueWorker (
 *   id = "mqtt_subscription_queue",
 *   title = @Translation("Consume MQTT Subscription Data"),
 *   cron = {"time" = 30}
 * )
 */
class MqttSubscriptionQueue extends QueueWorkerBase {

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {

    // Process item operations.
    $broker_config = \Drupal::config('mqtt.mqttbrokersettingsform');

    foreach ($data->subscriptions as $subscription) {
      $subscription_topic = $subscription->getName();
      $subscription_broker = $subscription->get('mqtt_broker')->getValue();
      $subscription_broker_id = $broker_config->get('mqtt_id');
      $broker_id = $subscription_broker[0]["target_id"];
      $broker = \Drupal::entityTypeManager()->getStorage('mqtt_broker')->load($broker_id);
      $broker_host = $broker->get('broker_address')->getValue()[0]["value"];
      $broker_host = (!empty($broker_host)) ? $broker_host : NULL;
      $broker_port = $broker->get('broker_port')->getValue()[0]["value"];
      $broker_port = (!empty($broker_port)) ? $broker_port : NULL;
      if (!empty($broker->get('broker_username')->getValue())) {
        $broker_user = $broker->get('broker_username')->getValue()[0]["value"];
      } else {
        $broker_user = NULL;
      }
      if (!empty($broker->get('broker_password')->getValue())) {
        $broker_password = $broker->get('broker_password')->getValue()[0]["value"];
      } else {
        $broker_password = NULL;
      }

      $timestamp = time();

      $client = new MQTTClient($broker_host, $broker_port);

      if (!empty($broker_host) && !empty($broker_password)) {
        $client->setAuthentication($broker_user, $broker_password);
      }

      // $client->setEncryption('cacerts.pem'); // Todo: Add support for SSL connection!
      $success = $client->sendConnect($subscription_broker_id);
      if ($success) {
        $client->sendSubscribe($subscription_topic);
        $mqtt_response = $client->getPublishMessages();
        $message = $mqtt_response[0]["message"];
        $client->sendDisconnect();

        $event = new MqttSubscriptionCheckEvent($subscription);
        $event_dispatcher = \Drupal::service('event_dispatcher');
        $event_dispatcher->dispatch(MqttSubscriptionCheckEvent::EVENT_NAME, $event);

      }
      $client->close();

      if (!is_null($subscription->get('csv_data')->getValue()[0]['target_id']) && !empty($message)) {
        $subscription_msg_csv = array($timestamp, $message);

        $data_file = $subscription->get('csv_data')->getValue()[0]['target_id'];

        $sub_data_file = \Drupal::entityTypeManager()->getStorage('file')->load($data_file);
        $file = $sub_data_file->getFileUri();

        $handle = fopen($file, "a");
        fputcsv($handle, $subscription_msg_csv);
        fclose($handle);

      } else {
        if (!empty($message)) {
          $subscription_msg_csv = array(
            ['timestamp', 'message'],
            [$timestamp, $message]
          );

          // Open a file in write mode ('w')
          $fp = fopen(file_directory_temp() . "/sub_$timestamp.csv", 'w');

          // Loop through file pointer and a line
          foreach ($subscription_msg_csv as $fields) {
            fputcsv($fp, $fields);
          }
          fclose($fp);

          // todo: destroy temp file

          $sub_directory = $subscription->getFieldDefinition('csv_data')->getSetting('file_directory');
          $url_scheme = $subscription->getFieldDefinition('csv_data')->getSetting('uri_scheme');
          $directory = "$url_scheme://$sub_directory";
          file_prepare_directory($directory, FILE_CREATE_DIRECTORY);
          $sub_file = \Drupal::service('file.repository')->writeData(fopen(file_directory_temp() . "/sub_$timestamp.csv", 'r'),  $directory . '/sub_' . $subscription->id() . '.csv', FILE_EXISTS_REPLACE);
          $subscription->set('csv_data', ['target_id' => $sub_file->id()]);
          $subscription->save();
        }
      }
    }
  }
}
