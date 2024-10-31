{
/*===========================================================================*/
/* Local Scope.                                                              */
/*===========================================================================*/

  /**
   * @brief   The localize object.
   */
  let loc_obj = pwwh_core_note_edit_obj;

  /**
   * @brief   Edit Textarea prefix.
   */
  let editor_pre = 'editor-edit-';

  /**
   * @brief   Notes container selector.
   */
  let notes_container = '#' + loc_obj.ui.element.box.id;

  /**
   * @brief   Edit buttons selector.
   */
  let edit_btn = notes_container + ' .' + loc_obj.ui.button.edit.class;

  /**
   * @brief     Updates a Note via AJAX.
   *
   * @param[in] int note_id         The note ID
   * @param[in] int post_id         The Post ID
   * @param[in] string content      The new content
   *
   * @return    void.
   */
  let pwwhNoteUpdate = function(note_id, post_id, content) {

    var target = editor_pre + note_id + "\\:" + post_id;

    var data = {
      url: loc_obj.ajax.url,
      type: 'POST',
      dataType: 'json',
      async: true,
      data: {
        action: loc_obj.ajax.action.edit,
        note_id: note_id,
        content: content
      },
      success: function(data) {

        var note = $('#' + target).closest('article');

        if(data['status']) {

          /* Updating note content. */
          $(note).replaceWith(data['data']);

          pwwhNoteAppendRowActions(note_id);
          pwwhNoteDecreaseTextareaCounter();
        }
        else {
          /* Adding Error. */
          pwwhNoteAddErrorLabel(target, data['code']);
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {

        /* Adding Error. */
        pwwhNoteAddErrorLabel(target);
      }
    };
    $.ajax(data);
  }

/*===========================================================================*/
/* Global Scope.                                                             */
/*===========================================================================*/

  /**
   * @brief     Handles the click on the Edit button.
   *
   * @param[in] Object btn          The button object
   *
   * @return    void.
   */
  var pwwhNoteEditButtonHandler = function(btn) {

    var data = $(btn).val().split(':');
    if(data.length == 2) {
      var note_id = data[0];
      var post_id = data[1];

      /* Editor identifier. */
      var editor_id = editor_pre + $(btn).val();
      var esc_editor_id = editor_id.replace(':', "\\:");

      /* Checking if the editor is already active. */
      if($('#' + esc_editor_id).length != 0) {
        return;
      }

      /* The destination of the editor. */
      var target = '#note-' + note_id + ' main';

      /* Storing the target current content. */
      var old_content = $(target).html();
      let old_content_height = $(target).children().first().height();

      /* Creating a new text area and appending it in the right spot. */
      {
        let pattern = /<\s*\/?br\s*[\/]?>/gi;
        let content = $(target + ' .content').html().replace(pattern, '');
        let classes = loc_obj.ui.element.textarea.class + ' edit';
        let hint = loc_obj.ui.element.textarea.placeholder;
        let editor = pwwhNoteCreateTextarea(editor_id, content, classes, hint);

        /* Appending the editor. */
        pwwhNoteIncreaseTextareaCounter();
        $(target).html(editor);

        /* Auto resizing text area. */
        $(esc_editor_id).height(old_content_height);
        $(esc_editor_id).on('input', function () {
          this.style.height = 'auto';
          this.style.height = (this.scrollHeight) + 'px';
        });

        /* Handling the text area key events. */
        $('#' + esc_editor_id).keydown(function(e) {

          let key_code = e.keyCode || e.which;
          if(key_code === 13 && e.shiftKey) {
            /* Enter pressed with Shift Key. */
            e.preventDefault();

            /* Getting the text area content. */
            let new_content = $(this).val();

            /* Adding the note. */
            if(new_content.length) {
              /* Updating the note. */
              pwwhNoteUpdate(note_id, post_id, new_content);
            }
            else {
              pwwhNoteAddErrorLabel(esc_editor_id , 'empty');
            }
            return false;
          }
          else if(key_code === 27) {
            /* Escape pressed. */
            e.preventDefault();

            /* ERestoring the old content. */
            $(target).html(old_content);

            /* Removing the confirm button. */
            $('#' + esc_editor_id + '-submit').parent().remove();

            pwwhNoteDecreaseTextareaCounter();
            return false;
          }
        });

        /* Adding confirm button. */
        {
          /* Creating a button and appending it. */
          var button = pwwhNoteCreateButton(editor_id + '-submit',
                                            loc_obj.ui.button.confirm.class,
                                            loc_obj.ui.button.confirm.label,
                                            editor_id);

          let confirm = '<span class="button-container align-right">' +
                          button +
                        '</span>';
          $(confirm).appendTo('#note-' + note_id + ' footer');

          $('#' + esc_editor_id + '-submit').on('click', function(e) {

            /* Enter pressed with Shift Key. */
            e.preventDefault();

            /* Getting the text area content. */
            let editor_id = $(this).val();
            let esc_editor_id = editor_id.replace(':', "\\:");
            var new_content = $('#' + esc_editor_id).val();

            /* Adding the note. */
            if(new_content.length) {
              /* Updating the note. */
              pwwhNoteUpdate(note_id, post_id, new_content);
            }
            else {
              pwwhNoteAddErrorLabel(textarea_id , 'empty');
            }
            return false;
          });
        }
      }
    }
  }

/*===========================================================================*/
/* Application entry point.                                                  */
/*===========================================================================*/

  jQuery(document).ready(function($) {

    /* Event edit-button click. */
    $(edit_btn).click(function(event) {

      pwwhNoteEditButtonHandler(this);
    });
  });
}