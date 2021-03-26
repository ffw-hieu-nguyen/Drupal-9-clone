<?php

namespace Drupal\test_custom\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Contribute form.
 */
class MyConfig extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'test_custom_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['test_custom.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('test_custom.settings');
    $config_site = \Drupal::config('system.site');
    $form['site_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Site name'),
      '#default_value' => !empty($config->get('site_name')) ? $config->get('site_name') : $config_site->get('name')
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    parent::validateForm($form, $form_state);
    if (!$form_state->getValue('site_name') || strlen($form_state->getValue('site_name')) < 6) {
      $form_state->setErrorByName('site_name', 'Độ dài tên site quá ngắn');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('test_custom.settings')
      ->set('site_name', $form_state->getValue('site_name'))
      ->save();
    $config_site = \Drupal::service('config.factory')->getEditable('system.site');
    $config_site->set('name', $form_state->getValue('site_name'));
    $config_site->save();

    parent::submitForm($form, $form_state);
  }
}
