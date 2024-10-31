<?php
/**
 * @file    admin/main/main-ui.php
 * @brief   UI function related to Main menu entry.
 *
 * @ingroup PWWH_ADMIN_MAIN
 */

/**
 * @brief     Displays the item flexbox inner.
 *
 * @return    void.
 */
function pwwh_admin_main_ui_item_flexbox() {
  $desc = __('The Item is the individual article which can be bought, lent ' .
             'or donated. An Item has a name, a description and a photo.' .
             'The Items can be organized by types and by locations.',
             'piwi-warehouse');

  $add_new_title = __('Add a new Item', 'piwi-warehouse');
  $args = array('post_type' => PWWH_CORE_ITEM);
  $add_new_url = pwwh_core_api_admin_url_post_new($args);
  $args = array('link' => $add_new_url,
                'value' => $add_new_title,
                'icon' => 'dashicons-plus',
                'class' => 'pwwh-item-new',
                'cap' => PWWH_CORE_ITEM_CAPS_MANAGE_ITEMS);
  $add_new = pwwh_lib_ui_admin_info_chunk($args, false);

  $see_all_title = __('See all the Items', 'piwi-warehouse');
  $args = array('post_type' => PWWH_CORE_ITEM);
  $see_all_url = pwwh_core_api_admin_url_edit($args);
  $args = array('link' => $see_all_url,
                'value' => $see_all_title,
                'icon' => 'dashicons-carrot',
                'class' => 'pwwh-item-all',
                'cap' => PWWH_CORE_ITEM_CAPS_MANAGE_ITEMS);
  $see_all = pwwh_lib_ui_admin_info_chunk($args, false);

  $manage_loc_title = __('Manage Locations', 'piwi-warehouse');
  $args = array('post_type' => PWWH_CORE_ITEM,
                'taxonomy' => PWWH_CORE_ITEM_LOCATION);
  $manage_loc_url = pwwh_core_api_admin_url_edit_tags($args);
  $args = array('link' => $manage_loc_url,
                'value' => $manage_loc_title,
                'icon' => 'dashicons-location',
                'class' => 'pwwh-location-manage',
                'cap' => PWWH_CORE_ITEM_CAPS_MANAGE_LOCATIONS);
  $manage_loc = pwwh_lib_ui_admin_info_chunk($args, false);

  $manage_type_title = __('Manage Item Types', 'piwi-warehouse');
  $args = array('post_type' => PWWH_CORE_ITEM,
                'taxonomy' => PWWH_CORE_ITEM_TYPE);
  $manage_type_url = pwwh_core_api_admin_url_edit_tags($args);
  $args = array('link' => $manage_type_url,
                'value' => $manage_type_title,
                'icon' => 'dashicons-tag',
                'class' => 'pwwh-item-all',
                'cap' => PWWH_CORE_ITEM_CAPS_MANAGE_TYPES);
  $manage_type = pwwh_lib_ui_admin_info_chunk($args, false);

  $output = '<div class="description"> ' .
              $desc .
            '</div>
            <div class="actions">' .
              $add_new .
              $see_all .
              $manage_loc .
              $manage_type .
            '</div>';
  return $output;
}

/**
 * @brief     Displays the purchase flexbox inner.
 *
 * @return    void.
 */
function pwwh_admin_main_ui_flexbox_purchase() {
  $desc = __('The Purchase increases the amount of an Item in the Warehouse. ' .
             'This movement should be do when you are moving a certain ' .
             'quantity of an Item from the outside to the warehouse.',
             'piwi-warehouse');

  $add_new_title = __('Add a new Purchase', 'piwi-warehouse');
  $args = array('post_type' => PWWH_CORE_PURCHASE);
  $add_new_url = pwwh_core_api_admin_url_post_new($args);
  $args = array('link' => $add_new_url,
                'value' => $add_new_title,
                'icon' => 'dashicons-plus',
                'class' => 'pwwh-purchase-new',
                'cap' => PWWH_CORE_PURCHASE_CAPS_MANAGE_PURCHASES);
  $add_new = pwwh_lib_ui_admin_info_chunk($args, false);

  $see_all_title = __('See all the Purchases', 'piwi-warehouse');
  $args = array('post_type' => PWWH_CORE_PURCHASE);
  $see_all_url = pwwh_core_api_admin_url_edit($args);
  $args = array('link' => $see_all_url,
                'value' => $see_all_title,
                'icon' => 'dashicons-cart',
                'class' => 'pwwh-purchase-all',
                'cap' => PWWH_CORE_PURCHASE_CAPS_MANAGE_PURCHASES);
  $see_all = pwwh_lib_ui_admin_info_chunk($args, false);

  $output = '<div class="description"> ' .
              $desc .
            '</div>
            <div class="actions">' .
              $add_new .
              $see_all .
            '</div>';
  return $output;
}

/**
 * @brief     Displays the movement flexbox inner.
 *
 * @return    void.
 */
function pwwh_admin_main_ui_flexbox_movement() {
  $desc = __('The Movement is the an operation that allows you to '.
             'displace Items. Use this to move Item from ' .
             'the Warehouse to an Holder and to take note of the quantity ' .
             'moved, lent, donated or loss.', 'piwi-warehouse');

  $add_new_title = __('Add a new Movement', 'piwi-warehouse');
  $add_new_url = pwwh_core_movement_api_url_movement_item();
  $args = array('link' => $add_new_url,
                'value' => $add_new_title,
                'icon' => 'dashicons-plus',
                'class' => 'pwwh-movement-new',
                'cap' => PWWH_CORE_MOVEMENT_CAPS_MANAGE_MOVEMENTS);
  $add_new = pwwh_lib_ui_admin_info_chunk($args, false);

  $see_all_title = __('See all the Movements', 'piwi-warehouse');
  $args = array('post_type' => PWWH_CORE_MOVEMENT);
  $see_all_url = pwwh_core_api_admin_url_edit($args);
  $args = array('link' => $see_all_url,
                'value' => $see_all_title,
                'icon' => 'dashicons-migrate',
                'class' => 'pwwh-movements-all',
                'cap' => PWWH_CORE_MOVEMENT_CAPS_MANAGE_MOVEMENTS);
  $see_all = pwwh_lib_ui_admin_info_chunk($args, false);

  $manage_holder_title = __('Manage Holders', 'piwi-warehouse');
  $args = array('post_type' => PWWH_CORE_MOVEMENT,
                'taxonomy' => PWWH_CORE_MOVEMENT_HOLDER);
  $manage_holder_url = pwwh_core_api_admin_url_edit_tags($args);
  $args = array('link' => $manage_holder_url,
                'value' => $manage_holder_title,
                'icon' => 'dashicons-admin-users',
                'class' => 'pwwh-holder-manage',
                'cap' => PWWH_CORE_MOVEMENT_CAPS_MANAGE_HOLDERS);
  $manage_holder = pwwh_lib_ui_admin_info_chunk($args, false);

  $output = '<div class="description"> ' .
              $desc .
            '</div>
            <div class="actions">' .
              $add_new .
              $see_all .
              $manage_holder .
            '</div>';
  return $output;
}

/**
 * @brief     Displays the tools flexbox inner.
 *
 * @return    void.
 */
function pwwh_admin_main_ui_flexbox_tools() {
  $desc = __('Utilities and tools for this plugin.',
             'piwi-warehouse');

  $consistency_title = __('Consistency Checker', 'piwi-warehouse');
  $consistency_url = pwwh_admin_common_get_admin_url(PWWH_ADMIN_CONSISTENCY_PAGE_ID);
  $args = array('link' => $consistency_url,
                'value' => $consistency_title,
                'icon' => 'dashicons-forms',
                'class' => 'pwwh-consistency',
                'cap' => PWWH_ADMIN_CONSISTENCY_CAPABILITY);
  $consistency = pwwh_lib_ui_admin_info_chunk($args, false);

  $output = '<div class="description"> ' .
              $desc .
            '</div>
            <div class="actions">' .
               $consistency .
            '</div>';
  return $output;
}

/**
 * @brief     Displays the main menu page.
 *
 * @return    void.
 */
function pwwh_admin_main_ui() {
  $label = __('Piwi Warehouse', 'piwi-warehouse');
  pwwh_lib_ui_admin_page_title($label, true);

  pwwh_lib_ui_flexboxes_add_flexbox('item_box', __('Items', 'piwi-warehouse'),
                                    'pwwh_admin_main_ui_item_flexbox', null,
                                    'pwwh_admin_page', 1, '',
                                    PWWH_CORE_ITEM_CAPS_MANAGE_ITEMS);
  pwwh_lib_ui_flexboxes_add_flexbox('purchase_box',
                                    __('Purchases', 'piwi-warehouse'),
                                    'pwwh_admin_main_ui_flexbox_purchase',
                                    null, 'pwwh_admin_page', 2, '',
                                    PWWH_CORE_PURCHASE_CAPS_MANAGE_PURCHASES);
  pwwh_lib_ui_flexboxes_add_flexbox('movement_box',
                                    __('Movements', 'piwi-warehouse'),
                                    'pwwh_admin_main_ui_flexbox_movement',
                                    null, 'pwwh_admin_page', 3, '',
                                    PWWH_CORE_MOVEMENT_CAPS_MANAGE_MOVEMENTS);
  pwwh_lib_ui_flexboxes_add_flexbox('tools_box', __('Tools', 'piwi-warehouse'),
                                    'pwwh_admin_main_ui_flexbox_tools', null,
                                    'pwwh_admin_page', 4, '',
                                    'update_core');
  pwwh_lib_ui_flexboxes_do_flexbox_area('pwwh_admin_page');
}