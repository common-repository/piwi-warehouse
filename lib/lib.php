<?php
/**
 * @file    lib/lib.php
 * @brief   This file contains all the inclusion related to library files.
 *
 * @addtogroup PWWH_LIB
 * @{
 */

/**
 * @brief   Modules inclusion block.
 * @{
 */
/* Including Logger module. */
foreach (glob(PWWH_LIB_DIR . "/logger/*.php") as $filename)
{
  include_once $filename;
}
/* Including Library UI module. */
foreach (glob(PWWH_LIB_DIR . "/ui/*.php") as $filename)
{
  include_once $filename;
}
/* Including Library Utils module. */
foreach (glob(PWWH_LIB_DIR . "/utils/*.php") as $filename)
{
  include_once $filename;
}
/** @} */

/**
 * @brief     Main lib backend CSS.
 */
define('PWWH_LIB_BACKEND_CSS', 'pwwh_lib_backend_css');

/**
 * @brief     Enqueues backend related stylesheets.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_lib_enqueue_backend_style() {

  /* Adding backend stylesheet. */
  $id = PWWH_LIB_BACKEND_CSS;
  $url = PWWH_LIB_URL . '/css/lib-backend.css';
  $deps = array();
  $ver = '20230926';
  wp_enqueue_style($id, $url, $deps, $ver);
}
add_action('admin_enqueue_scripts', 'pwwh_lib_enqueue_backend_style');

/**
 * @brief     UI Submit Div JS.
 */
define('PWWH_LIB_UI_SUBMITDIV_JS', 'pwwh_lib_ui_submitdiv_js');

/**
 * @brief     UI Form JS.
 */
define('PWWH_LIB_UI_FORM_JS', 'pwwh_lib_ui_form_js');

/**
 * @brief     Enqueues backend related scripts.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_lib_enqueue_backend_script() {

  /* Enqueue Submit Div Postbox js. */
  $id = PWWH_LIB_UI_SUBMITDIV_JS;
  $url = PWWH_LIB_URL . '/js/postbox.submitdiv.js';
  $deps = array('jquery');
  $ver = '20201020';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Localizing the script. */
  $data = array('validate_ajax_url' => admin_url('admin-ajax.php'),
                'save_label' => __('Save', 'piwi-library'),
                'saveas_label' => __('Save as %s', 'piwi-library'),
                'publish_label' => __('Publish', 'piwi-library'),
                'update_label' => __('Update', 'piwi-library'));
  wp_localize_script($id, 'pwwh_submitdiv_obj', $data);

  /* Enqueue UI Form js. */
  $id = PWWH_LIB_UI_FORM_JS;
  $url = PWWH_LIB_URL . '/js/ui.form.js';
  $deps = array('jquery');
  $ver = '20201020';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Localizing the script. */
  $data = array('switch' => 'pwwh-lib-switch input');
  wp_localize_script($id , 'pwwh_lib_ui_obj', $data);
}
add_action('admin_enqueue_scripts', 'pwwh_lib_enqueue_backend_script');

/**
 * @brief     This handler is used to validate date of Submit Postbox.
 *
 * @hooked    wp_ajax_pwwh_validate_date
 *
 * @return    void
 */
function pwwh_validate_date_handler() {
  $year = intval(pwwh_lib_utils_validate_array_field($_POST, 'pwwh_year', -1));
  $month = intval(pwwh_lib_utils_validate_array_field($_POST, 'pwwh_month', -1));
  $day = intval(pwwh_lib_utils_validate_array_field($_POST, 'pwwh_day', -1));
  $hour = intval(pwwh_lib_utils_validate_array_field($_POST, 'pwwh_hour', -1));
  $minute = intval(pwwh_lib_utils_validate_array_field($_POST, 'pwwh_minute', -1));
  $format = pwwh_lib_utils_validate_array_field($_POST, 'pwwh_format', 'M j, Y @ H:i');

  $date_valid = checkdate($month, $day, $year);
  if(($hour >= 0) && ($hour < 24) && ($minute >= 0) && ($minute < 60)) {
    $time_valid = true;
  }
  else {
    $time_valid = false;
  }

  if(!$date_valid && !$time_valid) {
    echo json_encode('BOTH_INVALID');
  }
  else if(!$time_valid) {
    echo json_encode('TIME_INVALID');
  }
  else if(!$date_valid) {
    echo json_encode('DATE_INVALID');
  }
  else {
    $date = $hour . ':' . $minute . ' ' . $year . '-' . $month . '-' . $day;
    $datestamp = strtotime($date);
    $newdate = date($format, $datestamp);
    echo json_encode($newdate);
  }
  /* All ajax handlers should die when finished */
  wp_die();
}
add_action('wp_ajax_pwwh_validate_date',
           'pwwh_validate_date_handler');