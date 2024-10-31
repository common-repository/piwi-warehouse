<?php
/**
 * @file    class/walker-management-comments.php
 * @brief   This class customize the HTML of the nav menu.
 *
 * @addtogroup PWWH_CORE
 * @{
 */

class pwwh_walker_caps extends Walker {

  /**
   * What the class handles.
   *
   * @since 2.7.0
   * @var string
   *
   * @see Walker::$tree_type
   */
  public $tree_type = 'capability';

  /**
   * Database fields to use.
   *
   * @since 2.7.0
   * @var array
   *
   * @see Walker::$db_fields
   * @todo Decouple this
   */
  public $db_fields = array(
    'parent' => 'dependency',
    'id'     => 'slug',
  );

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

    /* Composing classes. */
    $_classes = array(PWWH_ADMIN_CAPS_LEVEL_CLASS . '-container',
                      'depth-' . $lev);
    $_classes = implode(' ', $_classes);

    $output .= "\n$indent";
    $output .= '<ul class="' . $_classes . '">';
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
  public function start_el(&$output, $object, $depth = 0, $data = array(),
                           $id = 0) {
    $indent = str_repeat('  ', $depth);
    $lev = $depth + 1;

    /* Composing classes. */
    $_classes = array(PWWH_ADMIN_CAPS_LEVEL_CLASS, 'depth-' . $lev);

    /* Checking if this list is disabled. */
    $id = $this->db_fields['id'];
    $cap = $object->$id;
    $role = $data['role'];
    $dep = pwwh_core_caps_api_get_cap_data_by(PWWH_CORE_CAPS_KEY_CAP_DEP, $cap);
    if($dep && !pwwh_core_caps_api_role_can($role, $dep)) {
      array_push($_classes, 'readonly');
    }
    else {
      $readonly = false;
    }

    $_classes = implode(' ', $_classes);
    $output .= "\n$indent";
    $output .= '<li class="' . $_classes . '">';
    $output .= "\n";

    ob_start();
    $this->print_box($object, $depth, $data);
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
  public function end_el(&$output, $object, $depth = 0, $data = array()) {
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
  public function print_box($object, $depth, $data) {

    $id = $this->db_fields['id'];
    $cap = $object->$id;
    $role = $data['role'];

    $_classes = array(PWWH_ADMIN_CAPS_WRAP_CLASS, $cap);

    /* Generating Capability description. */
    $desc = pwwh_core_caps_api_get_cap_data_by(PWWH_CORE_CAPS_KEY_CAP_DESC, $cap);
    $args = array('description' => $desc,
                  'icon' => 'dashicons-info',
                  'class' => 'description');
    $_desc = pwwh_lib_ui_admin_info_chunk($args, false);

    /* Generating Capability switch. */
    $id = esc_attr($role . ':' . $cap);
    $_dep = pwwh_core_caps_api_get_cap_data_by(PWWH_CORE_CAPS_KEY_CAP_DEP, $cap);
    if($_dep && !pwwh_core_caps_api_role_can($role, $_dep)) {
      $readonly = true;
      array_push($_classes, 'readonly');
    }
    else {
      $readonly = false;
    }
    $args = array('id' => $id,
                  'classes' => PWWH_ADMIN_CAPS_SWITCH_CLASS,
                  'status' => pwwh_core_caps_api_role_can($role, $cap, true),
                  'readonly' => $readonly);
    $_switch = pwwh_lib_ui_form_switch($args);

    /* Additional pieces. */
    $_label = pwwh_core_caps_api_get_cap_data_by(PWWH_CORE_CAPS_KEY_CAP_LABEL, $cap);

    /* Composing output. */
    $_classes = implode(' ', $_classes);
    $_output = '<span class="' . $_classes . '">' .
                    $_switch .
                  '<span class="info">
                      <span class="label">' . $_label . '</span>'.
                        $_desc .
                  '</span>
                </span>';

    echo $_output;
  }
}

/** @} */