<?php
/**
 * @file      core/core-api.php
 * @brief     Common API.
 *
 * @addtogroup PWWH_CORE
 * @{
 */

/*===========================================================================*/
/* APIs related to admin URLs                                                */
/*===========================================================================*/

/**
 * @brief     Returns the URL to an admin page with queries.
 * @api
 *
 * @param[in] array $page         The admin page to link.
 * @param[in] array $queries      An array of queries to be added to the url.
 *
 *
 * @return    string the URL.
 */
function pwwh_core_api_admin_url($page = '', $queries = array()) {

  if(is_array($queries) && count($queries)) {
    $query = array();
    foreach($queries as $key => $value) {
      array_push($query, $key . '=' . $value);
    }

    $query = '?' . implode('&', $query);
  }
  else {
    $query = '';
  }

  return admin_url($page . $query);
}

/**
 * @brief     Returns the URL to add a new post with custom parameters.
 * @api
 *
 * @param[in] array $queries      An array of queries to be added to the url.
 *
 * @return    string the URL.
 */
function pwwh_core_api_admin_url_post_new($queries) {

  return pwwh_core_api_admin_url('post-new.php', $queries);
}

/**
 * @brief     Returns the URL to edit posts list with custom parameters.
 * @api
 *
 * @param[in] array $queries      An array of queries to be added to the url.
 *
 * @return    string the URL.
 */
function pwwh_core_api_admin_url_edit($queries) {

  return pwwh_core_api_admin_url('edit.php', $queries);
}

/**
 * @brief     Returns the URL to edit tags list with custom parameters.
 * @api
 *
 * @param[in] array $queries      An array of queries to be added to the url.
 *
 * @return    string the URL.
 */
function pwwh_core_api_admin_url_edit_tags($queries) {

  return pwwh_core_api_admin_url('edit-tags.php', $queries);
}

/**
 * @brief     Returns the URL to the post with custom parameters.
 * @api
 *
 * @param[in] array $queries      An array of queries to be added to the url.
 *
 * @return    string the URL.
 */
function pwwh_core_api_admin_url_post($queries) {

  return pwwh_core_api_admin_url('post.php', $queries);
}

/*===========================================================================*/
/* APIs related to labels and messages                                       */
/*===========================================================================*/

/**
 * @brief     Generates the lables for a custom post type starting from its
 *            base labels.
 *
 * @param[in] string $singular    The singular label
 * @param[in] string $plural      The plurar label
 *
 * @return    array the labels required by the register_post_type().
 */
function pwwh_core_api_get_post_labels($singular, $plural) {

  $singular = ucwords($singular);
  $plural = ucwords($plural);

  $labels = array('name' => $plural,
                  'singular_name' => $singular,
                  'add_new' => sprintf(__('Add New %s', 'piwi-warehouse'),
                                       $singular),
                  'add_new_item' => sprintf(__('Add New %s', 'piwi-warehouse'),
                                            $singular),
                  'edit_item' => sprintf(__('Edit %s', 'piwi-warehouse'),
                                         $singular),
                  'new_item' => sprintf(__('New %s', 'piwi-warehouse'),
                                        $singular),
                  'view_item' => sprintf(__('View %s', 'piwi-warehouse'),
                                         $singular),
                  'view_items' => sprintf(__('View %s', 'piwi-warehouse'),
                                          $plural),
                  'search_items' => sprintf(__('Search %s', 'piwi-warehouse'),
                                            $singular),
                  'not_found' => sprintf(__('No %s Found', 'piwi-warehouse'),
                                         $plural),
                  'not_found_in_trash' =>  sprintf(__('No %s found in Trash',
                                                      'piwi-warehouse'),
                                                   $plural),
                  'all_item' => sprintf(__('All %s', 'piwi-warehouse'),
                                        $plural),
                  'archives' => sprintf(__('%s Archives', 'piwi-warehouse'),
                                        $plural),
                  'attributes' => sprintf(__('%s Attributes', 'piwi-warehouse'),
                                          $plural),
                  'insert_into_item' => sprintf(__('Insert into %s',
                                                  'piwi-warehouse'),
                                                $singular),
                  'uploaded_to_this_item' => sprintf(__('Uploaded to this %s',
                                                        'piwi-warehouse'),
                                                     $singular));

  return $labels;
}

/**
 * @brief     Returns the terms of the taxonomy and the related ancestors of
 *            this post.
 * @api
 *
 * @param[in] string $tax         The taxonomy name.
 * @param[in] mixed $post         The Post object or Post ID
 * @param[in] array $args         An array of arguments.
 * @paramkey{limit}               The number of tax to return. @default{false}
 * @paramkey{depht}               The number of the tax ancestors.
 *                                @default{false}
 * @paramkey{start}               The start point to apply the depth.
 *                                @default{ancestor}
 * @paramval{ancestor}            Start from the ancestor.
 * @paramval{child}               Start from the last child.
 *
 * @return    array the terms and the related ancestors..
 */
function pwwh_core_api_get_taxs($tax, $post = null, $args = array()) {

  /* Validating array keys. */
  $limit = pwwh_lib_utils_validate_array_field($args, 'limit', false);
  $depth = pwwh_lib_utils_validate_array_field($args, 'depth', false);
  $start = pwwh_lib_utils_validate_array_field($args, 'start', 'ancestor',
                                               array('ancestor', 'child'),
                                               'string');

  /* Cheching Post consistency. */
  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }

  /* Retrieves the terms. */
  $terms = wp_get_post_terms($post->ID, $tax);

  /* Managing the limit. */
  if($limit && ($limit > 0)) {
    $terms = array_slice($terms, 0, $limit);
  }

  $taxs = array();
  if(is_array($terms)) {
    foreach($terms as $term) {
      /* Preparing parenthood: note tha get_ancestors() provide the list from
         lowest to highest without the current term. */
      $curr_id = $term->term_id;
      $ancestors = get_ancestors($curr_id, $tax, 'taxonomy');
      array_unshift($ancestors, $curr_id);

      /* Managing start. */
      if($start == 'ancestor') {
        $ancestors = array_reverse($ancestors);
      }

      /* Managing the depth. */
      if($depth && ($depth > 0)) {
        $ancestors = array_slice($ancestors, 0, $depth);
      }

      /* Composing the ancestry. */
      $ancestry = array();
      foreach($ancestors as $ancestor_id) {
        $ancestor = get_term($ancestor_id);

        $id = $ancestor_id;
        $name = $ancestor->name;
        $args = array('post_type' => $post->post_type,
                       $tax => $ancestor->slug);
        $edit_url =  pwwh_core_api_admin_url_edit($args);
        $archive_url = get_term_link($ancestor);
        array_push($ancestry, compact('id', 'name', 'edit_url', 'archive_url'));
      }

      /* Pushing ancestry in the output array. */
      array_push($taxs, $ancestry);
    }
  }
  else if($terms === false) {
    /* Nothing to do. */
  }
  else {
    $msg = sprintf('Error in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $msg = sprintf('Details: %s()', $terms->get_error_message());
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }

  return $taxs;
}

/**
 * @brief     Generates the lables for a taxonomy starting from its base labels.
 *
 * @param[in] string $singular    The singular label
 * @param[in] string $plural      The plurar label
 *
 * @return    array the labels required by the register_taxonomy().
 */
function pwwh_core_api_get_tax_labels($singular, $plural) {

  $singular = ucwords($singular);
  $plural = ucwords($plural);

  $labels = array('name' => $plural,
                  'singular_name' => $singular,
                  'search_items' => sprintf(__('Search %s', 'piwi-warehouse'),
                                            $plural),
                  'popular_items' => sprintf(__('Popular %s', 'piwi-warehouse'),
                                             $plural),
                  'all_items' => sprintf(__('All %s', 'piwi-warehouse'),
                                         $plural),
                  'parent_item' => null,
                  'parent_item_colon' => null,
                  'edit_item' => sprintf(__('Edit %s', 'piwi-warehouse'),
                                         $singular),
                  'view_item' => sprintf(__('View %s', 'piwi-warehouse'),
                                         $singular),
                  'update_item' => sprintf(__('Update %s', 'piwi-warehouse'),
                                           $singular),
                  'add_new_item' => sprintf(__('Add New %s', 'piwi-warehouse'),
                                            $singular),
                  'new_item_name' => sprintf(__('New %s Name', 'piwi-warehouse'),
                                             $singular),
                  'separate_items_with_commas' => sprintf(__('Separate %s ' .
                                                             'with commas',
                                                             'piwi-warehouse'),
                                                          $plural),
                  'add_or_remove_items' => sprintf(__('Add or remove %s',
                                                      'piwi-warehouse'),
                                                   $singular),
                  'choose_from_most_used' => sprintf(__('Choose from the ' .
                                                        'most used %s',
                                                        'piwi-warehouse'),
                                                     $plural),
                  'not_found' => sprintf(__('No %s found.', 'piwi-warehouse'),
                                         $plural),
                  'no_terms' => sprintf(__('No %s.', 'piwi-warehouse'),
                                        $plural),
                  'items_list_navigation' => sprintf(__('%s list navigation.',
                                                        'piwi-warehouse'),
                                                     $plural),
                  'items_list' => sprintf(__('%s list.', 'piwi-warehouse'),
                                          $plural),
                  'most_used' => sprintf(__('Most Used %s', 'piwi-warehouse'),
                                         $plural),
                  'back_to_items' => sprintf(__('&larr; Back to %s.',
                                                'piwi-warehouse'),
                                             $plural),
                  'menu_name' => $plural);

  return $labels;
}

/**
 * @brief     Composes admin updated messages for a custom post types from
              its base labels.
 *
 * @param[in] string $singular    The singular label
 * @param[in] string $plural      The plurar label
 *
 * @return    array the updated messages for this post type.
 */
function pwwh_core_api_post_updated_messages($singular, $plural) {

  $post = get_post();
  $post_type = get_post_type($post);
  $post_type_object = get_post_type_object($post_type);

  /* Composing view and preview link if the post is publicily querable. */
  if($post_type_object->publicly_queryable) {
    $permalink = esc_url(get_permalink($post->ID));

    $label = sprintf(__('View %s', 'piwi-warehouse'), $singular);
    $view_link = sprintf(' <a href="%s">%s</a>', $permalink, $label);

    $preview_permalink = esc_url(add_query_arg('preview', 'true', $permalink));
    $label = sprintf(__('Preview %s', 'piwi-warehouse'), $singular);
    $preview_link = sprintf(' <a target="_blank" href="%s">%s</a>',
                            $preview_permalink, $label);
  }
  else {
    $view_link = '';
    $preview_link = '';
  }

  /* Getting revision title. */
  if(isset($_GET['revision'])) {
    $revision = wp_post_revision_title((int)$_GET['revision'], false);
  }
  else {
    $revision = false;
  }

  /* Composing date. */
  $date = date_i18n(__('M j, Y @ G:i', 'piwi-warehouse'),
          strtotime($post->post_date));

  /* Composing messages array. Note that message 0 is unused. */
  $msgs = array();
  $msgs[0] = '';
  $msgs[1] = sprintf(__('%s updated.', 'piwi-warehouse'), $singular) . $view_link;
  $msgs[2] = __('Custom field updated.', 'piwi-warehouse');
  $msgs[3] = __('Custom field deleted.', 'piwi-warehouse');
  $msgs[4] = sprintf(__('%s updated.', 'piwi-warehouse'), $singular);
  if($revision) {
    $msgs[5] = sprintf(__('%s restored to revision from %s',
                          'piwi-warehouse'), $singular, $revision);
  }
  else {
    $msgs[5] = false;
  }
  $msgs[6] = sprintf(__('%s published.', 'piwi-warehouse'), $singular) .
             $view_link;
  $msgs[7] = sprintf(__('%s saved.', 'piwi-warehouse'), $singular);
  $msgs[8] = sprintf(__('%s submitted.', 'piwi-warehouse'), $singular) .
             $preview_link;
  $msgs[9] = sprintf(__('%s scheduled for: <strong>%1$s</strong>.',
                        'piwi-warehouse'), $singular, $date) . $view_link;
  $msgs[10] = sprintf(__('%s draft updated.', 'piwi-warehouse'), $singular) .
              $preview_link;

  return $msgs;
}

/**
 * @brief     Composes admin updated messages for a custom taxonomy from
 *            its base labels.
 *
 * @param[in] string $singular    The singular label
 *
 * @return    array the updated messages for this taxonomy.
 */
function pwwh_core_api_tax_updated_messages($singular) {

  /* Composing messages array. */
  $msgs = array();

  /* Index 0: unused. */
  $msgs[0] = '';
  $msgs[1] = sprintf(__('%s added.', 'piwi-warehouse'), $singular);
  $msgs[2] = sprintf(__('%s deleted.', 'piwi-warehouse'), $singular);
  $msgs[3] = sprintf(__('%s updated.', 'piwi-warehouse'), $singular);
  $msgs[4] = sprintf(__('%s not added.', 'piwi-warehouse'), $singular);
  $msgs[5] = sprintf(__('%s not updated.', 'piwi-warehouse'), $singular);
  $msgs[6] = sprintf(__('%s deleted.', 'piwi-warehouse'), $singular);

  return $msgs;
}

/**
 * @brief     Composes admin bulk messages for a custom post types from
              its base labels.
 *
 * @param[in] string $singular    The singular label
 * @param[in] string $plural      The plurar label
 * @param[in] array $bulk_counts  The array of bulk counts.
 *
 * @return    array the bulk messages for this post type.
 */
function pwwh_core_api_post_bulk_messages($singular, $plural, $bulk_counts) {

  /* Composing messages array. */
  $msgs = array();

  if($bulk_counts['updated'] == 1) {
    $msgs['updated'] = sprintf(__('1 %s updated.', 'piwi-warehouse'),
                               $singular);
  }
  else {
    $msgs['updated'] = sprintf(__('%s %s updated.', 'piwi-warehouse'),
                               $bulk_counts['updated'], $plural);
  }

  if($bulk_counts['locked'] == 1) {
    $msgs['locked'] = sprintf(__('1 %s not updated, somebody is editing it.',
                                 'piwi-warehouse'), $singular);
  }
  else {
    $msgs['locked'] = sprintf(__('%s %s not updated, somebody is editing them.',
                                 'piwi-warehouse'),  $bulk_counts['locked'],
                              $plural);
  }

  if($bulk_counts['deleted'] == 1) {
    $msgs['deleted'] = sprintf(__('1 %s permanently deleted.',
                                  'piwi-warehouse'), $singular);
  }
  else {
    $msgs['deleted'] = sprintf(__('%s %s permanently deleted.',
                                  'piwi-warehouse'),  $bulk_counts['deleted'],
                               $plural);
  }

  if($bulk_counts['trashed'] == 1) {
    $msgs['trashed'] = sprintf(__('1 %s moved to the Trash.',
                                  'piwi-warehouse'), $singular);
  }
  else {
    $msgs['trashed'] = sprintf(__('%s %s moved to the Trash.',
                                  'piwi-warehouse'), $bulk_counts['trashed'],
                               $plural);
  }

  if($bulk_counts['untrashed'] == 1) {
    $msgs['untrashed'] = sprintf(__('1 %s restored from the Trash.',
                                    'piwi-warehouse'), $singular);
  }
  else {
    $msgs['untrashed'] = sprintf(__('%s %s restored from the Trash.',
                                    'piwi-warehouse'), $bulk_counts['untrashed'],
                                 $plural);
  }

  return $msgs;
}

/*===========================================================================*/
/* APIs related to nonces                                                    */
/*===========================================================================*/

/**
 * @brief     Creates a nonce starting from the post and an action.
 * @details   Extends the WP nonce including the post ID in the process.
 *
 * @param[in] string $action      The action of the nonce.
 * @param[in] mixed $post         A Post object or a Post ID.
 *
 * @return    mixed a nonce or false in case of invalid post.
 */
function pwwh_core_api_create_nonce($action = -1, $post = null) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }

  if(is_a($post, 'WP_Post')) {
    $nonce_seed = PWWH_PREFIX . $post->ID . $action;
    $nonce = wp_create_nonce($nonce_seed);
  }
  else {
    $nonce = false;
    $msg = sprintf('Unexpected parameter in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }

  return $nonce;
}

/**
 * @brief     Verifies a nonce starting from the post and an action
 * @details   Extends the WP nonce including the post ID in the process.
 *
 * @param[in] string $action      The action of the nonce.
 * @param[in] mixed $post         A Post object or a Post ID.
 * @param[in] string $action      The action of the nonce.
 *
 * @return    boolean the operation result.
 */
function pwwh_core_api_verify_nonce($nonce, $action = -1, $post = null) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }

  if(is_a($post, 'WP_Post')) {
    $nonce_seed = PWWH_PREFIX . $post->ID . $action;
    $result = wp_verify_nonce($nonce, $nonce_seed);
  }
  else {
    $result = false;
    $msg = sprintf('Unexpected parameter in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }

  return $result;
}

/**
 * @brief     Verifies a nonce from the post global array against
 *            the seed generated starting from the post and an action.
 *
 * @param[in] mixed $post         A Post object or a Post ID.
 * @param[in] string $action      The action of the nonce.
 *
 * @return    boolean the operation result.
 */
function pwwh_core_api_verify_nonce_from_post($action, $post = null) {

  if(isset($_POST[$action])) {
    $result = pwwh_core_api_verify_nonce($_POST[$action], $action, $post);
  }
  else {
    $result = false;
  }

  return $result;
}

/*===========================================================================*/
/* APIs related to application Capabilites.                                  */
/*===========================================================================*/

/**
 * @brief     Check current capability engine revision.
 * @details   Shall be used to check whereas an update is required.
 *
 * @return    boolean true if current capability engine is out of date.
 */
function pwwh_core_api_shall_caps_be_updated() {
  return (pwwh_core_caps_api_db_get_revision() != PWWH_CORE_CAPS_REV);
}

/**
 * @brief     Restores all the capabilities to their default.
 *
 * @return    void.
 */
function pwwh_core_api_set_default_caps() {

  /* Configuring capabilities. */
  pwwh_core_note_caps_autoset();
  pwwh_core_item_caps_autoset();
  pwwh_core_movement_caps_autoset();
  pwwh_core_purchase_caps_autoset();

  /* Updating the db revision to the current one. */
  pwwh_core_caps_api_db_set_revision(PWWH_CORE_CAPS_REV);
}
/** @} */

