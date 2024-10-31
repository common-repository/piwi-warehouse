<?php
/**
 * @file        admin/caps/caps-ui.php
 * @brief       This file contains all the code related to Capabilities page UI.
 *
 * @ingroup     PWWH_CAPS
 * @{
 */

/**
 * @brief     Capability manager box identifier.
 */
define('PWWH_ADMIN_CAPS_UI_BOX', PWWH_PREFIX . '_capabilities');

/**
 * @brief     Capbility switch class.
 */
define('PWWH_ADMIN_CAPS_SWITCH_CLASS', PWWH_PREFIX . '-capability-switch');

/**
 * @brief     Capbility level class.
 */
define('PWWH_ADMIN_CAPS_LEVEL_CLASS', PWWH_PREFIX . '-capability-level');

/**
 * @brief     Capbility wrap class.
 */
define('PWWH_ADMIN_CAPS_WRAP_CLASS', PWWH_PREFIX . '-capability-wrap');

/**
 * @brief     Returns a capability box according to the context and the role.
 *
 * @param[in] mixed $context      The context.
 * @param[in] mixed $role         The role.
 *
 * @return    string the capability box as HTML.
 */
function pwwh_admin_caps_ui_capsbox($context, $role) {

  /* Getting all the registered capabilities for this context. */
  $caps = pwwh_core_caps_api_get_caps_by_context($context);

  if(count($caps)) {

    /* Assembling the title. */
    {
      $label = pwwh_core_caps_api_get_context_data_by(PWWH_CORE_CAPS_KEY_CNTX_LABEL,
                                                      $context);
      $_title = '<span class="group-title">' . $label . '</span>';
    }

    /* Assembling the inner. */
    {
      /* Composing the object array to walk it. */
      foreach($caps as $key => $cap) {
        $dep = pwwh_core_caps_api_get_cap_data_by(PWWH_CORE_CAPS_KEY_CAP_DEP, $cap);
        $caps[$key] = (object) array('slug' => $cap,
                                     'dependency' => $dep);
      }

      /* Composing the inner. */
      $args = array('role' => $role);
      $walker = new pwwh_walker_caps();
      $_inner = '<ul class="capabilities">' .
                  $walker->walk($caps, 0, $args) .
                '</ul>';
    }

    /* Composing the output. */
    $_classes = implode(' ', array('group', $context, $role));
    $output = '<div class="' . $_classes . '">' .
                $_title . $_inner .
              '</div>';
  }
  else {
    $output = '';
  }

  return $output;
}

/**
 * @brief     Displays the role capability flexbox inner.
 *
 * @param[in] mixed $role         The role object or the slug to edit.
 *
 * @return    void.
 */
function pwwh_admin_caps_ui_role_flexbox($role) {

  if(is_a($role, 'WP_Role')) {
    $role = $role->name;
  }

  /* Getting roles. */
  global $wpdb;
  $table_name = $wpdb->prefix;
  $roles = get_option($table_name . 'user_roles');

  if(is_string($role) && isset($roles[$role])) {

    $contexts = pwwh_core_caps_api_get_all_contexts();

    $output = '';
    foreach($contexts as $context) {
      $output .= pwwh_admin_caps_ui_capsbox($context, $role);
    }
  }
  else {
    $msg = 'Unexpected $role type in pwwh_common_ui_cap_flexbox()';
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
  return $output;
}

/**
 * @brief     Action associated to the form.
 */
define('PWWH_ADMIN_CAPS_UI_ACTION', PWWH_PREFIX . '_admin_caps_action');

/**
 * @brief     Submit button identifiers and values.
 * @{
 */
define('PWWH_ADMIN_CAPS_UI_SUBMIT', PWWH_PREFIX . '_admin_caps_submit');
define('PWWH_ADMIN_CAPS_UI_SAVE', PWWH_PREFIX . '_save');
define('PWWH_ADMIN_CAPS_UI_RESTORE', PWWH_PREFIX . '_restore');
/** @} */

/**
 * @brief     Displays the capability page.
 *
 * @return    void.
 */
function pwwh_admin_caps_ui_page() {

  /* Getting roles. */
  global $wpdb;
  $table_name = $wpdb->prefix;
  $roles = get_option($table_name . 'user_roles');

  /* Generating title of this page. */
  $label = __('Piwi Warehouse Capabilities', 'piwi-warehouse');
  $_title = pwwh_lib_ui_admin_page_title($label, false);

  /* Generating description. */
  $desc = __('Use this box to assign capabilities to user roles. This helps ' .
             'administrator to decide who can do what. Some default ' .
             'capabilities are already assigned: there is no need to change ' .
             'settings unless you have to match specific needs.',
             'piwi-warehouse');
  $_description = '<span class="pwwh-page-description">' . $desc . '</span>';

  /* Composing action. */
  $url = admin_url('admin-post.php');
  $action = '<input type="hidden" name="action"
                    value="' . PWWH_ADMIN_CAPS_UI_ACTION . '">';

  /* Composing form inner. */
  $context = 'pwwh_flex_caps';
  foreach($roles as $role => $role_info) {

    $cap_box_label = $role_info['name'];
    pwwh_lib_ui_flexboxes_add_flexbox('pwwh_cap_' . $role, $cap_box_label,
                                      'pwwh_admin_caps_ui_role_flexbox',
                                      array($role), $context, 10, 'widebox');
  }
  $inner = pwwh_lib_ui_flexboxes_do_flexbox_area($context, false);

  /* Composing button box. */
  $args = array('type' => 'submit',
                'id' => PWWH_ADMIN_CAPS_UI_SUBMIT,
                'value' => PWWH_ADMIN_CAPS_UI_SAVE,
                'classes' => 'pwwh-save pwwh-primary',
                'label' => __('Save Capabilities', 'piwi-warehouse'),
                'echo' => false);
  $save = pwwh_lib_ui_form_button($args);
  $args = array('type' => 'submit',
                'id' => PWWH_ADMIN_CAPS_UI_SUBMIT,
                'value' => PWWH_ADMIN_CAPS_UI_RESTORE,
                'classes' => 'pwwh-restore',
                'label' => __('Restore Default', 'piwi-warehouse'),
                'echo' => false);
  $restore = pwwh_lib_ui_form_button($args);
  $button_box = '<div class="pwwh-line pwwh-lib-buttons">' .
                  $save . $restore .
                '</div>';

  /* Assembling form. */
  $_form = '<form id="' . PWWH_ADMIN_CAPS_UI_BOX . '"
                  action="' . $url . '" method="post">' .
              $action .
              $inner .
              $button_box .
            '</form>';

  /* Composing output and echoing. */
  echo ($_title . $_description . $_form);
}
/** @} */