<?php

namespace Drupal\test_custom\Form;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
      '#title' => $this->t('List Article'),
      '#options' => $list_title,
      '#default_value' => '0',
      '#ajax' => [
      'callback' => '::handleField',
      'event' => 'change',
      'wrapper' => 'edit-output',
      'progress' => [
        'type' => 'throbber',
        'message' => $this->t('Loading value...'),
      ],
    ]
    ];
    $form['container_one'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['container'],
      ],
      '#prefix' => '<div id="edit-output">',
      '#suffix' => '</div>',
    ];
    $form['container_one']['status'] = [
      '#type' => 'select',
      '#title' => $this->t('Status of article'),
      '#options' => [
        ' ' => $this->t('Select status'),
        '0' => $this->t('UnPublished'),
        '1' => $this->t('Published'),
      ],
      '#default_value' => ' ',
    ];
    $form['container_one']['sticky'] = [
      '#type' => 'select',
      '#title' => $this->t('Sticky of article'),
      '#options' => [
        ' ' => $this->t('Select sticky'),
        '0' => $this->t('UnSticky'),
        '1' => $this->t('Sticky'),
      ],
      '#default_value' => ' ',
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
    $nid = $form_state->getValue('list_article');
    $status = $form_state->getValue('status');
    $sticky = $form_state->getValue('sticky');
    if($nid == '0' || !isset($nid)){
      $form_state->setErrorByName('list_article', $this->t('Dont select article'));
    }
    if($status == ' ' || !isset($nid)){
      $form_state->setErrorByName('status', $this->t('Dont select status'));
    }
    if($sticky == ' ' || !isset($nid)){
      $form_state->setErrorByName('sticky', $this->t('Dont select sticky'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $nid = $form_state->getValue('list_article');
    $status = $form_state->getValue('status');
    $sticky = $form_state->getValue('sticky');
    if($nid != '0' && isset($nid)){
      $update_node = Node::load($nid);
      $update_node->status = $status;
      $update_node->sticky = $sticky;
      $update_node->save();
    }
  }

  public function delete_entity_from_node(&$form, &$form_state) {
    $nid =  $form_state->getValue('list_article');
    if($nid != '0' && isset($nid)){
      $storage_handlers = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(array('nid' => $nid));
      foreach ($storage_handlers as $storage_handler) {
        $storage_handler->delete();
      }
      $this->messenger()->addMessage($this->t('You delete article a successfully'));
    }
    $url = Url::fromRoute('test_custom.form_handle')->toString();
    return new RedirectResponse($url);
  }

  public function handleField(array &$form, FormStateInterface $form_state) {
    $nid =  $form_state->getValue('list_article');
    if($nid != '0' && isset($nid)){
      $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
      $form['container_one']['sticky']['#value'] = $node->sticky->getValue()[0]['value'];
      $form['container_one']['status']['#value'] = $node->status->getValue()[0]['value'];
    }else{
      $form['container_one']['sticky']['#value'] = ' ';
      $form['container_one']['status']['#value'] = ' ';
    }
    return $form['container_one'];
  }
}


