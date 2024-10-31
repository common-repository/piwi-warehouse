<?php
/**
 * @file    class/walker-management-comments.php
 * @brief   This class customize the HTML of the nav menu.
 *
 * @addtogroup PWWH_CORE
 * @{
 */

class pwwh_walker_notes extends Walker_Comment {

  /**
   * @brief     Starts the list before the elements are added.
   *
   * @param[in/out] string $output  Used to append additional content.
   * @param[in] int $depth          Depth of the item.
   * @param[in] array $data         An object of arguments.
   *
   * @return    void.
   */
  function start_lvl(&$output, $depth = 0, $data = array()) {
    $indent = str_repeat('  ', $depth);
    $lev = $depth + 2;
    $class = sprintf($data['sublist']['class'], $lev);

    $output .= "\n$indent";
    $output .= '<ul class="' . $class . '">';
    $output .= "\n";
  }

  /**
   * @brief     Ends the list of after the elements are added.
   *
   * @param[in/out] string $output  Used to append additional content.
   * @param[in] int $depth          Depth of the item.
   * @param[in] array $data         An object of arguments.
   *
   * @return    void.
   */
  public function end_lvl(&$output, $depth = 0, $data = array()) {
    $indent = str_repeat("  ", $depth);
    $output .= "\n$indent";
    $output .= '</ul>';
    $output .= "\n";
  }

  /**
   * @brief     Starts the list before the elements are added.
   *
   * @param[in/out] string $output  Used to append additional content.
   * @param[in] WP_Comment $comment The comment.
   * @param[in] int $depth          Depth of the item.
   * @param[in] array $data         An object of arguments.
   *
   * @return    void.
   */
  public function start_el(&$output, $comment, $depth = 0, $data = array(),
                           $id = 0) {
    $indent = str_repeat('  ', $depth);
    $lev = $depth + 1;
    $class = sprintf($data['listelem']['class'], $lev);

    $output .= "\n$indent";
    $output .= '<li class="' . $class . '">';
    $output .= "\n";

    ob_start();
    $this->html5_comment($comment, $depth, $data);
    $output .= ob_get_clean();
  }

  /**
   * @brief     Ends the list of after the elements are added.
   *
   * @param[in/out] string $output  Used to append additional content.
   * @param[in] WP_Comment $comment The comment.
   * @param[in] int $depth          Depth of the item.
   * @param[in] array $data         An object of arguments.
   *
   * @return    void.
   */
  public function end_el(&$output, $comment, $depth = 0, $data = array()) {
    $indent = str_repeat("  ", $depth);
    $output .= "\n$indent";
    $output .= '</li>';
    $output .= "\n";
  }

  /**
   * Outputs a comment in the HTML5 format.
   *
   * @see wp_list_comments()
   *
   * @param[in] WP_Comment $comment The comment to display.
   * @param[in] int $depth          Depth of the item.
   * @param[in] array $data         An object of arguments.
   */
  public function html5_comment($comment, $depth, $data) {

    $lev = $depth + 1;
    $_id = 'note-' . $comment->comment_ID;

    {
      /* Getting User avatar. */
      $_avatar = get_avatar($comment->user_id, $data['avatar_size']);

      /* Getting User complete name. */
      $first = get_user_meta($comment->user_id, 'first_name', true);
      $last = get_user_meta($comment->user_id, 'last_name', true);
      $_author = $first . ' ' . $last;

      /* Getting comment date. */
      $raw_data = strtotime($comment->comment_date);
      $date_format = get_option('date_format');
      $time_format = get_option('time_format');
      $_date = date($date_format . ', ' . $time_format, $raw_data);

      /* Composing the header. */
      $_header =  '<span class="note-avatar">' .  $_avatar . '</span>
                   <span class="note-info">
                    <span class="note-author">' . $_author . '</span>
                    <span class="note-date">' . $_date . '</span>
                  </span>';
    }

    /* Composing the content. */
    {
      $_content = '<span class="content">' .
                    nl2br($comment->comment_content) .
                  '</span>';
    }

    /* Composing the row actions. */
    {
      $_actions = array();

      if(pwwh_core_caps_api_current_user_can(PWWH_CORE_NOTE_CAPS_ADD_NOTES)) {

        /* Reply action. */
        $value = $comment->comment_ID . ':' . $comment->comment_post_ID;
        $id =  sprintf($data['button-reply']['id'], $comment->comment_ID);
        $class =  $data['button-reply']['class'] . ' hide-if-no-js';
        $args = array('id' => $id,
                      'classes' => $class,
                      'value' => $value,
                      'name' => $id,
                      'label' => $data['button-reply']['label']);
        array_push($_actions, pwwh_lib_ui_form_button($args));
      }

      if(pwwh_core_note_api_current_user_can_edit($comment)) {

        /* Edit action. */
        $id =  sprintf($data['button-edit']['id'], $comment->comment_ID);
        $class =  $data['button-edit']['class'] . ' hide-if-no-js';
        $args = array('id' => $id,
                      'classes' => $class,
                      'value' => $value,
                      'name' => $id,
                      'label' => $data['button-edit']['label']);
        array_push($_actions, pwwh_lib_ui_form_button($args));
      }

      if(pwwh_core_note_api_current_user_can_delete($comment)) {
        /* Delete action. */
        $id =  sprintf($data['button-delete']['id'], $comment->comment_ID);
        $class =  $data['button-delete']['class'] . ' hide-if-no-js';
        $args = array('id' => $id,
                      'classes' => $class,
                      'value' => $value,
                      'name' => $id,
                      'label' => $data['button-delete']['label']);
        array_push($_actions, pwwh_lib_ui_form_button($args));
      }
    }

    $_row_actions = '<span class="hide-if-no-js notes-row-actions">' .
                      implode(' | ', $_actions) .
                    '</span>';

    $args = array('parent' => $comment->comment_ID,
                  'count' => true);
    $children_count = get_comments($args);

    if($children_count > 0) {
      $_class = 'note has-children depth-' . $lev;
    }
    else {
      $_class = 'note depth-' . $lev;
    }

    $_output = '<article id="' . $_id . '" class="' . $_class .'">
                  <header>' . $_header . '</header>
                  <main>' . $_content . '</main>
                  <footer>' . $_row_actions . '</footer>
                </article>';
    echo $_output;
  }
}

/** @} */