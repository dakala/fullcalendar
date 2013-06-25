<?php

/**
 * @file
 * Contains \Drupal\fullcalendar_legend\Plugin\Block\Term.
 */

namespace Drupal\fullcalendar_legend\Plugin\Block;

use Drupal\Component\Annotation\Plugin;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\field\FieldInfo;
use Drupal\fullcalendar_legend\Plugin\Block\FullcalendarLegendBase;
use Drupal\taxonomy\TermStorageControllerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @todo.
 *
 * @Plugin(
 *   id = "fullcalendar_legend_term",
 *   subject = @Translation("Fullcalendar Legend: Term"),
 *   module = "fullcalendar_legend"
 * )
 */
class Term extends FullcalendarLegendBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\taxonomy\TermStorageControllerInterface
   */
  protected $termStorage;

  /**
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $entityQuery;

  /**
   * @var \Drupal\field\FieldInfo
   */
  protected $fieldInfo;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, TermStorageControllerInterface $term_storage, QueryFactory $entity_query, FieldInfo $field_info) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->termStorage = $term_storage;
    $this->entityQuery = $entity_query;
    $this->fieldInfo = $field_info;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, array $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.entity')->getStorageController('taxonomy_term'),
      $container->get('entity.query'),
      $container->get('field.info')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function buildLegend(array $fields) {
    $types = array();
    foreach ($fields as $field_name => $field) {
      // Then by entity type.
      foreach ($field->getBundles() as $entity_type => $bundles) {
        foreach ($bundles as $bundle) {
          foreach ($this->fieldInfo->getBundleInstances($entity_type, $bundle) as $taxonomy_field_name => $taxonomy_field) {
            if ($taxonomy_field->getFieldType() != 'taxonomy_term_reference') {
              continue;
            }
            foreach ($taxonomy_field->getFieldSetting('allowed_values') as $vocab) {
              $term_ids = $this->entityQuery->get('taxonomy_term')
                ->condition('vid', $vocab['vocabulary'])
                ->execute();
              foreach ($this->termStorage->load($term_ids) as $term) {
                $types[$term->id()] = array(
                  'entity_type' => $entity_type,
                  'field_name' => $field_name,
                  'bundle' => $bundle,
                  'label' => $term->label(),
                  'taxonomy_field' => $taxonomy_field_name,
                  'tid' => $term->id(),
                );
              }
            }
          }
        }
      }
    }
    return $types;
  }

}
