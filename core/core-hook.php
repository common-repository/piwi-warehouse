<?php
/**
 * @file      core/core-hook.php
 * @brief     Hooks and function common across the core.
 *
 * @addtogroup PWWH_CORE
 * @{
 */

/**
 * @brief     jQuery validate script identifier.
 */
define('PWWH_CORE_VALIDATE_JS', PWWH_PREFIX . '_core_validate_js');

/**
 * @brief     jQuery validate extender script identifier.
 * @details   This script provides extra validation method used from the
 *            entire system.
 */
define('PWWH_CORE_EXTEND_VALIDATE_JS', PWWH_PREFIX . '_core_ext_validate_js');

/**
 * @brief     Core script identifier.
 * @details   This script provides extra functions used from the entire system
 *            such the Post Submit Div handler.
 */
define('PWWH_CORE_JS', PWWH_PREFIX . '_core_js');

/**
 * @brief     Enqueues core js.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_enqueue_scripts() {

  /* JQuery Validate. */
  $id = PWWH_CORE_VALIDATE_JS;
  $url = PWWH_CORE_URL . '/js/jquery.validate.min.js';
  $deps = array('jquery');
  $ver = '20201204';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Core JSValidate extension. */
  $id = PWWH_CORE_EXTEND_VALIDATE_JS;
  $url = PWWH_CORE_URL . '/js/pwwh.core.extend.validate.js';
  $deps = array(PWWH_CORE_VALIDATE_JS);
  $ver = '20201204';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Core JS functions. */
  $id = PWWH_CORE_JS;
  $url = PWWH_CORE_URL . '/js/pwwh.core.js';
  $deps = array(PWWH_CORE_EXTEND_VALIDATE_JS);
  $ver = '20201204';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Localizing the script. */
  $data = array('update' => __('Update'),
                'publish' => __('Publish'));
  wp_localize_script($id, 'pwwh_core_obj', $data);
}
add_action('admin_enqueue_scripts', 'pwwh_core_enqueue_scripts');

/**
 * @brief     Main core CSS.
 */
define('PWWH_CORE_CSS', PWWH_PREFIX . '_core_css');

/**
 * @brief     Main jQuery Validator CSS.
 */
define('PWWH_CORE_VALIDATE_CSS', PWWH_PREFIX . '_core_validate_css');

/**
 * @brief     Enqueues core style.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_enqueue_style() {

  /* Enqueue Core Custom Style. */
  $id = PWWH_CORE_CSS;
  $url = PWWH_CORE_URL . '/css/core.css';
  $deps = array();
  $ver = '20201029';
  wp_enqueue_style($id, $url, $deps, $ver);

  /* Enqueue JQuery Validate Custom Style. */
  $id = PWWH_CORE_VALIDATE_CSS;
  $url = PWWH_CORE_URL . '/css/validate.css';
  $deps = array();
  $ver = '20201029';
  wp_enqueue_style($id, $url, $deps, $ver);
}
add_action('admin_enqueue_scripts', 'pwwh_core_enqueue_style');

/**
 * @brief     Autoset capabilities and update capability engine revision.
 *
 * @hooked    init
 *
 * @return    void.
 */
function pwwh_core_manage_capabilities_update() {

  if(pwwh_core_api_shall_caps_be_updated()) {

    /* Note that this call also updated the capability revision. */
    pwwh_core_api_set_default_caps();
  }
}
add_action('init', 'pwwh_core_manage_capabilities_update', 20);

/**
 * @brief     Add an autotitle for Movements and Purchases.
 * @notes     The auto title is save as the combination of the post type
 *            and the post ID. This title has nothing to do with what
 *            displayed.
 *
 * @param[in] array $data         Post data
 * @param[in] array $postarr      $_POST array
 *
 * @hooked    wp_insert_post_data
 *
 * @return    array the filtered post data
 */
function pwwh_core_manage_autotitle($data, $postarr) {

  $post_type = $postarr['post_type'];
  if(($post_type == PWWH_CORE_MOVEMENT) || ($post_type == PWWH_CORE_PURCHASE)) {

    $post_status = $postarr['post_status'];
    if($post_status != 'auto-draft') {
      $post_date = $postarr['post_date'];
      $title = $post_type . '-' . $post_date;
      $data['post_title'] = $title;
      $data['post_name'] = sanitize_text_field($title);
    }
  }
  return $data;
}
add_filter('wp_insert_post_data', 'pwwh_core_manage_autotitle', 10, 2);

/**
 * @brief     Generates a display title for Movements and Purchases.
 *            This title is different from that stored in the DB. This because
 *            the title will contain a date and depend on the current user
 *            settings.
 *
 * @param[in] string $title       The original title.
 * @param[in] string $post_id     The post ID.
 *
 * @hooked    the_title
 *
 * @return    string the beautified title as HTML.
 */
function pwwh_core_beautify_autotitle($title, $post_id) {

  $post = get_post($post_id);
  if($post) {
    $post_type = get_post_type($post);

    if(($post_type == PWWH_CORE_MOVEMENT) ||
      ($post_type == PWWH_CORE_PURCHASE)) {
      /* Getting current date format. */
      $format = get_option('date_format');
      $datestamp = get_the_date($format, $post);
      $format = get_option('time_format');
      $timestamp = get_the_date($format, $post);

      /* Getting singular name label. */
      $post_type_obj = get_post_type_object($post_type);
      $label = $post_type_obj->labels->singular_name;

      $title = sprintf(__('%s of %s (%s)', 'piwi-warehouse'), ucfirst($label),
                       $datestamp, $timestamp);
    }
  }
  return $title;
}
add_filter('the_title', 'pwwh_core_beautify_autotitle', 1, 2);
/** @} */