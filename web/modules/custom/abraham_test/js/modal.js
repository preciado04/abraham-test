/**
 * @file
 * This is modal.js file.
 */

(function ($, Drupal, drupalSettings) {

  Drupal.behaviors.abrahamTest = {
    attach: function (context, settings) {
      console.log(drupalSettings.abraham_test.names);
      $('html', context).once('body').each(function (i, item) {
        // Validate if exists error message.
        if (!$('.messages__wrapper').length) {
          var message = '';
          // Check if url is "usuario/login".
          if (window.location.toString().includes('login')) {
            message = 'Ha iniciado sesión exitosamente.'
            var page = 'login';
            // Get name value.
            var name = $('#edit-name').val();
            // Get login markup.
            login_markup = $('a#main-content[tabindex="-1"]').next().prop('outerHTML');
            // Remove region-content wrapper.
            $('a#main-content[tabindex="-1"]').next().remove();
            // Create logged in markup.
            var logged_in_markup = '<div class="logged-in-wrapper"><div class="user">' + name + '</div><a href="#" class="close-section">Cerrar sesión</a></div>';
            // Add logged in markup.
            $('main#content .section').append(logged_in_markup);
          }
          else if (window.location.toString().includes('registro')) {
            message = 'Usuario registrado exitosamente.';
            var page = 'registro';
          }
          else if (window.location.toString().includes('importar')) {
            var names = drupalSettings.abraham_test.names;
            message = '<h2>Los datos del archivo han sido importados a la base de datos de manera exitosa.</h2><h3>Datos importados:</h3>';
            message += names;
            var page = 'importar';
          }

          // Get user id.
          var uid = drupalSettings.abraham_test.uid;

          showModal(message, uid, page);
        }

        /**
         * Function to close section.
         */
        $(document).on('click', 'a.close-section', function (e) {
          e.preventDefault();

          // Hide logged in wrapper.
          $('.logged-in-wrapper').remove();

          // Add login markup.
          $('main#content .section').append(login_markup);
        });
      });
    }
  };

  /**
   * Function to show a bootstrap modal.
   */
  function showModal(message, uid, page) {
    var modal = '';

    // Add value to body variable.
    if (page == 'importar') {
      var body = message;
    }
    else {
      var body = '<h2>' + message + '<br><br>ID del usuario: ' + uid + '</h2>';
    }

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
    modal += '      <div class="modal-body"><h2>' + body + '</h2></div>';
    modal += '      <div class="modal-footer">';
    modal += '        <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>';
    modal += '      </div>';
    modal += '    </div>';
    modal += '  </div>';
    modal += '</div>';

    // Add modal.
    $('body').append(modal);
  }

})(jQuery, Drupal, drupalSettings);
