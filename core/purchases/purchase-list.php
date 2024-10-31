<?php
/**
 * @file      purchases/purchase-list.php
 * @brief     Hooks and function to manage Purchase list table.
 *
 * @addtogroup PWWH_PURCHASE_LIST
 * @{
 */

/**
 * @brief     Defines the capabilites related to the purchases and returns
 *            the array required to create the post type.
 *
 * @return    array the array of capabilities required by the
 *            register_post_type().
 */
function pwwh_core_purchase_lists_init() {

  /* Managing row actions. */
  {
    /* Removing quick edit row action. */
    pwwh_core_list_remove_row_action(PWWH_CORE_PURCHASE, 'inline hide-if-no-js');
  }

  /* Managing bulk actions. */
  {
    /* Removing edit bulk action. */
    pwwh_core_list_remove_bulk_action(PWWH_CORE_PURCHASE, 'edit');
  }

  /* Managing WP List columns. */
  {
    /* Removing date column. */
    pwwh_core_list_remove_columns(PWWH_CORE_PURCHASE, 'date');

    /* Adding a custom Items column. */
    $label = __('Item', 'piwi-warehouse');
    $args = array('id' => 'item',
                  'label' => $label,
                  'content' => 'pwwh_core_purchase_list_item_content',
                  'sort' => false);
    pwwh_core_list_add_columns(PWWH_CORE_PURCHASE, $args);

    /* Adding a custom Quantities column. */
    $label = __('Quantity', 'piwi-warehouse');
    $args = array('id' => 'type',
                  'label' => $label,
                  'content' => 'pwwh_core_purchase_list_quantity_content',
                  'sort' => false);
    pwwh_core_list_add_columns(PWWH_CORE_PURCHASE, $args);
  }
}

/**
 * @brief     Manage the content of the 'Item' column.
 *
 * @param[in] int $post_id        The post ID.
 *
 * @return    void.
 */
function pwwh_core_purchase_list_item_content($post_id) {

  $data = pwwh_core_purchase_api_get_quantities($post_id);
  $count = count($data);
  if($count > 1) {
    echo sprintf(__('%s Items', 'piwi-warehouse'), $count);
  }
  else if($count == 1) {
    $items = array_keys($data);
    $args = array('post' => $items[0],
                  'action' => 'edit');
    $url = pwwh_core_api_admin_url_post($args);
    $title = get_the_title($items[0]);
    echo '<a href="' . $url . '" title="' . esc_attr($title) . '">' .
            $title .
          '</a>';
  }
  else {
    echo '';
    $msg = sprintf('Unexpected empty item in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
}

/**
 * @brief     Manage the content of the 'Quantity' column.
 *
 * @param[in] int $post_id        The post ID.
 *
 * @return    void.
 */
function pwwh_core_purchase_list_quantity_content($post_id) {
  $data = pwwh_core_purchase_api_get_quantities($post_id);
  $count = count($data);
  if($count > 1) {
    echo '&mdash;';
  }
  else if($count == 1) {
    $qnts = array_values($data);
    echo $qnts[0];
  }
  else {
    echo '0';
    $msg = sprintf('Unexpected empty quantity in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
}
/** @} */