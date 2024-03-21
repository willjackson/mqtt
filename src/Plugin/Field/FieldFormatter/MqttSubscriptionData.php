<?php

namespace Drupal\mqtt\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'mqtt_subscription_data' formatter.
 *
 * @FieldFormatter(
 *   id = "mqtt_subscription_data",
 *   label = @Translation("Subscription Data"),
 *   field_types = {
 *     "file"
 *   }
 * )
 */
class MqttSubscriptionData extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      // Implement default settings.
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [
      // Implement settings form.
    ] + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $file_url_generator = \Drupal::service('file_url_generator');

    foreach ($items as $delta => $item) {

      $file_id = $item->getValue();

      if (!empty($file_id)) {
        $file = \Drupal::entityTypeManager()->getStorage('file')->load($file_id['target_id']);
      }

      if (!empty($file)) {
        $file_url = $file_url_generator->generateString($file->getFileUri());

        $elements[$delta] = [
          '#csv_data' => $this->viewValue($item), // any other preprocessing
          '#attached' => [
            'library' => ['mqtt/subscription_csv_data'],
            'drupalSettings' => [
              'csvData' => $file_url,
              'subName' => $item->getEntity()->getName()
            ],
          ],
          '#markup' => '<div id="chartContainer" style="width:100%; height:300px;"></div>'
        ];
      }
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    return nl2br(Html::escape($item->value));
  }

}
