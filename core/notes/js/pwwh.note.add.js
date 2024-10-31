{
/*===========================================================================*/
/* Local Scope.                                                              */
/*===========================================================================*/

  /**
   * @brief   The localize object.
   */
  let loc_obj = pwwh_core_note_add_obj;

  /**
   * @brief   Editor identifier.
   */
  let editor_pre = 'editor-add-';

  /**
   * @brief   Notes container selector.
   */
  let notes_container = '#' + loc_obj.ui.element.box.id;

  /**
   * @brief   Notes main container selector.
   */
  let notes_main = notes_container + ' #' + loc_obj.ui.element.main.id;

  /**
   * @brief   Add button selector.
   */
  let add_btn = notes_container + ' #' + loc_obj.ui.button.add.id;

  /**
   * @brief     Restores the footer.
   *
   * @return    void.
   */
  let pwwhrestoreAddButton = function() {

    /* Restoring original button. */
    $(add_btn).unbind();
    $(add_btn).find('span').html(loc_obj.ui.button.add.label);
    $(add_btn).click(function(event) {

      /* Launching the handler. */
      pwwhAddButtonHandler(this);
    });
  }

  /**
   * @brief     Adds a Note to a post via AJAX.
   *
   * @param[in] int post_id         The Post ID
   * @param[in] string content      The new content
   *
   * @return    void.
   */
  let pwwhNoteAdd = function(post_id, content) {

    var target = editor_pre + "0\\:" + post_id;

    var data = {
      url: loc_obj.ajax.url,
      type: 'POST',
      dataType: 'json',
      async: true,
      data: {
        action: loc_obj.ajax.action.add,
        post_id: post_id,
        content: content
      },
      success: function(data) {
        if(data['status']) {

          /* Appending comment to the list. */
          let container = $('#' + target).closest('li');
          container.html(data['data']);
          pwwhNoteAppendRowActions(data['note_id']);
          pwwhNoteDecreaseTextareaCounter();

          /* Restoring original button. */
          pwwhrestoreAddButton();
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
   * @brief     Handles the click on the Add button.
   *
   * @param[in] Object btn          The button object
   *
   * @return    void.
   */
  var pwwhAddButtonHandler = function(btn) {

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

      /* Controlling if the target container already has an ul. */
      var main_list = $(notes_main).find('ul').first();
      if(main_list.length == 0) {
        main_list = $('<ul></ul>');
        main_list.attr('id', loc_obj.ui.element.list.id);
        main_list.attr('class', loc_obj.ui.element.list.class);
        main_list.appendTo(notes_main);
      }

      /* Creating a note/editor wrapper and placing it in the right spot. */
      {
        var listelem = $('<li></li>');
        let classes = loc_obj.ui.element.listelem.class
        classes = classes.replace(/%d/g, '1');
        listelem.attr('class', classes);
        listelem.appendTo(main_list);
      }

      /* Creating a new text area and appending it in the right spot. */
      {
        let classes = loc_obj.ui.element.textarea.class;
        let hint = loc_obj.ui.element.textarea.placeholder;
        let editor = pwwhNoteCreateTextarea(editor_id, '', classes, hint);

        /* Appending the editor. */
        pwwhNoteIncreaseTextareaCounter();
        $(editor).hide().appendTo(listelem).slideDown(200);

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
              /* Adding the note. */
              pwwhNoteAdd(post_id, new_content);
            }
            else {
              /* The text area is empty, applying an error label. */
              pwwhNoteAddErrorLabel(esc_editor_id , 'empty');
            }
            return false;
          }
          else if(key_code === 27) {
            /* Escape pressed. */
            e.preventDefault();

            /* Removing element from the list. If this element has no
               siblings killing the parent. */
            if(listelem.siblings().length == 0) {
              listelem.parent().fadeOut(400, function(){$(this).remove();});
            }
            else {
              listelem.fadeOut(400, function(){$(this).remove();});
            }

            /* Decreasing the Textarea counter. */
            pwwhNoteDecreaseTextareaCounter();

            /* Restoring Add button to its original style and behaviour. */
            pwwhrestoreAddButton();
            return false;
          }
        });
      }

      /* Reworking Add a note button. */
      {

        $(add_btn).unbind();
        $(add_btn).find('span').html(loc_obj.ui.button.confirm.label);
        $(add_btn).on('click', function(e) {

          /* Enter pressed with Shift Key. */
          e.preventDefault();

          /* Getting the text area content. */
          let editor_id = editor_pre + $(this).val().replace(':', "\\:");
          let new_content = $("#" + editor_id).val();

          /* Adding the note. */
          if(new_content.length) {

            /* Adding the note. */
            pwwhNoteAdd(post_id, new_content);
          }
          else {
            pwwhNoteAddErrorLabel('#' + editor_id , 'empty');
          }
          return false;
        });
      }
    }
  }

/*===========================================================================*/
/* Application entry point.                                                  */
/*===========================================================================*/

  jQuery(document).ready(function($) {

    /* Event ad-button click. */
    $(add_btn).click(function(event) {

      /* Launching the handler. */
      pwwhAddButtonHandler(this);
    });
  });
}