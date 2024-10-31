<?php
/**
 * @file    common/common.php
 * @brief   This file contains common defines and hooks.
 *
 * @addtogroup PWWH_ADMIN
 * @{
 */

/**
 * @brief     Updates the parent_file in order to keep the admin menu open
 *            when dealing with specific taxonomies.
 *
 * @param[in] string $parent_file The current parent file
 *
 * @hooked    parent_file
 *
 * @return    the filtered parent_file
 */
function pwwh_admin_main_open_menu($parent_file) {
  global $current_screen;

  $post_type = $current_screen->post_type;
  $taxonomy = $current_screen->taxonomy;

  if (($taxonomy == PWWH_CORE_ITEM_LOCATION) || 
      ($taxonomy == PWWH_CORE_ITEM_TYPE) || 
      ($taxonomy == PWWH_CORE_MOVEMENT_HOLDER) ||
      (($post_type == PWWH_CORE_MOVEMENT) && ($taxonomy == ''))) {
    $parent_file = PWWH_ADMIN_MAIN_PAGE_ID;
  }
  else {
    /* Nothing to do. */
  }

  return $parent_file;
}
add_action('parent_file', 'pwwh_admin_main_open_menu');

/**
 * @brief     Updateds the submenu_file in order to select the current menu 
 *            voice.
 *
 * @param[in] string $submenu_file The current parent file
 *
 * @hooked    submenu_file
 *
 * @return    the filtered submenu_file
 */
function pwwh_admin_main_select_menu($submenu_file) {
  global $current_screen;
  $post_type = $current_screen->post_type;
  $taxonomy = $current_screen->taxonomy;
  
  if ($taxonomy == PWWH_CORE_ITEM_LOCATION) {
    $submenu_file = PWWH_ADMIN_MAIN_SLUG_LOCATION;
  }
  else if ($taxonomy == PWWH_CORE_ITEM_TYPE) {
    $submenu_file = PWWH_ADMIN_MAIN_SLUG_TYPE;
  }  
  else if ($taxonomy == PWWH_CORE_MOVEMENT_HOLDER) {
    $submenu_file = PWWH_ADMIN_MAIN_SLUG_HOLDER;
  }
  else if (($post_type == PWWH_CORE_MOVEMENT) && ($taxonomy == '')) {
    $submenu_file = PWWH_ADMIN_MAIN_SLUG_MOVEMENT;
  }
  else {
    /* Nothing to do. */
  }

  return $submenu_file;
}
add_action('submenu_file', 'pwwh_admin_main_select_menu');

/**
 * @brief     Enqueues the scripts related to the main page.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_admin_main_enqueue_script() {

  /* Including Back-end extra style for the main page. */
  $url = PWWH_ADMIN_MAIN_URL . '/css/main.css';
  $deps = array();
  $ver = '20201010';
  wp_enqueue_style('pwwh-admin-main-css', $url, $deps, $ver);
}
add_action('admin_enqueue_scripts', 'pwwh_admin_main_enqueue_script');
/** @} */