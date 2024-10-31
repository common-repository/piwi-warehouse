<?php
/**
 * @file      lib/ui/ui-flexboxes.php
 * @brief     This file contains all the functions to manage flexbox mechanism.
 *
 * @addtogroup PWWH_LIB_UI_FLEXBOXES
 * @{
 */
require_once(PWWH_LIB_DIR . '/ui/class/ui-flexbox.php');
require_once(PWWH_LIB_DIR . '/ui/class/ui-flexbox-area.php');

/**
 * @brief     Global array contains all the flexbox area depending on context.
 * @{
 */
global $PWWH_FLEXBOXES;
if(!isset($PWWH_FLEXBOXES)) {
  $PWWH_FLEXBOXES = array();
}
/** @} */

/**
 * @brief     Adds a flexbox in a specific flexbox area.
 *
 * @param[in] int $id             The flexbox ID.
 * @param[in] string $title       The flexbox title.
 * @param[in] callable $call      A callable used to generate the inner part
 *                                of the flexbox.
 * @param[in] array $args         An array of arguments for the callable.
 * @param[in] string $context     The context in witch add flexbox.
 * @param[in] int $priority       The flexbox priority. @default{10}
 * @param[in] mixed $class        The flexbox class. @default{Empty}
 * @param[in] string $cap         The capability required to see this flexbox.
 *                                @default{read}
 *
 * @return    void.
 */
function pwwh_lib_ui_flexboxes_add_flexbox($id, $title, $call, $args = null,
                                           $context = 'default', $priority = 10,
                                           $class = '', $cap = 'read') {
  global $PWWH_FLEXBOXES;
  if(!isset($PWWH_FLEXBOXES[$context])) {
    $PWWH_FLEXBOXES[$context] = new pwwh_lib_ui_flexbox_area($context);
  }
  $flexbox = new pwwh_lib_ui_flexbox($id, $title, $call, $args, $class, $cap);
  $PWWH_FLEXBOXES[$context]->add_flexbox($flexbox, $priority);
}

/**
 * @brief     Prints or returns the flexbox area according to the context.
 *
 * @param[in] string $context     The flexbox area context.
 * @param[in] boolean $echo       If true prints the flexbox area otherwise
 *                                returns it as HTML. @default{true}
 *
 * @return    mixed the output string or void.
 */
function pwwh_lib_ui_flexboxes_do_flexbox_area($context, $echo = true) {
  global $PWWH_FLEXBOXES;
  if(isset($PWWH_FLEXBOXES[$context])) {
    if($echo)
      $PWWH_FLEXBOXES[$context]->display();
    else
      return ($PWWH_FLEXBOXES[$context]->get());
  }
}
/** @} */