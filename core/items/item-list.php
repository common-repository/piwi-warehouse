<?php
/**
 * @file      items/item-list.php
 * @brief     Hooks and function to manage Item list table.
 *
 * @addtogroup PWWH_ITEM_LIST
 * @{
 */

/**
 * @brief     Defines the capabilites related to the items and returns
 *            the array required to create the post type.
 *
 * @return    array the array of capabilities required by the
 *            register_post_type().
 */
function pwwh_core_item_lists_init() {

  /* Managing row actions. */
  {
    /* Removing quick edit row action. */
    pwwh_core_list_remove_row_action(PWWH_CORE_ITEM, 'inline hide-if-no-js');

    /* Registering Purchase Item row action. */
    $label = __('Purchase', 'piwi-warehouse');
    $args = array('id' => 'purchase',
                  'label' => $label,
                  'link' => 'pwwh_core_purchase_api_url_purchase_item',
                  'status' => array('publish'),
                  'cap' => PWWH_CORE_PURCHASE_CAPS_MANAGE_PURCHASES);
    pwwh_core_list_add_row_action(PWWH_CORE_ITEM, $args);

    /* Registering Move Item row action. */
    $label = __('Move', 'piwi-warehouse');
    $args = array('id' => 'move',
                  'label' => $label,
                  'link' => 'pwwh_core_movement_api_url_movement_item',
                  'status' => array('publish'),
                  'cap' => PWWH_CORE_MOVEMENT_CAPS_MANAGE_MOVEMENTS);
    pwwh_core_list_add_row_action(PWWH_CORE_ITEM, $args);
  }

  /* Managing bulk actions. */
  {
    /* Removing edit bulk action. */
    pwwh_core_list_remove_bulk_action(PWWH_CORE_ITEM, 'edit');

    /* Removing delete bulk action. */
    pwwh_core_list_remove_bulk_action(PWWH_CORE_ITEM, 'delete');
  }

  /* Managing WP List columns. */
  {
    /* Removing date column. */
    pwwh_core_list_remove_columns(PWWH_CORE_ITEM, 'date');

    /* Removing native Location column. */
    pwwh_core_list_remove_columns(PWWH_CORE_ITEM,
                                  'taxonomy-' . PWWH_CORE_ITEM_LOCATION);

    /* Removing native Type column. */
    pwwh_core_list_remove_columns(PWWH_CORE_ITEM,
                                  'taxonomy-' . PWWH_CORE_ITEM_TYPE);

    /* Adding a custom Location column. */
    $label = __('Locations', 'piwi-warehouse');
    $args = array('id' => 'location',
                  'label' => $label,
                  'content' => 'pwwh_core_item_list_location_content',
                  'sort' => false);
    pwwh_core_list_add_columns(PWWH_CORE_ITEM, $args);

    /* Adding a custom Type column. */
    $label = __('Types', 'piwi-warehouse');
    $args = array('id' => 'type',
                  'label' => $label,
                  'content' => 'pwwh_core_item_list_type_content',
                  'sort' => false);
    pwwh_core_list_add_columns(PWWH_CORE_ITEM, $args);

    /* Adding a custom Amount column. */
    $label = __('Amount', 'piwi-warehouse');
    $args = array('id' => 'amount',
                  'label' => $label,
                  'content' => 'pwwh_core_item_list_amount_content',
                  'sort' => false);
    pwwh_core_list_add_columns(PWWH_CORE_ITEM, $args);

    /* Adding a custom Availability column. */
    $label = __('Available', 'piwi-warehouse');
    $args = array('id' => 'avail',
                  'label' => $label,
                  'content' => 'pwwh_core_item_list_avail_content',
                  'sort' => false);
    pwwh_core_list_add_columns(PWWH_CORE_ITEM, $args);
  }
}

/**
 * @brief     Manage the content of the Location column.
 *
 * @param[in] int $post_id        The post ID.
 *
 * @return    void.
 */
function pwwh_core_item_list_location_content($post_id) {

  $args = array('linked' => true, 'echo' => true);
  pwwh_core_ui_tax_list(PWWH_CORE_ITEM_LOCATION, $post_id, $args);
}

/**
 * @brief     Manage the content of the Type column.
 *
 * @param[in] int $post_id        The post ID.
 *
 * @return    void.
 */
function pwwh_core_item_list_type_content($post_id) {
  $args = array('linked' => true, 'echo' => true);
  pwwh_core_ui_tax_list(PWWH_CORE_ITEM_TYPE, $post_id, $args);
}

/**
 * @brief     Manage the content of the 'Amount' column.
 *
 * @param[in] int $post_id        The post ID.
 *
 * @return    void.
 */
function pwwh_core_item_list_amount_content($post_id) {
  $amount = pwwh_core_item_api_get_amount($post_id);
  if($amount) {
    echo $amount;
  }
  else {
    echo '0';
  }
}

/**
 * @brief     Manage the content of the 'Avail' column.
 *
 * @param[in] int $post_id        The post ID.
 *
 * @return    void.
 */
function pwwh_core_item_list_avail_content($post_id) {
  $avail = pwwh_core_item_api_get_avail($post_id);
  if($avail) {
    echo $avail;
  }
  else {
    echo '0';
  }
}
/** @} */