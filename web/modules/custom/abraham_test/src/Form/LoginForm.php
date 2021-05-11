<?php

namespace Drupal\abraham_test\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Database\Database;
use Drupal;

/**
 * Our login form class.
 */
class LoginForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'login_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre'),
      '#required' => TRUE,
    ];

    $form['password'] = [
      '#type' => 'password',
      '#title' => $this->t('Contraseña'),
      '#required' => TRUE,
    ];

    $form['actions'] = [
      '#type' => 'button',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * Submit form.
   *
   * Variable $form of type array @param array $form.
   *
   * Variable $form_state @param FormStateInterface $form_state.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}

  /**
   * Validate name and password of the form.
   *
   * Variable $form of type array @param array $form.
   *
   * Variable $form_state @param FormStateInterface $form_state.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Get name and password values.
    $name = $form_state->getValue('name');
    $password = $form_state->getValue('password');

    // Set database connection.
    $con = mysqli_connect('dbserver.dev.a3aa4388-8579-40b7-990a-324bfc12bf6e.drush.in', 'pantheon', 'ae3889ab347c43c19d53e986c410f932', 'pantheon', '13378');
    if (mysqli_connect_errno()) {
      echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
      exit();
    }

    // Get name from myusers table.
    if ($result = mysqli_query($con, "SELECT name FROM myusers WHERE name = '" . $name . "' AND password = '" . $password . "'")) {
      if (!empty($result->num_rows)) {
        // Get ip from myusers table.
        $result = mysqli_query($con, "SELECT id FROM myusers WHERE name = '" . $name . "' AND password = '" . $password . "'");
        if (!empty($result->num_rows)) {
          foreach ($result as $record) {
            $uid = $record['id'];
          }
        }

        // Get date and ip values.
        $date = date('Y-m-d h:i:s');
        $ip = Drupal::request()->getClientIp();

        // Add values to access log table.
        $con = Database::getConnection();
        $con->insert('access_log')->fields(
          [
            'date' => $date,
            'ip' => $ip,
            'uid' => $uid,
            'log_type' => 'login',
          ]
        )->execute();

        // Set id value on logged in table.
        $con->insert('logged_in')->fields(
          [
            'id' => 1,
            'name' => $name,
          ]
        )->execute();
      }
      else {
        // Set an error for invalid name or password.
        $form_state->setErrorByName('', $this->t('Nombre o contraseña invalida.'));
      }
    }
  }

}
