<?php
namespace Drupal\test_custom\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides route responses for the test custom module.
 */
class RedirectController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function redirectForm() {
    $url = Url::fromRoute('test_custom.form_handle')->toString();

    return new RedirectResponse($url);
  }

}
