<?php

/**
 * @file
 * Implements FullCalendar Colors hooks for taxonomy.module.
 */

/**
 * Implements hook_fullcalendar_classes().
 */
function taxonomy_fullcalendar_classes($entity) {
  $filtered_entity = array_intersect_key((array) $entity, field_info_instances($entity->entity_type, $entity->bundle));
  $tids = _fullcalendar_colors_filter_term_ids($filtered_entity);
  $class_names = array();
  foreach (taxonomy_get_vocabularies() as $vid => $vocab) {
    if (variable_get('fullcalendar_colors_taxo_' . $vid . '_enabled', FALSE)) {
      // If enabled, get all the terms in the taxonomy.
      foreach (taxonomy_get_tree($vid) as $term) {
        if (in_array($term->tid, $tids)) {
          $class_names[] = 'fullcalendar_colors_taxo_term_' . $term->tid;
        }
      }
    }
  }
  return $class_names;
}

/**
 * Filters all the term ids found in an array of fields.
 *
 * @param array $fields
 *   An array filled with fields.
 *
 * @return array
 *   List of the term ids found in the given fields.
 */
function _fullcalendar_colors_filter_term_ids($fields) {
  $term_ids = array();
  foreach ($fields as $key => $value) {
    foreach ($value as $language => $term) {
      foreach ($term as $content) {
        if (isset($content['tid'])) {
          $term_ids[] = $content['tid'];
        }
      }
    }
  }
  return $term_ids;
}
