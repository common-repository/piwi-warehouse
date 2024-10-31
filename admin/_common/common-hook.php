<?php
/**
 * @file    common/common.php
 * @brief   This file contains common defines and hooks.
 *
 * @addtogroup PWWH_ADMIN
 * @{
 */

/**
 * @brief     Generates the whole admin menu related to Blinking Theme.
 *
 * @hooked    admin_menu
 *
 * @return    void
 */
function pwwh_admin_common_manage_menu() {

  $subs = pwwh_admin_common_get_subpages();

  if(count($subs)) {

    /* Adding top level menu page. */
    $id = pwwh_admin_common_get_menu_id();
    $label = pwwh_admin_common_get_menu_label();
    $cap = 'read';
    $icon = pwwh_admin_common_get_menu_icon();
    $prio = pwwh_admin_common_get_menu_prio();
    $fill = pwwh_admin_common_get_menu_fill();

    if(is_callable($fill)) {
      add_menu_page($label, $label, $cap, $id, $fill, 
              $icon, $prio);
    }
    else {
      add_menu_page($label, $label, $cap, $fill, '', 
                    $icon, $prio);
    }

    foreach($subs as $sub) {

      $label = pwwh_admin_common_get_subpage_label($sub);
      $cap = pwwh_admin_common_get_subpage_caps($sub);
      $prio = pwwh_admin_common_get_subpage_prio($sub);
      $fill = pwwh_admin_common_get_subpage_fill($sub);
      $parent = pwwh_admin_common_get_subpage_parent($sub);

      if(is_callable($fill)) {
        add_submenu_page($parent, $label, $label, $cap, $sub, $fill, $prio);
      }
      else {
        add_submenu_page($parent, $label, $label, $cap, $fill, '', $prio);
      }
    }
  }
}
add_action('admin_menu', 'pwwh_admin_common_manage_menu');
/** @} */