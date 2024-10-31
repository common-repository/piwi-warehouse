<?php
/**
 * @file      items/item-api.php
 * @brief     API related to Item post type.
 *
 * @addtogroup PWWH_CORE_ITEM
 * @{
 */

/*===========================================================================*/
/* Generic APIs                                                              */
/*===========================================================================*/

/**
 * @brief     Retrieves an Item starting from its title.
 *
 * @param[in] string $title       The Item title
 *
 * @return    mixed the Item as WP_Post or false.
 * @api
 */
function pwwh_core_item_api_get_item_by_title($title) {

  if($title) {
    $args = array('post_type' => PWWH_CORE_ITEM,
                  'posts_per_page' => -1,
                  'post_status' => 'publish',
                  'title' => $title);
    $items = get_posts($args);
    if((count($items) == 1) && is_a($items[0], 'WP_Post')) {
      return $items[0];
    }
    else if(count($items) > 1) {
      $msg = sprintf('Multiple item with the same name in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      $msg = sprintf('The name is: %s', $title);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      return false;
    }
    else {
      return false;
    }
  }
  else {
    return false;
  }
}

/*===========================================================================*/
/* API related to Item's Locations meta                                      */
/*===========================================================================*/

/**
 * @brief     Checks that the ID or WP_Term belongs to a valid location and
 *            returns the associated WP_Term.
 *
 * @param[in] mixed $term         The Location as WP_Term or Term ID
 *
 * @return    mixed the term if $term is a valid ID or Term, false otherwise.
 * @api
 */
function pwwh_core_item_api_sanitize_location($term = null) {

  /* Sanitizing the location. */
  $term = get_term($term, PWWH_CORE_ITEM_LOCATION, OBJECT);

  if(is_a($term, 'WP_Term')) {
    return $term;
  }
  else {
    return false;
  }
}

/**
 * @brief     Retrieves a Location starting from its name.
 *
 * @param[in] string $name        The Location name
 *
 * @return    mixed the Location as WP_Term or false.
 * @api
 */
function pwwh_core_item_get_location_by_name($name) {

  $loc = get_term_by('name', $name, PWWH_CORE_ITEM_LOCATION, OBJECT);

  if(is_a($loc, 'WP_Term')) {
    return $loc;
  }
  else {
    return false;
  }
}

/**
 * @brief     Gets the location name.
 *
 * @param[in] mixed $term         The Location as WP_Term or Term ID
 *
 * @return    mixed the location name or false.
 * @api
 */
function pwwh_core_item_api_get_location_name($term = null) {

  /* Sanitizing the location. */
  $term = pwwh_core_item_api_sanitize_location($term);

  if($term) {
    return $term->name;
  }
  else {
    return false;
  }
}

/**
 * @brief     Assigns a new location to an item appending or overwriting it.
 *
 * @param[in] mixed $post         The Item as WP_Post or Post ID
 * @param[in] mixed $term         The Location as WP_Term or Term ID
 * @param[in] bool $append        If true appends the Location otherwise
 *                                replaces it
 *
 * @return    bool the operation status.
 * @api
 */
function pwwh_core_item_api_assign_location($post = null, $term = null,
                                            $append = true) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_ITEM) {
    /* Sanitizing the location. */
    $term = pwwh_core_item_api_sanitize_location($term);
    if($term) {
      return is_array(wp_set_post_terms($post->ID, $term->term_id,
                                        PWWH_CORE_ITEM_LOCATION, $append));
    }
    else {
      $msg = sprintf('Unexpected term type in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      return false;
    }
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    return false;
  }
}

/**
 * @brief     Deassign a location from an item.
 * @note      If Term is null all the locations are deassigned.
 *
 * @param[in] mixed $post         The Item as WP_Post or Post ID
 * @param[in] mixed $term         The Location as WP_Term or Term ID
 *
 * @return    bool the operation status.
 * @api
 */
function pwwh_core_item_api_deassign_location($post = null, $term = null) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_ITEM) {

    if($term === null) {
      $terms = wp_get_post_terms($post->ID, PWWH_CORE_ITEM_LOCATION);

      $res = true;
      foreach($terms as $term) {
        $res |= wp_remove_object_terms($post->ID, $term->term_id,
                                       PWWH_CORE_ITEM_LOCATION);
      }
      return $res;
    }
    else {
      /* Sanitizing the location. */
      $term = pwwh_core_item_api_sanitize_location($term);
      if($term) {
        return wp_remove_object_terms($post->ID, $term->term_id,
                                      PWWH_CORE_ITEM_LOCATION);
      }
      else {
        $msg = sprintf('Unexpected term type in %s()', __FUNCTION__);
        pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
        return false;
      }
    }
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    return false;
  }
}

/*===========================================================================*/
/* API related to Item's Amount meta                                         */
/*===========================================================================*/

/**
 * @brief     Item Amount post meta identifier.
 */
define('PWWH_CORE_ITEM_META_AMOUNT', PWWH_CORE_ITEM . '_amount');

/**
 * @brief     Gets the amount values of an item.
 * @note      The return value will be an associative array
 *            in the form [Loc ID] => [Amount] where the total is associated
 *            to the key '0'.
 *
 * @param[in] mixed $post         The Item as WP_Post or Post ID
 *
 * @return    array  The item amounts as an associative array.
 * @api
 */
function pwwh_core_item_api_get_amounts($post = null) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_ITEM) {

    $amounts = get_post_meta($post_id, PWWH_CORE_ITEM_META_AMOUNT, true);

    if(!is_array($amounts)) {
      $amounts = array('0' => '0');
    }
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $amounts = array('0' => '0');
  }
  return $amounts;
}

/**
 * @brief     Sets the amount values of an item.
 * @note      $amounts shall be an associative array in the form
 *            [Loc ID] => [Amount] where the total is associated
 *            to the key '0'.
 *
 * @param[in] mixed $post         The Item as WP_Post or Post ID
 * @param[in] array $amounts      The associative array of amounts
 *
 * @return    void.
 * @api
 */
function pwwh_core_item_api_set_amounts($post = null, $amounts) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_ITEM) {
    if(is_array($amounts) && isset($amounts['0'])) {
      update_post_meta($post_id, PWWH_CORE_ITEM_META_AMOUNT, $amounts);
    }
    else {
      $msg = sprintf('Unexpected amount format in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    }
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
}

/**
 * @brief     Explicitily deletes the amount post meta.
 *
 * @param[in] mixed $post         The Item as WP_Post or Post ID
 *
 * @return    bool the operation status
 * @api
 */
function pwwh_core_item_api_delete_amounts($post = null) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_ITEM) {

    /* Deleting the post meta. */
    return delete_post_meta($post_id, PWWH_CORE_ITEM_META_AMOUNT);
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    return false;
  }
}

/**
 * @brief     Gets the total amount values of an item.
 *
 * @param[in] mixed $post         The Item as WP_Post or Post ID
 * @param[in] mixed $term         The Location as WP_Term or Term ID
 *
 * @return    mixed the amount of a specific location.
 * @api
 */
function pwwh_core_item_api_get_amount($post = null) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_ITEM) {

    /* Getting current amounts. */
    $amounts = pwwh_core_item_api_get_amounts($post);

    $amount = $amounts['0'];
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $amount = '0';
  }

  return $amount;
}

/**
 * @brief     Gets the amount values of an item in a specific location.
 *
 * @param[in] mixed $post         The Item as WP_Post or Post ID
 * @param[in] mixed $term         The Location as WP_Term or Term ID
 * @param[in] boolean $strict     If true gives the availability of this
 *                                specific location otherwise sums also the
 *                                availabilities of all its children.
 *                                @default{true}
 *
 * @return    mixed the amount of a specific location.
 * @api
 */
function pwwh_core_item_api_get_amount_by_location($post = null, $term = null,
                                                   $strict = true) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_ITEM) {
    $loc = pwwh_core_item_api_sanitize_location($term);

    if($loc) {
      /* Getting current amounts. */
      $amounts = pwwh_core_item_api_get_amounts($post);

      if($strict) {
        if(isset($amounts[$loc->term_id])) {
          $amount = $amounts[$loc->term_id];
        }
        else {
          $amount = '0';
        }
      }
      else {
        /* Getting location children. */
        $child_ids = get_term_children($loc->term_id, PWWH_CORE_ITEM_LOCATION);
        array_push($child_ids, $loc->term_id);

        $amount = 0;
        foreach($child_ids as $child_id) {
          if(isset($amounts[$child_id])) {
            $amount += floatval($amounts[$child_id]);
          }
          else {
            $amount += 0;
          }
        }
      }
    }
    else {
      $msg = sprintf('Wrong location format in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      $amount = '0';
    }
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $amount = '0';
  }
  return $amount;
}

/**
 * @brief     Sets the amount values of an item in a specific location.
 * @note      Amount is expected to be numeric.
 * @note      This function overwrites the existing amount
 *
 * @param[in] mixed $post         The Item as WP_Post or Post ID
   @param[in] mixed $term         The Location as WP_Term or Term ID
 * @param[in] mixed $amount       The amount value.
 *
 * @return    void
 * @api
 */
function pwwh_core_item_api_set_amount_by_location($post = null, $term = null,
                                                   $amount) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_ITEM) {

    $loc = pwwh_core_item_api_sanitize_location($term);

    if($loc) {
      if(is_numeric($amount)) {

        /* Converting amount to a number. */
        $amount = floatval($amount);

        /* Getting current amounts. */
        $amounts = pwwh_core_item_api_get_amounts($post);

        if($amount) {
          /* Updating the current total. */
          if(isset($amounts[$loc->term_id])) {
            $amounts['0'] -= $amounts[$loc->term_id];
          }
          $amounts['0'] += $amount;

          /* Pushing the new element. */
          $amounts[$loc->term_id] = $amount;
        }
        else {
          /* Updating the current total and deleting the key. */
          if(isset($amounts[$loc->term_id])) {
            $amounts['0'] -= $amounts[$loc->term_id];
            unset($amounts[$loc->term_id]);
          }
        }

        /* Updating the amounts. */
        pwwh_core_item_api_set_amounts($post, $amounts);
      }
      else {
        $msg = sprintf('Wrong amount format in %s()', __FUNCTION__);
        pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      }
    }
    else {
      $msg = sprintf('Wrong location format in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    }
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
}

/**
 * @brief     Updates item amount value in a specific location making an
 *            algebraic sum between current stored value and $delta.
 * @note      Amount is expected to be numeric.
 * @note      This function overwrites the existing amount
 *
 * @param[in] mixed $post         The Item as WP_Post or Post ID
   @param[in] mixed $term         The Location as WP_Term or Term ID
 * @param[in] mixed $delta        The delta to sum.
 *
 * @return    void
 * @api
 */
function pwwh_core_item_api_sum_amount_by_location($post = null, $term = null,
                                                   $delta) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_ITEM) {

    $loc = pwwh_core_item_api_sanitize_location($term);

    if($loc) {
      if(is_numeric($delta)) {

        /* Converting delta to a number. */
        $delta = floatval($delta);

        /* Getting current amounts. */
        $amounts = pwwh_core_item_api_get_amounts($post);

        if($delta) {
          if(isset($amounts[$loc->term_id])) {
            /* The stating amount was different from 0. Adding a delta. */
            $amounts[$loc->term_id] += $delta;

            if($amounts[$loc->term_id] == 0) {
              /* The new total is 0. Removing the element. */
              unset($amounts[$loc->term_id]);
            }
          }
          else {
            /* The stating amount was 0. Insering the delta. */
            $amounts[$loc->term_id] = $delta;
          }

          /* Updating the total. */
          $amounts['0'] += $delta;
        }
        else {
          /* Nothing to do. */
        }

        /* At this point could be that all location are empty and the array
           only contains the total that shall be 0. In this case make no sense
           to store the data. Hence the data is deleted. */
        if(count($amounts) > 1) {
          /* Updating the amounts. */
          pwwh_core_item_api_set_amounts($post, $amounts);
        }
        else {
          /* Deleting the amounts. */
          pwwh_core_item_api_delete_amounts($post);
        }
      }
      else {
        $msg = sprintf('Wrong amount format in %s()', __FUNCTION__);
        pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      }
    }
    else {
      $msg = sprintf('Wrong location format in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    }
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
}

/*===========================================================================*/
/* API related to Item's Availability meta                                   */
/*===========================================================================*/

/**
 * @brief     Item Availability post meta identifier.
 */
define('PWWH_CORE_ITEM_META_AVAIL', PWWH_CORE_ITEM . '_avail');

/**
 * @brief     Gets the availability values of an item.
 * @note      The return value will be an associative array
 *            in the form [Loc ID] => [Availability] where the total is associated
 *            to the key '0'.
 *
 * @param[in] mixed $post         The Item as WP_Post or Post ID
 *
 * @return    array  The item avails as an associative array.
 * @api
 */
function pwwh_core_item_api_get_avails($post = null) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_ITEM) {

    $avails = get_post_meta($post_id, PWWH_CORE_ITEM_META_AVAIL, true);

    if(!is_array($avails)) {
      $avails = array('0' => '0');
    }
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $avails = array('0' => '0');
  }
  return $avails;
}

/**
 * @brief     Sets the availability values of an item.
 * @note      $avails shall be an associative array in the form
 *            [Loc ID] => [Availability] where the total is associated
 *            to the key '0'.
 *
 * @param[in] mixed $post         The Item as WP_Post or Post ID
 * @param[in] array $avails      The associative array of avails
 *
 * @return    void.
 * @api
 */
function pwwh_core_item_api_set_avails($post = null, $avails) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_ITEM) {
    if(is_array($avails) && isset($avails['0'])) {
      update_post_meta($post_id, PWWH_CORE_ITEM_META_AVAIL, $avails);
    }
    else {
      $msg = sprintf('Unexpected avail format in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    }
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
}

/**
 * @brief     Explicitily deletes the availability post meta.
 *
 * @param[in] mixed $post         The Item as WP_Post or Post ID
 *
 * @return    bool the operation status
 * @api
 */
function pwwh_core_item_api_delete_avails($post = null) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_ITEM) {
    /* Deassigning al the locations to the Item. */
    $res = pwwh_core_item_api_deassign_location($post_id);

    /* Deleting the post meta. */
    $res |= delete_post_meta($post_id, PWWH_CORE_ITEM_META_AVAIL);
    return $res;
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    return false;
  }
}

/**
 * @brief     Gets the total availability values of an item.
 *
 * @param[in] mixed $post         The Item as WP_Post or Post ID
 * @param[in] mixed $term         The Location as WP_Term or Term ID
 *
 * @return    mixed the avail of a specific location.
 * @api
 */
function pwwh_core_item_api_get_avail($post = null) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_ITEM) {

    /* Getting current avails. */
    $avails = pwwh_core_item_api_get_avails($post);

    $avail = $avails['0'];
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $avail = '0';
  }

  return $avail;
}

/**
 * @brief     Gets the availability values of an item in a specific location.
 *
 * @param[in] mixed $post         The Item as WP_Post or Post ID
 * @param[in] mixed $term         The Location as WP_Term or Term ID
 * @param[in] boolean $strict     If true gives the availability of this
 *                                specific location otherwise sums also the
 *                                availabilities of all its children.
 *                                @default{true}
 *
 * @return    mixed the avail of a specific location.
 * @api
 */
function pwwh_core_item_api_get_avail_by_location($post = null, $term = null,
                                                  $strict = true) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_ITEM) {
    $loc = pwwh_core_item_api_sanitize_location($term);

    if($loc) {
      /* Getting current avails. */
      $avails = pwwh_core_item_api_get_avails($post);

      if($strict) {
        if(isset($avails[$loc->term_id])) {
          $avail = $avails[$loc->term_id];
        }
        else {
          $avail = '0';
        }
      }
      else {
        /* Getting location children. */
        $child_ids = get_term_children($loc->term_id, PWWH_CORE_ITEM_LOCATION);
        array_push($child_ids, $loc->term_id);

        $avail = 0;
        foreach($child_ids as $child_id) {
          if(isset($avails[$child_id])) {
            $avail += floatval($avails[$child_id]);
          }
          else {
            $avail += 0;
          }
        }
      }
    }
    else {
      $msg = sprintf('Wrong location format in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      $avail = '0';
    }
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $avail = '0';
  }
  return $avail;
}

/**
 * @brief     Sets the availability values of an item in a specific location.
 * @note      Availability is expected to be numeric.
 * @note      This function overwrites the existing avail
 *
 * @param[in] mixed $post         The Item as WP_Post or Post ID
   @param[in] mixed $term         The Location as WP_Term or Term ID
 * @param[in] mixed $avail       The avail value.
 *
 * @return    void
 * @api
 */
function pwwh_core_item_api_set_avail_by_location($post = null, $term = null,
                                                   $avail) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_ITEM) {

    $loc = pwwh_core_item_api_sanitize_location($term);

    if($loc) {
      if(is_numeric($avail)) {

        /* Converting avail to a number. */
        $avail = floatval($avail);

        /* Getting current avails. */
        $avails = pwwh_core_item_api_get_avails($post);

        if($avail) {
          /* Updating the current total. */
          if(isset($avails[$loc->term_id])) {
            $avails['0'] -= $avails[$loc->term_id];
          }
          $avails['0'] += $avail;

          /* Pushing the new element. */
          $avails[$loc->term_id] = $avail;

          /* Assigning this location to the item. */
          pwwh_core_item_api_assign_location($post_id, $loc->term_id);
        }
        else {
          /* Updating the current total and deleting the key. */
          if(isset($avails[$loc->term_id])) {
            $avails['0'] -= $avails[$loc->term_id];
            unset($avails[$loc->term_id]);
          }
        }

        /* Updating the avails. */
        pwwh_core_item_api_set_avails($post, $avails);
      }
      else {
        $msg = sprintf('Wrong avail format in %s()', __FUNCTION__);
        pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      }
    }
    else {
      $msg = sprintf('Wrong location format in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    }
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
}

/**
 * @brief     Updates item availability value in a specific location making an
 *            algebraic sum between current stored value and $delta.
 * @note      Availability is expected to be numeric.
 * @note      This function overwrites the existing avail
 *
 * @param[in] mixed $post         The Item as WP_Post or Post ID
   @param[in] mixed $term         The Location as WP_Term or Term ID
 * @param[in] mixed $delta        The delta to sum.
 *
 * @return    void
 * @api
 */
function pwwh_core_item_api_sum_avail_by_location($post = null, $term = null,
                                                   $delta) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_id = $post->ID;
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_ITEM) {

    $loc = pwwh_core_item_api_sanitize_location($term);

    if($loc) {
      if(is_numeric($delta)) {

        /* Converting delta to a number. */
        $delta = floatval($delta);

        /* Getting current avails. */
        $avails = pwwh_core_item_api_get_avails($post);

        if($delta) {
          if(isset($avails[$loc->term_id])) {
            /* The stating avail was different from 0. Adding a delta. */
            $avails[$loc->term_id] += $delta;

            if($avails[$loc->term_id] == 0) {
              /* The new total is 0. Removing the element. */
              unset($avails[$loc->term_id]);

              /* Deassigning this location to the item. */
              pwwh_core_item_api_deassign_location($post_id, $loc->term_id);
            }
          }
          else {
            /* The stating avail was 0. Insering the delta. */
            $avails[$loc->term_id] = $delta;

            /* Assigning this location to the item. */
            pwwh_core_item_api_assign_location($post_id, $loc->term_id);
          }

          /* Updating the total. */
          $avails['0'] += $delta;
        }
        else {
          /* Nothing to do. */
        }

        /* At this point could be that all location are empty and the array
           only contains the total that shall be 0. In this case make no sense
           to store the data. Hence the data is deleted. */
        if(count($avails) > 1) {
          /* Updating the avails. */
          pwwh_core_item_api_set_avails($post, $avails);
        }
        else {
          /* Deleting the avails. */
          pwwh_core_item_api_delete_avails($post);
        }
      }
      else {
        $msg = sprintf('Wrong avail format in %s()', __FUNCTION__);
        pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      }
    }
    else {
      $msg = sprintf('Wrong location format in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    }
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
}

/*===========================================================================*/
/* API related to Item UI                                                    */
/*===========================================================================*/

/**
 * @brief     Returns all the facts related to the User Interface.
 *
 * @api
 *
 * @return    array the facts as an associative array.
 */
function pwwh_core_item_api_get_ui_facts() {

  /* Metabox facts. */
  {
    /* Records Metabox facts. */
    $records = array('id' => PWWH_CORE_ITEM . '_records_box',
                     'label' => __('Records', 'piwi-warehouse'),
                     'callback' => 'pwwh_core_item_ui_metabox_records');

    /* Quick Ops Metabox facts. */
    $quick_ops = array('id' => PWWH_CORE_ITEM . '_quick_ops_box',
                       'label' => __('Quick Operations', 'piwi-warehouse'),
                       'callback' => 'pwwh_core_item_ui_metabox_quick_ops');

    /* Item Summary Metabox facts. */
    $item_summary = array('id' => PWWH_CORE_ITEM . '_summary_box',
                          'label' => __('%s related Info', 'piwi-warehouse'),
                          'callback' => 'pwwh_core_item_ui_metabox_item_summary');

    /* Composing Metabox facts. */
    $_boxes = compact('records', 'quick_ops', 'item_summary');
  }

  /* Input facts. */
  {
    /* Title Input facts. */
    $rule_remote = array('url' => admin_url('admin-ajax.php'),
                         'action' => PWWH_CORE_ITEM . '_validate_title',
                         'callback' => 'pwwh_core_item_validate_title_handler');
    $msg_required = __('The Item name is mandatory', 'piwi-warehouse');
    $msg_remote = __('An Item having this name already exists',
                     'piwi-warehouse');
    $title = array('id' => 'title',
                   'rule' => array('required' => true,
                                   'remote' => $rule_remote),
                   'msg' =>array('required' => $msg_required,
                                'remote' => $msg_remote));

    /* Composing Inputs facts. */
    $_inputs = compact('title');
  }

  /* Composing facts. */
  $facts = array('box' => $_boxes,
                 'input' => $_inputs);

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
function pwwh_core_item_api_get_ui_edit_tag_facts() {

  /* Input facts. */
  {

    /* Location Input facts. */
    $rule_remote = array('url' => admin_url('admin-ajax.php'),
                         'action' => PWWH_CORE_ITEM . '_validate_location',
                         'callback' => 'pwwh_core_item_validate_location_handler');
    $msg_remote = __('A Location having this name already exists',
                     'piwi-warehouse');
    $location = array('id' => 'tag-name',
                      'rule' => array('remote' => $rule_remote),
                      'msg' =>array('remote' => $msg_remote));

    /* Composing Inputs facts. */
    $_inputs = compact('location');
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
function pwwh_core_item_api_get_item_facts() {

  $_statuses = array('auto-draft' => __('Auto Draft', 'piwi-warehouse'),
                     'draft' => __('Draft', 'piwi-warehouse'),
                     'publish' => __('Published', 'piwi-warehouse'));

  $_labels = array('singular' => PWWH_CORE_ITEM_LABEL_SINGULAR,
                   'plural' => PWWH_CORE_ITEM_LABEL_PLURAL);

  $_type = PWWH_CORE_ITEM;

  $_taxonomy = array('location' => PWWH_CORE_ITEM_LOCATION,
                     'type' => PWWH_CORE_ITEM_TYPE);

  /* Composing facts. */
  $facts = array('status' => $_statuses,
                 'label' => $_labels,
                 'type' => $_type,
                 'taxonomy' => $_taxonomy);

  return $facts;
}

/*===========================================================================*/
/* API related to Item content                                               */
/*===========================================================================*/

/**
 * @brief     Returns/Displays the Location list of the Item.
 *
 * @param[in] mixed $post         A Post object or the Post ID
 * @param[in] array $args         An array of arguments to compose the HTML.
 * @paramkey{id}                  The list id. @default{''}
 * @paramkey{classes}             An array of classes or a string for the list.
 *                                @default{false}
 * @paramkey{sublist_classes}     An array of classes or a string for the
 *                                sublist. @default{false}
 * @paramkey{item_classes}        An array of classes or a string for the
 *                                list item. @default{true}
 * @paramkey{depth}               The list hierarchical depth: -1 means flatly
 *                                display every element; 0 means display all
 *                                levels. > 0 specifies the number of display
 *                                levels. @default{0}
 * @paramkey{avail}               Display the location availability on true.
 *                                @default{true}
 * @paramkey{echo}                Echoes if true return elsewhere.
 *                                @default{false}
 *
 * @return    mixed the list as HTML string or FALSE.
 * @api
 */
function pwwh_core_item_api_get_location_list($post = null, $args = array()) {

  /* Validating array keys. */
  $id = pwwh_lib_utils_validate_array_field($args, 'id', '');
  $classes = pwwh_lib_utils_validate_array_field($args, 'classes', '');
  $sublist_classes = pwwh_lib_utils_validate_array_field($args, 'sublist_classes',
                                                         '');
  $item_classes = pwwh_lib_utils_validate_array_field($args, 'item_classes', '');
  $depth = pwwh_lib_utils_validate_array_field($args, 'depth', 0);
  $avail = pwwh_lib_utils_validate_array_field($args, 'avail', true);
  $echo = pwwh_lib_utils_validate_array_field($args, 'echo', false);

  /* Checking post consistency. */
  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }

  /* Composing input array for the Walker.  */
  $avails = pwwh_core_item_api_get_avails($post->ID);
  unset($avails['0']);

  $locations = array_keys($avails);
  foreach ($avails as $key => $value) {
    $anc = get_ancestors($key, PWWH_CORE_ITEM_LOCATION, 'taxonomy');
    $locations = array_merge($locations, $anc);
  }

  $locations = array_unique($locations);

  $_locations = array();
  foreach ($locations as $loc) {
    array_push($_locations, get_term($loc));
  }

  /* Composing data array for the Walker.  */
  /* Converting sublist-classes to string. */
  if($sublist_classes) {
    if(is_array($sublist_classes)) {
      $sublist_classes = implode(' ', $sublist_classes);
    }
  }

  /* Converting item-classes to string. */
  if($item_classes) {
    if(is_array($item_classes)) {
      $item_classes = implode(' ', $item_classes);
    }
  }

  $data = array('sublist_classes' => $sublist_classes,
                'item_classes' => $item_classes,
                'avail' => $avail);

  /* Composing locations list. */
  $walker = new pwwh_walker_locations();
  $list = $walker->walk($_locations, $depth, $data);

  /* Composing output. */
  if($list) {
    $_id = pwwh_lib_ui_form_attribute('id', $id);

    if($classes) {
      if(is_array($classes)) {
        $classes = implode(' ', $classes);
      }
    }
    $classes .= ' main-list';
    $_classes = plsr_lib_ui_form_attribute('class', $classes);

    $output = '<ul' . $_id . $_classes . '>' .
                  $list .
              '</ul>';
  }
  else {
    $output = '';
  }

  /* Deciding whether echo or not. */
  if($echo)
    echo $output;
  else
    return $output;
}
/** @} */