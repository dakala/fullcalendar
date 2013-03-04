<?php

/**
 * @file
 * Contains \Drupal\fullcalendar_options\Form\SettingsForm.
 */

namespace Drupal\fullcalendar_options\Form;

use Drupal\system\SystemConfigFormBase;
use Drupal\Core\Config\ConfigFactory;

/**
 * @todo.
 */
class SettingsForm extends SystemConfigFormBase {

  /**
   * Stores the configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * @todo.
   */
  public function __construct(ConfigFactory $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * @todo.
   */
  public function getFormID() {
    return 'fullcalendar_options_admin_settings';
  }

  /**
   * @todo.
   */
  public function getForm() {
    return drupal_get_form($this);
  }

  /**
   * @todo.
   */
  public function buildForm(array $form, array &$form_state) {
    $config = $this->configFactory->get('fullcalendar_options.settings');
    $form['fullcalendar_options'] = array(
      '#type' => 'details',
      '#title' => t('Options'),
      '#description' => t('Each setting can be exposed for all views.'),
    );
    foreach (_fullcalendar_options_list(TRUE) as $key => $info) {
      $form['fullcalendar_options'][$key] = array(
        '#type' => 'checkbox',
        '#default_value' => $config->get($key),
      ) + $info;
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * @todo.
   */
  public function submitForm(array &$form, array &$form_state) {
    $config = $this->configFactory->get('fullcalendar_options.settings');
    foreach (_fullcalendar_options_list(TRUE) as $key => $info) {
      $config->set($key, $form_state['values'][$key]);
    }
    $config->save();

    parent::submitForm($form, $form_state);
  }

}
