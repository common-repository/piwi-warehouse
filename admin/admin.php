<?php
/**
 * @file    admin/admin.php
 * @brief   This file contains all the inclusion related to admin menu
 *          management.
 *
 * @addtogroup  PWWH_ADMIN
 * @{
 */

/**
 * @brief   Admin inclusion block.
 * @{
 */

/* Including common API and Hooks. */
require_once(PWWH_ADMIN_DIR . '/_common/common.php');

/* Including the Main menu entry. */
require_once(PWWH_ADMIN_DIR . '/_main/main.php');

/* Including the Capability page. */
require_once(PWWH_ADMIN_DIR . '/caps/caps.php');

/* Including the Consistency page. */
require_once(PWWH_ADMIN_DIR . '/consistency/consistency.php');
/** @} */

/** @} */