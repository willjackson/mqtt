<?php

namespace Drupal\mqtt_subscribe\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\mqtt_subscribe\Entity\MqttBrokerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MqttBrokerController.
 *
 *  Returns responses for MQTT Broker routes.
 */
class MqttBrokerController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a MQTT Broker revision.
   *
   * @param int $mqtt_broker_revision
   *   The MQTT Broker revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($mqtt_broker_revision) {
    $mqtt_broker = $this->entityTypeManager()->getStorage('mqtt_broker')
      ->loadRevision($mqtt_broker_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('mqtt_broker');

    return $view_builder->view($mqtt_broker);
  }

  /**
   * Page title callback for a MQTT Broker revision.
   *
   * @param int $mqtt_broker_revision
   *   The MQTT Broker revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($mqtt_broker_revision) {
    $mqtt_broker = $this->entityTypeManager()->getStorage('mqtt_broker')
      ->loadRevision($mqtt_broker_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $mqtt_broker->label(),
      '%date' => $this->dateFormatter->format($mqtt_broker->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a MQTT Broker.
   *
   * @param \Drupal\mqtt_subscribe\Entity\MqttBrokerInterface $mqtt_broker
   *   A MQTT Broker object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(MqttBrokerInterface $mqtt_broker) {
    $account = $this->currentUser();
    $mqtt_broker_storage = $this->entityTypeManager()->getStorage('mqtt_broker');

    $langcode = $mqtt_broker->language()->getId();
    $langname = $mqtt_broker->language()->getName();
    $languages = $mqtt_broker->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $mqtt_broker->label()]) : $this->t('Revisions for %title', ['%title' => $mqtt_broker->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all mqtt broker revisions") || $account->hasPermission('administer mqtt broker entities')));
    $delete_permission = (($account->hasPermission("delete all mqtt broker revisions") || $account->hasPermission('administer mqtt broker entities')));

    $rows = [];

    $vids = $mqtt_broker_storage->revisionIds($mqtt_broker);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\mqtt_subscribe\MqttBrokerInterface $revision */
      $revision = $mqtt_broker_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $mqtt_broker->getRevisionId()) {
          $link = $this->l($date, new Url('entity.mqtt_broker.revision', [
            'mqtt_broker' => $mqtt_broker->id(),
            'mqtt_broker_revision' => $vid,
          ]));
        }
        else {
          $link = $mqtt_broker->link($date);
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
              Url::fromRoute('entity.mqtt_broker.translation_revert', [
                'mqtt_broker' => $mqtt_broker->id(),
                'mqtt_broker_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.mqtt_broker.revision_revert', [
                'mqtt_broker' => $mqtt_broker->id(),
                'mqtt_broker_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.mqtt_broker.revision_delete', [
                'mqtt_broker' => $mqtt_broker->id(),
                'mqtt_broker_revision' => $vid,
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

    $build['mqtt_broker_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
