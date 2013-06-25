<?php

/**
 * @file
 * Contains \Drupal\fullcalendar_options\Form\SettingsForm.
 */

namespace Drupal\fullcalendar_options\Form;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Config\Context\ContextInterface;
use Drupal\Core\Config\ConfigFactory;
use Drupal\system\SystemConfigFormBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @todo.
 */
class SettingsForm extends SystemConfigFormBase {

  /**
   * An array of Fullcalendar Options available to use.
   *
   * @var array
   */
  protected $options;

  /**
   * Constructs a SettingsForm object.
   */
  public function __construct(ConfigFactory $config_factory, ContextInterface $context, PluginManagerInterface $manager) {
    parent::__construct($config_factory, $context);

    $definition = $manager->getDefinition('fullcalendar_options');
    $this->options = call_user_func(array($definition['class'], 'optionsList'));
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('config.context.free'),
      $container->get('plugin.manager.fullcalendar')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'fullcalendar_options_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    $config = $this->configFactory->get('fullcalendar_options.settings');
    $form['fullcalendar_options'] = array(
      '#type' => 'details',
      '#title' => t('Options'),
      '#description' => t('Each setting can be exposed for all views.'),
    );
    foreach ($this->options as $key => $info) {
      $form['fullcalendar_options'][$key] = array(
        '#type' => 'checkbox',
        '#default_value' => $config->get($key),
      ) + $info;
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    $config = $this->configFactory->get('fullcalendar_options.settings');
    foreach ($this->options as $key => $info) {
      $config->set($key, $form_state['values'][$key]);
    }
    $config->save();

    parent::submitForm($form, $form_state);
  }

}
