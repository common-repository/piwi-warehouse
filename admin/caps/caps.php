<?php
/**
 * @file    admin/caps/caps.php
 * @brief   Hooks and function related to Caps menu entry.
 *
 * @ingroup PWWH_ADMIN_CAPS
 */

/**
 * @brief     Caps page related definition.
 * @{
 */
{
  /* Directory and URL. */
  define('PWWH_ADMIN_CAPS_URL', PWWH_ADMIN_URL . '/caps');
  define('PWWH_ADMIN_CAPS_DIR', PWWH_ADMIN_DIR . '/caps');

  /* Required capability to deal with this page. */
  define('PWWH_ADMIN_CAPS_CAPABILITY', 'update_core');

  /* Capability page slug. */
  define('PWWH_ADMIN_CAPS_PAGE_ID', PWWH_PREFIX . '_capabilities');
}
/** @} */

/**
 * @brief     Caps page inclusion block.
 * @{
 */
{
  require_once(PWWH_ADMIN_CAPS_DIR . '/class/walker-caps.php');
  require_once(PWWH_ADMIN_CAPS_DIR . '/caps-ui.php');
  require_once(PWWH_ADMIN_CAPS_DIR . '/caps-hook.php');
}
/** @} */

/**
 * @brief     Module caps definitions and registration block.
 * @{
 */
{
  $id = PWWH_ADMIN_CAPS_PAGE_ID;
  $label = __('Manage Capabilities', 'piwi-warehouse');
  $cap = PWWH_ADMIN_CAPS_CAPABILITY;
  $fill = 'pwwh_admin_caps_ui_page';
  $prio = 10;
  pwwh_admin_common_register_subpage($id, $label, $cap, $fill, $prio);
}
/** @} */