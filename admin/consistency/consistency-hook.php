<?php
/**
 * @file      consistency/consistency-hook.php
 * @brief     Hooks and function related to the COnsistency Tool.
 *
 * @addtogroup PWWH_ADMIN
 * @{
 */

/**
 * @brief     Enqueues the scripts related to the consistency tool.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_admin_consistency_enqueue_script() {

  /* Including Back-end extra style for the the the consistency tool. */
  $url = PWWH_ADMIN_CONSISTENCY_URL . '/css/consistency.css';
  $deps = array();
  $ver = '20201107';
  wp_enqueue_style('pwwh-admin-consistency-css', $url, $deps, $ver);
}
add_action('admin_enqueue_scripts', 'pwwh_admin_consistency_enqueue_script');
/** @} */