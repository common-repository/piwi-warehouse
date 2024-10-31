<?php
/**
 * @file    notes/notes.php
 * @brief   This file contains all the inclusion related to notes.
 *
 * @addtogroup  PWWH_CORE_NOTE
 * @{
 */

/**
 * @brief     Movement defines.
 * @{
 */
define('PWWH_CORE_NOTE', PWWH_PREFIX . '_note');
define('PWWH_CORE_NOTE_MAX_DEPTH', 3);
/** @} */

/**
 * @brief     Inclusion block.
 * @{
 */
require_once(PWWH_CORE_NOTE_DIR . '/class/walker-notes.php');
require_once(PWWH_CORE_NOTE_DIR . '/note-api.php');
require_once(PWWH_CORE_NOTE_DIR . '/note-caps.php');
require_once(PWWH_CORE_NOTE_DIR . '/note-hook.php');
require_once(PWWH_CORE_NOTE_DIR . '/note-ui.php');
require_once(PWWH_CORE_NOTE_DIR . '/note-ajax.php');
/** @} */

/** @} */