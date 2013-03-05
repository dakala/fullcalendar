<?php

/**
 * @file
 * Contains \Drupal\fullcalendar\Form\SettingsForm.
 */

namespace Drupal\fullcalendar\Form;

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
   * Implements \Drupal\Core\Form\FormInterface::getFormID().
   */
  public function getFormID() {
    return 'fullcalendar_admin_settings';
  }

  /**
   * Creates a new instance of this form.
   */
  public function getForm() {
    return drupal_get_form($this);
  }

  /**
   * Overrides \Drupal\system\SystemConfigFormBase::buildForm().
   */
  public function buildForm(array $form, array &$form_state) {
    $config = $this->configFactory->get('fullcalendar.settings');

    $form['path'] = array(
      '#type' => 'textfield',
      '#title' => t('Path to FullCalendar'),
      '#default_value' => $config->get('path'),
      '#description' => t('Enter the path relative to Drupal root where the FullCalendar plugin directory is located.'),
    );
    $form['compression'] = array(
      '#type' => 'radios',
      '#title' => t('Choose FullCalendar compression level'),
      '#options' => array(
        'min' => t('Production (Minified)'),
        'none' => t('Development (Uncompressed code)'),
      ),
      '#default_value' => $config->get('compression'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * Overrides \Drupal\system\SystemConfigFormBase::submitForm().
   */
  public function submitForm(array &$form, array &$form_state) {
    $this->configFactory->get('fullcalendar.settings')
      ->set('path', rtrim($form['fullcalendar_path']['#value'], "/"))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
