<?php
/**
 * @file      movements/movement-hook.php
 * @brief     Hooks and function related to Movement post type.
 *
 * @addtogroup PWWH_CORE_MOVEMENT
 * @{
 */

/**
 * @brief     Registers custom post type.
 *
 * @hooked    init
 *
 * @return    void
 */
function pwwh_core_movement_init() {

  /* Registering the Movement custom post type. */
  {
    $labels = pwwh_core_api_get_post_labels(PWWH_CORE_MOVEMENT_LABEL_SINGULAR,
                                            PWWH_CORE_MOVEMENT_LABEL_PLURAL);

    $supports = false;

    $rewrite = array('slug' => PWWH_CORE_MOVEMENT,
                     'with_front' => false);

    $caps = pwwh_core_movement_caps_init();

    $args = array('labels' => $labels,
                  'public' => true,
                  'show_ui' => true,
                  'show_in_menu' => false,
                  'supports' => $supports,
                  'has_archive' => true,
                  'capability_type' => PWWH_CORE_MOVEMENT,
                  'capabilities' => $caps,
                  'map_meta_cap' => true,
                  'rewrite' => $rewrite,
                  'exclude_from_search' => true,
                  'publicly_queryable' => false);
    register_post_type(PWWH_CORE_MOVEMENT, $args);
  }

  /* Registering the Holder taxonomy. */
  {
    $labels = pwwh_core_api_get_tax_labels(PWWH_CORE_MOVEMENT_HOLDER_LABEL_SINGULAR,
                                           PWWH_CORE_MOVEMENT_HOLDER_LABEL_PLURAL);

    $caps = pwwh_core_movement_caps_holder_init();

    $args = array('hierarchical' => true,
                  'labels' => $labels,
                  'show_ui' => true,
                  'meta_box_cb' => false,
                  'show_in_quick_edit' => false,
                  'update_count_callback' => '_update_post_term_count',
                  'show_admin_column' => true,
                  'query_var' => true,
                  'capabilities' => $caps,
                  'rewrite' => array('slug' => PWWH_CORE_MOVEMENT_HOLDER));
    register_taxonomy(PWWH_CORE_MOVEMENT_HOLDER, PWWH_CORE_MOVEMENT, $args);
  }

  /* Adding new custom statuses. */
  {
    $label = _n_noop('Active <span class="count">(%s)</span>',
                     'Active <span class="count">(%s)</span>',
                     'piwi-warehouse');
    $args = array('label' => __('Active', 'piwi-warehouse'),
                  'public' => true,
                  'exclude_from_search' => false,
                  'show_in_admin_all_list' => true,
                  'show_in_admin_status_list' => true,
                  'label_count' => $label);
    register_post_status(PWWH_CORE_MOVEMENT_STATUS_ACTIVE, $args);

    $label = _n_noop('Concluded <span class="count">(%s)</span>',
                     'Concluded <span class="count">(%s)</span>',
                     'piwi-warehouse');
    $args = array('label' => __('Concluded', 'piwi-warehouse'),
                  'public' => true,
                  'exclude_from_search' => false,
                  'show_in_admin_all_list' => true,
                  'show_in_admin_status_list' => true,
                  'label_count' => $label);
    register_post_status(PWWH_CORE_MOVEMENT_STATUS_CONCLUDED, $args);
  }

  /* Configuring Movement post list behaviour. */
  pwwh_core_movement_lists_init();
}
add_action('init', 'pwwh_core_movement_init');

/**
 * @brief     Main jQuery Validator CSS.
 */
define('PWWH_CORE_MOVEMENT_CSS', PWWH_CORE_MOVEMENT . '_css');

/**
 * @brief     Enqueues movement related admin style.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_movement_enqueue_style() {

  /* Enqueue Core Custom Style. */
  $id = PWWH_CORE_MOVEMENT_CSS;
  $url = PWWH_CORE_MOVEMENT_URL . '/css/movement.css';
  $deps = array();
  $ver = '20201123';
  wp_enqueue_style($id, $url, $deps, $ver);
}
add_action('admin_enqueue_scripts', 'pwwh_core_movement_enqueue_style');

/**
 * @brief     Disables autosave for the movements.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_movement_disable_autosave() {

  /* Disabling autosave for this post type. */
  if(get_post_type() == PWWH_CORE_MOVEMENT) {
    wp_dequeue_script('autosave');
  }
}
add_action('admin_enqueue_scripts', 'pwwh_core_movement_disable_autosave');

/**
 * @brief     Customizes the updated messages for the Movement post type.
 *
 * @param[in] array $msgs         The array of messages.
 *
 * @hooked    post_updated_messages
 *
 * @return    array the filtered messages array.
 */
function pwwh_core_movement_custom_post_updated_messages($msgs) {

  $_msgs = pwwh_core_api_post_updated_messages(PWWH_CORE_MOVEMENT_LABEL_SINGULAR,
                                               PWWH_CORE_MOVEMENT_LABEL_PLURAL);
  $msgs[PWWH_CORE_MOVEMENT] = $_msgs;

  return $msgs;
}
add_action('post_updated_messages',
           'pwwh_core_movement_custom_post_updated_messages');

/**
 * @brief     Customizes the updated messages for the Holder taxonomy.
 *
 * @param[in] array $msgs         The array of messages.
 *
 * @hooked    term_updated_messages
 *
 * @return    array the filtered messages array.
 */
function pwwh_core_movement_custom_tax_updated_messages($messages) {

  $msgs = pwwh_core_api_tax_updated_messages(PWWH_CORE_MOVEMENT_HOLDER_LABEL_SINGULAR);
  $messages[PWWH_CORE_MOVEMENT_HOLDER] = $msgs;

  return $messages;
}
add_action('term_updated_messages',
           'pwwh_core_movement_custom_tax_updated_messages');

/**
 * @brief     Customizes the bulk messages for the Movement post type.
 *
 * @param[in] array $msgs         The array of messages.
 * @param[in] array $bulk_counts   The array of bulk counts.
 *
 * @hooked    bulk_post_updated_messages
 *
 * @return    array the filtered messages array.
 */
function pwwh_core_movement_custom_bulk_messages($msgs, $bulk_counts) {

  $_msgs = pwwh_core_api_post_bulk_messages(PWWH_CORE_MOVEMENT_LABEL_SINGULAR,
                                            PWWH_CORE_MOVEMENT_LABEL_PLURAL,
                                            $bulk_counts);
  $msgs[PWWH_CORE_MOVEMENT] = $_msgs;

  return $msgs;
}
add_action('bulk_post_updated_messages',
           'pwwh_core_movement_custom_bulk_messages', 10, 2);

/**
 * @brief     Manages meta boxes for the Movement custom post type.
 *
 * @param[in] WP_Post $post       Post object
 *
 * @hooked    edit_form_after_title
 *
 * @return    void
 */
function pwwh_core_movement_manage_body_content($post) {

  $post_id = $post->ID;
  $post_type = get_post_type($post);
  $post_status = get_post_status($post);

  if($post_type == PWWH_CORE_MOVEMENT) {

    /* Generating the Title postbox. */
    pwwh_core_ui_post_title($post);

    /* Generating the nonce for this postbox. */
    pwwh_core_ui_nonce(PWWH_CORE_MOVEMENT_NONCE_EDIT, $post);

    /* Generating instance collector. */
    pwwh_core_movement_ui_collector($post, true);

    /* Get UI facts. */
    $ui_facts = pwwh_core_movement_api_get_ui_facts();

    if(($post_status == 'new') || ($post_status == 'auto-draft') ||
       ($post_status == 'draft')) {
      /* Nothing to do here. */
    }
    else if(($post_status == PWWH_CORE_MOVEMENT_STATUS_ACTIVE) ||
            ($post_status == PWWH_CORE_MOVEMENT_STATUS_CONCLUDED)) {

      /* Getting movement quantities and item count. */
      $data = pwwh_core_movement_api_get_quantities($post);
      $item_count = count($data);

      /* Sorting array. */
      uasort($data, function($a, $b){return($a['lent'] < $b['lent']);});

      /* Verifing UI options. */
      $show_del_ui = pwwh_core_caps_api_current_user_can(PWWH_CORE_MOVEMENT_CAPS_DELETE_MOVEMENTS) &&
                     ($item_count > 1);
      $show_man_ui = pwwh_core_caps_api_current_user_can(PWWH_CORE_MOVEMENT_CAPS_MANAGE_MOVEMENTS);

      /* Instance counter. */
      $instance_count = 0;
      foreach($data as $key => $qnts) {

        /* A key is in the format [Item ID]-[Location ID]. */
        $item_id = null;
        $loc_id = null;
        pwwh_core_movement_api_parse_key($key, $item_id, $loc_id);

        /* Verifing Item dependent UI options. */
        $col_man_ui = (($post_status == PWWH_CORE_MOVEMENT_STATUS_CONCLUDED) ||
                       ($qnts['lent'] == 0));

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
                      'show_history' => true,
                      'show_man_ui' => $show_man_ui,
                      'col_man_ui' => $col_man_ui,
                      'show_del_ui' => $show_del_ui,
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
    else {
      $msg = sprintf('Unexpected status in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      $msg = sprintf('Post status is %s', $post_status);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    }
  }
}
add_action('edit_form_after_title', 'pwwh_core_movement_manage_body_content');

/**
 * @brief     Manages meta boxes for the Movement custom post type.
 *
 * @hooked    add_meta_boxes
 *
 * @return    void
 */
function pwwh_core_movement_manage_meta_boxes() {

  $post = get_post();
  $post_id = $post->ID;
  $post_type = get_post_type($post);
  $post_status = get_post_status($post);

  if($post_type == PWWH_CORE_MOVEMENT) {

    /* Get UI facts. */
    $ui_facts = pwwh_core_movement_api_get_ui_facts();

    if(($post_status == 'new') || ($post_status == 'auto-draft') ||
       ($post_status == 'draft')) {

      /* Adding Holder postbox. */
      $label = $ui_facts['box']['holder']['label'];
      add_meta_box($ui_facts['box']['holder']['id'], $label,
                   $ui_facts['box']['holder']['callback'],
                   PWWH_CORE_MOVEMENT, 'normal', 'high');

      /* Adding Movement Option postbox. Note that some filed could be
         pre-populated from the $_GET. This is used frome external links such
         the Item's Quick Operations or the Item's row actions. */
      if(isset($_GET[PWWH_CORE_MOVEMENT_QUERY_ITEM])) {
        $item_id = intval($_GET[PWWH_CORE_MOVEMENT_QUERY_ITEM]);
      }
      else {
        $item_id = null;
      }

      if(isset($_GET[PWWH_CORE_MOVEMENT_QUERY_MOVED])) {
        $moved = floatval($_GET[PWWH_CORE_MOVEMENT_QUERY_MOVED]);
        if($moved <= 0) {
          $moved = null;
        }
      }
      else {
        $moved = null;
      }

      $label = $ui_facts['box']['add_item']['label'];
      $args = array('show_item_ui' => true,
                    'show_loc_ui' => true,
                    'show_mov_ui' => true,
                    'is_primary' => true,
                    'item_id' => $item_id,
                    'mov_value' => $moved);

      add_meta_box($ui_facts['box']['add_item']['id'], $label,
                   $ui_facts['box']['add_item']['callback'], PWWH_CORE_MOVEMENT,
                   'normal', 'default', $args);

      /* Adding Movement Discussion postbox. */
      $label = $ui_facts['box']['notes']['label'];
      add_meta_box($ui_facts['box']['notes']['id'], $label,
                   $ui_facts['box']['notes']['callback'], PWWH_CORE_MOVEMENT,
                   'normal', 'default');
    }
    else if(($post_status == PWWH_CORE_MOVEMENT_STATUS_ACTIVE) ||
            ($post_status == PWWH_CORE_MOVEMENT_STATUS_CONCLUDED)) {

      /* Adding Move more Items postbox. */
      $label = $ui_facts['box']['add_item']['label'];
      $count = count(pwwh_core_movement_api_get_quantities($post_id));

      $args = array('show_item_ui' => false,
                    'show_loc_ui' => false,
                    'show_mov_ui' => false,
                    'is_primary' => true,
                    'instance' => ($count - 1));
      add_meta_box($ui_facts['box']['add_item']['id'], $label,
                   $ui_facts['box']['add_item']['callback'], PWWH_CORE_MOVEMENT,
                   'normal', 'default', $args);

      /* Adding Movement Discussion postbox. */
      $label = $ui_facts['box']['notes']['label'];
      add_meta_box($ui_facts['box']['notes']['id'], $label,
                   $ui_facts['box']['notes']['callback'], PWWH_CORE_MOVEMENT,
                   'normal', 'default');
    }
    else if($post_status == 'trash') {
      /* Nothing to do. */
    }
    else {
      $msg = sprintf('Unexpected status in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      $msg = sprintf('Post status is %s', $post_status);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    }

    /* Removing unwanted meta boxes. */
    remove_meta_box('slugdiv', PWWH_CORE_MOVEMENT, 'normal');
    remove_meta_box('commentsdiv', PWWH_CORE_MOVEMENT, 'normal');
  }
}
add_action('add_meta_boxes', 'pwwh_core_movement_manage_meta_boxes');

/**
 * @brief     Manages the movement during post stauts transitions.
 *
 * @param[in] string $new         New post status.
 * @param[in] string $old         Old post status.
 * @param[in] WP_Post $post       Post object
 *
 * @hooked    transition_post_status
 *
 * @return    void
 */
function pwwh_core_movement_manage_status_transitions($new, $old, $post) {

  $post_id = $post->ID;
  $post_type = $post->post_type;

  /* This must act only on movements. */
  if(($post->post_type) == PWWH_CORE_MOVEMENT) {

    /* Instantiating an history object. */
    $history = new pwwh_core_movement_history();

    /* Post creation. */
    if(($old == 'new') && ($new == 'auto-draft')) {
      /* This transition is expected but there is nothing to do here. */
    }
    /* Post is going to become active. */
    else if(($old == 'auto-draft') &&
            (($new == PWWH_CORE_MOVEMENT_STATUS_ACTIVE) ||
             ($new == PWWH_CORE_MOVEMENT_STATUS_CONCLUDED))) {

      /* Getting new meta data from $_POST. */
      $new_data = pwwh_core_movement_api_get_quantities_from_post();

      if($new_data) {
        /* Saving holder. */
        if(isset($_POST[PWWH_CORE_MOVEMENT_HOLDER])) {
          $holder = sanitize_text_field($_POST[PWWH_CORE_MOVEMENT_HOLDER]);
          $holder_id = term_exists($holder);
          wp_set_post_terms($post_id, $holder_id, PWWH_CORE_MOVEMENT_HOLDER);
        }

        /* Updating Item accounts. */
        foreach($new_data as $key => $meta) {
          /* A key is in the format [Item ID]-[Location ID]. */
          $item_id = null;
          $loc_id = null;
          pwwh_core_movement_api_parse_key($key, $item_id, $loc_id);

          /* Updating item availability in the location. */
          $moved = floatval($meta['moved']);
          $delta = -1 * $moved;
          pwwh_core_item_api_sum_avail_by_location($item_id, $loc_id,
                                                   $delta);

          /* Pushing a new bit of history. */
          $args = array('mov_id' => $post_id, 'item_id' => $item_id,
                        'loc_id' => $loc_id, 'hold_id' => $holder_id,
                        'moved' => $moved);
          $history->insert($args);
        }

        /* Saving new meta data. */
        pwwh_core_movement_api_set_quantities($post, $new_data);
      }
      else {
        $msg = sprintf('Corrupted POST data in %s()', __FUNCTION__);
        pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
        $msg = sprintf('Transition from %s to %s', $old, $new);
        pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      }
    }
    /* An active/conclude movement is going to be updated.
       Potentially it could move to concluded or could be reactivated. */
    else if((($old == PWWH_CORE_MOVEMENT_STATUS_ACTIVE) ||
             ($old == PWWH_CORE_MOVEMENT_STATUS_CONCLUDED)) &&
            (($new == PWWH_CORE_MOVEMENT_STATUS_ACTIVE) ||
             ($new == PWWH_CORE_MOVEMENT_STATUS_CONCLUDED))) {

      /* Getting new meta data from $_POST. */
      $new_data = pwwh_core_movement_api_get_quantities_from_post();

      /* Getting old meta data from database. */
      $old_data = pwwh_core_movement_api_get_quantities($post);

      if($new_data) {
        /* Getting a unique array keys merging old an new keys.
           This will be used as base to check if items have been removed,
           added or updated. */
        $keys = array_unique(array_merge(array_keys($new_data),
                                         array_keys($old_data)));

        /* Getting Holder information. */
        $holder_id = pwwh_core_movement_api_get_holder($post_id, 'term_id');

        foreach($keys as $key) {

          /* Parsing the key. */
          $item_id = null;
          $loc_id = null;
          pwwh_core_movement_api_parse_key($key, $item_id, $loc_id);

          if(isset($new_data[$key]) && !isset($old_data[$key])) {
            /* Item just added. */

            /* Updating item availability. */
            $new_moved = floatval($new_data[$key]['moved']);
            $delta = (-1 * $new_moved);
            pwwh_core_item_api_sum_avail_by_location($item_id, $loc_id,
                                                     $delta);

            /* Pushing a new bit of history. */
            $args = array('mov_id' => $post_id, 'item_id' => $item_id,
                          'loc_id' => $loc_id, 'hold_id' => $holder_id,
                          'moved' => $new_moved);
            $history->insert($args);

          }
          else if(isset($old_data[$key]) && !isset($new_data[$key])) {
            /* Item just deleted. */
            if(pwwh_core_caps_api_current_user_can(PWWH_CORE_MOVEMENT_CAPS_DELETE_MOVEMENTS)) {
              /* Computing the delta quantity. */
              $old_moved = floatval($old_data[$key]['moved']);
              $old_ret = floatval($old_data[$key]['returned']);
              $delta = ($old_moved - $old_ret);
              pwwh_core_item_api_sum_avail_by_location($item_id, $loc_id,
                                                       $delta);

              /* Partially erasing movement history removing item related
                 entries. */
              $args = array('mov_id' => $post_id,
                            'item_id' => $item_id,
                            'loc_id' => $loc_id);
              $history->erase($args);
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
            $old_moved = $old_data[$key]['moved'];

            /* Getting stored metabox values. */
            $old_mov = floatval($old_data[$key]['moved']);
            $old_ret = floatval($old_data[$key]['returned']);
            $old_don = floatval($old_data[$key]['donated']);
            $old_lost = floatval($old_data[$key]['lost']);
            $old_lent = floatval($old_data[$key]['lent']);

            /* Updating history on change values. */
            $new_mov = floatval($new_data[$key]['moved']);
            $new_ret = floatval($new_data[$key]['returned']);
            $new_don = floatval($new_data[$key]['donated']);
            $new_lost = floatval($new_data[$key]['lost']);
            $new_lent = floatval($new_data[$key]['lent']);

            if(($new_mov != $old_mov) || ($new_ret != $old_ret) ||
               ($new_don != $old_don) || ($new_lost != $old_lost) ||
               ($new_lent != $old_lent)) {

              /* Updating item availability. */
              $delta = ($new_ret - $old_ret);
              pwwh_core_item_api_sum_avail_by_location($item_id, $loc_id,
                                                       $delta);

              /* Pushing a new bit of history. */
              $args = array('mov_id' => $post_id, 'item_id' => $item_id,
                            'loc_id' => $loc_id, 'hold_id' => $holder_id,
                            'moved' => $new_mov, 'donated' => $new_don,
                            'returned' => $new_ret, 'lost' => $new_lost);

              $history->insert($args);
            }
          }
        }

        /* Saving new meta data. */
        pwwh_core_movement_api_set_quantities($post, $new_data);
      }
      else {
        $msg = sprintf('Corrupted POST data in %s()', __FUNCTION__);
        pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
        $msg = sprintf('Transition from %s to %s', $old, $new);
        pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      }
    }
    else if((($old == PWWH_CORE_MOVEMENT_STATUS_ACTIVE) ||
             ($old == PWWH_CORE_MOVEMENT_STATUS_CONCLUDED)) &&
            ($new == 'trash')) {

      /* Getting old meta data from database. */
      $old_data = pwwh_core_movement_api_get_quantities($post);

      /* Updating Item accounts. */
      foreach($old_data as $key => $qnt) {

        /* Parsing the key. */
        $item_id = null;
        $loc_id = null;
        pwwh_core_movement_api_parse_key($key, $item_id, $loc_id);

        /* Computing the delta quantity. */
        $delta = floatval($qnt['moved']) - floatval($qnt['returned']);

        /* Updating Item availability data. */
        pwwh_core_item_api_sum_avail_by_location($item_id, $loc_id,
                                                 $delta);
      }
    }
    else if(($old == 'trash') &&
            (($new == PWWH_CORE_MOVEMENT_STATUS_ACTIVE) ||
             ($new == PWWH_CORE_MOVEMENT_STATUS_CONCLUDED))) {
      /* Getting old meta data from database. */
      $old_data = pwwh_core_movement_api_get_quantities($post);

      /* Updating Item accounts. */
      foreach($old_data as $key => $qnt) {

        /* Parsing the key. */
        $item_id = null;
        $loc_id = null;
        pwwh_core_movement_api_parse_key($key, $item_id, $loc_id);

        /* Computing the delta quantity. */
        $delta = floatval(floatval($qnt['returned'] - $qnt['moved']));

        /* Updating Item availability data. */
        pwwh_core_item_api_sum_avail_by_location($item_id, $loc_id,
                                                 $delta);
      }
    }
    else {
      $msg = sprintf('Unexpected transition in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      $msg = sprintf('Transition is from %s to %s', $old, $new);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    }
  }
}
add_action('transition_post_status',
           'pwwh_core_movement_manage_status_transitions', 10, 3);

/**
 * @brief     Restores availability of items when a movement is deleted and
 *            destroys its history.
 *
 * @param[in] int $post_id        Post ID
 *
 * @hooked    save_post
 *
 * @return    int the post ID
 */
function pwwh_core_movement_on_delete($post_id) {

  $post_type = get_post_type($post_id);

  /* This must act only on movements. */
  if(($post_type) == PWWH_CORE_MOVEMENT) {

    /* Erasing movement history. */
    $history = new pwwh_core_movement_history();
    $args = array('mov_id' => $post_id);
    $history->erase($args);

    /* If the post went through the trash the status transition hook
       restores the Items availabilities. If not, this hook has to take
       care of it. This happens if an active or concluded movement is deleted
       permanently. */
    if((get_post_status($post_id) == PWWH_CORE_MOVEMENT_STATUS_ACTIVE) ||
       (get_post_status($post_id) == PWWH_CORE_MOVEMENT_STATUS_CONCLUDED)) {
      /* Getting meta data from database. */
      $qnts = pwwh_core_movement_api_get_quantities($post_id);

      /* Updating Item accounts. */
      foreach($qnts as $key => $qnt) {

        /* Parsing the key. */
        $item_id = null;
        $loc_id = null;
        pwwh_core_movement_api_parse_key($key, $item_id, $loc_id);

        /* Computing the delta quantity. */
        $delta = floatval($qnt['moved']) - floatval($qnt['returned']);

        pwwh_core_item_api_sum_avail_by_location($item_id, $loc_id, $delta);
      }
    }
  }
}
add_action('before_delete_post', 'pwwh_core_movement_on_delete');

/**
 * @brief     Verifies the Holder existency before the push in the database.
 * @details   This checks the holder as enforcement of the JS Validate.
 *
 * @param[in] mixed $term_name    The name of the term to insert.
 * @param[in] array $taxonomy     The taxonomy slug.
 *
 * @hooked    pre_insert_term
 *
 * @return    void
 */
function pwwh_core_movement_on_holder_insert($term_name, $taxonomy) {

  if($taxonomy === PWWH_CORE_MOVEMENT_HOLDER) {

    /* Checking if the holder exists. */
    $loc = pwwh_core_movement_get_holder_by_name($term_name);

    if($loc) {
      /* Getting the facts. */
      $ui_facts = pwwh_core_movement_api_get_ui_edit_tag_facts();

      /* Composing and returning an error. */
      $msg = $ui_facts['input']['holder']['msg']['remote'];
      return new WP_Error('holder_duplicated', $msg);
    }
  }
  return $term_name;
}
add_filter('pre_insert_term', 'pwwh_core_movement_on_holder_insert', 10, 2);
/** @} */