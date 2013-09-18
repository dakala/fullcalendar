<?php

/**
 * @file
 * Contains \Drupal\fullcalendar\Form\SettingsForm.
 */

namespace Drupal\fullcalendar\Form;

use Drupal\Core\Form\ConfigFormBase;

/**
 * @todo.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'fullcalendar_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    $config = $this->config('fullcalendar.settings');

    $form['path'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Path to FullCalendar'),
      '#default_value' => $config->get('path'),
      '#description' => $this->t('Enter the path relative to Drupal root where the FullCalendar plugin directory is located.'),
    );
    $form['compression'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Choose FullCalendar compression level'),
      '#options' => array(
        'min' => $this->t('Production (Minified)'),
        'none' => $this->t('Development (Uncompressed code)'),
      ),
      '#default_value' => $config->get('compression'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    $this->config('fullcalendar.settings')
      ->set('path', rtrim($form_state['values']['path'], '/'))
      ->set('compression', $form_state['values']['compression'])
      ->save();

    parent::submitForm($form, $form_state);
  }

}
