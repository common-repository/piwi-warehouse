<?php
/**
 * @file    core/post-list/post-list-api.php
 * @brief   List Engine APIs.
 *
 * @addtogroup  PWWH_CORE
 * @{
 */

/*===========================================================================*/
/* General related                                                           */
/*===========================================================================*/

/**
 * @brief     Get all the post type to be managed.
 *
 * @return    array the array of post types to be managed.
 */
function pwwh_core_list_get_post_types() {
  global $PWWH_CORE_LIST;

  if(is_array($PWWH_CORE_LIST)) {
    return array_keys($PWWH_CORE_LIST);
  }
  else {
    return array();
  }
}

/*===========================================================================*/
/* Row Action related                                                        */
/*===========================================================================*/

/**
 * @brief      Row Actions key in the global array.
 */
define('PWWH_CORE_LISTS_ROW_ACTIONS', 'row_actions');

/**
 * @brief     Removes a row action in a wp-list for a specific post type.
 *
 * @param[in] string $post_type   The post_type to target.
 * @param[in] array $args         An array of argument.
 * @paramkey{id}                  The id of the action to add.
 * @paramkey{label}               The label of the action to add.
 * @paramkey{link}                The link of the action to add or a callable
 *                                that generates it. The first parameter of the
 *                                callable should be a WP_Post or a Post ID.
 * @paramkey{status}              The post statuses on which the action should
 *                                appear. This can be an array or a boolean.
 *                                True means appear always.
 * @paramkey{cap}                 The capability required to see this action.
 *
 * @return    void
 */
function pwwh_core_list_add_row_action($post_type, $args = array()) {
  global $PWWH_CORE_LIST;

  $id = pwwh_lib_utils_validate_array_field($args, 'id', null);
  $label = pwwh_lib_utils_validate_array_field($args, 'label', null);
  $link = pwwh_lib_utils_validate_array_field($args, 'link', null);
  $status = pwwh_lib_utils_validate_array_field($args, 'status', true);
  $cap = pwwh_lib_utils_validate_array_field($args, 'cap', null);

  if(($id !== null) && ($id !== null) && ($id !== null) && ($id !== null)) {
    if(!isset($PWWH_CORE_LIST)) {
      $PWWH_CORE_LIST = array();
    }

    if(!isset($PWWH_CORE_LIST[$post_type])) {
      $PWWH_CORE_LIST[$post_type] = array();
    }

    if(!isset($PWWH_CORE_LIST[$post_type][PWWH_CORE_LISTS_ROW_ACTIONS])) {
      $PWWH_CORE_LIST[$post_type][PWWH_CORE_LISTS_ROW_ACTIONS] = array();
    }

    $data = array('label' => $label,
                  'link' => $link,
                  'status' => $status,
                  'cap' => $cap);
    $PWWH_CORE_LIST[$post_type][PWWH_CORE_LISTS_ROW_ACTIONS][$id] = $data;
  }
  else {
    $msg = sprintf('Missing parameters in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
}

/**
 * @brief     Removes a row action in a wp-list for a specific post type.
 *
 * @param[in] string $post_type   The post_type to target.
 * @param[in] string $id          The id of the action to remove.
 *
 * @return    void
 */
function pwwh_core_list_remove_row_action($post_type, $id) {
  global $PWWH_CORE_LIST;

  if(!isset($PWWH_CORE_LIST)) {
    $PWWH_CORE_LIST = array();
  }

  if(!isset($PWWH_CORE_LIST[$post_type])) {
    $PWWH_CORE_LIST[$post_type] = array();
  }

  if(!isset($PWWH_CORE_LIST[$post_type][PWWH_CORE_LISTS_ROW_ACTIONS])) {
    $PWWH_CORE_LIST[$post_type][PWWH_CORE_LISTS_ROW_ACTIONS] = array();
  }

  $PWWH_CORE_LIST[$post_type][PWWH_CORE_LISTS_ROW_ACTIONS][$id] = NULL;
}

/**
 * @brief     Get all the row actions to be managed for a specific post type.
 *
 * @param[in] string $post_type   The post_type to target.
 *
 * @return    array the array of actions to be managed.
 */
function pwwh_core_list_get_row_actions($post_type) {
  global $PWWH_CORE_LIST;

  if(is_array($PWWH_CORE_LIST) &&
     isset($PWWH_CORE_LIST[$post_type]) &&
     isset($PWWH_CORE_LIST[$post_type][PWWH_CORE_LISTS_ROW_ACTIONS])) {
    return $PWWH_CORE_LIST[$post_type][PWWH_CORE_LISTS_ROW_ACTIONS];
  }
  else {
    return array();
  }
}

/*===========================================================================*/
/* Bulk Action related                                                       */
/*===========================================================================*/

/**
 * @brief      Bulk Actions key in the global array.
 */
define('PWWH_CORE_LISTS_BULK_ACTIONS', 'bulk_actions');

/**
 * @brief     Removes a bulk action in a wp-list for a specific post type.
 *
 * @param[in] string $post_type   The post_type to target.
 * @param[in] string $id          The id of the action to remove.
 *
 * @return    void
 */
function pwwh_core_list_remove_bulk_action($post_type, $id) {
  global $PWWH_CORE_LIST;

  if(!isset($PWWH_CORE_LIST)) {
    $PWWH_CORE_LIST = array();
  }

  if(!isset($PWWH_CORE_LIST[$post_type])) {
    $PWWH_CORE_LIST[$post_type] = array();
  }

  if(!isset($PWWH_CORE_LIST[$post_type][PWWH_CORE_LISTS_BULK_ACTIONS])) {
    $PWWH_CORE_LIST[$post_type][PWWH_CORE_LISTS_BULK_ACTIONS] = array();
  }

  $PWWH_CORE_LIST[$post_type][PWWH_CORE_LISTS_BULK_ACTIONS][$id] = NULL;
}

/**
 * @brief     Get all the bulk actions to be managed for a specific post type.
 *
 * @param[in] string $post_type   The post_type to target.
 *
 * @return    array the array of actions to be managed.
 */
function pwwh_core_list_get_bulk_actions($post_type) {
  global $PWWH_CORE_LIST;

  if(is_array($PWWH_CORE_LIST) &&
     isset($PWWH_CORE_LIST[$post_type]) &&
     isset($PWWH_CORE_LIST[$post_type][PWWH_CORE_LISTS_BULK_ACTIONS])) {
    return $PWWH_CORE_LIST[$post_type][PWWH_CORE_LISTS_BULK_ACTIONS];
  }
  else {
    return array();
  }
}

/*===========================================================================*/
/* Table column related                                                      */
/*===========================================================================*/

/**
 * @brief      Bulk Actions key in the global array.
 */
define('PWWH_CORE_LISTS_COLUMNS', 'columns');

/**
 * @brief     Removes a row action in a wp-list for a specific post type.
 *
 * @param[in] string $post_type   The post_type to target.
 * @param[in] array $args         An array of argument.
 * @paramkey{id}                  The id of the action to add.
 * @paramkey{label}               The label of the action to add.
 * @paramkey{content}             A callable that generates the content. The
 *                                first parameter of the callable should be a
 *                                WP_Post or a Post ID.
 * @paramkey{sort}                A callable that sorts the column. The
 *                                first parameter of the callable should be a
 *                                WP_Query. Leave it empty for a non sortable
 *                                column
 *
 * @return    void
 */
function pwwh_core_list_add_columns($post_type, $args = array()) {
  global $PWWH_CORE_LIST;

  $id = pwwh_lib_utils_validate_array_field($args, 'id', null);
  $label = pwwh_lib_utils_validate_array_field($args, 'label', null);
  $content = pwwh_lib_utils_validate_array_field($args, 'content', null);
  $sort = pwwh_lib_utils_validate_array_field($args, 'sort', null);

  if($id !== null) {
    if(!isset($PWWH_CORE_LIST)) {
      $PWWH_CORE_LIST = array();
    }

    if(!isset($PWWH_CORE_LIST[$post_type])) {
      $PWWH_CORE_LIST[$post_type] = array();
    }

    if(!isset($PWWH_CORE_LIST[$post_type][PWWH_CORE_LISTS_COLUMNS])) {
      $PWWH_CORE_LIST[$post_type][PWWH_CORE_LISTS_COLUMNS] = array();
    }

    $data = array('label' => $label,
                  'content' => $content,
                  'sort' => $sort);
    $PWWH_CORE_LIST[$post_type][PWWH_CORE_LISTS_COLUMNS][$id] = $data;
  }
  else {
    $msg = sprintf('Missing parameters in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
}

/**
 * @brief     Removes a bulk action in a wp-list for a specific post type.
 *
 * @param[in] string $post_type   The post_type to target.
 * @param[in] string $id          The id of the action to remove.
 *
 * @return    void
 */
function pwwh_core_list_remove_columns($post_type, $id) {
  global $PWWH_CORE_LIST;

  if(!isset($PWWH_CORE_LIST)) {
    $PWWH_CORE_LIST = array();
  }

  if(!isset($PWWH_CORE_LIST[$post_type])) {
    $PWWH_CORE_LIST[$post_type] = array();
  }

  if(!isset($PWWH_CORE_LIST[$post_type][PWWH_CORE_LISTS_COLUMNS])) {
    $PWWH_CORE_LIST[$post_type][PWWH_CORE_LISTS_COLUMNS] = array();
  }

  $PWWH_CORE_LIST[$post_type][PWWH_CORE_LISTS_COLUMNS][$id] = NULL;
}

/**
 * @brief     Get all the columns to be managed for a specific post type.
 *
 * @param[in] string $post_type   The post_type to target.
 *
 * @return    array the array of columns to be managed.
 */
function pwwh_core_list_get_columns($post_type) {
  global $PWWH_CORE_LIST;

  if(is_array($PWWH_CORE_LIST) &&
     isset($PWWH_CORE_LIST[$post_type]) &&
     isset($PWWH_CORE_LIST[$post_type][PWWH_CORE_LISTS_COLUMNS])) {
    return $PWWH_CORE_LIST[$post_type][PWWH_CORE_LISTS_COLUMNS];
  }
  else {
    return array();
  }
}
/** @} */