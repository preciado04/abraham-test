/**
 * @file
 * This is scripts.js file.
 */

(function ($, Drupal, drupalSettings) {

  Drupal.behaviors.abrahamTest = {
    attach: function (context, settings) {
      $('html', context).once('body').each(function (i, item) {
        $(document).on('keyup', '#edit-name', function (e) {
          // Get name value.
          var name = $('#edit-name').val();

          // Set pattern for regular expresion.
          var exp = /^[a-zA-Z ñÑ]+$/;

          // Show modal.
          if (e.key != 'Enter' && e.key != 'Backspace' && e.key != 'ñ' && e.key != 'Ñ') {
            if (!exp.test(name)) {
              showModal('Solo se aceptan letras.');
            }
          }
        });

        // Initialize data table.
        $('#abraham-test-table').DataTable({
          dom: 'Bfrtip',
          buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
          ]
        });
      });
    }
  };

  /**
   * Function to show a bootstrap modal.
   */
  function showModal(message) {
    var modal = '';

    // Create modal markup.
    modal = '<div class="modal fade show" id="abraham-test-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" style="display: block;">';
    modal += '  <div class="modal-dialog modal-dialog-centered" role="document">';
    modal += '    <div class="modal-content">'
    modal += '      <div class="modal-header">'
    modal += '        <h5 class="modal-title" id="exampleModalLongTitle">Mensaje</h5>'
    modal += '       <button type="button" class="close" data-dismiss="modal" aria-label="Close">';
    modal += '          <span aria-hidden="true">&times;</span>';
    modal += '        </button>';
    modal += '      </div>';
    modal += '      <div class="modal-body">' + message + '</div>';
    modal += '      <div class="modal-footer">';
    modal += '        <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>';
    modal += '      </div>';
    modal += '    </div>';
    modal += '  </div>';
    modal += '</div>';

    // Add modal.
    $('body').append(modal);
  }

  /**
   * Function to close bootstrap modal.
   */
  $(document).on('click', '.modal-footer > button, .modal-header > button', function (e) {
    // Remove modal.
    $('#abraham-test-modal').remove();
  });

})(jQuery, Drupal, drupalSettings);
