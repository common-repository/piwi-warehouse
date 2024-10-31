<?php
/**
 * @file    common/common-api.php
 * @brief   This file contains common APIs which can be used by admin pages.
 *
 * @addtogroup PWWH_ADMIN
 * @{
 */

/**
 * @brief    Modules global array key identifiers.
 * @{
 */
/* Pages groups. */
define('PWWH_ADMIN_KEY_PAGE', 'page');
define('PWWH_ADMIN_KEY_SUBPAGE', 'subpage');

/* Pages fields. */
define('PWWH_ADMIN_KEY_ID', 'id');
define('PWWH_ADMIN_KEY_LABEL', 'label');
define('PWWH_ADMIN_KEY_ICON', 'icon');
define('PWWH_ADMIN_KEY_CAP', 'cap');
define('PWWH_ADMIN_KEY_PRIO', 'prio');
define('PWWH_ADMIN_KEY_FILL', 'fill');
define('PWWH_ADMIN_KEY_PARENT', 'parent_id');
/** @} */

/**
 * @brief     Initializes the system creating the main structure.
 * 
 * @return    void.
 */
function pwwh_admin_common_init() {

  global $PWWH_ADMIN_PAGES;
  $PWWH_ADMIN_PAGES = array(PWWH_ADMIN_KEY_PAGE => null,
                            PWWH_ADMIN_KEY_SUBPAGE => array());
}

/**
 * @brief     Registers the main admin menu entry.
 * @details   The entry could be a link to an existing admin page or a 
 *            new custome page. 
 *             - To link an existing page $fill should be filled with the slug 
 *               of the existing page (e.g. "post.php?post=1&action=edit").
 *             - To generate a new custom page $fill should be a callable used
 *               to populate the page.
 *
 * @param[in] string $id          The menu unique id.
 * @param[in] string $label       The menu short label.
 * @param[in] mixed $fill         The callable used to populate the custom page
 *                                or the existing page slug. 
 * @param[in] string $icon        The dashicon identifier of the main menu.
 * @param[in] integer $prio       The entry priority: lower number are shown 
 *                                first.
 *
 * @return    void.
 */
function pwwh_admin_common_register_page($id, $label, $fill, $icon = '', 
                                         $prio = 25) {


  global $PWWH_ADMIN_PAGES;
  $page = $PWWH_ADMIN_PAGES[PWWH_ADMIN_KEY_PAGE];

  if($page === null) {
    $data = array(PWWH_ADMIN_KEY_ID => $id,
                  PWWH_ADMIN_KEY_LABEL => $label,
                  PWWH_ADMIN_KEY_ICON => $icon,
                  PWWH_ADMIN_KEY_PRIO => $prio,
                  PWWH_ADMIN_KEY_FILL => $fill);

    $PWWH_ADMIN_PAGES[PWWH_ADMIN_KEY_PAGE] = $data;
  }
  else {
    $msg = 'Overwriting the main page in ' .
           'pwwh_admin_common_register_page()';
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
}

/**
 * @brief     Registers a new admin menu entry.
 * @details   Adds an entry in the plugin admin menu. The entry could be a link
 *            to an existing admin page or a new custome page. 
 *             - To link an existing page $fill should be filled with the slug 
 *               of the existing page (e.g. "post.php?post=1&action=edit").
 *             - To generate a new custom page $fill should be a callable used
 *               to populate the page. The slug of the new page would be the 
 *               $id.
 *
 * @param[in] string $id          The menu unique id.
 * @param[in] string $label       The menu short label.
 * @param[in] string $cap         The capability required to access this page.
 * @param[in] mixed $fill         The callable used to populate the custom page
 *                                or the existing page slug. 
 * @param[in] integer $prio       The entry priority: lower number are shown 
 *                                first.
 * @param[in] string $parent      The parent menu id. The default is the main
                                  menu.
 *
 * @return    void.
 */
function pwwh_admin_common_register_subpage($id, $label, $cap, $fill,
                                            $prio = 25,
                                            $parent = null) {


  global $PWWH_ADMIN_PAGES;
  
  if($parent === null) {
    $parent = pwwh_admin_common_get_menu_id();
  }
  
  $data = array(PWWH_ADMIN_KEY_LABEL => $label,
                PWWH_ADMIN_KEY_CAP => $cap,
                PWWH_ADMIN_KEY_PRIO => $prio,
                PWWH_ADMIN_KEY_FILL => $fill,
                PWWH_ADMIN_KEY_PARENT => $parent);

  $PWWH_ADMIN_PAGES[PWWH_ADMIN_KEY_SUBPAGE][$id] = $data;
}

/**
 * @brief     Return the IDs of the currently registered menu entries.
 * @details   It is possible to filter the entries to get back only the entries
 *            the user has the capability to deal with
 *
 * @param[in] bool $filted        If true the entries are filted depending
 *                                on the current user capabilities.
 *
 * @return    array The IDs of the currently registered menu entries.
 */
function pwwh_admin_common_get_subpages($filted = true) {


  global $PWWH_ADMIN_PAGES;
  $subpages = $PWWH_ADMIN_PAGES[PWWH_ADMIN_KEY_SUBPAGE];

  $entries = array();
  foreach($subpages as $id => $entry) {
    if(pwwh_core_caps_api_current_user_can($entry[PWWH_ADMIN_KEY_CAP]) ||
       ($filted == false)) {
      array_push($entries, $id);
    }
  }

  return $entries;
}

/**
 * @brief     Return the ID of the main menu entry.
 *
 * @return    string The ID of the main menu entry.
 */
function pwwh_admin_common_get_menu_id() {

  global $PWWH_ADMIN_PAGES;
  $page = $PWWH_ADMIN_PAGES[PWWH_ADMIN_KEY_PAGE];

  if(is_array($page) && array_key_exists(PWWH_ADMIN_KEY_ID, $page)) {
    $id = $page[PWWH_ADMIN_KEY_ID];
  }
  else {
    $id = '';
    $msg = 'Missing data in  pwwh_admin_common_get_menu_id()';
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }

  return $id;
}

/**
 * @brief     Return the Label of the main menu entry.
 *
 * @return    string The Label of the main menu entry.
 */
function pwwh_admin_common_get_menu_label() {

  global $PWWH_ADMIN_PAGES;
  $page = $PWWH_ADMIN_PAGES[PWWH_ADMIN_KEY_PAGE];

  if(is_array($page) && array_key_exists(PWWH_ADMIN_KEY_LABEL, $page)) {
    $label = $page[PWWH_ADMIN_KEY_LABEL];
  }
  else {
    $label = '';
    $msg = 'Missing data in  pwwh_admin_common_get_menu_label()';
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }

  return $label;
}

/**
 * @brief     Return the Icon of the main menu entry.
 *
 * @return    string The Icon of the main menu entry.
 */
function pwwh_admin_common_get_menu_icon() {

  global $PWWH_ADMIN_PAGES;
  $page = $PWWH_ADMIN_PAGES[PWWH_ADMIN_KEY_PAGE];

  if(is_array($page) && array_key_exists(PWWH_ADMIN_KEY_ICON, $page)) {
    $icon = $page[PWWH_ADMIN_KEY_ICON];
  }
  else {
    $icon = '';
    $msg = 'Missing data in  pwwh_admin_common_get_menu_icon()';
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }

  return $icon;
}

/**
 * @brief     Return the Filler of the main menu entry.
 *
 * @return    mixed The Filler of the main menu entry.
 */
function pwwh_admin_common_get_menu_fill() {

  global $PWWH_ADMIN_PAGES;
  $page = $PWWH_ADMIN_PAGES[PWWH_ADMIN_KEY_PAGE];

  if(is_array($page) && array_key_exists(PWWH_ADMIN_KEY_FILL, $page)) {
    $fill = $page[PWWH_ADMIN_KEY_FILL];
  }
  else {
    $fill = '';
    $msg = 'Missing data in  pwwh_admin_common_get_menu_fill()';
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }

  return $fill;
}

/**
 * @brief     Return the Prio of the main menu entry.
 *
 * @return    string The Prio of the main menu entry.
 */
function pwwh_admin_common_get_menu_prio() {

  global $PWWH_ADMIN_PAGES;
  $page = $PWWH_ADMIN_PAGES[PWWH_ADMIN_KEY_PAGE];

  if(is_array($page) && array_key_exists(PWWH_ADMIN_KEY_PRIO, $page)) {
    $prio = $page[PWWH_ADMIN_KEY_PRIO];
  }
  else {
    $prio = 100;
    $msg = 'Missing data in  pwwh_admin_common_get_menu_prio()';
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }

  return $prio;
}

/**
 * @brief     Return the Label of a menu subpage.
 *
 * @param[in] string $id          The id of the subpage.
 *
 * @return    string The Label of the menu subpage.
 */
function pwwh_admin_common_get_subpage_label($id) {

  global $PWWH_ADMIN_PAGES;
  $subpages = $PWWH_ADMIN_PAGES[PWWH_ADMIN_KEY_SUBPAGE];

  if(array_key_exists($id, $subpages)) {
    $label = $subpages[$id][PWWH_ADMIN_KEY_LABEL];
  }
  else {
    $label = '';
    $msg = 'Unexisting subpage entry in pwwh_admin_common_get_subpage_label()';
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }

  return $label;
}

/**
 * @brief     Return the Capability of a menu subpage.
 *
 * @param[in] string $id          The id of the subpage.
 *
 * @return    string The Capability of the menu subpage.
 */
function pwwh_admin_common_get_subpage_caps($id) {

  global $PWWH_ADMIN_PAGES;
  $subpages = $PWWH_ADMIN_PAGES[PWWH_ADMIN_KEY_SUBPAGE];

  if(array_key_exists($id, $subpages)) {
    $caps = $subpages[$id][PWWH_ADMIN_KEY_CAP];
  }
  else {
    $caps = '';
    $msg = 'Unexisting subpage entry in pwwh_admin_common_get_subpage_caps()';
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }

  return $caps;
}

/**
 * @brief     Return the Priority of a menu subpage.
 *
 * @param[in] string $id          The id of the subpage.
 *
 * @return    string The Priority of the menu subpage.
 */
function pwwh_admin_common_get_subpage_prio($id) {

  global $PWWH_ADMIN_PAGES;
  $subpages = $PWWH_ADMIN_PAGES[PWWH_ADMIN_KEY_SUBPAGE];

  if(array_key_exists($id, $subpages)) {
    $prio = $subpages[$id][PWWH_ADMIN_KEY_PRIO];
  }
  else {
    $prio = 99;
    $msg = 'Unexisting subpage entry in pwwh_admin_common_get_subpage_prio()';
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }

  return $prio;
}

/**
 * @brief     Return the Filler of a menu subpage.
 *
 * @param[in] string $id          The id of the subpage.
 *
 * @return    string The Filler of the menu subpage.
 */
function pwwh_admin_common_get_subpage_fill($id) {

  global $PWWH_ADMIN_PAGES;
  $subpages = $PWWH_ADMIN_PAGES[PWWH_ADMIN_KEY_SUBPAGE];

  if(array_key_exists($id, $subpages)) {
    $fill = $subpages[$id][PWWH_ADMIN_KEY_FILL];
  }
  else {
    $fill = '';
    $msg = 'Unexisting subpage entry in pwwh_admin_common_get_subpage_fill()';
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }

  return $fill;
}

/**
 * @brief     Return the Parent ID of a menu subpage.
 *
 * @param[in] string $id          The id of the subpage.
 *
 * @return    string The Parent ID of the menu subpage.
 */
function pwwh_admin_common_get_subpage_parent($id) {

  global $PWWH_ADMIN_PAGES;
  $subpages = $PWWH_ADMIN_PAGES[PWWH_ADMIN_KEY_SUBPAGE];

  if(array_key_exists($id, $subpages)) {
    $parent = $subpages[$id][PWWH_ADMIN_KEY_PARENT];
  }
  else {
    $parent = '';
    $msg = 'Unexisting subpage entry in pwwh_admin_common_get_subpage_fill()';
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }

  return $parent;
}

/**
 * @brief     Returns the URL to an admin page.
 *
 * @param[in] string $slug        The page slug
 * @param[in] array $queries      A keyed array organized as query => value
 *
 * @return    array an array of Post object.
 */
function pwwh_admin_common_get_admin_url($slug, $queries = array()) {
  if(is_array($queries)) {
    $query = array();
    foreach($queries as $key => $value) {
      array_push($query, $key . '=' . $value);
    }
    $query = implode('&', $query);
    if($query) {
      $query = '&' . $query;
    }
  }
  return admin_url('admin.php?page=' . $slug . $query);
}
/** @} */