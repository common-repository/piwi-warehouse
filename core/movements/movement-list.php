<?php
/**
 * @file      movements/movement-list.php
 * @brief     Hooks and function to manage Movement list table.
 *
 * @addtogroup PWWH_CORE_MOVEMENT_LIST
 * @{
 */

/**
 * @brief     Defines the capabilites related to the movements and returns
 *            the array required to create the post type.
 *
 * @return    array the array of capabilities required by the
 *            register_post_type().
 */
function pwwh_core_movement_lists_init() {

  /* Managing row actions. */
  {
    /* Removing quick edit row action. */
    pwwh_core_list_remove_row_action(PWWH_CORE_MOVEMENT, 'inline hide-if-no-js');
  }

  /* Managing bulk actions. */
  {
    /* Removing edit bulk action. */
    pwwh_core_list_remove_bulk_action(PWWH_CORE_MOVEMENT, 'edit');

    /* Removing delete bulk action. */
    pwwh_core_list_remove_bulk_action(PWWH_CORE_MOVEMENT, 'delete');
  }

  /* Managing WP List columns. */
  {
    /* Removing date column. */
    pwwh_core_list_remove_columns(PWWH_CORE_MOVEMENT, 'date');

    /* Adding a custom Item column. */
    $label = __('Item', 'piwi-warehouse');
    $args = array('id' => 'item',
                  'label' => $label,
                  'content' => 'pwwh_core_movement_list_item_content',
                  'sort' => false);
    pwwh_core_list_add_columns(PWWH_CORE_MOVEMENT, $args);

    /* Adding a custom Moved column. */
    $label = __('Moved', 'piwi-warehouse');
    $args = array('id' => 'moved',
                  'label' => $label,
                  'content' => 'pwwh_core_movement_list_moved_content',
                  'sort' => false);
    pwwh_core_list_add_columns(PWWH_CORE_MOVEMENT, $args);

    /* Adding a custom Lent column. */
    $label = __('Lent', 'piwi-warehouse');
    $args = array('id' => 'lent',
                  'label' => $label,
                  'content' => 'pwwh_core_movement_list_lent_content',
                  'sort' => false);
    pwwh_core_list_add_columns(PWWH_CORE_MOVEMENT, $args);

    /* Adding a custom Donated column. */
    $label = __('Donated', 'piwi-warehouse');
    $args = array('id' => 'donated',
                  'label' => $label,
                  'content' => 'pwwh_core_movement_list_donated_content',
                  'sort' => false);
    pwwh_core_list_add_columns(PWWH_CORE_MOVEMENT, $args);

    /* Adding a custom Lost column. */
    $label = __('Lost', 'piwi-warehouse');
    $args = array('id' => 'lost',
                  'label' => $label,
                  'content' => 'pwwh_core_movement_list_lost_content',
                  'sort' => false);
    pwwh_core_list_add_columns(PWWH_CORE_MOVEMENT, $args);

    /* Adding a custom Status column. */
    $label = __('Status', 'piwi-warehouse');
    $args = array('id' => 'status',
                  'label' => $label,
                  'content' => 'pwwh_core_movement_list_status_content',
                  'sort' => 'pwwh_core_movement_list_status_sort');
    pwwh_core_list_add_columns(PWWH_CORE_MOVEMENT, $args);
  }
}

/**
 * @brief     Manage the content of the 'Item' column.
 *
 * @param[in] int $post_id        The post ID.
 *
 * @return    void.
 */
function pwwh_core_movement_list_item_content($post_id) {
  $data = pwwh_core_movement_api_get_quantities($post_id);
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
    $msg = sprintf('Unexpected empty quantity in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
}

/**
 * @brief     Manage the content of the 'Moved' column.
 *
 * @param[in] int $post_id        The post ID.
 *
 * @return    void.
 */
function pwwh_core_movement_list_moved_content($post_id) {
  $data = pwwh_core_movement_api_get_quantities($post_id);
  $count = count($data);
  if($count > 1) {
    echo '&mdash;';
  }
  else if($count == 1) {
    $qnts = array_values($data);
    echo $qnts[0]['moved'];
  }
  else {
    echo '0';
    $msg = sprintf('Unexpected empty quantity in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
}

/**
 * @brief     Manage the content of the 'Lent' column.
 *
 * @param[in] int $post_id        The post ID.
 *
 * @return    void.
 */
function pwwh_core_movement_list_lent_content($post_id) {
  $data = pwwh_core_movement_api_get_quantities($post_id);
  $count = count($data);
  if($count > 1) {
    echo '&mdash;';
  }
  else if($count == 1) {
    $qnts = array_values($data);
    echo $qnts[0]['lent'];
  }
  else {
    echo '0';
    $msg = sprintf('Unexpected empty quantity in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
}

/**
 * @brief     Manage the content of the 'Donated' column.
 *
 * @param[in] int $post_id        The post ID.
 *
 * @return    void.
 */
function pwwh_core_movement_list_donated_content($post_id) {
  $data = pwwh_core_movement_api_get_quantities($post_id);
  $count = count($data);
  if($count > 1) {
    echo '&mdash;';
  }
  else if($count == 1) {
    $qnts = array_values($data);
    echo $qnts[0]['donated'];
  }
  else {
    echo '0';
    $msg = sprintf('Unexpected empty quantity in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
}

/**
 * @brief     Manage the content of the 'Lost' column.
 *
 * @param[in] int $post_id        The post ID.
 *
 * @return    void.
 */
function pwwh_core_movement_list_lost_content($post_id) {
  $data = pwwh_core_movement_api_get_quantities($post_id);
  $count = count($data);
  if($count > 1) {
    echo '&mdash;';
  }
  else if($count == 1) {
    $qnts = array_values($data);
    echo $qnts[0]['lost'];
  }
  else {
    echo '0';
    $msg = sprintf('Unexpected empty quantity in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
}

/**
 * @brief     Manage the content of the 'Status' column.
 *
 * @param[in] int $post_id        The post ID.
 *
 * @return    void.
 */
function pwwh_core_movement_list_status_content($post_id) {
  $post_status = get_post_status($post_id);
  $post_status_obj = get_post_status_object($post_status);
  $label = $post_status_obj->label;
  if($label) {
    echo ucfirst($label);
  }
  else {
    echo '';
    $msg = sprintf('Unexpected empty status in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
}

/**
 * @brief     Acts on main query to properly sort 'Status' column.
 *
 * @param[in] WP_Query $query     The reference to the main query.
 *
 * @return    void.
 */
function pwwh_core_movement_list_status_sort($query) {
  $order = pwwh_lib_utils_validate_array_field($_GET, 'order', 'ASC', array('ASC', 'DESC'));
  $query->set('order', $order);
  $query->set('orderby', 'post_status');
}
/** @} */