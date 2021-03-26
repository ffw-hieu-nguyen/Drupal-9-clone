<?php

namespace Drupal\test_custom\Form;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Url;

/**
 * Contribute form.
 */
class HandleForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'handle_form';
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple();
    $list_title ['0'] = t('Select article');
    foreach ($nodes as $node) {
      if($node->type->getValue()[0]['target_id'] == 'article'){
        $list_title [$node->nid->getValue()[0]['value']] = $node->title->getValue()[0]['value'];
      }
    }
    $form['list_article'] = [
      '#type' => 'select',
      '#title' => $this->t('Select article'),
      '#options' => $list_title,
      '#default_value' => !empty($node->list_article->value) ? $node->list_article->value : 0
    ];

    $form['status'] = [
      '#type' => 'select',
      '#title' => $this->t('Select status'),
      '#options' => [
        '' => $this->t('Select status'),
        '0' => $this->t('UnPublished'),
        '1' => $this->t('Published'),
      ],
      '#default_value' => !empty($node->status->value) ? $node->status->value : ''
    ];
    $form['sticky'] = [
      '#type' => 'select',
      '#title' => $this->t('Select sticky'),
      '#options' => [
        '' => $this->t('Select sticky'),
        '0' => $this->t('UnSticky'),
        '1' => $this->t('Sticky'),
      ],
      '#default_value' => !empty($node->sticky->value) ? $node->sticky->value : ''
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update'),
    ];
    $form['actions']['delete'] = array(
      '#type' => 'submit',
      '#executes_submit_callback' => TRUE,
      '#submit' => array([$this, 'delete_entity_from_node']),
      '#value' => $this->t('Delete'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

  public function delete_entity_from_node(&$form, &$form_state) {
    $nid =  $form_state->getValue('list_article')[0]['value'];
    if(!empty($nid)){
      $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
      $storage_handlers = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(array('nid' => $nid));
      foreach ($storage_handlers as $storage_handler) {
        $storage_handler->delete();
      }
      $this->messenger()->addMessage($this->t('You delete article a successfully'));
    }
    $form_state->setRebuild();
  }
}


