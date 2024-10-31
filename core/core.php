<?php
/**
 * @file    core/core.php
 * @brief   This file contains all the inclusion related to Warehose core.
 *
 * @addtogroup  PWWH_CORE
 * @{
 */

/**
 * @brief    Core Directories.
 * @{
 */
define('PWWH_CORE_CAPS_DIR', PWWH_CORE_DIR . '/capabilities');
define('PWWH_CORE_ITEM_DIR', PWWH_CORE_DIR . '/items');
define('PWWH_CORE_NOTE_DIR', PWWH_CORE_DIR . '/notes');
define('PWWH_CORE_LIST_DIR', PWWH_CORE_DIR . '/lists');
define('PWWH_CORE_MOVEMENT_DIR', PWWH_CORE_DIR . '/movements');
define('PWWH_CORE_PURCHASE_DIR', PWWH_CORE_DIR . '/purchases');
/** @} */

/**
 * @brief    Core URls.
 * @{
 */
define('PWWH_CORE_CAPS_URL', PWWH_CORE_URL . '/capabilities');
define('PWWH_CORE_ITEM_URL', PWWH_CORE_URL . '/items');
define('PWWH_CORE_NOTE_URL', PWWH_CORE_URL . '/notes');
define('PWWH_CORE_LIST_URL', PWWH_CORE_URL . '/lists');
define('PWWH_CORE_MOVEMENT_URL', PWWH_CORE_URL . '/movements');
define('PWWH_CORE_PURCHASE_URL', PWWH_CORE_URL . '/purchases');
/** @} */

/**
 * @brief    Core inclusion block.
 * @{
 */
{
  /* Including Core common code. */
  require_once(PWWH_CORE_DIR . '/core-api.php');
  require_once(PWWH_CORE_DIR . '/core-hook.php');
  require_once(PWWH_CORE_DIR . '/core-ui.php');

  /* Including Capabilities Engine. */
  require_once(PWWH_CORE_CAPS_DIR . '/caps.php');

  /* Including List Engine. */
  require_once(PWWH_CORE_LIST_DIR . '/list.php');

  /* Including Notes Engine. */
  require_once(PWWH_CORE_NOTE_DIR . '/note.php');

  /* Post types and related. */
  require_once(PWWH_CORE_ITEM_DIR . '/item.php');
  require_once(PWWH_CORE_MOVEMENT_DIR . '/movement.php');
  require_once(PWWH_CORE_PURCHASE_DIR . '/purchase.php');
}
/** @} */

/** @} */