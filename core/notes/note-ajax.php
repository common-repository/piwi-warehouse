<?php
/**
 * @file      notes/notes-ajax.php
 * @brief     Ajax related to Notes.
 *
 * @addtogroup PWWH_CORE_NOTE
 * @{
 */

/*===========================================================================*/
/* Common AJAX.                                                              */
/*===========================================================================*/

/**
 * @brief     Common Note script ID.
 */
define('PWWH_CORE_NOTE_COMMON_JS', 'pwwh_core_note_common_js');

/**
 * @brief     Enqueues a the script that handles the notes.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_note_common() {

  /* Enqueuing the script. */
  $id = PWWH_CORE_NOTE_COMMON_JS;
  $url = PWWH_CORE_NOTE_URL . '/js/pwwh.note.common.js';
  $deps = array(PWWH_CORE_JS);
  $ver = '20201127';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Localizing the script. */

  $data = array('ui' => pwwh_core_note_api_get_ui_facts());
  wp_localize_script($id, 'pwwh_core_note_common_obj', $data);
}
add_action('admin_enqueue_scripts', 'pwwh_core_note_common');

/*===========================================================================*/
/* Add Note AJAX.                                                            */
/*===========================================================================*/

/**
 * @brief     Common Note script ID.
 */
define('PWWH_CORE_NOTE_ADD_JS', 'pwwh_core_note_add_js');

/**
 * @brief     Ajax action triggered on note edit.
 */
define('PWWH_CORE_NOTE_ACTION_ADD', PWWH_CORE_NOTE . '_add');

/**
 * @brief     Enqueues a the script that handles the add a notes.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_note_manage_add() {

  /* Enqueuing the script. */
  $id = PWWH_CORE_NOTE_ADD_JS;
  $url = PWWH_CORE_NOTE_URL . '/js/pwwh.note.add.js';
  $deps = array(PWWH_CORE_NOTE_COMMON_JS);
  $ver = '20201126';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Localizing the script. */
  $actions = array('add' => PWWH_CORE_NOTE_ACTION_ADD);
  $data = array('ajax' => array('url' => admin_url('admin-ajax.php'),
                                'action' => $actions),
                'ui' => pwwh_core_note_api_get_ui_facts());
  wp_localize_script($id, 'pwwh_core_note_add_obj', $data);
}
add_action('admin_enqueue_scripts', 'pwwh_core_note_manage_add');

/**
 * @brief     Add note action handler.
 *
 * @hooked    wp_ajax_[PWWH_CORE_NOTE_ACTION_ADD]
 *
 * @return    void
 */
function pwwh_core_note_add_handler() {

  $post_id = sanitize_text_field($_POST['post_id']);

  /* Checking user capabilities. */
  if(pwwh_core_caps_api_current_user_can(PWWH_CORE_NOTE_CAPS_ADD_NOTES)) {

    /* Sanitizing input as HTML. */
    $allowed = wp_kses_allowed_html();
    $content = wp_kses($_POST['content'], $allowed);

    if($content) {
      /* Pushing the note. */
      $note_id = pwwh_core_note_api_insert($content, $post_id, 0);

      if($note_id) {
        /* Add ok. Sending back the new note. */
        $_response = array('status' => 1,
                           'data' => pwwh_core_note_api_get($note_id, 'HTML'),
                           'note_id' => $note_id);
      }
      else {
        /* Error while updating the note. */
        $_response = array('status' => 0,
                           'code' => 'generic');
      }
    }
    else {
      /* Error while updating the note. */
      $_response = array('status' => 0,
                         'code' => 'generic');
    }
  }
  else {
    /* The user has no rights to edit this note. */
    $_response = array('status' => 0,
                       'code' => 'add_cap');
  }

  echo json_encode($_response);

  /* All ajax handlers should die when finished */
  wp_die();
}
add_action('wp_ajax_' . PWWH_CORE_NOTE_ACTION_ADD,
           'pwwh_core_note_add_handler');

/*===========================================================================*/
/* Reply to a Note AJAX.                                                     */
/*===========================================================================*/

/**
 * @brief     Common Note script ID.
 */
define('PWWH_CORE_NOTE_REPLY_JS', 'pwwh_core_note_reply_js');

/**
 * @brief     Ajax action triggered on note edit.
 */
define('PWWH_CORE_NOTE_ACTION_REPLY', PWWH_CORE_NOTE . '_reply');

/**
 * @brief     Enqueues a the script that handles the add a notes.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_note_manage_reply() {

  /* Enqueuing the script. */
  $id = PWWH_CORE_NOTE_REPLY_JS;
  $url = PWWH_CORE_NOTE_URL . '/js/pwwh.note.reply.js';
  $deps = array(PWWH_CORE_NOTE_COMMON_JS);
  $ver = '20201126';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Localizing the script. */
  $actions = array('reply' => PWWH_CORE_NOTE_ACTION_REPLY);
  $data = array('ajax' => array('url' => admin_url('admin-ajax.php'),
                                'action' => $actions),
                'ui' => pwwh_core_note_api_get_ui_facts());
  wp_localize_script($id, 'pwwh_core_note_reply_obj', $data);
}
add_action('admin_enqueue_scripts', 'pwwh_core_note_manage_reply');

/**
 * @brief     Add note action handler.
 *
 * @hooked    wp_ajax_[PWWH_CORE_NOTE_ACTION_REPLY]
 *
 * @return    void
 */
function pwwh_core_note_reply_handler() {

  $post_id = sanitize_text_field($_POST['post_id']);
  $parent_id = sanitize_text_field($_POST['parent_id']);

  /* Checking user capabilities. */
  if(pwwh_core_caps_api_current_user_can(PWWH_CORE_NOTE_CAPS_ADD_NOTES)) {

    /* Sanitizing input as HTML. */
    $allowed = wp_kses_allowed_html();
    $content = wp_kses($_POST['content'], $allowed);

    if($content) {
      /* Pushing the note. */
      $note_id = pwwh_core_note_api_insert($content, $post_id, $parent_id);

      if($note_id) {
        /* Add ok. Sending back the new note. */
        $_response = array('status' => 1,
                           'data' => pwwh_core_note_api_get($note_id, 'HTML'),
                           'parent' => pwwh_core_note_api_get($parent_id,
                                                              'HTML'),
                           'note_id' => $note_id);
      }
      else {
        /* Error while updating the note. */
        $_response = array('status' => 0,
                           'code' => 'generic');
      }
    }
    else {
      /* Error while updating the note. */
      $_response = array('status' => 0,
                         'code' => 'generic');
    }
  }
  else {
    /* The user has no rights to edit this note. */
    $_response = array('status' => 0,
                       'code' => 'add_cap');
  }

  echo json_encode($_response);

  /* All ajax handlers should die when finished */
  wp_die();
}
add_action('wp_ajax_' . PWWH_CORE_NOTE_ACTION_REPLY,
           'pwwh_core_note_reply_handler');

/*===========================================================================*/
/* Edit Note AJAX.                                                           */
/*===========================================================================*/

/**
 * @brief     Common Note script ID.
 */
define('PWWH_CORE_NOTE_EDIT_JS', 'pwwh_core_note_edit_js');

/**
 * @brief     Ajax action triggered on note edit.
 */
define('PWWH_CORE_NOTE_ACTION_EDIT', PWWH_CORE_NOTE . '_edit');

/**
 * @brief     Enqueues a the script that handles the add a notes.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_note_manage_edit() {

  /* Enqueuing the script. */
  $id = PWWH_CORE_NOTE_EDIT_JS;
  $url = PWWH_CORE_NOTE_URL . '/js/pwwh.note.edit.js';
  $deps = array(PWWH_CORE_NOTE_COMMON_JS);
  $ver = '20201126';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Localizing the script. */
  $actions = array('edit' => PWWH_CORE_NOTE_ACTION_EDIT);
  $data = array('ajax' => array('url' => admin_url('admin-ajax.php'),
                                'action' => $actions),
                'ui' => pwwh_core_note_api_get_ui_facts());
  wp_localize_script($id, 'pwwh_core_note_edit_obj', $data);
}
add_action('admin_enqueue_scripts', 'pwwh_core_note_manage_edit');

/**
 * @brief     Edit note action handler.
 *
 * @hooked    wp_ajax_[PWWH_CORE_NOTE_ACTION_EDIT]
 *
 * @return    void
 */
function pwwh_core_note_edit_handler() {

  $note_id = sanitize_text_field($_POST['note_id']);

  /* Checking user capabilities. */
  if(pwwh_core_note_api_current_user_can_edit($note_id)) {

    /* Sanitizing input as HTML. */
    $allowed = wp_kses_allowed_html();
    $content = wp_kses($_POST['content'], $allowed);

    if($content) {
      if(pwwh_core_note_api_edit($note_id, $content)) {
        /* Edit ok. Sending back the new note. */
        $_response = array('status' => 1,
                           'data' => pwwh_core_note_api_get($note_id, 'HTML'));
      }
      else {
        /* Error while updating the note. */
        $_response = array('status' => 0,
                           'code' => 'generic');
      }
    }
    else {
      /* Error while updating the note. */
      $_response = array('status' => 0,
                         'code' => 'generic');
    }
  }
  else {
    /* The user has no rights to edit this note. */
    $_response = array('status' => 0,
                       'code' => 'edit_cap');
  }

  echo json_encode($_response);

  /* All ajax handlers should die when finished */
  wp_die();
}
add_action('wp_ajax_' . PWWH_CORE_NOTE_ACTION_EDIT,
           'pwwh_core_note_edit_handler');

/*===========================================================================*/
/* Delete Note AJAX.                                                         */
/*===========================================================================*/

/**
 * @brief     Common Note script ID.
 */
define('PWWH_CORE_NOTE_DELETE_JS', 'pwwh_core_note_delete_js');

/**
 * @brief     Ajax action triggered on note edit.
 */
define('PWWH_CORE_NOTE_ACTION_TRASH', PWWH_CORE_NOTE . '_trash');

/**
 * @brief     Ajax action triggered on note edit.
 */
define('PWWH_CORE_NOTE_ACTION_UNTRASH', PWWH_CORE_NOTE . '_untrash');

/**
 * @brief     Ajax action triggered on note edit.
 */
define('PWWH_CORE_NOTE_ACTION_DELETE', PWWH_CORE_NOTE . '_delete');

/**
 * @brief     Enqueues a the script that handles the delete a notes.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_note_manage_delete() {

  /* Enqueuing the script. */
  $id = PWWH_CORE_NOTE_DELETE_JS;
  $url = PWWH_CORE_NOTE_URL . '/js/pwwh.note.delete.js';
  $deps = array(PWWH_CORE_NOTE_COMMON_JS);
  $ver = '20201126';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Localizing the script. */
  $actions = array('trash' => PWWH_CORE_NOTE_ACTION_TRASH,
                   'untrash' => PWWH_CORE_NOTE_ACTION_UNTRASH,
                   'delete' => PWWH_CORE_NOTE_ACTION_DELETE);
  $data = array('ajax' => array('url' => admin_url('admin-ajax.php'),
                                'action' => $actions),
                'ui' => pwwh_core_note_api_get_ui_facts());
  wp_localize_script($id, 'pwwh_core_note_delete_obj', $data);
}
add_action('admin_enqueue_scripts', 'pwwh_core_note_manage_delete');

/**
 * @brief     Trash note action handler.
 *
 * @hooked    wp_ajax_[PWWH_CORE_NOTE_ACTION_TRASH]
 *
 * @return    void
 */
function pwwh_core_note_trash_handler() {

  $note_id = sanitize_text_field($_POST['note_id']);

  /* Checking user capabilities. */
  if(pwwh_core_note_api_current_user_can_delete($note_id)) {

    if(pwwh_core_note_api_trash($note_id)) {
      /* Edit ok. Sending back the new note. */
      $_response = array('status' => 1);
    }
    else {
      /* Error while updating the note. */
      $_response = array('status' => 0,
                         'code' => 'generic');
    }
  }
  else {
    /* The user has no rights to edit this note. */
    $_response = array('status' => 0,
                       'code' => 'del_cap');
  }

  echo json_encode($_response);

  /* All ajax handlers should die when finished */
  wp_die();
}
add_action('wp_ajax_' . PWWH_CORE_NOTE_ACTION_TRASH,
           'pwwh_core_note_trash_handler');

/**
 * @brief     Trash note action handler.
 *
 * @hooked    wp_ajax_[PWWH_CORE_NOTE_ACTION_TRASH]
 *
 * @return    void
 */
function pwwh_core_note_untrash_handler() {

  $note_id = sanitize_text_field($_POST['note_id']);

  if(pwwh_core_note_api_untrash($note_id)) {
    /* Edit ok. Sending back the new note. */
    $_response = array('status' => 1,
                       'data' => pwwh_core_note_api_get($note_id, 'HTML'));
  }
  else {
    /* Error while updating the note. */
    $_response = array('status' => 0,
                       'code' => 'generic');
  }
  echo json_encode($_response);

  /* All ajax handlers should die when finished */
  wp_die();
}
add_action('wp_ajax_' . PWWH_CORE_NOTE_ACTION_UNTRASH,
           'pwwh_core_note_untrash_handler');

/**
 * @brief     Delete note action handler.
 *
 * @hooked    wp_ajax_[PWWH_CORE_NOTE_ACTION_DELETE]
 *
 * @return    void
 */
function pwwh_core_note_delete_handler() {

  $note_id = sanitize_text_field($_POST['note_id']);

  /* Before deleting need to take the parent ID. */
  $curr_note = pwwh_core_note_api_get($note_id, OBJECT);

  /* Deleting the comment. */
  pwwh_core_note_api_delete($note_id);

  /* Getting the parent as its row action may be changed. */
  if($curr_note->comment_parent) {
    $parent = pwwh_core_note_api_get($curr_note->comment_parent, 'HTML');
  }
  else {
    $parent = false;
  }

  /* Edit ok. Sending back the new note. */
  $_response = array('status' => 1,
                     'parent' => $parent);

  echo json_encode($_response);

  /* All ajax handlers should die when finished */
  wp_die();
}
add_action('wp_ajax_' . PWWH_CORE_NOTE_ACTION_DELETE,
           'pwwh_core_note_delete_handler');

/** @} */