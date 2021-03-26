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
    return [
      '#markup' => 'Hello, page custom test',
    ];
  }

}
