<?php

namespace Drupal\abraham_test\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Defines ConsultationController class.
 */
class ConsultationController extends ControllerBase {

  /**
   * Display the markup.
   *
   * @return array
   *   Return markup array.
   */
  public function content() {
    $markup = '';

    $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    // Get current url.
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    // Create markup.
    if (strpos($url, 'excel') !== FALSE) {
      $markup .= '<h1 class="title">For favor haga click en el botón Excel para exportar los usuarios en un archivo de excel.</h1>';
    }

    // Add html table on markup.
    $markup .= '<table id="abraham-test-table" class="display nowrap" style="width:100%">';
    $markup .= '  <thead>';
    $markup .= '    <tr>';
    $markup .= '      <th>Nombre</th>';
    $markup .= '    </tr>';
    $markup .= '  </thead>';
    $markup .= '  <tbody>';

    // Get each name from myusers table.
    $query = \Drupal::database()->select('myusers', 'm');
    $query->addField('m', 'name');
    $result = $query->execute();
    foreach ($result as $record) {
      $markup .= '<tr><td>' . $record->name . '</td></tr>';
    }

    $markup .= '  </tbody>';
    $markup .= '  <tfoot>';
    $markup .= '    <tr>';
    $markup .= '      <th>Nombre</th>';
    $markup .= '    </tr>';
    $markup .= '  </tfoot>';
    $markup .= '</table>';

    return [
      '#type' => 'markup',
      '#markup' => $markup,
    ];
  }

}
