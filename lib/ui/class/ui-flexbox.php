<?php
/**
 * @file      ui/class/ui-flexbox.php
 * @brief     This class represent a flexbox, which is such a widget displayable
 *            in specific admin areas named flexbox area.
 *
 * @addtogroup PWWH_LIB_UI_FLEXBOXES
 * @{
 */
class pwwh_lib_ui_flexbox {
  /**
   * @brief     The flexbox title.
   */
  public $title;

  /**
   * @brief     The flexbox ID.
   */
  public $id;

  /**
   * @brief     A callable used to generate the inner part of the flexbox.
   */
  private $__callable;

  /**
   * @brief     Arguments for the callable.
   */
  private $__callable_args;

  /**
   * @brief     Flexbox classes.
   */
  private $__classes;

  /**
   * @brief     Capability required to see this flexbox.
   */
  private $__cap;

  /**
   * @brief     Constructor method.
   *
   * @param[in] string $id          The flexbox ID.
   * @param[in] string $title       The flexbox title.
   * @param[in] callable $call      A callable used to generate the inner part
   *                                of the flexbox.
   * @param[in] array $args         An array of arguments for the callable.
   * @param[in] mixed $class        An array or a single class.
   * @param[in] mixed $cap          Capability required to see this flexbox.
   *
   * @return    void.
   */
  public function __construct($id, $title, $call, $args = null,
                              $class = array(), $cap = 'read') {
    $this->id = $id;
    $this->title = $title;
    $this->__callable = $call;
    $this->__callable_args = $args;
    $this->__classes = $class;
    $this->__cap = $cap;
  }

  /**
   * @brief     Returns the output string representing a flexbox.
   *
   * @return    mixed the output string or void.
   */
  public function get() {

    if(current_user_can($this->__cap)) {
      $title = '<h2 class="pwwh-lib-title">' . $this->title . '</h2>';

      /* Generating inner. */
      if(is_array($this->__callable_args))
        $inner = call_user_func_array($this->__callable, $this->__callable_args);
      else if($this->__callable_args)
        $inner = call_user_func($this->__callable, $this->__callable_args);
      else
        $inner = call_user_func($this->__callable);
      $inner = '<div class="pwwh-lib-inner">
                  <div class="pwwh-lib-main">' .
                    $inner .
                  '</div>
                </div>';
      if(is_string($this->__classes))
        $this->__classes = explode(' ', $this->__classes);
      array_push($this->__classes, 'pwwh-lib-flexbox');
      $this->__classes = esc_attr(trim(implode(' ', $this->__classes)));

      $output = '<div id="' . $this->id . '" class="' . $this->__classes . '">' .
                  $title . $inner .
                '</div>';
    }
    else {
      $output = '';
    }
    return $output;
  }

  /**
   * @brief     Displays the flexbox.
   *
   * @return    mixed the output string or void.
   */
  public function display() {
    echo $this->get();
  }
}

/** @} */