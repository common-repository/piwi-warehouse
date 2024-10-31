{
/*===========================================================================*/
/* Local Scope.                                                              */
/*===========================================================================*/
  /**
   * @brief   The localize object.
   */
  let loc_obj = pwwh_core_note_reply_obj;

  /**
   * @brief   Editor identifier.
   */
  let editor_pre = 'editor-reply-';

  /**
   * @brief   Notes container selector.
   */
  let notes_container = '#' + loc_obj.ui.element.box.id;

  /**
   * @brief   Add button selector.
   */
  let reply_btn = notes_container + ' .' + loc_obj.ui.button.reply.class;

  /**
   * @brief     Adds a reply to a Note via AJAX.
   *
   * @param[in] int post_id         The Post ID
   * @param[in] int parent_id       The ID of the partent note
   * @param[in] string content      The content
   *
   * @return    void.
   */
  let pwwhNoteReply = function(post_id, parent_id, content) {

    var target = editor_pre + parent_id + "\\:" + post_id;

    var data = {
      url: loc_obj.ajax.url,
      type: 'POST',
      dataType: 'json',
      async: true,
      data: {
        action: loc_obj.ajax.action.reply,
        post_id: post_id,
        content: content,
        parent_id: parent_id
      },
      success: function(data) {
        if(data['status']) {

          let listelem = $('#' + target).closest('li');
          let parent_article = $(listelem).closest('ul').siblings('article').first();

          /* Appending comment to the list. */
          listelem.html(data['data']);
          pwwhNoteAppendRowActions(data['note_id']);

          /* Updating the parent. */
          let note_id = parent_article.attr('id').replace('note-', '');
          parent_article.replaceWith(data['parent']);
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
   * @brief     Handles the click on the Reply button.
   *
   * @param[in] Object btn          The button object
   *
   * @return    void.
   */
  var pwwhNoteReplyButtonHandler = function(btn) {

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

      var parent = '#note-' + note_id;
      var pattern = /depth-([0-9]*)/i;
      var parent_depth = parseInt($(parent).attr('class').match(pattern)[1]);

      /* Checkinh if is possible to nest the reply.*/
      if(parent_depth < parseInt(loc_obj.ui.max_depth)) {

        /* Computing the next depth. */
        var new_depth = parent_depth + 1;

        /* Controlling if the note already has children. */
        var sub_list = $(parent).siblings('ul').first();

        if(sub_list.length == 0) {
          /* Computing the next depth. */
          var new_depth = parent_depth + 1;
          sub_list = $('<ul></ul>');
          let classes = loc_obj.ui.element.sublist.class;
          classes = classes.replace(/%d/g, new_depth);
          sub_list.attr('class', classes);
          sub_list.insertAfter(parent);
        }
      }
      else {
        /* Computing the next depth. */
        var new_depth = parent_depth;

        var sub_list = $(parent).closest('ul');
        var new_parent = $(sub_list).siblings('article').first();

        /* Recomputing the new parent id and editor id. */
        note_id = new_parent.attr('id').replace('note-', '');
        editor_id = editor_pre + note_id + ':' + post_id;
        esc_editor_id = editor_id.replace(':', "\\:");
      }

      /* Creating a note/editor wrapper and placing it in the right spot. */
      {
        var listelem = $('<li></li>');
        let classes = loc_obj.ui.element.listelem.class;
        classes = classes.replace(/%d/g, new_depth);
        listelem.attr('class', classes);
        listelem.appendTo(sub_list);
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
              pwwhNoteReply(post_id, note_id, new_content);
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

            return false;
          }
        });
      }
    }
  }

/*===========================================================================*/
/* Application entry point.                                                  */
/*===========================================================================*/

  /**
   * @brief     Application entry point.
   */
  jQuery(document).ready(function($) {

    /* Event reply-button click. */
    $(reply_btn).click(function(event) {

      /* Launching the handler. */
      pwwhNoteReplyButtonHandler(this);
    });
  });
}