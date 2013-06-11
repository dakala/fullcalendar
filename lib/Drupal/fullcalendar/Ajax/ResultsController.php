<?php

/**
 * @file
 * Contains \Drupal\fullcalendar\Ajax\ResultsController.
 */

namespace Drupal\fullcalendar\Ajax;

use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\fullcalendar\Ajax\ResultsCommand;
use Drupal\views\ViewStorageInterface;

/**
 * @todo.
 */
class ResultsController {

  /**
   * @todo.
   */
  public function getResults(ViewStorageInterface $view, $display_id, Request $request) {
    // Get the view and check access.
    $view = $view->get('executable');
    if (!$view || !$view->access($display_id)) {
      return;
    }

    if (!$view->setDisplay($display_id)) {
      return;
    }

    $args = array();
    $view->dom_id = $request->request->get('dom_id');
    $view->fullcalendar_ajax = TRUE;
    $view->preExecute($args);
    $view->initStyle();
    $view->execute($display_id);
    $output = $view->style_plugin->render();
    $view->postExecute();

    $response = new AjaxResponse();
    $response->addCommand(new ResultsCommand(drupal_render($output)));
    return $response;
  }

}
