<?php
namespace Drupal\test_custom\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the test custom module.
 */
class CustomPageController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function myPage() {
    $render = [];
    $render['element1'] = [
      '#type' => 'markup',
      '#markup' => t('Hello, page custom test'),
    ];
    $render['element2'] = \Drupal::formBuilder()->getForm('Drupal\test_custom\Form\MyConfig');
    return $render;
  }

}
