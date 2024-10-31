<?php
/**
 * @file      purchases/purchase-hook.php
 * @brief     Hooks and function related to Purchase post type.
 *
 * @addtogroup PWWH_CORE_PURCHASE
 * @{
 */

/**
 * @brief     Registers the Purchase custom post type and its taxonomies.
 *
 * @hooked    init
 *
 * @return    void
 */
function pwwh_core_purchase_init() {

  /* Registering the Purchase custom post type. */
  {
    $labels = pwwh_core_api_get_post_labels(PWWH_CORE_PURCHASE_LABEL_SINGULAR,
                                            PWWH_CORE_PURCHASE_LABEL_PLURAL);

    $supports = false;

    $rewrite = array('slug' => PWWH_CORE_PURCHASE,
                     'with_front' => false);

    $caps = pwwh_core_purchase_caps_init();

    $args = array('labels' => $labels,
                  'public' => true,
                  'show_ui' => true,
                  'show_in_menu' => false,
                  'supports' => $supports,
                  'has_archive' => true,
                  'capability_type' => PWWH_CORE_PURCHASE,
                  'capabilities' => $caps,
                  'map_meta_cap' => true,
                  'rewrite' => $rewrite,
                  'exclude_from_search' => true,
                  'publicly_queryable' => false);
    register_post_type(PWWH_CORE_PURCHASE, $args);
  }

  /* Configuring Item post list behaviour. */
  pwwh_core_purchase_lists_init();
}
add_action('init', 'pwwh_core_purchase_init');

/**
 * @brief     Enqueues generic scripts.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_purchase_enqueue_style() {

  /* Enqueue Purchase specific Custom Style. */
  wp_enqueue_style(PWWH_PREFIX . '_purchase_css',
                   PWWH_CORE_PURCHASE_URL . '/css/purchase.css');
}
add_action('admin_enqueue_scripts', 'pwwh_core_purchase_enqueue_style');

/**
 * @brief     Disables autosave for the purchases.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_purchase_disable_autosave() {

  /* Disabling autosave for this post type. */
  if(get_post_type() == PWWH_CORE_PURCHASE) {
    wp_dequeue_script('autosave');
  }
}
add_action('admin_enqueue_scripts', 'pwwh_core_purchase_disable_autosave');

/**
 * @brief     Customizes the updated messages for the Purchase post type.
 *
 * @param[in] array $msgs         The array of messages.
 *
 * @hooked    post_updated_messages
 *
 * @return    array the filtered messages array.
 */
function pwwh_core_purchase_custom_post_updated_messages($msgs) {

  $_msgs = pwwh_core_api_post_updated_messages(PWWH_CORE_PURCHASE_LABEL_SINGULAR,
                                               PWWH_CORE_PURCHASE_LABEL_PLURAL);
  $msgs[PWWH_CORE_PURCHASE] = $_msgs;

  return $msgs;
}
add_action('post_updated_messages',
           'pwwh_core_purchase_custom_post_updated_messages');

/**
 * @brief     Customizes the bulk messages for the Purchase post type.
 *
 * @param[in] array $msgs         The array of messages.
 * @param[in] array $bulk_counts   The array of bulk counts.
 *
 * @hooked    bulk_post_updated_messages
 *
 * @return    array the filtered messages array.
 */
function pwwh_core_purchase_custom_bulk_messages($msgs, $bulk_counts) {

  $_msgs = pwwh_core_api_post_bulk_messages(PWWH_CORE_PURCHASE_LABEL_SINGULAR,
                                            PWWH_CORE_PURCHASE_LABEL_PLURAL,
                                            $bulk_counts);
  $msgs[PWWH_CORE_PURCHASE] = $_msgs;

  return $msgs;
}
add_action('bulk_post_updated_messages',
           'pwwh_core_purchase_custom_bulk_messages',  10, 2);

/**
 * @brief     Manages meta boxes for the Purchase custom post type.
 *
 * @param[in] WP_Post $post       Post object
 *
 * @hooked    edit_form_after_title
 *
 * @return    void
 */
function pwwh_core_purchase_manage_body_content($post) {

  $post_id = $post->ID;
  $post_type = get_post_type($post);
  $post_status = get_post_status($post);

  if($post_type == PWWH_CORE_PURCHASE) {

    /* Generating the Title postbox. */
    pwwh_core_ui_post_title($post);

    /* Generating the nonce for this postbox. */
    pwwh_core_ui_nonce(PWWH_CORE_PURCHASE_NONCE_EDIT, $post);

    /* Generating instance collector. */
    pwwh_core_purchase_ui_collector($post, true);

    /* Get UI facts. */
    $ui_facts = pwwh_core_purchase_api_get_ui_facts();

    if(($post_status == 'new') || ($post_status == 'auto-draft') ||
       ($post_status == 'draft')) {
      /* Nothing to do here. */
    }
    else if($post_status == 'publish') {

      /* Getting purchase quantities and item count. */
      $data = pwwh_core_purchase_api_get_quantities($post_id);
      $item_count = count($data);

      /* Verifing UI options. */
      $show_del_ui = pwwh_core_caps_api_current_user_can(PWWH_CORE_PURCHASE_CAPS_DELETE_PURCHASES) &&
                     ($item_count > 1);
      $show_mod_ui = pwwh_core_caps_api_current_user_can(PWWH_CORE_PURCHASE_CAPS_EDIT_QUANTITIES);

      /* Instance counter. */
      $instance_count = 0;
      foreach($data as $key => $qnt) {

        /* A key is in the format [Item ID]-[Location ID]. */
        $item_id = null;
        $loc_id = null;
        pwwh_core_purchase_api_parse_key($key, $item_id, $loc_id);

        /* Adding read-only info. */
        $info_label = sprintf($ui_facts['box']['item_summary']['label'],
                              get_the_title($item_id));
        $args = array('item_id' => $item_id,
                      'loc_id' => $loc_id,
                      'instance' => $instance_count,
                      'show_title' => true,
                      'linked_title' => true,
                      'show_thumb' => true,
                      'show_type' => true,
                      'show_avail' => true,
                      'show_amount' => true,
                      'show_del_ui' => $show_del_ui,
                      'show_mod_ui' => $show_mod_ui,
                      'echo' => false);

        /* Encapsulating data to be compliant with postbox callbacks. */
        $args = array('args' => $args);
        $info = new pwwh_lib_ui_flexbox($ui_facts['box']['item_summary']['id'],
                                        $info_label,
                                        $ui_facts['box']['item_summary']['callback'],
                                        array($post, $args),
                                        'widebox inst-' . $instance_count);
        $info->display();

        $instance_count++;
      }
    }
    else if($post_status == 'trash') {
      /* Nothing to do here. */
    }
    else {
      $msg = sprintf('Unexpected status in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      $msg = sprintf('Post status is %s', $post_status);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    }
  }
}
add_action('edit_form_after_title', 'pwwh_core_purchase_manage_body_content');

/**
 * @brief     Manages meta boxes for the Purchase custom post type.
 *
 * @hooked    add_meta_boxes
 *
 * @return    void
 */
function pwwh_core_purchase_manage_meta_boxes() {

  $post = get_post();
  $post_id = $post->ID;
  $post_type = get_post_type($post);
  $post_status = get_post_status($post);

  if($post_type === PWWH_CORE_PURCHASE) {

    /* Get UI facts. */
    $ui_facts = pwwh_core_purchase_api_get_ui_facts();

    if(($post_status == 'new') || ($post_status == 'auto-draft') ||
       ($post_status == 'draft')) {

      /* Adding Purchase Option postbox. Note that some filed could be
         pre-populated from the $_GET. This is used frome external links such
         the Item's Quick Operations or the Item's row actions. */
      if(isset($_GET[PWWH_CORE_PURCHASE_QUERY_ITEM])) {
        $item_id = intval($_GET[PWWH_CORE_PURCHASE_QUERY_ITEM]);
      }
      else {
        $item_id = null;
      }

      if(isset($_GET[PWWH_CORE_PURCHASE_QUERY_QNT])) {
        $qnt = floatval($_GET[PWWH_CORE_PURCHASE_QUERY_QNT]);
        if($qnt == 0) {
          $qnt = null;
        }
      }
      else {
        $qnt = null;
      }

      $label = $ui_facts['box']['add_item']['label'];
      $args = array('show_item_ui' => true,
                    'show_loc_ui' => true,
                    'show_qnt_ui' => true,
                    'is_primary' => true,
                    'item_id' => $item_id,
                    'qnt_value' => $qnt);
      add_meta_box($ui_facts['box']['add_item']['id'], $label,
                   $ui_facts['box']['add_item']['callback'], PWWH_CORE_PURCHASE,
                   'normal', 'default', $args);

      /* Adding Purchase Discussion postbox. */
      $label = $ui_facts['box']['notes']['label'];
      add_meta_box($ui_facts['box']['notes']['id'], $label,
                   $ui_facts['box']['notes']['callback'], PWWH_CORE_PURCHASE,
                   'normal', 'default');

    }
    else if($post_status == 'publish') {

      /* Adding Purchase Option postbox. */
      $label = $ui_facts['box']['add_item']['label'];
      $count = count(pwwh_core_purchase_api_get_quantities($post_id));

      $args = array('show_item_ui' => false,
                    'show_loc_ui' => false,
                    'show_qnt_ui' => false,
                    'is_primary' => true,
                    'instance' => ($count - 1));
      add_meta_box($ui_facts['box']['add_item']['id'], $label,
                    $ui_facts['box']['add_item']['callback'], PWWH_CORE_PURCHASE,
                   'normal', 'default', $args);

      /* Adding Purchase Discussion postbox. */
      $label = $ui_facts['box']['notes']['label'];
      add_meta_box($ui_facts['box']['notes']['id'], $label,
                   $ui_facts['box']['notes']['callback'], PWWH_CORE_PURCHASE,
                   'normal', 'default');
    }
    else if($post_status == 'trash') {
      /* Nothing to do here. */
    }
    else {
      $msg = sprintf('Unexpected status in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      $msg = sprintf('Post status is %s', $post_status);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    }

    /* Removing unwanted meta boxes. */
    remove_meta_box('slugdiv', PWWH_CORE_PURCHASE, 'normal');
    remove_meta_box('commentsdiv', PWWH_CORE_PURCHASE, 'normal');
  }
}
add_action('add_meta_boxes', 'pwwh_core_purchase_manage_meta_boxes');

/**
 * @brief     Manages the purchase during post stauts transitions.
 *
 * @param[in] string $new         New post status.
 * @param[in] string $old         Old post status.
 * @param[in] WP_Post $post       Post object
 *
 * @hooked    transition_post_status
 *
 * @return    void
 */
function pwwh_core_purchase_manage_status_transitions($new, $old, $post) {

  $post_id = $post->ID;
  $post_type = $post->post_type;

  if(($post->post_type) == PWWH_CORE_PURCHASE) {

    /* Post creation. */
    if(($old == 'new') && ($new == 'auto-draft')) {
      /* This transition is expected but there is nothing to do here. */
    }
    /* Post is going to be published for the first time. */
    else if(($old == 'auto-draft') && ($new == 'publish')) {

      /* Getting new meta data from $_POST. */
      $new_data = pwwh_purchase_api_get_quantities_from_post();

      if($new_data) {
        /* Updating Item accounts. */
        foreach($new_data as $key => $qnt) {
          /* A key is in the format [Item ID]-[Location ID]. */
          $item_id = null;
          $loc_id = null;
          pwwh_core_purchase_api_parse_key($key, $item_id, $loc_id);

          /* Updating Item meta data. */
          pwwh_core_item_api_sum_amount_by_location($item_id, $loc_id,
                                                    floatval($qnt));
          pwwh_core_item_api_sum_avail_by_location($item_id, $loc_id,
                                                   floatval($qnt));
        }

        /* Saving new meta data. */
        pwwh_core_purchase_api_set_quantities($post->ID, $new_data);
      }
      else {
        $msg = sprintf('Corrupted POST data in %s()', __FUNCTION__);
        pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
        $msg = sprintf('Transition from %s to %s', $old, $new);
        pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      }
    }
    /* A published post is going to be updated/edited. */
    else if(($old == 'publish') && ($new == 'publish')) {

      /* Getting new meta data from $_POST. */
      $new_data = pwwh_purchase_api_get_quantities_from_post();

      /* Getting old meta data from database. */
      $old_data = pwwh_core_purchase_api_get_quantities($post);

      if($new_data) {
        /* Getting a unique array keys merging old an new keys.
           This will be used as base to check if a item in a location
           has been removed, added or updated. */
        $keys = array_unique(array_merge(array_keys($new_data),
                                         array_keys($old_data)));

        foreach($keys as $key) {

          /* Parsing the key. */
          $item_id = null;
          $loc_id = null;
          pwwh_core_purchase_api_parse_key($key, $item_id, $loc_id);

          if(isset($new_data[$key]) && !isset($old_data[$key])) {
            /* Item just added. */

            /* Computing the delta quantity. */
            $delta = floatval($new_data[$key]);

            /* Updating Item meta data. */
            pwwh_core_item_api_sum_amount_by_location($item_id, $loc_id,
                                                      $delta);
            pwwh_core_item_api_sum_avail_by_location($item_id, $loc_id,
                                                     $delta);
          }
          else if(isset($old_data[$key]) && !isset($new_data[$key])) {
            /* Item just deleted. */

            if(pwwh_core_caps_api_current_user_can(PWWH_CORE_PURCHASE_CAPS_DELETE_PURCHASES)) {
              /* Computing the delta quantity. */
              $delta = (-1 * floatval($old_data[$key]));

              /* Updating Item meta data. */
              pwwh_core_item_api_sum_amount_by_location($item_id, $loc_id,
                                                        $delta);
              pwwh_core_item_api_sum_avail_by_location($item_id, $loc_id,
                                                       $delta);
            }
            else {
              /* The user cannot do that. Restoring the deleted data.*/
              $new_data[$key] = $old_data[$key];
              $msg = sprintf('Unexpected item deletion in %s()', __FUNCTION__);
              pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
              $current_user = wp_get_current_user();
              $msg = sprintf('The user is %s %s',
                             $current_user->user_firstname,
                             $current_user->user_lastname);
              pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
            }
          }
          else {
            /* Checking if item quantity shoud be updated updated. */
            $new_qnt = floatval($new_data[$key]);
            $old_qnt = floatval($old_data[$key]);

            if($new_qnt != $old_qnt) {
              /* Item quantity changed. */
              if(pwwh_core_caps_api_current_user_can(PWWH_CORE_PURCHASE_CAPS_EDIT_QUANTITIES)) {

                /* Computing the delta quantity. */
                $delta = ($new_qnt - $old_qnt);

                /* Updating Item meta data. */
                pwwh_core_item_api_sum_amount_by_location($item_id, $loc_id,
                                                          $delta);
                pwwh_core_item_api_sum_avail_by_location($item_id, $loc_id,
                                                         $delta);
              }
              else {
                /* The user cannot do that. Restoring the deleted data.*/
                $new_data[$key] = $old_data[$key];
                $msg = sprintf('Unexpected item quantity update in %s()',
                               __FUNCTION__);
                pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
                $current_user = wp_get_current_user();
                $msg = sprintf('The user is %s %s',
                               $current_user->user_firstname,
                               $current_user->user_lastname);
                pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
              }
            }
          }
        }

        /* Saving new meta data. */
        pwwh_core_purchase_api_set_quantities($post->ID, $new_data);

      }
      else {
        $msg = sprintf('Corrupted POST data in %s()', __FUNCTION__);
        pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
        $msg = sprintf('Transition from %s to %s', $old, $new);
        pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      }
    }
    /* A published post is going to be trashed. */
    else if(($old == 'publish') && ($new == 'trash')) {

      /* Getting old meta data from database. */
      $old_data = pwwh_core_purchase_api_get_quantities($post);

      /* Updating Item accounts. */
      foreach($old_data as $key => $qnt) {

        /* Parsing the key. */
        $item_id = null;
        $loc_id = null;
        pwwh_core_purchase_api_parse_key($key, $item_id, $loc_id);

        /* Computing the delta quantity. */
        $delta = (-1 * floatval($qnt));

        /* Updating Item meta data. */
        pwwh_core_item_api_sum_amount_by_location($item_id, $loc_id,
                                                  $delta);
        pwwh_core_item_api_sum_avail_by_location($item_id, $loc_id,
                                                 $delta);
      }
    }
    /* A published post is going to be un-trashed. */
    else if(($old == 'trash') && ($new == 'publish')) {

      /* Getting old meta data from database. */
      $old_data = pwwh_core_purchase_api_get_quantities($post);

      /* Updating Item accounts. */
      foreach($old_data as $key => $qnt) {

        /* Parsing the key. */
        $item_id = null;
        $loc_id = null;
        pwwh_core_purchase_api_parse_key($key, $item_id, $loc_id);

        /* Computing the delta quantity. */
        $delta = floatval($qnt);

        /* Updating Item meta data. */
        pwwh_core_item_api_sum_amount_by_location($item_id, $loc_id,
                                                  $delta);
        pwwh_core_item_api_sum_avail_by_location($item_id, $loc_id,
                                                 $delta);
      }
    }
    else {
      $msg = 'Unexpected transition status in pwwh_purchase_on_transition()';
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      $msg = sprintf('Transition is from %s to %s', $old, $new);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    }
  }
}
add_action('transition_post_status',
           'pwwh_core_purchase_manage_status_transitions', 10, 3);

/**
 * @brief     Restores availability of items when a purchase is deleted and
 *            destroys its history.
 *
 * @param[in] int $post_id        Post ID
 *
 * @hooked    save_post
 *
 * @return    int the post ID
 */
function pwwh_core_purchase_on_delete($post_id) {

  $post_type = get_post_type($post_id);

  /* This must act only on purchases. */
  if(($post_type) == PWWH_CORE_PURCHASE) {

    /* If the post went through the trash the status transition hook
       restores the Items availabilities. If not, this hook has to take
       care of it. This happens if an published purchase is deleted
       permanently. */
    if(get_post_status($post_id) == 'publish') {
      /* Getting meta data from database. */
      $qnts = pwwh_core_purchase_api_get_quantities($post_id);

      /* Updating Item accounts. */
      foreach($qnts as $key => $qnt) {

        /* Parsing the key. */
        $item_id = null;
        $loc_id = null;
        pwwh_core_purchase_api_parse_key($key, $item_id, $loc_id);

        /* Computing the delta quantity. */
        $delta = (-1 * floatval($qnt));

        /* Updating Item meta data. */
        pwwh_core_item_api_sum_amount_by_location($item_id, $loc_id,
                                                  $delta);
        pwwh_core_item_api_sum_avail_by_location($item_id, $loc_id,
                                                 $delta);
      }
    }
  }
}
add_action('before_delete_post', 'pwwh_core_purchase_on_delete');
/** @} */