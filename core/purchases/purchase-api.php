<?php
/**
 * @file      purchases/purchase-api.php
 * @brief     API related to Purchase post type.
 *
 * @addtogroup PWWH_CORE_PURCHASE
 * @{
 */

/*===========================================================================*/
/* API related to Purchase's quantities                                      */
/*===========================================================================*/

/**
 * @brief     Purchase quantities post meta identifier.
 */
define('PWWH_CORE_PURCHASE_META_QNT', PWWH_CORE_PURCHASE . '_qnt');

/**
 * @brief     Gets the quantities of a purchase associated to the related
 *            item-location.
 * @note      The return value will be an associative array in the form
 *            [Item ID]-[Location ID] => [Qnt].
 * @api
 *
 * @param[in] mixed $post         The Purchase as WP_Post or Post ID
 *
 * @return    array  The purchase quantities as an associative array.
 */
function pwwh_core_purchase_api_get_quantities($post = null) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_PURCHASE) {
    $quantities = get_post_meta($post_id, PWWH_CORE_PURCHASE_META_QNT, true);

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
 * @brief     Sets the quantities of a purchase associated to the related
 *            item-location.
 * @note      Quantities is expected to be an associative array in the form
 *            [Item ID]-[Location ID] => [Qnt].
 *
 * @param[in] mixed $post         The Purchase as WP_Post or Post ID
 * @param[in] mixed $quantities   The quantities associative array.
 *
 * @return    void
 * @api
 */
function pwwh_core_purchase_api_set_quantities($post = null, $quantities) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_PURCHASE) {
    if(!is_array($quantities)) {
      $msg = sprintf('Unexpected quantities format in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    }
    else {
      update_post_meta($post_id, PWWH_CORE_PURCHASE_META_QNT, $quantities);
    }
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $qnts = array();
  }
}

/**
 * @brief     Deletes the quantities post meta of a purchase.
 *
 * @param[in] mixed $post         The Purchase as WP_Post or Post ID
 *
 * @return    bool the operation status
 * @api
 */
function pwwh_core_purchase_api_delete_quantities($post = null) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_PURCHASE) {
    return delete_post_meta($post_id, PWWH_CORE_PURCHASE_META_QNT);
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    return false;
  }
}

/**
 * @brief     Gets the quantities of a purchase for a specific item associated
 *            to the related location.
 * @note      The return value will be an associative array in the form
 *            [Location ID] => [Qnt].
 * @api
 *
 * @param[in] mixed $post         The Purchase as WP_Post or Post ID
 * @param[in] string $item_id     The item identifier
 *
 * @return    array  The purchase quantities as an associative array.
 */
function pwwh_core_purchase_api_get_quantities_by_item($post = null,
                                                       $item_id = null) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_PURCHASE) {

    /* Validating the item id. */
    if(get_post_type($item_id) === PWWH_CORE_ITEM) {

      /* The item ids are in the keys. */
      $qnts = pwwh_core_purchase_api_get_quantities($post);

      /* Removing the total. */
      unset($qnts[0]);

      /* Extracting Item IDs from the keys. */
      $item_quantities = array();
      foreach($qnts as $key => $qnt) {

        /* A key is in the format [Item ID]-[Location ID]. */
        $key_item_id = null;
        $key_loc_id = null;
        pwwh_core_purchase_api_parse_key($key, $key_item_id, $key_loc_id);
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
/* API related to Purchase's quantity key                                    */
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
function pwwh_core_purchase_api_create_key($item_id, $loc_id) {
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
function pwwh_core_purchase_api_parse_key($key, &$item_id, &$loc_id) {

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
 * @brief     Gets the keys of a the purchase quantity array filtered by
 *            item, location or both of them in AND logic.
 * @api
 *
 * @param[in] mixed $post         The Purchase as WP_Post or Post ID
 * @param[in] mixed $item_id      An item ID to use as filter. With a valid
 *                                item_id all the keys unrelated to the item
 *                                are filtered.
 * @param[in] mixed $loc_id       A location ID to use as filter. With a valid
 *                                loc_id all the keys unrelated to the location
 *                                are filtered.
 *
 * @return    array  The keys of the purchase quantity filtered by item and
 *            location.
 */
function pwwh_core_purchase_api_get_keys($post = null, $item_id = null,
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

  if($post_type === PWWH_CORE_PURCHASE) {

    /* The item ids are in the keys. */
    $_keys = array_keys(pwwh_core_purchase_api_get_quantities($post));

    if($item_id || $loc_id) {
      /* Filtering keys. */

      foreach($_keys as $id => $key) {

        /* A key is in the format [Item ID]-[Location ID]. */
        $key_item_id = null;
        $key_loc_id = null;
        pwwh_core_purchase_api_parse_key($key, $key_item_id, $key_loc_id);

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
/* API related to Purchase's Items                                           */
/*===========================================================================*/

/**
 * @brief     Gets the items of a purchase.
 * @api
 *
 * @param[in] mixed $post         The Purchase as WP_Post or Post ID
 *
 * @return    array  The purchase items.
 */
function pwwh_core_purchase_api_get_items($post = null, $associative = false) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_PURCHASE) {

    /* The item ids are in the keys. */
    $keys = array_keys(pwwh_core_purchase_api_get_quantities($post));

    /* Extracting Item IDs from the keys. */
    $_items = array();
    foreach($keys as $key) {

      /* A key is in the format [Item ID]-[Location ID]. */
      $key_item_id = null;
      $key_loc_id = null;
      pwwh_core_purchase_api_parse_key($key, $key_item_id, $key_loc_id);
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
 * @brief     Gets the purchased quantity of a specific Item in a specific
 *            location.
 * @api
 *
 * @param[in] mixed $post         The Purchase as WP_Post or Post ID
 * @param[in] mixed $item         The Item as WP_Post or Post ID
 * @param[in] mixed $location     The Location as WP_Term or Term ID
 *
 * @return    string  The purchased item quantity.
 */
function pwwh_core_purchase_api_get_quantity_by_item($post = null,
                                                     $item = null,
                                                     $location = null) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }

  if(!is_a($item, 'WP_Post')) {
    $item = get_post($item);
  }

  $location = pwwh_core_item_api_sanitize_location($location);

  if((get_post_type($post) === PWWH_CORE_PURCHASE) &&
     (get_post_type($item) === PWWH_CORE_ITEM) && $location) {

    $quantities = pwwh_core_purchase_api_get_quantities($post);
    $key = pwwh_core_purchase_api_create_key($item->ID, $location->term_id);
    if(isset($quantities[$key])) {
      $_quantity = $quantities[$key];
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
 * @brief     Removes an Item and the related quantities from a purchase.
 * @note      If purchase has no remaining items after the removal it is
 *            completely deleted.
 *
 * @param[in] mixed $post         The Purchase as WP_Post or Post ID
 * @param[in] mixed $items        The ID of the Item to remove or an array of
 *                                item IDs
 *
 * @return    mixed the number of deleted items, true if he whole purchase
 *            has been deleted or false if an error occured.
 * @api
 */
function pwwh_core_purchase_api_remove_item($post = null, $items) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_PURCHASE) {

    $deleted = 0;

    if(!is_array($items)) {
      $items = array($items);
    }

    /* Getting the stored data. */
    $quantities = pwwh_core_purchase_api_get_quantities($post_id);

    /* Removing data from local data structure. */
    foreach($items as $item) {

      /* Getting the keys associated to this item. */
      $keys = pwwh_core_purchase_api_get_keys($post, $item);

      foreach($keys as $key) {
        if(isset($quantities[$key])) {
          unset($quantities[$key]);
          $deleted++;
        }
      }
    }

    /* Checking if any item is remained. */
    if(count($quantities)) {

      /* Updating quantities. */
      pwwh_core_purchase_api_set_quantities($post_id, $quantities);
      return $deleted;
    }
    else {
      /* Deleting the empty purchase. */
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
 * @brief     Returns an array of Purchases related to a specific Item.
 *
 * @param[in] mixed $item         The item as Post object or as Post ID.
 * @param[in] mixed $post_status  The post status of movement to get
 *                                @default{'any'}
 *
 * @return    array an array of Post object.
 */
function pwwh_core_purchase_api_get_by_item($item = null,
                                            $post_status = 'any') {

  if(!is_a($item, 'WP_Post')) {
    $item = get_post($item);
  }

  if(get_post_type($item) == PWWH_CORE_ITEM) {

    /* Searching all the Purchases related to the item. */
    $args = array('post_type' => PWWH_CORE_PURCHASE,
                  'post_status' => $post_status,
                  'nopaging' => true);
    $query = new WP_Query($args);

    /* Checking whereas there are some results. */
    if($query->have_posts()) {
      $purchases = $query->get_posts();

      foreach($purchases as $key => $purchase) {
        $_items = pwwh_core_purchase_api_get_items($purchase);

        if(array_search($item->ID, $_items) === false) {
          /* The current purchase does not have this item. */
          unset($purchases[$key]);
        }
      }
    }
    else {
      $purchases = array();
    }

    wp_reset_postdata();
  }
  else {
    $purchases = array();
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $msg = sprintf('Post type is %s', get_post_type($item));
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
  return $purchases;
}

/*===========================================================================*/
/* Utility APIs                                                              */
/*===========================================================================*/

/**
 * @brief     Gets the quantities of a purchase associated to the related
 *            item from the POST data.
 * @note      The return value will be an associative array in the form
 *            [Item ID]-[Location ID] => [Qnt].
 * @api
 *
 * @return    mixed The purchase quantities as an associative array or null
 *            in case of error.
 */
function pwwh_purchase_api_get_quantities_from_post() {

  /* Verifies nonce */
  if(pwwh_core_api_verify_nonce_from_post(PWWH_CORE_PURCHASE_NONCE_EDIT)) {

    /* Getting UI Facts.  */
    $ui_facts = pwwh_core_purchase_api_get_ui_facts();

    /* Getting Instances. */
    $coll = sanitize_text_field($_POST[$ui_facts['input']['collector']['id']]);
    $insts = explode(':', $coll);

    foreach($insts as $inst) {
      /* Basically the instance identifier is a number. */
      $inst = intval($inst);

      /* Composing identifiers. */
      $_item = $inst . ':' . $ui_facts['input']['item']['id'];
      $_loc = $inst . ':' . $ui_facts['input']['location']['id'];
      $_qnt = $inst . ':' . $ui_facts['input']['quantity']['id'];

      if(isset($_POST[$_item]) && isset($_POST[$_loc]) &&
         isset($_POST[$_qnt])) {
        $item_title = sanitize_text_field(stripslashes_deep($_POST[$_item]));
        $item = pwwh_core_item_api_get_item_by_title($item_title);

        $loc_title = sanitize_text_field(stripslashes_deep($_POST[$_loc]));
        $loc = pwwh_core_item_get_location_by_name($loc_title);

        if(is_a($item, 'WP_Post') && is_a($loc, 'WP_Term')) {
          /* Composing output data. */
          if(isset($_POST[$_qnt]) && (floatval($_POST[$_qnt]) != 0)) {
            $key = pwwh_core_purchase_api_create_key($item->ID, $loc->term_id);
            $_quantities[$key] = floatval($_POST[$_qnt]);
          }
          else {
            $msg = sprintf('Unexpected Quantity in %s()', __FUNCTION__);
            pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
            return null;
          }
        }
        else {
          $msg = sprintf('Unexpected Item or Location in %s()', __FUNCTION__);
          pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
          $msg = sprintf('The item title is %s', $item_title);
          pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
          $msg = sprintf('The location title is %s', $loc_title);
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
 * @brief     Purchase's Item backend query.
 */
define('PWWH_CORE_PURCHASE_QUERY_ITEM', 'item');

/**
 * @brief     Purchase's Quantity backend query.
 */
define('PWWH_CORE_PURCHASE_QUERY_QNT', 'qnt');

/**
 * @brief     Returns the URL to add a new Purchase with pre-set parameters.
 *
 * @param[in] mixed $item         The item as Post object or as Post ID.
 * @param[in] mixed $qnt          The quantity to purchase.
 *
 * @return    string the URL.
 */
function pwwh_core_purchase_api_url_purchase_item($item = null, $qnt = 0) {

  if(!is_a($item, 'WP_Post')) {
    $item = get_post($item);
  }

  $queries = array('post_type' => PWWH_CORE_PURCHASE);

  if(get_post_type($item) == PWWH_CORE_ITEM) {
    $queries[PWWH_CORE_PURCHASE_QUERY_ITEM] = intval($item->ID);

    if(floatval($qnt)) {
      $queries[PWWH_CORE_PURCHASE_QUERY_QNT] = floatval($qnt);
    }
  }

  return pwwh_core_api_admin_url_post_new($queries);
}

/*===========================================================================*/
/* API related to Purchase UI                                                */
/*===========================================================================*/

/**
 * @brief     Returns all the facts related to the User Interface.
 *
 * @api
 *
 * @return    array the facts as an associative array.
 */
function pwwh_core_purchase_api_get_ui_facts() {

  /* Metabox facts. */
  {
    /* Item Summary Metabox facts. */
    $buttons = array('edit' => PWWH_PREFIX . '-qnt-edit',
                     'confirm' => PWWH_PREFIX . '-qnt-edit-confirm',
                     'abort' => PWWH_PREFIX . '-qnt-edit-abort');
    $item_summary = array('id' => PWWH_CORE_PURCHASE . '_item_summary_box',
                          'label' => __('%s related Info', 'piwi-warehouse'),
                          'callback' => 'pwwh_core_purchase_ui_metabox_item_summary',
                          'button' => $buttons);

    /* Add Item Metabox facts. */
    $add_item = array('id' => PWWH_CORE_PURCHASE . '_add_item_box',
                      'label' => __('Purchase more Items', 'piwi-warehouse'),
                      'callback' => 'pwwh_core_purchase_ui_metabox_add_item');

    /* Notes Metabox facts. */
    $notes = array('id' => PWWH_CORE_PURCHASE . '_notes_box',
                   'label' => __('Purchase Notes', 'piwi-warehouse'),
                   'callback' => 'pwwh_core_note_ui_metabox_notes');

    /* Composing Metabox facts. */
    $_boxes = compact('item_summary', 'add_item', 'notes');
  }

  /* Input facts. */
  {
    /* Item Input facts. */
    $rule_remote = array('url' => admin_url('admin-ajax.php'),
                         'action' => PWWH_CORE_PURCHASE . '_validate_item',
                         'callback' => 'pwwh_core_purchase_validate_item_handler');
    $msg_required = __('The Item is mandatory', 'piwi-warehouse');
    $msg_duplicate = __('This Item-Location couple has been already added',
                        'piwi-warehouse');
    $msg_remote = __('This Item does not exist', 'piwi-warehouse');
    $item = array('id' => PWWH_CORE_PURCHASE . '_item',
                 'datalist' => PWWH_CORE_PURCHASE . '_item_list',
                  'rule' => array('required' => true,
                                  'check_duplicate' => true,
                                  'remote' => $rule_remote),
                  'msg' =>array('required' => $msg_required,
                                'check_duplicate' => $msg_duplicate,
                                'remote' => $msg_remote));

    /* Location Input facts. */
    $rule_remote = array('url' => admin_url('admin-ajax.php'),
                         'action' => PWWH_CORE_PURCHASE . '_validate_loc',
                         'callback' => 'pwwh_core_purchase_validate_location_handler');
    $msg_required = __('The Location is mandatory', 'piwi-warehouse');
    $msg_duplicate = __('This Item-Location couple has been already added',
                        'piwi-warehouse');
    $msg_remote = __('This Location does not exist', 'piwi-warehouse');
    $location = array('id' => PWWH_CORE_PURCHASE . '_location',
                      'datalist' => PWWH_CORE_PURCHASE . '_location_list',
                      'rule' => array('required' => true,
                                      'check_duplicate' => true,
                                      'remote' => $rule_remote),
                      'msg' =>array('required' => $msg_required,
                                    'check_duplicate' => $msg_duplicate,
                                    'remote' => $msg_remote));

    /* Quantity Input facts. */
    $msg_required = __('The Quantity is mandatory', 'piwi-warehouse');
    $msg_number = __('The Quantity must be a number', 'piwi-warehouse');
    $msg_not_equal = __('The Quantity cannot be 0', 'piwi-warehouse');
    $msg_step = __('The Quantity must be multiple of 1', 'piwi-warehouse');
    $quantity = array('id' => PWWH_CORE_PURCHASE . '_qnt',
                      'rule' => array('required' => true,
                                      'number' => true,
                                      'not_equal' => 0,
                                      'step' => 1),
                      'msg' =>array('required' => $msg_required,
                                    'number' => $msg_number,
                                    'not_equal' => $msg_not_equal,
                                    'step' => $msg_step));

    /* Collector Input facts. */
    $collector = array('id' =>  PWWH_CORE_PURCHASE . '_collector');

    /* Composing Inputs facts. */
    $_inputs = compact('item', 'location', 'quantity', 'collector');
  }

  /* Composing facts. */
  $facts = array('box' => $_boxes,
                 'input' => $_inputs);

  return $facts;
}

/**
 * @brief     Returns all the facts related to the User Interface.
 *
 * @api
 *
 * @return    array The facts as an associative array.
 */
function pwwh_core_purchase_api_get_purchase_facts() {

  $_statuses = array('auto-draft' => __('Auto Draft', 'piwi-warehouse'),
                     'draft' => __('Draft', 'piwi-warehouse'),
                     'publish' => __('Published', 'piwi-warehouse'));

  $_labels = array('singular' => PWWH_CORE_PURCHASE_LABEL_SINGULAR,
                   'plural' => PWWH_CORE_PURCHASE_LABEL_PLURAL);

  $_type = PWWH_CORE_PURCHASE;

  /* Composing facts. */
  $facts = array('status' => $_statuses,
                 'label' => $_labels,
                 'type' => $_type);

  return $facts;
}
/** @} */