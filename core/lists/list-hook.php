<?php
/**
 * @file      lists/list-hook.php
 * @brief     Hooks and function related to the List Engine.
 *
 * @addtogroup  PWWH_CORE
 * @{
 */

/**
 * @brief     Manages the row actions.
 *
 * @param[in] array $_actions     The array of row actions.
 * @param[in] WP_Post $post       Post object.
 *
 * @hooked    post_row_actions
 *
 * @return    array the filtered action links array.
 */
function pwwh_core_list_manage_row_actions($_actions, $post) {

  $post_type = $post->post_type;
  $post_status = get_post_status($post);

  $actions = pwwh_core_list_get_row_actions($post_type);

  foreach($actions as $action => $data) {
    if($data && current_user_can($data['cap'])) {

      /* Checking statuses. If this is true or the current post status is in
         array the row action will be shown. */
      $statuses = $data['status'];
      if(($statuses === true) ||
         (is_array($statuses) && in_array($post_status, $statuses))) {

        /* Generating and adding the row action. */
        $label = $data['label'];
        if(is_callable($data['link'])) {
          $url = call_user_func($data['link'], $post);
        }
        else {
          $url = esc_url($data['link']);
        }
        $_actions[$action] = '<a href="' . $url . '"
                                aria-label="' . $label . '">' .
                                $label .
                              '</a>';
      }
    }
    else {
      if(isset($_actions[$action])) {
        unset($_actions[$action]);
      }
    }
  }
  return $_actions;
}
add_filter('post_row_actions', 'pwwh_core_list_manage_row_actions', 10, 2);

/**
 * @brief     Manage all the bulk actions.
 *
 * @hooked    plugins_loaded
 *
 * @return    void
 */
function pwwh_core_list_manage_manage_bulk_actions() {
  /**
   * @brief     Manage bulk actions in a specific screen.
   *
   * @param[in] array $_actions      The array of bulk actions.
   *
   * @hooked    bulk_actions-edit-[screen_id]
   *
   * @return    array the filtered $actions array.
   */
  function __pwwh_core_list_manage_manage_bulk_actions($_actions) {

    if(function_exists('get_current_screen')) {
      $screen = get_current_screen();

      if($screen && ($screen->base == 'edit')) {
        $actions = pwwh_core_list_get_bulk_actions($screen->post_type);

        foreach($actions as $action => $data) {
          if($data) {
            /** @todo add custom bulk action management. */
          }
          else {
            if(isset($_actions[$action])) {
              unset($_actions[$action]);
            }
          }
        }
      }
    }

    return $_actions;
  }

  $post_types = pwwh_core_list_get_post_types();

  foreach ($post_types as $post_type) {
    add_filter('bulk_actions-edit-' . $post_type,
               '__pwwh_core_list_manage_manage_bulk_actions');
  }
}
add_filter('init', 'pwwh_core_list_manage_manage_bulk_actions', 100);

/**
 * @brief     Manages the WP List column labels.
 *
 * @hooked    init
 *
 * @return    void.
 */
function pwwh_core_list_manage_column_labels() {

  /**
   * @brief     Manages the WP List column labels for a specific post type.
   *
   * @param[in] array $columns      The columns name.
   *
   * @hooked    manage_pwwh_item_posts_columns
   *
   * @return    array the filtered $columns array.
   */
  function __pwwh_core_list_manage_column_labels($_columns) {

    if(function_exists('get_current_screen')) {
      $screen = get_current_screen();

      if($screen && ($screen->base == 'edit')) {
        $columns = pwwh_core_list_get_columns($screen->post_type);

        foreach($columns as $column => $data) {
          if($data) {
            $_columns[$column] = $data['label'];
          }
          else {
            if(isset($_columns[$column])) {
              unset($_columns[$column]);
            }
          }
        }
      }
    }

    return $_columns;
  }

  $post_types = pwwh_core_list_get_post_types();

  foreach ($post_types as $post_type) {
    add_filter('manage_' . $post_type . '_posts_columns',
               '__pwwh_core_list_manage_column_labels');
  }
}
add_filter('init', 'pwwh_core_list_manage_column_labels', 100);

/**
 * @brief     Manages the WP List column contents.
 *
 * @hooked    init
 *
 * @return    void.
 */
function pwwh_core_list_manage_column_contents() {

  /**
   * @brief     Manages the WP List column contents for a specific post type.
   *
   * @param[in] string $column      The current column id.
   * @param[in] string $post_id     The post ID.
   *
   * @hooked    manage_[$post_type]_posts_columns
   *
   * @return    array the filtered $columns array.
   */
  function __pwwh_core_list_manage_column_contents($column, $post_id) {

    if(function_exists('get_current_screen')) {
      $screen = get_current_screen();

      if($screen && ($screen->base == 'edit')) {
        $columns = pwwh_core_list_get_columns($screen->post_type);

        if(isset($columns[$column])) {

          $data = $columns[$column];
          if($data && isset($data['content']) && is_callable($data['content'])) {
            call_user_func($data['content'], $post_id);
          }
        }
      }
    }
  }

  $post_types = pwwh_core_list_get_post_types();

  foreach ($post_types as $post_type) {
    add_filter('manage_' . $post_type . '_posts_custom_column',
               '__pwwh_core_list_manage_column_contents', 10, 2);
  }
}
add_filter('init', 'pwwh_core_list_manage_column_contents', 100);

/**
 * @brief     Manages the WP List sortable columns.
 *
 * @hooked    init
 *
 * @return    void.
 */
function pwwh_core_list_manage_sortable_columns() {

  /**
   * @brief     Manages the WP List column labels for a specific post type.
   *
   * @param[in] array $columns      The columns name.
   *
   * @hooked    manage_pwwh_item_posts_columns
   *
   * @return    array the filtered $columns array.
   */
  function __pwwh_core_list_manage_sortable_columns($_columns) {

    if(function_exists('get_current_screen')) {
      $screen = get_current_screen();

      if($screen && ($screen->base == 'edit')) {
        $columns = pwwh_core_list_get_columns($screen->post_type);

        foreach($columns as $column => $data) {
          if($data &&
             (isset($data['sort'])) &&
             (is_callable($data['sort']))) {
            $_columns[$column] = $column;
          }
        }
      }
    }

    return $_columns;
  }

  $post_types = pwwh_core_list_get_post_types();

  foreach ($post_types as $post_type) {
    add_filter('manage__edit' . $post_type . '_sortable_columns',
               '__pwwh_core_list_manage_sortable_columns');
  }
}
add_filter('init', 'pwwh_core_list_manage_sortable_columns', 100);

/**
 * @brief     Manages the WP List column contents for a specific post type.
 *
 * @param[in] WP_Query $query     The query instance.
 *
 * @hooked    'pre_get_posts'
 *
 * @return    array the filtered $columns array.
 */
function pwwh_core_list_sort_column_contents($query) {

  if(function_exists('get_current_screen')) {
    $screen = get_current_screen();

    if($screen && ($screen->base == 'edit')) {
      $columns = pwwh_core_list_get_columns($screen->post_type);

      /* Checking parameter existence. */
      if((isset($_GET['orderby'])) &&
         (isset($columns[$_GET['orderby']])) &&
         (isset($columns[$_GET['orderby']]['sort'])) &&
         (is_callable($columns[$_GET['orderby']]['sort']))) {
        /* Calling sort method. */
        call_user_func_array($columns[$_GET['orderby']]['sort'],
                             array(&$query));
      }
    }
  }
}
add_filter('pre_get_posts', 'pwwh_core_list_sort_column_contents', 1, 1);
/** @} */