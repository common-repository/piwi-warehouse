<?php
/**
 * @file    class/walker-locations.php
 * @brief   This class customize the HTML of the locations.
 *
 * @addtogroup PWWH_CORE
 * @{
 */

class pwwh_walker_locations extends Walker {

  /**
   * @brief     What the class handles.
   */
  public $tree_type = PWWH_CORE_ITEM_LOCATION;

  /**
   * @brief     Defines the fields to use.
   */
  public $db_fields = array('parent' => 'parent',
                            'id' => 'term_id');

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
    $class = $data['sublist_classes'] . ' sub-list lev-' . $lev;

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
   * @param[in] WP_TERM $locations  The location.
   * @param[in] int $depth          Depth of the item.
   * @param[in] array $data         An object of arguments.
   *
   * @return    void.
   */
  public function start_el(&$output, $location, $depth = 0, $data = array(),
                           $id = 0) {
    $indent = str_repeat('  ', $depth);
    $lev = $depth + 1;
    $class = $data['item_classes'] . ' list-item depth-' . $lev;

    $output .= "\n$indent";
    $output .= '<li class="' . $class . '">';
    $output .= "\n";
    $output .= $this->get_el($location, $depth, $data);
  }

  /**
   * @brief     Ends the list of after the elements are added.
   *
   * @param[in/out] string $output  Used to append additional content.
   * @param[in] WP_TERM $location   The location.
   * @param[in] int $depth          Depth of the item.
   * @param[in] array $data         An object of arguments.
   *
   * @return    void.
   */
  public function end_el(&$output, $location, $depth = 0, $data = array()) {
    $indent = str_repeat("  ", $depth);

    $output .= "\n$indent";
    $output .= '</li>';
    $output .= "\n";
  }

  /**
   * Outputs the location node in the HTML format.
   *
   * @param[in] WP_Term $location   The location to display.
   * @param[in] int $depth          Depth of the item.
   * @param[in] array $data         An object of arguments.
   */
  public function get_el($location, $depth, $data) {
    $lev = $depth + 1;
    $class = 'item depth-' . $lev;

    /* Composing location name. */
    $_name = '<span class="item-name">' . $location->name .'</span>';

    /* Composing location avail. */
    if($data['avail']) {
      if($depth == 0) {
        $avail = pwwh_core_item_api_get_avail_by_location(null, $location,
                                                          false);
      }
      else {
        $avail = pwwh_core_item_api_get_avail_by_location(null, $location,
                                                          true);
      }

      if($avail) {
        $_value = '<span class="item-value">' . $avail . '</span>';
        $class .= ' has-value';
      }
      else {
        $_value = '<span class="item-value">-</span>';
      }
    }
    else {
      $_value = '';
    }

    /* Composing output. */
    $_output = '<span class="' . $class . '">' .
                  $_name . $_value .
                '</span>';

    return $_output;
  }
}

/** @} */