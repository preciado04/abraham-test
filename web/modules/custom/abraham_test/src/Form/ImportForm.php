<?php

namespace Drupal\abraham_test\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Drupal\Core\Database\Database;

/**
 * Our import form class.
 */
class ImportForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'import_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $allowed_ext = 'xlsx xls csv';
    $max_upload = 256000000;

    $form = [
      '#attributes' => ['enctype' => 'multipart/form-data'],
    ];

    $form['csv_excel_file'] = [
      '#type' => 'managed_file',
      '#name' => 'csv_excel_file',
      '#title' => t('File'),
      '#size' => 100,
      '#description' => t('Sólo los formatos xlsx y csv están permitidos.'),
      '#upload_validators' => [
        'file_validate_extensions' => [
          $allowed_ext,
        ],
        'file_validate_size' => [
          $max_upload,
        ],
      ],
      '#upload_location' => 'public://content/csv_excel_files',
      '#required' => TRUE,
    ];

    $form['actions']['#type'] = 'actions';

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Importar'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * Validate file.
   *
   * Variable $form of type array @param array $form.
   *
   * Variable $form_state @param FormStateInterface $form_state.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('csv_excel_file') == NULL) {
      $form_state->setErrorByName('csv_excel_file', $this->t('Sube un archivo apropiado.'));
    }
  }

  /**
   * Submit form.
   *
   * Variable $form of type array @param array $form.
   *
   * Variable $form_state @param FormStateInterface $form_state.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // The file id will be stored as an array.
    $file = \Drupal::entityTypeManager()->getStorage('file')->load($form_state->getValue('csv_excel_file')[0]);
    $full_path = $file->get('uri')->value;
    $file_name = basename($full_path);

    try {
      // Read csv or excel values.
      $real_path_and_file_name = \Drupal::service('file_system')->realpath('public://content/csv_excel_files/' . $file_name);
      $spreadsheet = IOFactory::load($real_path_and_file_name);
      $sheet_data = $spreadsheet->getActiveSheet();

      $names = [];
      $count = 0;
      foreach ($sheet_data->getRowIterator() as $row) {
        $cell_iterator = $row->getCellIterator();
        $cell_iterator->setIterateOnlyExistingCells(FALSE);

        $cells = [];
        foreach ($cell_iterator as $cell) {
          if ($count !== 0) {
            // Add values to myusers table.
            $con = Database::getConnection();
            $con->insert('myusers')->fields(
              [
                'name' => $cell->getValue(),
              ]
            )->execute();

            // Create array with all names.
            $cells[] = $cell->getValue();
          }
        }

        // Increment count.
        $count++;
        // Set value to names array.
        $names[] = $cells;
        // Wrap names into a div tag.
        $names_markup = '';
        foreach ($names as $name) {
          $names_markup .= '<div>' . $name[0] . '</div>';
        }

        // Store names in a session variable.
        $_SESSION['names_markup'] = $names_markup;
      }

      // Add id to imported file table.
      $con = Database::getConnection();
      $con->insert('imported_file')->fields(
        [
          'id' => 1,
        ]
      )->execute();

      // Add message.
      \Drupal::messenger()->addMessage('El archivo ha sido importado exitosamente.');

    }
    catch (Exception $e) {
      \Drupal::logger('type')->error($e->getMessage());
    }
  }

}
