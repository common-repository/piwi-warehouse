<?php
/**
 * @file      movements/movement-api.php
 * @brief     API related to Movement post type.
 *
 * @addtogroup PWWH_CORE_MOVEMENT
 * @{
 */

/*===========================================================================*/
/* Generic APIs                                                              */
/*===========================================================================*/

/**
 * @brief     Get the holder of movement.
 * @note      This function skips the transition status hook accessing
 *            directly the DB.
 *
 * @param[in] mixed $post         The Movement as WP_Post or Post ID
 * @param[in] mixed $status       The new status
 *
 * @return    mixed the term if $term is a valid ID or Term, false otherwise.
 * @api
 */
function pwwh_core_movement_api_change_state($post = null, $status) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_MOVEMENT) {

    /* A list representing the allowed status for this post. */
    $allowed = array('trash', PWWH_CORE_MOVEMENT_STATUS_ACTIVE,
                     PWWH_CORE_MOVEMENT_STATUS_CONCLUDED);

    if(in_array($status, $allowed)) {

      /* Accessing the database directly to avoid post transition from active
         to unactive with no data into the post. */
      global $wpdb;

      $table_name = $wpdb->prefix . "posts";
      $data = array('post_status' => PWWH_CORE_MOVEMENT_STATUS_CONCLUDED);
      $where = array('ID' => $post_id);
      $wpdb->update($table_name, $data, $where);
    }
    else {
      $msg = sprintf('Unexpected post status in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      $msg = sprintf('Post status is %s', $status);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    }

  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $msg = sprintf('Post type is %s', get_post_type($post));
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
}

/*===========================================================================*/
/* API related to Movement's Holder meta                                     */
/*===========================================================================*/

/**
 * @brief     Get the holder of movement.
 *
 * @param[in] mixed $post         The Movement as WP_Post or Post ID
 * @param[in] mixed $field        The field to return. If null return the
 *                                object
 *
 * @return    mixed the term if $term is a valid ID or Term, false otherwise.
 * @api
 */
function pwwh_core_movement_api_get_holder($post = null, $field = null) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_MOVEMENT) {

    /* Retrieving the older. */
    $terms = wp_get_post_terms($post_id, PWWH_CORE_MOVEMENT_HOLDER);

    if(is_array($terms)) {

      $term = $terms[0];

      if(is_a($term, 'WP_Term')) {
        if($field === null) {
          return $term;
        }
        else if(property_exists($term, $field)) {
          return $term->$field;
        }
        else {
          $msg = sprintf('Unexpected field in %s()', __FUNCTION__);
          pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
          return false;
        }
      }
    }
    else {
      $msg = sprintf('Missing holder in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      return false;
    }
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $msg = sprintf('Post type is %s', get_post_type($post));
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    return false;
  }
}

/**
 * @brief     Retrieves a Holder starting from its name.
 *
 * @param[in] string $name        The Holder name
 *
 * @return    mixed the Holder as WP_Term or false.
 * @api
 */
function pwwh_core_movement_get_holder_by_name($name) {

  $holder = get_term_by('name', $name, PWWH_CORE_MOVEMENT_HOLDER, OBJECT);

  if(is_a($holder, 'WP_Term')) {
    return $holder;
  }
  else {
    return false;
  }
}

/*===========================================================================*/
/* API related to Movement's quantities                                      */
/*===========================================================================*/

/**
 * @brief     Movement quantities post meta identifier.
 */
define('PWWH_CORE_MOVEMENT_META_QNT', PWWH_CORE_MOVEMENT . '_qnt');

/**
 * @brief     Gets the quantities of a movement associated to the related
 *            item-location.
 * @note      The return value will be an associative array in the form
 *            [Item ID]-[Location ID] => ('moved'    => [Moved],
 *                                        'returned' => [Returned],
 *                                        'donated'  => [Donated],
 *                                        'lost'     => [Lost],
 *                                        'lent'     => [Lent]).
 *
 * @param[in] mixed $post         The Movement as WP_Post or Post ID
 *
 * @return    array  The movement quantities as an associative array.
 * @api
 */
function pwwh_core_movement_api_get_quantities($post = null) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_MOVEMENT) {
    $quantities = get_post_meta($post_id, PWWH_CORE_MOVEMENT_META_QNT, true);

    if(is_array($quantities)) {
      /* Nothing to do. */
    }
    else if($quantities == false) {
      /* Post meta never saved. */
      $quantities = array();
    }
    else {
      $msg = sprintf('Unexpected quantities format in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      $quantities = array();
    }
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $msg = sprintf('Post type is %s', get_post_type($post));
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $quantities = array();
  }
  return $quantities;
}

/**
 * @brief     Sets the quantities of a movement associated to the related
 *            item-location.
 * @note      Quantities is expected to be an associative array in the form
 *            [Item ID]-[Location ID] => ('moved'    => [Moved],
 *                                        'returned' => [Returned],
 *                                        'donated'  => [Donated],
 *                                        'lost'     => [Lost],
 *                                        'lent'     => [Lent]).
 *
 * @param[in] mixed $post         The Movement as WP_Post or Post ID
 * @param[in] mixed $quantities   The quantities associative array.
 *
 * @return    void
 * @api
 */
function pwwh_core_movement_api_set_quantities($post = null, $quantities) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_MOVEMENT) {
    if(!is_array($quantities)) {
      $msg = sprintf('Unexpected quantities format in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    }
    else {
      update_post_meta($post_id, PWWH_CORE_MOVEMENT_META_QNT, $quantities);
    }
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $qnts = array();
  }
}

/**
 * @brief     Deletes the quantities post meta of a movement.
 *
 * @param[in] mixed $post         The Movement as WP_Post or Post ID
 *
 * @return    bool the operation status
 * @api
 */
function pwwh_core_movement_api_delete_quantities($post = null) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_MOVEMENT) {
    return delete_post_meta($post_id, PWWH_CORE_MOVEMENT_META_QNT);
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    return false;
  }
}

/**
 * @brief     Gets the quantities of a movement for a specific item associated
 *            to the related location.
 * @note      The return value will be an associative array in the form
 *            [Location ID] => [Qnt].
 * @api
 *
 * @param[in] mixed $post         The Movement as WP_Post or Post ID
 * @param[in] string $item_id     The item identifier
 *
 * @return    array  The movement quantities as an associative array.
 */
function pwwh_core_movement_api_get_quantities_by_item($post = null,
                                                       $item_id = null) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_MOVEMENT) {

    /* Validating the item id. */
    $item = get_post($item_id);
    if(get_post_type($item) === PWWH_CORE_ITEM) {

      /* The item ids are in the keys. */
      $qnts = pwwh_core_movement_api_get_quantities($post);

      /* Removing the total. */
      unset($qnts[0]);

      /* Extracting Item IDs from the keys. */
      $item_quantities = array();
      foreach($qnts as $key => $qnt) {

        /* A key is in the format [Item ID]-[Location ID]. */
        $key_item_id = null;
        $key_loc_id = null;
        pwwh_core_movement_api_parse_key($key, $key_item_id, $key_loc_id);
        if($key_item_id == $item_id) {
          $item_quantities[$key_loc_id] = $qnt;
        }
      }
    }
    else {
      $msg = sprintf('Unexpected item in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      $msg = sprintf('The item is %s', $item_id);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      $item_quantities = array();
    }
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $msg = sprintf('Post type is %s', get_post_type($post));
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $item_quantities = array();
  }
  return $item_quantities;
}

/*===========================================================================*/
/* API related to Movement's quantity key                                    */
/*===========================================================================*/

/**
 * @brief     Composes a key for the quantities array.
 * @note      No check is performed on the input parameters.
 *
 * @param[in] string $item_id     The item identifier
 * @param[in] string $loc_id      The location identifier
 *
 * @return    string the quantity key
 * @api
 */
function pwwh_core_movement_api_create_key($item_id, $loc_id) {
  return implode('-', array($item_id, $loc_id));
}

/**
 * @brief     Parses a key for the quantities array.
 * @note      No check is performed on the input parameters.
 *
 * @param[in]  string $key        The key to parse
 * @param[out] string $item_id    The item identifier
 * @param[out] string $loc_id     The location identifier
 *
 * @return    void
 * @api
 */
function pwwh_core_movement_api_parse_key($key, &$item_id, &$loc_id) {

  $tmp = explode('-', $key);
  if(count($tmp) == 2) {
    $item_id = $tmp[0];
    $loc_id = $tmp[1];
  }
  else {
    $msg = sprintf('Unexpected key format in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $msg = sprintf('The key is %s', $key);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $item_id = 0;
    $loc_id = 0;
  }
}

/**
 * @brief     Gets the keys of a the movement quantity array filtered by
 *            item, location or both of them in AND logic.
 * @api
 *
 * @param[in] mixed $post         The Movement as WP_Post or Post ID
 * @param[in] mixed $item_id      An item ID to use as filter. With a valid
 *                                item_id all the keys unrelated to the item
 *                                are filtered.
 * @param[in] mixed $loc_id       A location ID to use as filter. With a valid
 *                                loc_id all the keys unrelated to the location
 *                                are filtered.
 *
 * @return    array  The keys of the movement quantity filtered by item and
 *            location.
 */
function pwwh_core_movement_api_get_keys($post = null, $item_id = null,
                                         $loc_id = null) {

  /* Validating the item id. */
  $item = get_post($item_id);
  if(get_post_type($item) !== PWWH_CORE_ITEM) {
    $item_id = null;
  }

  /* Validating the location id. */
  $loc = pwwh_core_item_api_sanitize_location($loc_id);
  if(!$loc) {
    $loc_id = null;
  }

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_MOVEMENT) {

    /* The item ids are in the keys. */
    $_keys = array_keys(pwwh_core_movement_api_get_quantities($post));

    if($item_id || $loc_id) {
      /* Filtering keys. */

      foreach($_keys as $id => $key) {

        /* A key is in the format [Item ID]-[Location ID]. */
        $key_item_id = null;
        $key_loc_id = null;
        pwwh_core_movement_api_parse_key($key, $key_item_id, $key_loc_id);

        if($item_id && $loc_id) {
          if(($item_id != $key_item_id) || ($loc_id != $key_loc_id)) {
            /* Filtering by Item and Location: the item and/or the location
               do not match. */
            unset($_keys[$id]);
          }
        }
        else if($item_id && !$loc_id)  {
          if($item_id != $key_item_id) {
            /* Filtering by Item and it does not match. */
            unset($_keys[$id]);
          }
        }
        else if(!$item_id && $loc_id)  {
         if($loc_id != $key_loc_id) {
            /* Filtering by Location and it does not match. */
            unset($_keys[$id]);
          }
        }
      }
    }
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $_keys = array();
  }
  return $_keys;
}

/*===========================================================================*/
/* API related to Movement's Items                                           */
/*===========================================================================*/

/**
 * @brief     Gets the items of a movement.
 * @api
 *
 * @param[in] mixed $post         The Movement as WP_Post or Post ID
 *
 * @return    array  The movement items.
 */
function pwwh_core_movement_api_get_items($post = null) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_MOVEMENT) {

    /* The item ids are in the keys. */
    $keys = array_keys(pwwh_core_movement_api_get_quantities($post));

    /* Extracting Item IDs from the keys. */
    $_items = array();
    foreach($keys as $key) {

      /* A key is in the format [Item ID]-[Location ID]. */
      $key_item_id = null;
      $key_loc_id = null;
      pwwh_core_movement_api_parse_key($key, $key_item_id, $key_loc_id);
      if($key_item_id) {
        array_push($_items, $key_item_id);
      }
      else {
        $msg = sprintf('Unexpected key format in %s()', __FUNCTION__);
        pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
        $msg = sprintf('Broken key %s for the post %s()', $key, $post_id);
        pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      }
    }
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $_items = array();
  }
  return $_items;
}

/**
 * @brief     Gets the movemented quantity of a specific Item in a specific
 *            location.
 * @api
 *
 * @param[in] mixed $post         The Movement as WP_Post or Post ID
 * @param[in] mixed $item         The Item as WP_Post or Post ID
 * @param[in] mixed $location     The Location as WP_Term or Term ID
 * @param[in] mixed $type         The quantity type
 * @paramval{'moved'}             The movimented quantity
 * @paramval{'returned'}          The returned quantity
 * @paramval{'donated'}           The donated quantity
 * @paramval{'lost'}              The lost quantity
 * @paramval{'lent'}              The lent quantity
 * @paramval{any other}           The associative array of the quantities
 *
 * @return    mixed  An array or a value depending on type
 */
function pwwh_core_movement_api_get_quantity_by_item($post = null,
                                                     $item = null,
                                                     $location = null,
                                                     $type = null) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }

  if(!is_a($item, 'WP_Post')) {
    $item = get_post($item);
  }

  $location = pwwh_core_item_api_sanitize_location($location);

  if((get_post_type($post) === PWWH_CORE_MOVEMENT) &&
     (get_post_type($item) === PWWH_CORE_ITEM) && $location) {

    $quantities = pwwh_core_movement_api_get_quantities($post);
    $key = pwwh_core_movement_api_create_key($item->ID, $location->term_id);
    if(isset($quantities[$key])) {
      if(is_string($type) && isset($quantities[$key][$type])) {
        $_quantity = $quantities[$key][$type];
      }
      else {
        $_quantity = $quantities[$key];
      }
    }
    else {
      $_quantity = '0';
    }
  }
  else {
    $msg = sprintf('Unexpected parameters type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $_quantity = '0';
  }
  return $_quantity;
}

/**
 * @brief     Gets the movemented quantity of a specific Item.
 * @api
 *
 * @param[in] mixed $post         The Movement as WP_Post or Post ID
 * @param[in] mixed $item         The Item as WP_Post or Post ID
 * @param[in] mixed $location     The Location as WP_Term or Term ID
 *
 * @return    string  The movimented item quantity.
 */
function pwwh_core_movement_api_get_moved_by_item($post = null, $item = null,
                                                  $location = null) {
  return pwwh_core_movement_api_get_quantity_by_item($post, $item, $location,
                                                     'moved');
}

/**
 * @brief     Gets the returned quantity of a specific Item.
 * @api
 *
 * @param[in] mixed $post         The Movement as WP_Post or Post ID
 * @param[in] mixed $item         The Item as WP_Post or Post ID
 * @param[in] mixed $location     The Location as WP_Term or Term ID
 *
 * @return    array  The returned items.
 */
function pwwh_core_movement_api_get_returned_by_item($post = null,
                                                     $item = null,
                                                     $location = null) {
  return pwwh_core_movement_api_get_quantity_by_item($post, $item, $location,
                                                     'returned');
}

/**
 * @brief     Gets the donated quantity of a specific Item.
 * @api
 *
 * @param[in] mixed $post         The Movement as WP_Post or Post ID
 * @param[in] mixed $item         The Item as WP_Post or Post ID
 * @param[in] mixed $location     The Location as WP_Term or Term ID
 *
 * @return    array  The donated items.
 */
function pwwh_core_movement_api_get_donated_by_item($post = null, $item = null,
                                                    $location = null) {
  return pwwh_core_movement_api_get_quantity_by_item($post, $item, $location,
                                                     'donated');
}

/**
 * @brief     Gets the lost quantity of a specific Item.
 * @api
 *
 * @param[in] mixed $post         The Movement as WP_Post or Post ID
 * @param[in] mixed $item         The Item as WP_Post or Post ID
 * @param[in] mixed $location     The Location as WP_Term or Term ID
 *
 * @return    array  The lost items.
 */
function pwwh_core_movement_api_get_lost_by_item($post = null, $item = null,
                                                 $location = null) {
  return pwwh_core_movement_api_get_quantity_by_item($post, $item, $location,
                                                     'lost');
}

/**
 * @brief     Gets the lent quantity of a specific Item.
 * @api
 *
 * @param[in] mixed $post         The Movement as WP_Post or Post ID
 * @param[in] mixed $item         The Item as WP_Post or Post ID
 * @param[in] mixed $location     The Location as WP_Term or Term ID
 *
 * @return    array  The lent items.
 */
function pwwh_core_movement_api_get_lent_by_item($post = null, $item = null,
                                                 $location = null) {
  return pwwh_core_movement_api_get_quantity_by_item($post, $item, $location,
                                                     'lent');
}

/**
 * @brief     Removes an Item and the related quantities from a movement.
 * @note      If movement has no remaining items after the removal it is
 *            completely deleted.
 *
 * @param[in] mixed $post         The Movement as WP_Post or Post ID
 * @param[in] mixed $items        The ID of the Item to remove or an array of
 *                                item IDs
 *
 * @return    mixed the number of deleted items, true if he whole movement
 *            has been deleted or false if an error occured.
 * @api
 */
function pwwh_core_movement_api_remove_item($post = null, $items) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_MOVEMENT) {

    $deleted = 0;

    if(!is_array($items)) {
      $items = array($items);
    }

    /* History handler. */
    $history = new pwwh_core_movement_history();

    /* Getting the stored data. */
    $quantities = pwwh_core_movement_api_get_quantities($post_id);

    /* Removing data from local data structure. */
    foreach($items as $item) {

      /* Getting the keys associated to this item. */
      $keys = pwwh_core_movement_api_get_keys($post, $item);

      foreach($keys as $key) {

        if(isset($quantities[$key])) {
          unset($quantities[$key]);
          $deleted++;
        }
      }

      /* Erasing history related to this item in this movement. */
      $args = array('mov_id' => $post_id,
                    'item_id' => $item);
      $history->erase($args);
    }

    /* Checking if any item is remained. */
    if(count($quantities)) {

      /* Updating quantities. */
      pwwh_core_movement_api_set_quantities($post_id, $quantities);

      /* Checking if it is required a status change. */
      $change_status = true;
      foreach($quantities as $id => $qnt) {
        if($qnt['lent'] != 0) {
          $change_status = false;
          break;
        }
      }

      if($change_status) {
        pwwh_core_movement_api_change_state($post,
                                            PWWH_CORE_MOVEMENT_STATUS_CONCLUDED);
      }
      return $deleted;
    }
    else {
      /* Deleting the empty movement. */
      wp_delete_post($post_id, true);
      return true;
    }
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    return false;
  }
}

/**
 * @brief     Returns an array of Movements related to a specific Item.
 *
 * @param[in] mixed $item         The item as Post object or as Post ID.
 * @param[in] mixed $post_status  The post status of movement to get
 *                                @default{'any'}
 *
 * @return    array an array of Post object.
 */
function pwwh_core_movement_api_get_by_item($item = null,
                                            $post_status = 'any') {

  if(!is_a($item, 'WP_Post')) {
    $item = get_post($item);
  }

  if(get_post_type($item) == PWWH_CORE_ITEM) {

    /* Searching all the Movements related to the item. */
    $args = array('post_type' => PWWH_CORE_MOVEMENT,
                  'post_status' => $post_status,
                  'nopaging' => true);
    $query = new WP_Query($args);

    /* Checking whereas there are some results. */
    if($query->have_posts()) {
      $movements = $query->get_posts();

      foreach($movements as $key => $movement) {
        $_items = pwwh_core_movement_api_get_items($movement);

        if(array_search($item->ID, $_items) === false) {
          /* The current movement does not have this item. */
          unset($movements[$key]);
        }
      }
    }
    else {
      $movements = array();
    }

    wp_reset_postdata();
  }
  else {
    $movements = array();
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $msg = sprintf('Post type is %s', get_post_type($item));
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
  return $movements;
}

/*===========================================================================*/
/* Utility APIs                                                              */
/*===========================================================================*/

/**
 * @brief     Gets the quantities of a movement associated to the related
 *            item from the POST data.
 * @note      The return value will be an associative array in the form
 *            [Item ID]-[Location ID] => ('moved'    => [Moved],
 *                                        'returned' => [Returned],
 *                                        'donated'  => [Donated],
 *                                        'lost'     => [Lost],
 *                                        'lent'     => [Lent]).
 * @api
 *
 * @return    array  The movement quantities as an associative array.
 */
function pwwh_core_movement_api_get_quantities_from_post() {

  /* Verifies nonce. */
  if(pwwh_core_api_verify_nonce_from_post(PWWH_CORE_MOVEMENT_NONCE_EDIT)) {

    /* Getting UI Facts. */
    $ui_facts = pwwh_core_movement_api_get_ui_facts();

    /* Getting Instances .*/
    $coll = sanitize_text_field($_POST[$ui_facts['input']['collector']['id']]);
    $insts = explode(':', $coll);

    $_quantities = array();
    foreach($insts as $inst) {
      /* Basically the instance identifier is a number. */
      $inst = intval($inst);

      /* Composing identifiers. */
      $_item = $inst . ':' . $ui_facts['input']['item']['id'];
      $_loc = $inst . ':' . $ui_facts['input']['location']['id'];
      $_moved = $inst . ':' . $ui_facts['input']['moved']['id'];
      $_returned = $inst . ':' . $ui_facts['input']['returned']['id'];
      $_donated = $inst . ':' . $ui_facts['input']['donated']['id'];
      $_lost = $inst . ':' . $ui_facts['input']['lost']['id'];

      if(isset($_POST[$_item]) && isset($_POST[$_loc]) &&
         isset($_POST[$_moved])) {
        $item_title = sanitize_text_field(stripslashes_deep($_POST[$_item]));
        $item = pwwh_core_item_api_get_item_by_title($item_title);

        $loc_title = sanitize_text_field(stripslashes_deep($_POST[$_loc]));
        $loc = pwwh_core_item_get_location_by_name($loc_title);

        if(is_a($item, 'WP_Post') && is_a($loc, 'WP_Term')) {
          /* Composing output data. */
          $qnt = array();
          if(isset($_POST[$_moved]) && (floatval($_POST[$_moved]) > 0)) {
            $qnt['moved'] = floatval($_POST[$_moved]);
          }
          else {
            $msg = sprintf('Unexpected Moved in %s()', __FUNCTION__);
            pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
            return null;
          }

          if(isset($_POST[$_returned]) && (floatval($_POST[$_returned]) > 0)) {
            $qnt['returned'] = floatval($_POST[$_returned]);
          }
          else {
            $qnt['returned'] = 0;
          }

          if(isset($_POST[$_donated]) && (floatval($_POST[$_donated]) > 0)) {
            $qnt['donated'] = floatval($_POST[$_donated]);
          }
          else {
            $qnt['donated'] = 0;
          }

          if(isset($_POST[$_lost]) && (floatval($_POST[$_lost]) > 0)) {
            $qnt['lost'] = floatval($_POST[$_lost]);
          }
          else {
            $qnt['lost'] = 0;
          }

          $qnt['lent'] = $qnt['moved'] - $qnt['lost'] -
                         $qnt['returned'] - $qnt['donated'];

          if($qnt['lent'] < 0) {
            $msg = sprintf('Unexpected negative Lent in %s()', __FUNCTION__);
            pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
            return null;
          }

          /* Composing output data. */
          $key = pwwh_core_movement_api_create_key($item->ID, $loc->term_id);
          $_quantities[$key] = $qnt;
        }
        else {
          $msg = sprintf('Unexpected Item in %s()', __FUNCTION__);
          pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
          $msg = sprintf('The item title is %s', $item_title);
          pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
          return null;
        }
      }
      else {
        $msg = sprintf('Wrong data in %s()', __FUNCTION__);
        pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
        return null;
      }
    }
  }
  else {
    $msg = sprintf('Unverified Nonce in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    return null;
  }
  return $_quantities;
}

/*===========================================================================*/
/* API related to backend URLs                                               */
/*===========================================================================*/

/**
 * @brief     Movement's Item backend query.
 */
define('PWWH_CORE_MOVEMENT_QUERY_ITEM', 'item');

/**
 * @brief     Movement's Holder backend query.
 */
define('PWWH_CORE_MOVEMENT_QUERY_HOLDER', 'holder');

/**
 * @brief     Movement's moved quantity backend query.
 */
define('PWWH_CORE_MOVEMENT_QUERY_MOVED', 'moved');

/**
 * @brief     Returns the URL to add a new Movement with pre-set parameters.
 *
 * @param[in] mixed $item         The item as Post object or as Post ID.
 * @param[in] mixed $holder_id    The holder ID.
 * @param[in] mixed $moved        The quantity to move.
 *
 * @api
 *
 * @return    string the URL.
 */
function pwwh_core_movement_api_url_movement_item($item = null, $holder_id = 0,
                                                  $moved = 0) {

  if(!is_a($item, 'WP_Post')) {
    $item = get_post($item);
  }

  $queries = array('post_type' => PWWH_CORE_MOVEMENT);

  if(get_post_type($item) == PWWH_CORE_ITEM) {
    $queries[PWWH_CORE_MOVEMENT_QUERY_ITEM] = intval($item->ID);

    if(intval($holder_id)) {
      $queries[PWWH_CORE_MOVEMENT_QUERY_HOLDER] = intval($holder_id);
    }

    if(floatval($moved)) {
      $queries[PWWH_CORE_MOVEMENT_QUERY_MOVED] = floatval($moved);
    }
  }

  return pwwh_core_api_admin_url_post_new($queries);
}

/*===========================================================================*/
/*  API  related to Movement UI                                              */
/*===========================================================================*/

/**
 * @brief     Returns all the facts related to the User Interface.
 *
 * @api
 *
 * @return    array The facts as an associative array.
 */
function pwwh_core_movement_api_get_ui_facts() {

  /* Metabox facts. */
  {
    /* Item Summary Metabox facts. */
    $item_summary = array('id' => PWWH_CORE_MOVEMENT . '_item_summary_box',
                          'label' => __('%s related Info', 'piwi-warehouse'),
                          'callback' => 'pwwh_core_movement_ui_metabox_item_summary');

    /* Item Summary Metabox facts. */
    $add_item = array('id' => PWWH_CORE_MOVEMENT . '_add_item_box',
                      'label' => __('Move more Items', 'piwi-warehouse'),
                      'callback' => 'pwwh_core_movement_ui_metabox_add_item');

    /* Holder Metabox facts. */
    $holder = array('id' => PWWH_CORE_MOVEMENT . '_holder_box',
                    'label' => __('Holder', 'piwi-warehouse'),
                    'callback' => 'pwwh_core_movement_ui_metabox_holder');

    /* Notes Metabox facts. */
    $notes = array('id' => PWWH_CORE_MOVEMENT . '_notes_box',
                   'label' => __('Movement Notes', 'piwi-warehouse'),
                   'callback' => 'pwwh_core_note_ui_metabox_notes');

    /* Composing Metabox facts. */
    $_boxes = compact('item_summary', 'add_item', 'holder', 'notes');
  }

  /* Input facts.*/
  {
    /* Item Input Facts. */
    $rule_remote = array('url' => admin_url('admin-ajax.php'),
                         'action' => PWWH_CORE_MOVEMENT . '_validate_item',
                         'callback' => 'pwwh_core_movement_validate_item_handler');
    $msg_required = __('The Item name is mandatory', 'piwi-warehouse');
    $msg_duplicate = __('This Item-Location couple has been already added',
                        'piwi-warehouse');
    $msg_remote = __('An Item having this name does not exists',
                     'piwi-warehouse');
    $item = array('id' => PWWH_CORE_MOVEMENT . '_item',
                  'datalist' => PWWH_CORE_MOVEMENT . '_item_list',
                  'rule' => array('required' => true,
                                  'check_duplicate' => true,
                                  'remote' => $rule_remote),
                  'msg' =>  array('required' => $msg_required,
                                  'check_duplicate' => $msg_duplicate,
                                  'remote' => $msg_remote));

    /* Location Input Facts. */
    $rule_remote = array('url' => admin_url('admin-ajax.php'),
                         'action' => PWWH_CORE_MOVEMENT . '_validate_loc',
                         'callback' => 'pwwh_core_movement_validate_location_handler');
    $msg_required = __('The Location is mandatory', 'piwi-warehouse');
    $msg_duplicate = __('This Item-Location couple has been already added',
                        'piwi-warehouse');
    $msg_remote = __('This Location does not exist', 'piwi-warehouse');
    $location = array('id' => PWWH_CORE_MOVEMENT . '_location',
                      'datalist' => PWWH_CORE_MOVEMENT . '_location_list',
                      'rule' => array('required' => true,
                                      'check_duplicate' => true,
                                      'remote' => $rule_remote),
                      'msg' => array('required' => $msg_required,
                                     'check_duplicate' => $msg_duplicate,
                                     'remote' => $msg_remote));

    /* Moved Input Facts. */
    $rule_remote = array('url' => admin_url('admin-ajax.php'),
                         'action' => PWWH_CORE_MOVEMENT . '_validate_moved',
                         'callback' => 'pwwh_core_movement_validate_moved_handler');
    $msg_required = __('The Moved field is mandatory', 'piwi-warehouse');
    $msg_number = __('The Moved field must be a number', 'piwi-warehouse');
    $msg_min = __('The Moved field must be at least 1', 'piwi-warehouse');
    $msg_step = __('The Moved field must be multiple of 1','piwi-warehouse');
    $msg_remote = __('The quantity entered exceeds Item availability in ' .
                     'this location', 'piwi-warehouse');
    $moved = array('id' => PWWH_CORE_MOVEMENT . '_moved',
                   'rule' => array('required' => true,
                                   'number' => true,
                                   'min' => 1,
                                   'step' => 1,
                                   'remote' => $rule_remote),
                   'msg' =>  array('required' => $msg_required,
                                   'number' => $msg_number,
                                   'min' => $msg_min,
                                   'step' => $msg_step,
                                   'remote' => $msg_remote));

    /* Returned Input Facts. */
    $msg_check_eq = __('The total Item quantity (returned + donated + lost) ' .
                       'shall be less or equal to moved Items',
                       'piwi-warehouse');
    $msg_min = __('The Returned field must be at least 0', 'piwi-warehouse');
    $msg_step = __('The Returned field must be multiple of 1',
                   'piwi-warehouse');
    $returned = array('id' => PWWH_CORE_MOVEMENT . '_returned',
                      'rule' => array('check_eq' => true,
                                      'min' => 0,
                                      'step' => 1),
                      'msg' =>  array('check_eq' => $msg_check_eq,
                                      'min' => 0,
                                      'step' => 1));

    /* Donated Input Facts. */
    $msg_check_eq = __('The total Item quantity (returned + donated + lost) ' .
                       'shall be less or equal to moved Items',
                       'piwi-warehouse');
    $msg_min = __('The Donated field must be at least 0', 'piwi-warehouse');
    $msg_step = __('The Donated field must be multiple of 1',
                   'piwi-warehouse');
    $donated = array('id' => PWWH_CORE_MOVEMENT . '_donated',
                     'rule' => array('check_eq' => true,
                                     'min' => 0,
                                     'step' => 1),
                     'msg' =>  array('check_eq' => $msg_check_eq,
                                     'min' => 0,
                                     'step' => 1));

    /* Lost Input Facts. */
    $msg_check_eq = __('The total Item quantity (returned + donated + lost) ' .
                       'shall be less or equal to moved Items',
                       'piwi-warehouse');
    $msg_min = __('The Lost field must be at least 0', 'piwi-warehouse');
    $msg_step = __('The Lost field must be multiple of 1', 'piwi-warehouse');
    $lost = array('id' => PWWH_CORE_MOVEMENT . '_lost',
                  'rule' => array('check_eq' => true,
                                  'min' => 0,
                                  'step' => 1),
                  'msg' =>  array('check_eq' => $msg_check_eq,
                                  'min' => $msg_min,
                                  'step' => $msg_step));

    /* Collector Input Facts. */
    $collector = array('id' => PWWH_CORE_MOVEMENT . '_collector');

    /* Holder Input Facts. */
    $rule_remote = array('url' => admin_url('admin-ajax.php'),
                         'action' => PWWH_CORE_MOVEMENT . '_validate_holder',
                         'callback' => 'pwwh_core_movement_validate_holder_handler');
    $msg_required = __('The Holder name is mandatory', 'piwi-warehouse');
    $msg_remote = __('An Holder having this name does not exists',
                     'piwi-warehouse');
    $holder = array('id' => PWWH_CORE_MOVEMENT_HOLDER,
                    'datalist' => PWWH_CORE_MOVEMENT_HOLDER . '_list',
                    'rule' => array('required' => true,
                                    'remote' => $rule_remote),
                    'msg' =>  array('required' => $msg_required,
                                    'remote' => $msg_remote));

    /* Composing Input facts. */
    $_inputs = compact('item', 'location', 'moved', 'returned', 'donated',
                       'lost', 'collector', 'holder');
  }

  /* Button facts.*/
  {
    /* Publish buttons. */
    $publish = array('label' => array('conclude' => __('Conclude', 'piwi-warehouse'),
                                      'activate' => __('Reactivate', 'piwi-warehouse'),
                                      'confirm' => __('Confirm', 'piwi-warehouse'),
                                      'update' => __('Update', 'piwi-warehouse')));
    /* Composing Button facts. */
    $_buttons = compact('publish');
  }

  /* Field facts. */
  {
    /* New Lent Field Facts. */
    $new_lent = array('id' => PWWH_CORE_MOVEMENT . '_new_lent');

    /* Composing Field facts. */
    $_fields = compact('new_lent');
  }

  /* Composing facts. */
  $facts = array('box' => $_boxes,
                 'input' => $_inputs,
                 'button' => $_buttons,
                 'field' => $_fields);

  return $facts;
}

/**
 * @brief     Returns all the facts related to the User Interface for
 *            the edit tag screens
 *
 * @api
 *
 * @return    array the facts as an associative array.
 */
function pwwh_core_movement_api_get_ui_edit_tag_facts() {

  /* Input facts. */
  {

    /* Location Input facts. */
    $rule_remote = array('url' => admin_url('admin-ajax.php'),
                         'action' => PWWH_CORE_MOVEMENT . '_holder_name',
                         'callback' => 'pwwh_core_movement_holder_validate_name_handler');
    $msg_remote = __('An Holder having this name already exists',
                     'piwi-warehouse');
    $holder = array('id' => 'tag-name',
                    'rule' => array('remote' => $rule_remote),
                    'msg' =>array('remote' => $msg_remote));

    /* Composing Inputs facts. */
    $_inputs = compact('holder');
  }

  /* Composing facts. */
  $facts = array('input' => $_inputs);

  return $facts;
}

/**
 * @brief     Returns all the facts related to the User Interface.
 *
 * @api
 *
 * @return    array The facts as an associative array.
 */
function pwwh_core_movement_api_get_movement_facts() {

  /* Movement meta-data. */
  $_statuses = array('auto-draft' => __('Auto Draft', 'piwi-warehouse'),
                     PWWH_CORE_MOVEMENT_STATUS_ACTIVE => __('Active',
                                                            'piwi-warehouse'),
                     PWWH_CORE_MOVEMENT_STATUS_CONCLUDED => __('Concluded',
                                                               'piwi-warehouse'));
  $_labels = array('singular' => PWWH_CORE_MOVEMENT_LABEL_SINGULAR,
                   'plural' => PWWH_CORE_MOVEMENT_LABEL_PLURAL);

  $_type = PWWH_CORE_MOVEMENT;

  $_taxonomy = array('holder' => PWWH_CORE_MOVEMENT_HOLDER);

  /* Composing facts. */
  $facts = array('status' => $_statuses,
                 'label' => $_labels,
                 'type' => $_type,
                 'taxonomy' => $_taxonomy);

  return $facts;
}
/** @} */

