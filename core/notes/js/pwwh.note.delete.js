{
/*===========================================================================*/
/* Local Scope.                                                              */
/*===========================================================================*/

  /**
   * @brief   The localize object.
   */
  let loc_obj = pwwh_core_note_delete_obj;

  /**
   * @brief     Accumulator containing all the IDs of notes to be deleted.
   */
  let pwwhNoteToBeDeleted = [];

  /**
   * @brief     The lenght of the countdown to the permanent delete.
   */
  let pwwhNoteDeleteCountdownLength = 5;

  /**
   * @brief   Notes container selector.
   */
  let notes_container = '#' + loc_obj.ui.element.box.id;

  /**
   * @brief   Delete buttons selector.
   */
  let delete_btn = notes_container + ' .' + loc_obj.ui.button.delete.class;

  /**
   * @brief     Enqueues a note for delation.
   * @details   This allows to delete notes still undeleted before leaving the
   *            page.
   *
   * @param[in] int note_id         The note ID
   *
   * @return void
   */
  let pwwhNoteEnqueueForDeletion = function(note_id) {

    pwwhNoteToBeDeleted.push(note_id);

      /* Adding an event Listner to delete all the comments before leaving. */
    if(pwwhNoteToBeDeleted.length == 1) {
      $(window).on("beforeunload",function(event) {
        $(pwwhNoteToBeDeleted).each(function(index, elem){
          pwwhNoteDelete(elem);
        });
        return undefined;
      });
    }
  }

  /**
   * @brief     Dequeues a note from deletion queue.
   * @details   This allows to delete notes still undeleted before leaving the
   *            page.
   *
   * @param[in] int note_id         The note ID
   *
   * @return void
   */
  let pwwhNoteDequeueFromDeletion = function(note_id) {

    var index = pwwhNoteToBeDeleted.indexOf(note_id);
    if (index > -1) {
      pwwhNoteToBeDeleted.splice(index, 1);
    }

    /* Disabling event Listner. */
    if(pwwhNoteToBeDeleted.length == 0) {
      $(window).off("beforeunload");
    }
  }

  /**
   * @brief     Creates a the trash notice identifier
   *
   * @param[in] string note_id      The note ID
   *
   * @return string The trash notice identifier
   */
  let pwwhNoteTrashNoticeId = function(note_id) {
    return 'trash-notice-' + note_id
  };

  /**
   * @brief     Generates the undo message.
   * @details   The content will be replaced with the generated undo.
   *
   * @param[in] int note_id         The note ID
   * @param[in] int timeleft        The initial time of timeleft
   * @param[in] string countdown    The class of the countdown container
   * @param[in] string undo         The class of the undo link container
   *
   * @return void
   */
  let pwwhNoteGetDeleteNotice = function(note_id, timeleft, countdown, undo) {

    /* Updating note content. */
    let preundo_msg = (loc_obj.ui.msg.preundo).replace(/%s/g, timeleft);
    let notice_id = pwwhNoteTrashNoticeId(note_id);
    var notice = '<div id="' + notice_id + '" class="trash-notice">' +
                    loc_obj.ui.msg.deleted +
                   '<span class="' + countdown +'">' +
                      preundo_msg +
                    '</span>' +
                   '<a class="' + undo +'" href="#">' +
                      loc_obj.ui.msg.undo +
                    '</a>' +
                 '</div>';
    return notice;
  }

  /**
   * @brief     Trash a Note via AJAX.
   *
   * @param[in] int note_id         The note ID
   *
   * @return    void.
   */
  let pwwhNoteTrash = function(note_id) {

    var target = 'note-' + note_id;

    var data = {
      url: loc_obj.ajax.url,
      type: 'POST',
      dataType: 'json',
      async: true,
      data: {
        action: loc_obj.ajax.action.trash,
        note_id: note_id
      },
      success: function(data) {

        var note_wrapper = $('#note-' + note_id).parent();

        if(data['status']) {

          let timeleft = pwwhNoteDeleteCountdownLength;
          let countdown = 'countdown';
          let undo = 'undo'
          let notice = pwwhNoteGetDeleteNotice(note_id, timeleft, countdown,
                                               undo);

          /* Forcing target size before to replace its content. */
          var height = $(note_wrapper).children().first().height();
          var width = $(note_wrapper).children().first().width();
          $(note_wrapper).addClass('has-undo');
          $(note_wrapper).html(notice);

          /* Adding style transition. */
          $(note_wrapper).children().first().css('min-height', height +'px');
          $(note_wrapper).children().first().css('min-width', width +'px');
          let cfg = {'min-height': "0",
                     'background-color': "#ffeded"};
          $(note_wrapper).children().first().animate(cfg, 500);

          /* Adding this note to the deletion queue. */
          pwwhNoteEnqueueForDeletion(note_id);

          /* Composing the timer. */

          let interval = setInterval(function() {

            timeleft--;

            /* Composing identifier and message. */
            let countdown_container = $(note_wrapper).find('.' + countdown);
            msg = (loc_obj.ui.msg.preundo).replace(/%s/g, timeleft);

            if(timeleft <= 0){
              clearInterval(interval);
              $(countdown_container).html(msg);
              pwwhNoteDelete(note_id);
            }
            else {
              $(countdown_container).html(msg);
            }
          }, 1000);

          let undo_btn = $(note_wrapper).find('.' + undo);
          $(undo_btn).click(function(event) {
            event.preventDefault();

            /* Stops the delete action. */
            clearInterval(interval);

            /* Untrashing the note. */
            pwwhNoteUntrash(note_id, interval);
          });

        }
        else {
          /* Adding Error. */
          pwwhNoteAddErrorLabel(target, data['code']);
        }
      },
      error: function(XMLHttpRequest, note_wrapper, errorThrown) {

        /* Adding Error. */
        pwwhNoteAddErrorLabel(target);
      }
    };
    $.ajax(data);
  }

  /**
   * @brief     Untrash a Note via AJAX.
   *
   * @param[in] int note_id         The note ID
   *
   * @return    void.
   */
  let pwwhNoteUntrash = function(note_id, interval) {

    var target = pwwhNoteTrashNoticeId(note_id);

    var data = {
      url: loc_obj.ajax.url,
      type: 'POST',
      dataType: 'json',
      async: true,
      data: {
        action: loc_obj.ajax.action.untrash,
        note_id: note_id
      },
      success: function(data) {

        /* Computing wrapper. */
        var note_wrapper = $('#' + target).parent();

        if(data['status']) {

          /* Removing element from array of deletions. */
          pwwhNoteDequeueFromDeletion(note_id);

          /* Updating note content. */
          $(note_wrapper).removeClass('has-undo');
          $(note_wrapper).html(data['data']);

          pwwhNoteAppendRowActions(note_id);
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

  /**
   * @brief     Trash a Note via AJAX.
   *
   * @param[in] int note_id         The note ID
   *
   * @return    void.
   */
  let pwwhNoteDelete = function(note_id) {

    var target = pwwhNoteTrashNoticeId(note_id);

    var data = {
      url: loc_obj.ajax.url,
      type: 'POST',
      dataType: 'json',
      async: true,
      data: {
        action: loc_obj.ajax.action.delete,
        note_id: note_id
      },
      success: function(data) {

        let listelem = $('#' + target).closest('li');
        let parent_article = $(listelem).closest('ul').siblings('article').first();

        /* Removing element from the list. If this element has no
           siblings killing the parent. */
        if(listelem.siblings().length == 0) {
          listelem.parent().fadeOut(400, function(){$(this).remove();});
        }
        else {
          listelem.fadeOut(400, function(){$(this).remove();});
        }

        if(parent_article.length) {
          let note_id = parent_article.attr('id').replace('note-', '');
          parent_article.replaceWith(data['parent']);
          pwwhNoteAppendRowActions(note_id);
        }

        /* Removing element from array of deletions. */
        pwwhNoteDequeueFromDeletion(note_id);
      },
      error: function(XMLHttpRequest, note_wrapper, errorThrown) {

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
   * @brief     Handles the click on the delete button.
   *
   * @param[in] Object btn          The button object
   *
   * @return    void.
   */
  var pwwhNoteDeleteButtonHandler = function(btn) {

    var data = $(btn).val().split(':');
    if(data.length == 2) {
      var note_id = data[0];
      var post_id = data[1];

      pwwhNoteTrash(note_id);
    }
  }

  /**
   * @brief     Application entry point.
   */
  jQuery(document).ready(function($) {

    /* Event delete-button click. */
    $(delete_btn).click(function(event) {

      /* Launching the handler. */
      pwwhNoteDeleteButtonHandler(this);
    });
  });
}