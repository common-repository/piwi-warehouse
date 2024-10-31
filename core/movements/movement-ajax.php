<?php
/**
 * @file      movement/movement-ajax.php
 * @brief     Ajax related to Movement.
 *
 * @addtogroup PWWH_CORE_MOVEMENT
 * @{
 */

/*===========================================================================*/
/* Common AJAX.                                                              */
/*===========================================================================*/

/**
 * @brief     Movement common script ID.
 */
define('PWWH_CORE_MOVEMENT_COMMON_JS', 'pwwh_core_movement_common_js');

/**
 * @brief     Ajax action triggered on when an Item input changes is valid to
 *            shortlist its location.
 */
define('PWWH_CORE_MOVEMENT_ACTION_UPD_LOCS',
       PWWH_CORE_MOVEMENT . '_update_locations');

/**
 * @brief     Enqueues a common script containing JS functions
 *            used by all the other Movement script.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_movement_common() {

  /* Enqueuing the script. */
  $id = PWWH_CORE_MOVEMENT_COMMON_JS;
  $url = PWWH_CORE_MOVEMENT_URL . '/js/pwwh.movement.common.js';
  $deps = array(PWWH_CORE_JS);
  $ver = '20201122';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Localizing the script. */
  $actions = array('upd_locs' => PWWH_CORE_MOVEMENT_ACTION_UPD_LOCS);
  $data = array('ajax' => array('url' => admin_url('admin-ajax.php'),
                                'action' => $actions),
                'ui' => pwwh_core_movement_api_get_ui_facts(),
                'post' => pwwh_core_movement_api_get_movement_facts());
  wp_localize_script($id, 'pwwh_core_movement_comm_obj', $data);
}
add_action('admin_enqueue_scripts', 'pwwh_core_movement_common');

/**
 * @brief     This handler shortlists the locations when a specific item is
 *            selected.
 *
 * @hooked    wp_ajax_[PWWH_CORE_MOVEMENT_ACTION_UPD_LOCS]
 *
 * @return    void
 */
function pwwh_core_movement_update_locations_handler() {

  /* This handler is launched as POST method and receives the title of the
     item we are going to validate currently and the instance number. */
  $item_title = sanitize_text_field($_POST['item_title']);
  $instance = sanitize_text_field($_POST['instance']);
  $ui_facts = pwwh_core_movement_api_get_ui_facts();
  $_id = $instance . ':' . $ui_facts['input']['location']['datalist'];

  /* Looking for an item which as the post title user is trying to add. */
  $item = pwwh_core_item_api_get_item_by_title($item_title);

  if($item) {
    $avails = pwwh_core_item_api_get_avails($item);

    if(isset($avails['0'])) {
      unset($avails['0']);
    }

    $_locs = array();
    foreach ($avails as $loc_ids => $avail) {
      $loc_name = pwwh_core_item_api_get_location_name($loc_ids);

      if($loc_name && ($avail > 0)) {
        array_push($_locs, $loc_name);
      }
    }

    $_response = pwwh_lib_ui_form_datalist($_locs, $_id);
  }
  else {
    $args = array('taxonomy' => PWWH_CORE_ITEM_LOCATION,
                  'hide_empty' => false);
    $_locs = get_terms($args);
    $_response = pwwh_lib_ui_form_datalist($_locs, $_id, 'name');
  }

  echo json_encode($_response);

  /* All ajax handlers should die when finished */
  wp_die();
}
add_action('wp_ajax_' . PWWH_CORE_MOVEMENT_ACTION_UPD_LOCS,
           'pwwh_core_movement_update_locations_handler');

/*===========================================================================*/
/* AJAX related to Movement fields validation in the edit screen.            */
/*===========================================================================*/

/**
 * @brief     Movement validation script ID.
 */
define('PWWH_CORE_MOVEMENT_VALIDATE_JS', PWWH_CORE_MOVEMENT . '_validate_js');

/**
 * @brief     Intialize the validation of the Movement.
 * @details   This AJAX validates an Movement when publishing it.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_movement_validate_init() {

  /* Enqueuing the script. */
  $id = PWWH_CORE_MOVEMENT_VALIDATE_JS;
  $url = PWWH_CORE_MOVEMENT_URL . '/js/pwwh.movement.validate.js';
  $deps = array(PWWH_CORE_MOVEMENT_COMMON_JS);
  $ver = '20201122';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Localizing the script. */
  $data = array('ui' => pwwh_core_movement_api_get_ui_facts(),
                'post' => pwwh_core_movement_api_get_movement_facts());
  wp_localize_script($id, 'pwwh_core_movement_val_obj', $data);
}
add_action('admin_enqueue_scripts', 'pwwh_core_movement_validate_init');

/**
 * @brief     This handler is used by JS.validate to check existency of item
 *            title.
 *
 * @hooked    wp_ajax_[PWWH_CORE_MOVEMENT_ACTION_VALIDATE_ITEM]
 *
 * @return    void
 */
function pwwh_core_movement_validate_item_handler() {

  /* This handler is launched as POST method and receives the title of the item
     we are going to validate currently. */
  $item_title = sanitize_text_field($_POST['item_title']);

  /* Looking for an item which as the post title user is trying to add. */
  $item = pwwh_core_item_api_get_item_by_title($item_title);

  /* If an item with this title exists the handler return true. */
  if($item) {
    echo json_encode(true);
  }
  else {
    echo json_encode(false);
  }

  /* All ajax handlers should die when finished */
  wp_die();
}

/**
 * @brief     This handler is used by JS.validate to check if the selected
 *            location exists.
 *
 * @hooked    wp_ajax_[PWWH_CORE_MOVEMENT_ACTION_VALIDATE_LOCATION]
 *
 * @return    void
 */
function pwwh_core_movement_validate_location_handler() {

  /* This handler is launched as POST method and receives the name of the
     location we are going to validate currently. */
  $loc_name = sanitize_text_field($_POST['loc_name']);

  /* Looking for a location which as the post name user is trying to add. */
  $loc = pwwh_core_item_get_location_by_name($loc_name);

  /* If an location with this name exists the handler return true. */
  if($loc) {
    echo json_encode(true);
  }
  else {
    echo json_encode(false);
  }

  /* All ajax handlers should die when finished */
  wp_die();
}

/**
 * @brief     This handler is used by JS.validate to check if item availability
 *            is greather than quantity the user are going to move.
 *
 * @hooked    wp_ajax_[PWWH_CORE_MOVEMENT_ACTION_VALIDATE_MOVED]
 *
 * @return    void
 */
function pwwh_core_movement_validate_moved_handler() {
  /* This handler is launched as POST method and receives the title of the item
     its location and the moved quantity that we are currently going
     to validate. */
  $item_title = sanitize_text_field($_POST['item_title']);
  $loc_name = sanitize_text_field($_POST['loc_name']);
  $moved = floatval($_POST['moved']);

  /* Looking for the item availability. */
  $item = pwwh_core_item_api_get_item_by_title($item_title);
  $loc = pwwh_core_item_get_location_by_name($loc_name);

  /* If the Item Title or the Location Name are wrong, we cannot state
     anything about Moved validity, thus we consider it valid.
     Note that the form will not be validated anyway as Item is invalid. */
  if(($item === false) || ($loc=== false)) {
    echo json_encode(true);
  }
  else {
    $avail = pwwh_core_item_api_get_avail_by_location($item->ID,
                                                      $loc->term_id);

    /* If moved quantity is equal or less than availability the handler return
       true. */
    if($moved <= $avail) {
      echo json_encode(true);
    }
    else {
      echo json_encode(false);
    }
  }

  /* All ajax handlers should die when finished */
  wp_die();
}

/**
 * @brief     This handler is used by JS.validate to check existence of holder.
 *
 * @hooked    wp_ajax_movement_validate_holder_name
 *
 * @return    void
 */
function pwwh_core_movement_validate_holder_handler() {

  /* This handler is launched as POST method and receives the name of the holder
     we are currently going to validate. */
  $holder_name = sanitize_text_field($_POST['holder_name']);

  /* Looking for an holder having the same name of the one which user is trying
     to choose. */
  $holder = term_exists($holder_name, PWWH_CORE_MOVEMENT_HOLDER);

  /* If an holder with this name exists the handler return true. */
  if(($holder !== 0) && ($holder !== null)) {
    echo json_encode(true);
  }
  else {
    echo json_encode(false);
  }

  /* All ajax handlers should die when finished */
  wp_die();
}

/* Adding all the remote action from the validate rules. */
{
  $ui_facts = pwwh_core_movement_api_get_ui_facts();
  $inputs = $ui_facts['input'];
  foreach($inputs as $input) {
    if(isset($input['rule']) && isset($input['rule']['remote'])) {
      add_action('wp_ajax_' . $input['rule']['remote']['action'],
                 $input['rule']['remote']['callback']);
    }
  }
}

/*===========================================================================*/
/* AJAX related to Movement fields validation in the edit tag screen.        */
/*===========================================================================*/

/**
 * @brief     Movement's Holder validate script identifier.
 * @details   This script validates an movement on insertion.
 */
define('PWWH_CORE_MOVEMENT_VALIDATE_HOLDER_JS',
       PWWH_CORE_MOVEMENT . '_validate_holder_js');

/**
 * @brief     Intialize the validation of the Movement.
 * @details   This AJAX validates an Movement when publishing it.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_movement_holder_validate_init() {

  /* Enqueuing the script. */
  $id = PWWH_CORE_MOVEMENT_VALIDATE_HOLDER_JS;
  $url = PWWH_CORE_MOVEMENT_URL . '/js/pwwh.movement.holder.validate.js';
  $deps = array(PWWH_CORE_JS);
  $ver = '20201213';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Localizing the script. */
  $data = array('ui' => pwwh_core_movement_api_get_ui_edit_tag_facts(),
                'post' => pwwh_core_movement_api_get_movement_facts());
  wp_localize_script($id, 'pwwh_core_movement_loc_val_obj', $data);
}
add_action('admin_enqueue_scripts', 'pwwh_core_movement_holder_validate_init');

/**
 * @brief     This handler is used by JS.validate to check uniqueness of movement's
 *            holder tag name.
 *
 * @hooked    wp_ajax_validate_holder
 *
 * @return    void
 */
function pwwh_core_movement_holder_validate_name_handler() {
  global $wpdb;
  /* This handler is launched as POST method and receives the title of the movement
     we are currently going to validate. */
  $loc_title = sanitize_text_field(stripslashes_deep($_POST['holder_tag_name']));
  $loc = pwwh_core_movement_get_holder_by_name($loc_title);

  /* If a holder with this title already exists the handler return false. */
  if($loc) {
    echo json_encode(false);
  }
  else {
    echo json_encode(true);
  }

  /* All ajax handlers should die when finished */
  wp_die();
}

/* Adding all the remote action from the validate rules. */
{
  $ui_facts = pwwh_core_movement_api_get_ui_edit_tag_facts();
  $inputs = $ui_facts['input'];
  foreach($inputs as $input) {
    if(isset($input['rule']) && isset($input['rule']['remote'])) {
      add_action('wp_ajax_' . $input['rule']['remote']['action'],
                 $input['rule']['remote']['callback']);
    }
  }
}

/*===========================================================================*/
/* AJAX related to add an Item to a Movement (Move an Item box).             */
/*===========================================================================*/

/**
 * @brief     Add Item script ID.
 */
define('PWWH_CORE_MOVEMENT_ADD_ITEM_JS', PWWH_CORE_MOVEMENT . '_add_item_js');

/**
 * @brief     Ajax action triggered on Add Item event.
 */
define('PWWH_CORE_MOVEMENT_ACTION_ADD_ITEM', PWWH_CORE_MOVEMENT . '_add_item');

/**
 * @brief     Enqueues scripts needed to add an Item to a movement.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_movement_add_items() {

  /* Enqueuing the script. */
  $id = PWWH_CORE_MOVEMENT_ADD_ITEM_JS;
  $url = PWWH_CORE_MOVEMENT_URL . '/js/pwwh.movement.add.items.js';
  $deps = array(PWWH_CORE_MOVEMENT_COMMON_JS);
  $ver = '20230926';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Localizing the script. */
  $actions = array('add_item' => PWWH_CORE_MOVEMENT_ACTION_ADD_ITEM);
  $data = array('ajax' => array('url' => admin_url('admin-ajax.php'),
                                'action' => $actions),
                'ui' => pwwh_core_movement_api_get_ui_facts(),
                'post' => pwwh_core_movement_api_get_movement_facts());
  wp_localize_script($id, 'pwwh_core_movement_add_obj', $data);
}
add_action('admin_enqueue_scripts', 'pwwh_core_movement_add_items');

/**
 * @brief     This handler is used to generate a new Item box.
 *
 * @hooked    wp_ajax_[PWWH_CORE_MOVEMENT_ACTION_ADD_ITEM]
 *
 * @return    void
 */
function pwwh_core_movement_add_item_handler() {

  /* This handler is launched as POST method and receives the title of the item
     we are going to validate currently. */
  $args = array('instance' => intval($_POST['instance']),
                'is_primary' => false,
                'echo' => false);
  $out = pwwh_core_movement_ui_metabox_add_item(null, array('args' => $args));

  echo json_encode($out);

  /* All ajax handlers should die when finished */
  wp_die();
}
add_action('wp_ajax_' . PWWH_CORE_MOVEMENT_ACTION_ADD_ITEM,
           'pwwh_core_movement_add_item_handler');

/*===========================================================================*/
/* AJAX related to Item remove from a Movement.                              */
/*===========================================================================*/

/**
 * @brief     Remove Item script ID.
 */
define('PWWH_CORE_MOVEMENT_REMOVE_ITEM_JS',
       PWWH_CORE_MOVEMENT . '_remove_item_js');

/**
 * @brief     Script required to manage the removal of an Item from a movement.
 * @details   This AJAX validates an Movement when publishing it.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_movement_remove_item_init() {

  /* Enqueuing the script. */
  $id = PWWH_CORE_MOVEMENT_REMOVE_ITEM_JS;
  $url = PWWH_CORE_MOVEMENT_URL . '/js/pwwh.movement.remove.items.js';
  $deps = array(PWWH_CORE_MOVEMENT_COMMON_JS);
  $ver = '20201122';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Localizing the script. */
  $data = array('ui' => pwwh_core_movement_api_get_ui_facts(),
                'post' => pwwh_core_movement_api_get_movement_facts());
  wp_localize_script($id, 'pwwh_core_movement_rem_obj', $data);
}
add_action('admin_enqueue_scripts', 'pwwh_core_movement_remove_item_init');

/*===========================================================================*/
/* AJAX related to the Movement Operations.                                  */
/*===========================================================================*/

/**
 * @brief     Manage Operations script ID.
 */
define('PWWH_CORE_MOVEMENT_MANAGE_OPERATIONS_JS',
       PWWH_CORE_MOVEMENT . '_manage_operations_js');

/**
 * @brief     Handles the Item Management section.
 * @details   Handles the Hide/Show button, updates the lent forecast on input
 *            change.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_movement_manage_operations() {

  /* Enqueuing the script. */
  $id = PWWH_CORE_MOVEMENT_MANAGE_OPERATIONS_JS;
  $url = PWWH_CORE_MOVEMENT_URL . '/js/pwwh.movement.manage.operations.js';
  $deps = array(PWWH_CORE_MOVEMENT_COMMON_JS);
  $ver = '20201122';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Localizing the script. */
  $labels = array('show' => __('Show', 'piwi-warehouse'),
                  'hide' => __('Hide', 'piwi-warehouse'));
  $titles = array('show' => __('Show Management area', 'piwi-warehouse'),
                  'hide' => __('Hide Management area', 'piwi-warehouse'));
  $data = array('ui' => pwwh_core_movement_api_get_ui_facts(),
                'post' => pwwh_core_movement_api_get_movement_facts(),
                'label' => $labels,
                'title' => $titles);
  wp_localize_script($id, 'pwwh_core_movement_operations_obj', $data);
}
add_action('admin_enqueue_scripts', 'pwwh_core_movement_manage_operations');

/*===========================================================================*/
/* AJAX related to Movement delete alert.                                    */
/*===========================================================================*/

/**
 * @brief     Manage Operations script ID.
 */
define('PWWH_CORE_MOVEMENT_DELETE_ALERT_JS',
       PWWH_CORE_MOVEMENT . '_delete_alert_js');

/**
 * @brief     Intialize the delete alert for the Movement.
 * @details   This AJAX triggers a message while trying to delete permanently
 *            an Movement.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_movement_delete_alert() {

  /* Enqueuing the script. */
  $id = PWWH_CORE_MOVEMENT_DELETE_ALERT_JS;
  $url = PWWH_CORE_MOVEMENT_URL . '/js/pwwh.movement.delete.alert.js';
  $deps = array(PWWH_CORE_JS);
  $ver = '20201122';
  wp_enqueue_script($id, $url, $deps, $ver);

  $data = array('msg_alert' => __('This would delete Movement and its ' .
                                  'History for good. Are you sure you want ' .
                                  'to do this?', 'piwi-warehouse'),
                'ui' => pwwh_core_movement_api_get_ui_facts(),
                'post' => pwwh_core_movement_api_get_movement_facts());
  wp_localize_script($id, 'pwwh_core_movement_del_obj', $data);
}
add_action('admin_enqueue_scripts', 'pwwh_core_movement_delete_alert');

/*===========================================================================*/
/* AJAX related to Movement post submit metabox.                             */
/*===========================================================================*/

/**
 * @brief     Manage Operations script ID.
 */
define('PWWH_CORE_MOVEMENT_MANAGE_SUBMITDIV_JS',
       PWWH_CORE_MOVEMENT . '_manage_submitdiv_js');

/**
 * @brief     Modifies the Post Submit Div removing extra buttons.
 * @details   This AJAX is associated to some CSS that early hides the buttons
 *            before this JS is able to remove them.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_movement_manage_submit_div() {

  $post = get_post();
  $post_type = get_post_type($post);
  $post_status = get_post_status($post);

  if($post_type == PWWH_CORE_MOVEMENT) {

    /* Enqueuing the script. */
    $id = PWWH_CORE_MOVEMENT_MANAGE_SUBMITDIV_JS;
    $url = PWWH_CORE_MOVEMENT_URL . '/js/pwwh.movement.manage.submitdiv.js';
    $deps = array(PWWH_CORE_JS);
    $ver = '20201122';
    wp_enqueue_script($id, $url, $deps, $ver);

    /* Localizing the script. */
    $post_facts = pwwh_core_movement_api_get_movement_facts();
    if(($post_status == 'new') || ($post_status == 'auto-draft') ||
       ($post_status == 'draft')) {
      $data = array('post_type' => PWWH_CORE_MOVEMENT,
                    'statuses' => $post_facts['status'],
                    'future_status' => PWWH_CORE_MOVEMENT_STATUS_ACTIVE,
                    'allowed_visibility' => array('public'),
                    'remove_minor' => true,
                    'publish_label' => __('Confirm', 'piwi-warehouse'));
    }
    else if(($post_status == PWWH_CORE_MOVEMENT_STATUS_ACTIVE) ||
            ($post_status == PWWH_CORE_MOVEMENT_STATUS_CONCLUDED)) {
      $data = array(
        'post_type' => PWWH_CORE_MOVEMENT,
        'statuses' => $post_facts['status'],
        'future_status' => $post_status,
        'allowed_visibility' => array('public'),
        'remove_minor' => true,
        'publish_label' => __('Update', 'piwi-warehouse')
      );
    }
    else {
      $data = array('post_type' => PWWH_CORE_MOVEMENT,
                    'statuses' => $post_facts['status'],
                    'future_status' => get_post_status($post),
                    'allowed_visibility' => array('public'),
                    'remove_minor' => true,
                    'publish_label' => __('Update', 'piwi-warehouse'));
    }
    wp_localize_script($id, 'pwwh_core_movement_sub_obj', $data);
  }
}
add_action('admin_enqueue_scripts', 'pwwh_core_movement_manage_submit_div');
/** @} */