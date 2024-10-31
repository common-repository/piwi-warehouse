<?php
/**
 * @file      notes/notes-ui.php
 * @brief     Common User Interface.
 *
 * @addtogroup PWWH_CORE
 * @{
 */

/**
 * @brief     Returns the content for notes postbox as HTML.
 *
 * @param[in] mixed $post         A Post object or the Post ID
 * @param[in] boolean $echo       Echoes on true
 *
 * @return    string the content as HTML.
 */
function pwwh_core_note_ui_metabox_notes($post = null, $echo = true) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }

  if($post) {

    /* Getting UI facts. */
    $ui_facts = pwwh_core_note_api_get_ui_facts();

    /* Comments */
    $args = array('post_id' => $post->ID,
                  'type' => PWWH_CORE_NOTE,
                  'status' => PWWH_CORE_NOTE,
                  'orderby'=>'comment_date',
                  'order'=>'ASC');
    $comments = get_comments($args);

    if(pwwh_core_caps_api_current_user_can(PWWH_CORE_NOTE_CAPS_ADD_NOTES)) {
      /* Displaying the comment for. */
      $args = array('id' => $ui_facts['button']['add']['id'],
                    'classes' => $ui_facts['button']['add']['class'],
                    'value' => '0:' . $post->ID,
                    'name' => $ui_facts['button']['add']['id'],
                    'label' => $ui_facts['button']['add']['label']);
      $_add_button = pwwh_lib_ui_form_button($args);
    }
    else {
      $_add_button = '';
    }

    /* Displaying comments list. */
    if(pwwh_core_note_api_has_notes($post)) {
      $args = array('walker' => new pwwh_walker_notes(),
                    'max_depth' => PWWH_CORE_NOTE_MAX_DEPTH,
                    'reverse_children' => false,
                    'sublist' => $ui_facts['element']['sublist'],
                    'listelem' => $ui_facts['element']['listelem'],
                    'button-reply' => $ui_facts['button']['reply'],
                    'button-edit' => $ui_facts['button']['edit'],
                    'button-delete' => $ui_facts['button']['delete'],
                    'avatar_size' => 50,
                    'short_ping'  => true,
                    'echo' => false);
      $_list = '<ul id="' . $ui_facts['element']['list']['id'] . '"
                    class="' . $ui_facts['element']['list']['class'] . '">' .
                  wp_list_comments($args, $comments) .
               '</ul>';
    }
    else {
      $_list = '';
    }

    $_output = '<div id="' . $ui_facts['element']['box']['id'] . '">
                  <main id="' . $ui_facts['element']['main']['id'] . '">' .
                    $_list .
                  '</main>
                  <footer id="' . $ui_facts['element']['footer']['id'] . '"
                          class="pwwh-notes-footer">' .
                    $_add_button .
                  '</footer>
                </div>';
  }
  else {
    $_output = '';
  }

  if($echo) {
    echo $_output;
  }
  else {
    return $_output;
  }
}
/** @} */