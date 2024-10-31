<?php
/**
 * @file    admin/consistency/consistency.php
 * @brief   Hooks and function related to the consistency check tool.
 *
 * @ingroup PWWH_ADMIN_CONSISTENCY
 */

/**
 * @brief     Main menu related definition.
 * @{
 */
{
  /* Directory and URL. */
  define('PWWH_ADMIN_CONSISTENCY_DIR', PWWH_ADMIN_DIR . '/consistency');
  define('PWWH_ADMIN_CONSISTENCY_URL', PWWH_ADMIN_URL . '/consistency');

  /* Main page identifier, label and icon. */
  define('PWWH_ADMIN_CONSISTENCY_PAGE_ID', PWWH_PREFIX . '_consistency');

  /* Required capability to deal with this page. */
  define('PWWH_ADMIN_CONSISTENCY_CAPABILITY', 'update_core');
}
/** @} */

/**
 * @brief     Main menu inclusion block.
 * @{
 */
{
  require_once(PWWH_ADMIN_CONSISTENCY_DIR . '/consistency-hook.php');
  require_once(PWWH_ADMIN_CONSISTENCY_DIR . '/consistency-ui.php');
}
/** @} */

/**
 * @brief     Module main definitions and registration block.
 * @{
 */
{
  $id = PWWH_ADMIN_CONSISTENCY_PAGE_ID;
  $label = __('Consistency Tool', 'piwi-warehouse');
  $cap = PWWH_ADMIN_CONSISTENCY_CAPABILITY;
  $fill = 'pwwh_admin_consistency_ui_page';
  $prio = 10;
  pwwh_admin_common_register_subpage($id, $label, $cap, $fill, $prio, false);
}
/** @} */