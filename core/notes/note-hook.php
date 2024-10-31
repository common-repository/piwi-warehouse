<?php
/**
 * @file      notes/notes-hook.php
 * @brief     Hooks and function related to notes.
 *
 * @addtogroup PWWH_CORE_NOTE
 * @{
 */

/**
 * @brief     Initialized the capabilities related to the notes.
 *
 * @hooked    init
 *
 * @return    void
 */
function pwwh_core_note_init() {

  pwwh_core_note_caps_register();
}
add_action('init', 'pwwh_core_note_init');

/**
 * @brief     Notes style ID defines.
 * @{
 */
define('PWWH_CORE_NOTE_CSS', 'pwwh_core_note_css');
/** @} */

/**
 * @brief     Enqueues notes style.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_note_enqueue_style() {

  /* Enqueue Notes Custom Style. */
  $id = PWWH_CORE_NOTE_CSS;
  $url = PWWH_CORE_NOTE_URL . "/css/note.css";
  $deps = array();
  $ver = '20201120';
  wp_enqueue_style($id, $url, $deps, $ver);
}
add_action('admin_enqueue_scripts', 'pwwh_core_note_enqueue_style');
/** @} */