<?php
/**
 * @file    admin/main/main.php
 * @brief   Hooks and function related to Main menu entry.
 *
 * @ingroup PWWH_ADMIN_MAIN
 */

/**
 * @brief     Main menu related definition.
 * @{
 */
{
  /* Directory and URL. */
  define('PWWH_ADMIN_MAIN_DIR', PWWH_ADMIN_DIR . '/_main');
  define('PWWH_ADMIN_MAIN_URL', PWWH_ADMIN_URL . '/_main');

  /* Main page identifier, label and icon. */
  define('PWWH_ADMIN_MAIN_PAGE_ID', PWWH_PREFIX . '_main');
  define('PWWH_ADMIN_MAIN_LABEL', 'Piwi Warehouse');
  define('PWWH_ADMIN_MAIN_ICON', 'dashicons-archive');

  /* Page Slugs. */
  define('PWWH_ADMIN_MAIN_SLUG_ITEM',
         'edit.php?post_type=' . PWWH_CORE_ITEM);
  define('PWWH_ADMIN_MAIN_SLUG_LOCATION',
         'edit-tags.php?post_type=' . PWWH_CORE_ITEM .
         '&taxonomy=' . PWWH_CORE_ITEM_LOCATION);
  define('PWWH_ADMIN_MAIN_SLUG_TYPE',
         'edit-tags.php?post_type=' . PWWH_CORE_ITEM .
         '&taxonomy=' . PWWH_CORE_ITEM_TYPE);
  define('PWWH_ADMIN_MAIN_SLUG_PURCHASE',
         'edit.php?post_type=' . PWWH_CORE_PURCHASE);
  define('PWWH_ADMIN_MAIN_SLUG_MOVEMENT',
         'edit.php?post_type=' . PWWH_CORE_MOVEMENT . '&post_status=active');
  define('PWWH_ADMIN_MAIN_SLUG_HOLDER',
         'edit-tags.php?post_type=' . PWWH_CORE_MOVEMENT .
         '&taxonomy=' . PWWH_CORE_MOVEMENT_HOLDER);
}
/** @} */

/**
 * @brief     Main menu inclusion block.
 * @{
 */
{
  require_once(PWWH_ADMIN_MAIN_DIR . '/main-ui.php');
  require_once(PWWH_ADMIN_MAIN_DIR . '/main-hook.php');
}
/** @} */

/**
 * @brief     Module main definitions and registration block.
 * @{
 */
{

  pwwh_admin_common_register_page(PWWH_ADMIN_MAIN_PAGE_ID,
                                  PWWH_ADMIN_MAIN_LABEL,
                                  'pwwh_admin_main_ui',
                                  PWWH_ADMIN_MAIN_ICON, 25);

  $id = 'item_link';
  $label = __('Items', 'piwi-warehouse');
  $cap = PWWH_CORE_ITEM_CAPS_MANAGE_ITEMS;
  $fill = PWWH_ADMIN_MAIN_SLUG_ITEM;
  $prio = 1;
  pwwh_admin_common_register_subpage($id, $label, $cap, $fill, $prio);

  $id = 'location_link';
  $label = __('Items - Locations', 'piwi-warehouse');
  $cap = PWWH_CORE_ITEM_CAPS_MANAGE_LOCATIONS;
  $fill = PWWH_ADMIN_MAIN_SLUG_LOCATION;
  $prio = 2;
  pwwh_admin_common_register_subpage($id, $label, $cap, $fill, $prio);

  $id = 'type_link';
  $label = __('Items - Types', 'piwi-warehouse');
  $cap = PWWH_CORE_ITEM_CAPS_MANAGE_TYPES;
  $fill = PWWH_ADMIN_MAIN_SLUG_TYPE;
  $prio = 3;
  pwwh_admin_common_register_subpage($id, $label, $cap, $fill, $prio);

  $id = 'purchase_link';
  $label = __('Purchases', 'piwi-warehouse');
  $cap = PWWH_CORE_PURCHASE_CAPS_MANAGE_PURCHASES;
  $fill = PWWH_ADMIN_MAIN_SLUG_PURCHASE;
  $prio = 4;
  pwwh_admin_common_register_subpage($id, $label, $cap, $fill, $prio);

  $id = 'movement_link';
  $label = __('Movements', 'piwi-warehouse');
  $cap = PWWH_CORE_MOVEMENT_CAPS_MANAGE_MOVEMENTS;
  $fill = PWWH_ADMIN_MAIN_SLUG_MOVEMENT;
  $prio = 5;
  pwwh_admin_common_register_subpage($id, $label, $cap, $fill, $prio);

  $id = 'holder_link';
  $label = __('Holders', 'piwi-warehouse');
  $cap = PWWH_CORE_MOVEMENT_CAPS_MANAGE_HOLDERS;
  $fill = PWWH_ADMIN_MAIN_SLUG_HOLDER;
  $prio = 6;
  pwwh_admin_common_register_subpage($id, $label, $cap, $fill, $prio);
}
/** @} */