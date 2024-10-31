<?php
/**
 * @file    admin/common/common.php
 * @brief   This file contains all the admin menu common handlers.
 *
 * @ingroup PWWH_ADMIN
 */

/**
 * @brief   Admin/_Common Directory and URL.
 * @{
 */
define('PWWH_ADMIN_COMMON_DIR', PWWH_ADMIN_DIR . '/_common');
define('PWWH_ADMIN_COMMON_URL', PWWH_ADMIN_URL . '/_common');
/** @} */

/**
 * @brief    Admin/_Common inclusion block.

 */
{
  require_once(PWWH_ADMIN_COMMON_DIR . '/common-api.php');
  require_once(PWWH_ADMIN_COMMON_DIR . '/common-hook.php');
}

/* Initialazing this module. */
pwwh_admin_common_init();
/** @} */