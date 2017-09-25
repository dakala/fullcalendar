<?php

namespace Drupal\fullcalendar\Ajax;

use Drupal\Core\Ajax\CommandInterface;

/**
 * Provides an AJAX command for showing the save and cancel buttons.
 *
 * This command is implemented in Drupal.AjaxCommands.prototype.ResultsCommand.
 */
class ResultsCommand implements CommandInterface {

  protected $data;

  /**
   * Constructs a \Drupal\views\Ajax\ReplaceTitleCommand object.
   *
   * @param string $data
   *   The form to display in the modal.
   */
  public function __construct($data) {
    $this->data = $data;
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    return [
      'command' => 'ResultsCommand',
      'data'    => $this->data,
    ];
  }

}
