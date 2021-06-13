<?php

namespace Drupal\mqtt\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\mqtt\Entity\MqttSubscriptionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MqttSubscriptionController.
 *
 *  Returns responses for MQTT Subscription routes.
 */
class MqttSubscriptionController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * Displays a MQTT Subscription revision.
   *
   * @param int $mqtt_revision
   *   The MQTT Subscription revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($mqtt_revision) {
    $mqtt = $this->entityTypeManager()->getStorage('mqtt')
      ->loadRevision($mqtt_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('mqtt');

    return $view_builder->view($mqtt);
  }

  /**
   * Page title callback for a MQTT Subscription revision.
   *
   * @param int $mqtt_revision
   *   The MQTT Subscription revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($mqtt_revision) {
    $mqtt = $this->entityTypeManager()->getStorage('mqtt')
      ->loadRevision($mqtt_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $mqtt->label(),
      '%date' => $this->dateFormatter->format($mqtt->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a MQTT Subscription.
   *
   * @param \Drupal\mqtt_subscribe\Entity\MqttSubscriptionInterface $mqtt
   *   A MQTT Subscription object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(MqttSubscriptionInterface $mqtt) {
    $account = $this->currentUser();
    $mqtt_storage = $this->entityTypeManager()->getStorage('mqtt');

    $langcode = $mqtt->language()->getId();
    $langname = $mqtt->language()->getName();
    $languages = $mqtt->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $mqtt->label()]) : $this->t('Revisions for %title', ['%title' => $mqtt->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all mqtt subscription revisions") || $account->hasPermission('administer mqtt subscription entities')));
    $delete_permission = (($account->hasPermission("delete all mqtt subscription revisions") || $account->hasPermission('administer mqtt subscription entities')));

    $rows = [];

    $vids = $mqtt_storage->revisionIds($mqtt);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\mqtt_subscribe\MqttSubscriptionInterface $revision */
      $revision = $mqtt_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $mqtt->getRevisionId()) {
          $link = $this->l($date, new Url('entity.mqtt.revision', [
            'mqtt' => $mqtt->id(),
            'mqtt_revision' => $vid,
          ]));
        }
        else {
          $link = $mqtt->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.mqtt.translation_revert', [
                'mqtt' => $mqtt->id(),
                'mqtt_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.mqtt.revision_revert', [
                'mqtt' => $mqtt->id(),
                'mqtt_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.mqtt.revision_delete', [
                'mqtt' => $mqtt->id(),
                'mqtt_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['mqtt_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
