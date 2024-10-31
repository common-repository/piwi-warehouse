<?php
/**
 * @file      notes/notes-api.php
 * @brief     Notes related API.
 *
 * @addtogroup PWWH_CORE_NOTE
 * @{
 */

/*===========================================================================*/
/* Generic APIs                                                              */
/*===========================================================================*/

/**
 * @brief     Get an note as WP_Comment or HTML.
 *
 * @param[in] mixed $note_id      The comment ID.
 * @param[in] string $type        The type of output.
 * @paramval{'HTML'}              Outputs the note as HTML
 * @paramval{OBJECT}              Outputs the note as WP_Comment
 *
 * @return    mixed The new comment's ID on success, false on failure.
 */
function pwwh_core_note_api_get($note_id, $type = 'HTML') {

  $comment = get_comment($note_id);

  if(is_a($comment, 'WP_Comment')) {
    if($type === 'HTML') {

      /* Getting UI facts. */
      $ui_facts = pwwh_core_note_api_get_ui_facts();

      $walker = new pwwh_walker_notes();
      $depth = pwwh_core_note_api_get_depth($note_id);
      $data = array('button-reply' => $ui_facts['button']['reply'],
                    'button-edit' => $ui_facts['button']['edit'],
                    'button-delete' => $ui_facts['button']['delete'],
                    'avatar_size' => 50);

      ob_start();
      $walker->html5_comment($comment, $depth, $data);
      $output = ob_get_clean();

      return $output;
    }
    else if ($type === OBJECT) {
      return $comment;
    }
    else {
      $msg = sprintf('Unexpected type in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      return false;
    }
  }
  else {
    $msg = sprintf('Unexpected note id in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    return false;
  }
}

/**
 * @brief     Insert a new note to a post.
 * @notes     Only currently logged-in user can push a note.
 *
 * @param[in] string $content     The comment content.
 * @param[in] mixed $post         A Post object or a Post ID.
 * @param[in] mixed $parent       The parent comment ID.
 *
 * @return    mixed The new comment's ID on success, false on failure.
 */
function pwwh_core_note_api_insert($content, $post = null, $parent = 0) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_type = get_post_type($post);
  $allowed_post_types = array(PWWH_CORE_PURCHASE, PWWH_CORE_MOVEMENT);

  if(in_array($post_type, $allowed_post_types)) {
    /* Replacing Current title with first name only. */
    $current_user = wp_get_current_user();
    if(is_a($current_user, 'WP_User')) {

      if($parent) {
        /* Checking if parent is a comment belonging to the same post of the
           current comment. */
        $parent = get_comment($parent);

        if((is_a($parent, 'WP_Comment')) &&
           ($parent->comment_post_ID == $post->ID)) {

          $parent = $parent->comment_ID;
        }
        else {
          $parent = 0;
        }
      }
      else {
        $parent = 0;
      }

      /* Computing the ip. */
      $ip = pwwh_lib_utils_get_user_ip_address();

      /* Computing the agent. */
      if(isset($_SERVER['HTTP_USER_AGENT'])) {
        $agent = $_SERVER['HTTP_USER_AGENT'];
      }
      else {
        $agent = '';
      }

      $data = array('comment_author' => $current_user->data->user_nicename,
                    'comment_author_url' => $current_user->data->user_url,
                    'comment_author_email' => $current_user->data->user_email,
                    'comment_author_IP' => $ip,
                    'comment_agent' => $agent,
                    'comment_content' => sanitize_textarea_field($content),
                    'comment_post_ID' => $post->ID,
                    'comment_type' => PWWH_CORE_NOTE,
                    'comment_parent' => $parent,
                    'comment_approved' => PWWH_CORE_NOTE,
                    'user_id' => $current_user->ID);

      return wp_insert_comment($data);
    }
    else {
      $msg = sprintf('Unexpected missing user in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      return false;
    }
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $msg = sprintf('Post type is %s', $post_type);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    return false;
  }
}

/**
 * @brief     Updates an existing note to changing its content.
 *
 * @param[in] mixed $note_id      The comment ID.
 * @param[in] string $content     The note content.
 *
 * @return    bool the operation status.
 */
function pwwh_core_note_api_edit($note_id, $content) {

  $data = array('comment_ID' => $note_id,
                'comment_content' => $content);
  $res = wp_update_comment($data);

  if(($res === 0) || ($res === 1)) {
    return true;
  }
  else {
    $msg = sprintf('Error in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $msg = sprintf('The note is %s', $note_id);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    return false;
  }
}

/**
 * @brief     Deletes an existing note.
 *
 * @param[in] mixed $note_id      The comment ID.
 * @param[in] bool $force         Whether the comment should be deleted
 *                                forcefully bypassing the Trash.
 *
 * @return    bool the operation status.
 */
function pwwh_core_note_api_delete($note_id) {

  $comment = get_comment($note_id);

  if(is_a($comment, 'WP_Comment')) {
    $res = wp_delete_comment($note_id, true);

    if($res) {
      return true;
    }
    else {
      $msg = sprintf('Error in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      $msg = sprintf('The note is %s', $note_id);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      return false;
    }
  }
  else {
    return false;
  }
}

/**
 * @brief     Trash an existing note.
 *
 * @param[in] mixed $note_id      The comment ID.
 *
 * @return    bool the operation status.
 */
function pwwh_core_note_api_trash($note_id) {

  $data = array('comment_ID' => $note_id,
                'comment_approved' => PWWH_CORE_NOTE . '-trash');
  $res = wp_update_comment($data);

  if(($res === 0) || ($res === 1)) {
    return true;
  }
  else {
    $msg = sprintf('Error in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $msg = sprintf('The note is %s', $note_id);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    return false;
  }
}

/**
 * @brief     Untrash an existing note.
 *
 * @param[in] mixed $note_id      The comment ID.
 *
 * @return    bool the operation status.
 */
function pwwh_core_note_api_untrash($note_id) {

  $data = array('comment_ID' => $note_id,
                'comment_approved' => PWWH_CORE_NOTE);
  $res = wp_update_comment($data);

  if(($res === 0) || ($res === 1)) {
    return true;
  }
  else {
    $msg = sprintf('Error in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $msg = sprintf('The note is %s', $note_id);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    return false;
  }
}

/*===========================================================================*/
/* Utility APIs                                                              */
/*===========================================================================*/

/**
 * @brief     Checks if a note belongs to the current user.
 * @notes     Only currently logged-in user can push a note.
 *
 * @param[in] mixed $note         The note to check as Comment object or
 *                                Comment ID.
 *
 * @return    bool the operation result.
 */
function pwwh_core_note_api_belongs_to_current_user($note) {

  if(!is_a($note, 'WP_Comment')) {
    $note = get_comment($note);
  }

  if(is_a($note, 'WP_Comment')) {
    $curr_user_id = get_current_user_id();
    return ($curr_user_id == $note->user_id);
  }
  else {
    $msg = sprintf('Unexpected note format in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    return false;
  }
}

/**
 * @brief     Checks if the current user can edit a note.
 *
 * @param[in] mixed $note         The note to check as Comment object or
 *                                Comment ID.
 *
 * @return    bool the operation status.
 */
function pwwh_core_note_api_current_user_can_edit($note) {

  if(pwwh_core_caps_api_current_user_can(PWWH_CORE_NOTE_CAPS_EDIT_OTHER_NOTES)) {
    return true;
  }
  else if(pwwh_core_caps_api_current_user_can(PWWH_CORE_NOTE_CAPS_EDIT_NOTES) &&
          pwwh_core_note_api_belongs_to_current_user($note)) {
    return true;
  }
  else {
    return false;
  }
}

/**
 * @brief     Checks if the current user can delete a note.
 *
 * @param[in] mixed $note         The note to check as Comment object or
 *                                Comment ID.
 *
 * @return    bool the operation status.
 */
function pwwh_core_note_api_current_user_can_delete($note) {

  if(pwwh_core_note_api_has_children($note)) {
    return false;
  }
  else {
    if(pwwh_core_caps_api_current_user_can(PWWH_CORE_NOTE_CAPS_DELETE_OTHER_NOTES)) {
      return true;
    }
    else if(pwwh_core_caps_api_current_user_can(PWWH_CORE_NOTE_CAPS_DELETE_NOTES) &&
            pwwh_core_note_api_belongs_to_current_user($note)) {
      return true;
    }
    else {
      return false;
    }
  }
}

/**
 * @brief     Get the depth of a note.
 *
 * @param[in] mixed $note         The note to check as Comment object or
 *                                Comment ID.
 *
 * @return    int The note depth.
 */
function pwwh_core_note_api_get_depth($note) {

  if(!is_a($note, 'WP_Comment')) {
    $note = get_comment($note);
  }

  $note_id = $note->comment_ID;

  $depth = -1;
  while($note_id != 0) {
    $note = get_comment($note_id);
    $note_id = $note->comment_parent;
    $depth++;
  }
  return $depth;
}

/**
 * @brief     Checks if a note as children from its ID.
 *
 * @param[in] mixed $note         The note to check as Comment object or
 *                                Comment ID.
 *
 * @return    bool The operation status.
 */
function pwwh_core_note_api_has_children($note) {

  if(!is_a($note, 'WP_Comment')) {
    $note = get_comment($note);
  }

  $note_id = $note->comment_ID;

  $args = array('parent' => $note_id,
                'type' => PWWH_CORE_NOTE,
                'status' => PWWH_CORE_NOTE,
                'count' => true);
  return (get_comments($args) > 0);
}

/**
 * @brief     Checks if a post as notes.
 *
 * @param[in] mixed $post         The Post as WP_Post or Post ID
 *
 * @return    bool The operation status.
 */
function pwwh_core_note_api_has_notes($post) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_comment($post);
  }

  $post_id = $post->ID;

  $args = array('post_ID' => $post_id,
                'type' => PWWH_CORE_NOTE,
                'status' => PWWH_CORE_NOTE,
                'count' => true);
  $count = get_comments($args);

  return ($count > 0);
}

/*===========================================================================*/
/* API related to Note UI                                                    */
/*===========================================================================*/

/**
 * @brief     Returns all the facts related to the User Interface.
 *
 * @api
 *
 * @return    array the facts as an associative array.
 */
function pwwh_core_note_api_get_ui_facts() {

  /* Elements facts. */
  {
    /* External wrapper facts. */
    $box = array('id' => PWWH_PREFIX . '-notes');

    /* Main facts. */
    $main = array('id' => PWWH_PREFIX . '-notes-main');

    /* Footer facts. */
    $footer = array('id' => PWWH_PREFIX . '-notes-footer');

    /* List facts. */
    $list = array('id' => PWWH_PREFIX . '-notes-list',
                  'class' => 'note-level-container depth-1');

    /* List facts. */
    $sublist = array('class' => 'note-level-container depth-%d');

    /* List facts. */
    $listelem = array('class' => 'note-level depth-%d');

    /* Textarea facts. */
    $textarea = array('class' => PWWH_PREFIX . '-note-editor',
                      'placeholder' => __('Press Shift + Enter to confirm, ' .
                                          'Escape to cancel ...',
                                          'piwi-warehouse'));

    /* Composing Metabox facts. */
    $_elems = compact('box', 'main', 'footer', 'list', 'sublist', 'listelem',
                      'textarea');
  }

  /* Button facts. */
  {
    /* Add button facts. */
    $add = array('id' => PWWH_CORE_NOTE . '_add',
                 'class' => PWWH_CORE_NOTE . '_add hide-if-no-js',
                 'label' => __('Add a Note', 'piwi-warehouse'));

    /* Reply button facts. */
    $reply = array('id' => PWWH_CORE_NOTE . '_reply-%d',
                   'class' => PWWH_CORE_NOTE . '_reply',
                   'label' => __('Reply', 'piwi-warehouse'));

    /* Edit button facts. */
    $edit = array('id' => PWWH_CORE_NOTE . '_edit-%d',
                  'class' => PWWH_CORE_NOTE . '_edit',
                  'label' => __('Edit', 'piwi-warehouse'));

    /* Delete button facts. */
    $delete = array('id' => PWWH_CORE_NOTE . '_delete-%d',
                    'class' => PWWH_CORE_NOTE . '_delete',
                    'label' => __('Delete', 'piwi-warehouse'));

    /* Delete button facts. */
    $confirm = array('class' => PWWH_CORE_NOTE . '_confirm',
                     'label' => __('Confirm', 'piwi-warehouse'));

    /* Composing Metabox facts. */
    $_btns = compact('add', 'reply', 'edit', 'delete', 'confirm');
  }

  /* msg facts. */
  {
    /* Add button facts. */
    $error = array('generic' => __('An error occured. Please try again.',
                                   'piwi-warehouse'),
                   'empty' => __('The note cannot be empty.', 'piwi-warehouse'),
                   'add_cap' => __('You are not allowed to modify this note.',
                                    'piwi-warehouse'),
                   'edit_cap' => __('You are not allowed to modify this note.',
                                    'piwi-warehouse'),
                   'del_cap' => __('You are not allowed to delete this note.',
                                    'piwi-warehouse'));

    $deleted = __('This note has been deleted...', 'piwi-warehouse');
    $preundo = __('%ss to ', 'piwi-warehouse');
    $undo = __('Undo', 'piwi-warehouse');

    /* Composing Metabox facts. */
    $_msgs = compact('error', 'deleted', 'preundo', 'undo');
  }

  /* Composing facts. */
  $facts = array('element' => $_elems,
                 'button' => $_btns,
                 'msg' => $_msgs,
                 'error_class' => PWWH_PREFIX . '-note-error',
                 'max_depth' => PWWH_CORE_NOTE_MAX_DEPTH);

  return $facts;
}

/** @} */

