<?php
/**
 * @file      ui/class/ui-flexbox.php
 * @brief     This class represent a flexbox area.
 *
 * @addtogroup PWWH_LIB_UI_FLEXBOXES
 * @{
 */
class pwwh_lib_ui_flexbox_area {
  /**
   * @brief     The flexboxes data structure.
   */
  private $data;

  /**
   * @brief     The flexbox area context.
   */
  public $context;

  /**
   * @brief     Constructor method.
   *
   * @param[in] string $context       The flexbox area context.
   *
   * @return    void.
   */
  public function __construct($context = 'default') {
    $this->context = $context;
  }

  /**
   * @brief     Adds a flexbox in a flexbox area column depending on priority.
   *
   * @param[in] object $flexbox     The flexbox to add.
   * @param[in] int $priority       The flexbox priority. @default{10}
   *
   * @return    void.
   */
  public function add_flexbox($flexbox, $priority = 10) {

    if(!isset($this->data[$priority]))
      $this->data[$priority] = array();
    array_push($this->data[$priority], $flexbox);

  }

  /**
   * @brief     Returns the output string representing a flexbox area.
   *
   * @param[in] boolean $echo       If true prints the string otherwise returns.
   *                                @default{false}
   *
   * @return    mixed the output string or void.
   */
  public function get() {
    /* This string is a buffer used to accumulate columns' HTML. */
    $inner = '';

    /* Generating all the flexboxes. */

    foreach($this->data as $prio) {
      foreach($prio as $flexbox) {
        $inner .= $flexbox->get();
      }
    }

    /* Wrapping columns with external divs. */
    $output = '<div class="pwwh-lib-flexbox-area ' . esc_attr($this->context) . '">' .
                $inner .
              '</div>';

    return $output;
  }

  /**
   * @brief     Displays the flexbox area.
   *
   * @return    mixed the output string or void.
   */
  public function display() {
    echo $this->get();
  }
}
/** @} */