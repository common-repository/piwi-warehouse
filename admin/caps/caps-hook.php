<?php
/**
 * @file      caps/caps-hook.php
 * @brief     Hooks and function related to the Capability Page.
 *
 * @addtogroup PWWH_ADMIN
 * @{
 */

/**
 * @brief     Action triggered on submit of the Capability page.
 *
 * @hooked    admin_post_our_action_hook
 *
 * @return    void.
 */
function pwwh_admin_caps_submit_action() {

  /* Checking that user has capabilities to manage this. */
  if(pwwh_core_caps_api_current_user_can(PWWH_ADMIN_CAPS_CAPABILITY)) {
    if((isset($_POST[PWWH_ADMIN_CAPS_UI_SUBMIT])) &&
       ($_POST[PWWH_ADMIN_CAPS_UI_SUBMIT] == PWWH_ADMIN_CAPS_UI_SAVE)) {

      /* Revoking all capabilities. */
      pwwh_core_caps_api_revoke_all();

      /* Granting capabilities according to post data. */
      foreach($_POST as $data => $grant) {
        /* Valid input are expected in form of $role:$capability. */
        $data = explode(':', $data);
        if(count($data) == 2) {
          $role = $data[0];
          $cap = $data[1];
          /* Checking role existence. */
          if(is_a(get_role($role), 'WP_Role')) {
            pwwh_core_caps_api_add($role, $cap);
          }
          else {
            $msg = sprintf('Unexpected role in %s()', __FUNCTION__);
            pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
            $msg = sprintf('$role is %s', $role);
            pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
          }
        }
      }
    }
    else if((isset($_POST[PWWH_ADMIN_CAPS_UI_SUBMIT])) &&
            ($_POST[PWWH_ADMIN_CAPS_UI_SUBMIT] == PWWH_ADMIN_CAPS_UI_RESTORE)) {
      pwwh_core_api_set_default_caps();
    }
    else {
      $msg = sprintf('Unexpected trigger action in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    }
    $url = pwwh_admin_common_get_admin_url(PWWH_ADMIN_CAPS_PAGE_ID);
  }
  else {
    $url = admin_url();
    $msg = sprintf('Reached hook with no right in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }

  wp_redirect($url);
  exit;
}
add_action('admin_post_' . PWWH_ADMIN_CAPS_UI_ACTION, 
           'pwwh_admin_caps_submit_action');

/**
 * @brief     Capability UI common script ID.
 */
define('PWWH_ADMIN_CAPABILITY_UI_JS', 'pwwh_admin_capability_ui_js');


/**
 * @brief     Enqueues the scripts related to the capability page.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_admin_caps_enqueue_script() {

  /* Including Custom Media Script*/
  $id = PWWH_ADMIN_CAPABILITY_UI_JS;
  $url = PWWH_ADMIN_CAPS_URL . '/js/pwwh.admin.caps.ui.js';
  $deps = array('jquery');
  $ver = '20201122';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Localizing the script. */
  $data = array('ui' => array('box' => PWWH_ADMIN_CAPS_UI_BOX,
                              'level' => PWWH_ADMIN_CAPS_LEVEL_CLASS,
                              'wrap'  => PWWH_ADMIN_CAPS_WRAP_CLASS,
                              'switch' => PWWH_ADMIN_CAPS_SWITCH_CLASS));
  wp_localize_script($id, 'pwwh_admin_cap_ui_obj', $data);

  /* Including Back-end extra style for the the capability page. */
  $url = PWWH_ADMIN_CAPS_URL . '/css/caps.css';
  $deps = array();
  $ver = '20201121';
  wp_enqueue_style('pwwh-admin-caps-css', $url, $deps, $ver);
}
add_action('admin_enqueue_scripts', 'pwwh_admin_caps_enqueue_script');
/** @} */