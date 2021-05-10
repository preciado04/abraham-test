<?php

namespace Drupal\abraham_test\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Database\Database;
use Drupal;

/**
 * Our register form class.
 */
class RegisterForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'register_form';
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
    $name = $form_state->getValue('name');
    $password = $form_state->getValue('password');

    if (strlen($name) < 5) {
      // Set an error for the form element with a key of 'name'.
      $form_state->setErrorByName('name', $this->t('El título debe ser de al menos 5 caracteres.'));
    }

    // Set database connection.
    $con = mysqli_connect('localhost', 'root', 'admin', 'learning_abraham_test');
    if (mysqli_connect_errno()) {
      echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
      exit();
    }

    // Get name from table myusers.
    if ($result = mysqli_query($con, "SELECT name FROM myusers WHERE name = '" . $name . "'")) {
      if (!empty($result->num_rows)) {
        // Set an error for repeated name.
        $form_state->setErrorByName('name', $this->t('Este nombre de usuario ya existe. Por favor elija otro.'));
      }
      else {
        // Get ip from myusers table.
        $result = mysqli_query($con, 'SELECT MAX(id) + 1 AS id FROM myusers');
        if (!empty($result->num_rows)) {
          foreach ($result as $record) {
            $uid = $record['id'];
          }
        }

        // Insert user on myusers table.
        $con = Database::getConnection();
        $con->insert('myusers')->fields(
          [
            'name' => $name,
            'password' => $password,
          ]
        )->execute();

        // Insert id value on registered table.
        $con->insert('registered')->fields(
          [
            'id' => 1,
          ]
        )->execute();

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
            'log_type' => 'registro',
          ]
        )->execute();
      }
    }

    mysqli_close($con);
  }

}
