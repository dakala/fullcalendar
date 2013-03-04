<?php

/**
 * @file
 * Contains \Drupal\fullcalendar_legend\Plugin\block\block\Term.
 */

namespace Drupal\fullcalendar_legend\Plugin\block\block;

use Drupal\Core\Annotation\Plugin;
use Drupal\Core\Annotation\Translation;

/**
 * @todo.
 *
 * @Plugin(
 *   id = "fullcalendar_legend_term",
 *   subject = @Translation("Fullcalendar Legend: Term"),
 *   module = "fullcalendar_legend"
 * )
 */
class Term extends FullcalendarLegendBase {

  protected function buildLegend($fields) {
    $types = array();
    $use_i18n = module_exists('i18n_taxonomy');
    $field_info = field_info_fields();
    foreach ($fields as $field_name => $field) {
      // Then by entity type.
      foreach ($field['bundles'] as $entity_type => $bundles) {
        foreach ($bundles as $bundle) {
          foreach (field_info_instances($entity_type, $bundle) as $taxonomy_field_name => $taxonomy_field) {
            if ($field_info[$taxonomy_field_name]['type'] != 'taxonomy_term_reference') {
              continue;
            }
            foreach ($field_info[$taxonomy_field_name]['settings']['allowed_values'] as $vocab) {
              $vocabulary = taxonomy_vocabulary_machine_name_load($vocab['vocabulary']);
              foreach (taxonomy_get_tree($vocabulary->vid) as $term) {
                $term->vocabulary_machine_name = $vocabulary->machine_name;
                $types[$term->tid]['entity_type'] = $entity_type;
                $types[$term->tid]['field_name'] = $field_name;
                $types[$term->tid]['bundle'] = $bundle;
                $types[$term->tid]['label'] = ($use_i18n) ? i18n_taxonomy_term_name($term) : $term->name;
                $types[$term->tid]['taxonomy_field'] = $taxonomy_field_name;
                $types[$term->tid]['tid'] = $term->tid;
                $types[$term->tid]['uri'] = entity_uri('taxonomy_term', $term);
              }
            }
          }
        }
      }
    }
    return $types;
  }

}
