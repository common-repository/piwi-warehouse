{
/*===========================================================================*/
/* Local Scope.                                                              */
/*===========================================================================*/

/*===========================================================================*/
/* Global Scope.                                                             */
/*===========================================================================*/

  /**
   * @brief     Creates a textarea according to the parameter.
   *
   * @param[in] string id         The area ID
   * @param[in] string content    The area content
   * @param[in] string classes    The area classes
   * @param[in] string hint       The area placeholder
   *
   * @return    void.
   */
  var pwwhNoteCreateTextarea = function(id, content = '', classes = '',
                                        hint = '') {
    let extra = '';
    if (classes) {
      extra += ' class="' + classes + '"';
    }
    if (hint) {
      extra += ' placeholder="' + hint + '"';
    }

    var output = '<textarea id="' + id + '"' + extra + '>' +
                    content +
                 '</textarea>';
    return output;
  }

  /**
   * @brief     Creates a button according to the parameter.
   *
   * @param[in] string id         The button ID
   * @param[in] string classes    The button classes
   * @param[in] string label      The button label
   * @param[in] string value      The button value

   * @return    string The button.
   */
  var pwwhNoteCreateButton = function(id, classes, label, value) {

    let attr = 'type="button" id="' + id + '" name="' + id + '"';
    if (classes) {
      attr += ' class="hide-if-no-js has-label pwwh-lib-button ' + classes + '"';
    }
    else {
      attr += ' class="hide-if-no-js has-label pwwh-lib-button"';
    }

    if (value) {
      attr += ' value="' + value + '"';
    }

    let button = '<button ' + attr + '>'+
                   '<span class="pwwh-lib-label">' + label +'</span>' +
                 '</button>';

    return button;
  }

  /**
   * @brief     Adds an error Label to a specific target.
   * @details   The label will be added after the target.
   *
   * @param[in] string target     The ID of a box to be marked as not valid
   * @param[in] string error_code TA string representing the error code
   *
   * @return    void.
   */
  var pwwhNoteAddErrorLabel = function(target, error_code = 'generic') {

    var loc_obj = pwwh_core_note_common_obj;

    if($('#' + target).length) {

      /* Composing error data. */
      label_id = target + '-error';
      error_msg = loc_obj.ui.msg.error[error_code];
      error_class = loc_obj.ui.error_class;

      if($('#' + label_id).length == 0) {
        /* Adding error label and textarea error class. */
        var error_label = '<label id="' + label_id + '" ' +
                                 'class="' + error_class + '"' +
                                 'for="' + target + '">' +
                            error_msg +
                          '</label>';

        $('#' + target).after(error_label);
        $('#' + target).addClass(error_class);
      }
      else {
        /* Replacing error text. */
        $('#' + label_id).html(error_msg);
      }
    }
  }

  /**
   * @brief     Textarea Counter. This variable keeps track of how many textareas
   *            are currently active
   */
  var PWWH_TEXTAREA_COUNTER = 0;

  /**
   * @brief     Increases the textarea counter.
   * @details   The textarea counter keeps track of how many textarea are
   *            currently active. When there are active textarea leaving
   *            is prevented.
   *
   * @return    void.
   */
  var pwwhNoteIncreaseTextareaCounter = function() {

    PWWH_TEXTAREA_COUNTER++;

    /* There are active textareas. Preventing leaving the page. */
    if(PWWH_TEXTAREA_COUNTER > 0) {
      $(window).on("beforeunload",function(event) {
        console.log(PWWH_TEXTAREA_COUNTER)
        return true;
      });

      /* On Update the beforeunload is skipped. */
      $('#publishing-action #publish').on("click",function(event) {

        $(window).off("beforeunload");
      });
    }
  }

  /**
   * @brief     Increases the textarea counter.
   * @details   The textarea counter keeps track of how many textarea are
   *            currently active. When there are active textarea leaving
   *            is prevented.
   *
   * @return void
   */
  var pwwhNoteDecreaseTextareaCounter = function() {

    PWWH_TEXTAREA_COUNTER--;

    /* There are no active textareas. Allowing to leave the page. */
    if(PWWH_TEXTAREA_COUNTER == 0) {
      $(window).off("beforeunload");
    }
  }

  /**
   * @brief     Appends the row action to a note.
   *
   * @param[in] int note_id         The note ID
   *
   * @return void
   */
  var pwwhNoteAppendRowActions = function(note_id) {

    var loc_obj = pwwh_core_note_common_obj;

    var row_action_container = '#note-' + note_id + ' footer';

    /* Event reply-button click. */
    let btn = row_action_container + ' .' +
              loc_obj.ui.button.reply.class;
    $(btn).click(function(event) {

      /* Launching the handler. */
      pwwhNoteReplyButtonHandler(this);
    });

    /* Event edit-button click. */
    btn = row_action_container + ' .' +
          loc_obj.ui.button.edit.class;
    $(btn).click(function(event) {

      pwwhNoteEditButtonHandler(this);
    });

    /* Event delete-button click. */
    btn = row_action_container + ' .' +
          loc_obj.ui.button.delete.class;
    $(btn).click(function(event) {

      /* Launching the handler. */
      pwwhNoteDeleteButtonHandler(this);
    });
  }

/*===========================================================================*/
/* Application entry point.                                                  */
/*===========================================================================*/

  jQuery(document).ready(function($) {

  });
}