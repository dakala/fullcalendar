<?php

/**
 * @file
 * Collection of fullcalendar_colors hook implementations.
 */

/**
 * Implements hook_fullcalendar_colors_create_css().
 *
 * Provide colors per node type.
 */
function node_fullcalendar_colors_class_names($entity) {
  $class_names = array();
  if (variable_get('fullcalendar_colors_node_types_enabled', FALSE)) {
    if ($entity->entity_type == 'node') {
      $class_names[] = 'fullcalendar_colors_node_type_' . $entity->type;
    }
  }
  return $class_names;
}

/**
 * Implements hook_fullcalendar_colors_create_css().
 *
 * Provide colors per user role.
 */
function user_fullcalendar_colors_class_names($entity) {
  $class_names = array();
  if (variable_get('fullcalendar_colors_user_roles_enabled', FALSE)) {
    if ($entity->entity_type == 'user') {
      foreach ($entity->roles as $role_id => $role) {
        $class_names[] = 'fullcalendar_colors_user_role_' . $role_id;
      }
    }
  }
  return $class_names;
}

/**
 * Implements hook_fullcalendar_colors_create_css().
 *
 * Provide colors per taxonomy term.
 */
function taxonomy_fullcalendar_colors_class_names($entity) {
  $filtered_entity = (array) $entity;
  $filtered_entity = array_intersect_key($filtered_entity, field_info_instances($entity->entity_type, $entity->bundle));
  $tids = _fullcalendar_colors_filter_term_ids($filtered_entity);
  $class_names = array();
  foreach (taxonomy_get_vocabularies() as $vid => $vocab) {
    if (variable_get('fullcalendar_colors_taxo_' . $vid . '_enabled', FALSE)) {
      // If enabled, get all the terms in the taxonomy.
      $terms = taxonomy_get_tree($vid);
      foreach ($terms as $term) {
        if (in_array($term->tid, $tids)) {
          $class_names[] = 'fullcalendar_colors_taxo_term_' . $term->tid;
        }
      }
    }
  }
  return $class_names;
}

/**
 * Filters all the term id's found in an array of fields.
 *
 * @param $fields
 *   An array filled with fields.
 *
 * @return
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
