<?php
/**
 * @file    core/capabilities/caps.php
 * @brief   This file contains all the inclusion related to Capbilities Engine.
 *
 * @addtogroup  PWWH_CORE
 * @{
 */

/**
 * @brief   Capabilities DBrevision number.
 * @details This is used to detect whereas the DB should be intialized or not.
 *          This number should be increased every time the list of registered
 *          capabilities changes
 */
define('PWWH_CORE_CAPS_REV', 'rev03-a');

/**
 * @brief   Capbilities Engine inclusion block.
 */
{
  require_once(PWWH_CORE_CAPS_DIR . '/caps-api.php');
}

/* Initialazing this module. */
pwwh_core_caps_api_init();
/** @} */