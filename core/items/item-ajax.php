<?php
/**
 * @file      item/item-ajax.php
 * @brief     Ajax related to Item.
 *
 * @addtogroup PWWH_CORE_ITEM
 * @{
 */

/*===========================================================================*/
/* AJAX related to Item fields validation in the edit screen.                */
/*===========================================================================*/

/**
 * @brief     Item validate script identifier.
 * @details   This script validates an item on insertion.
 */
define('PWWH_CORE_ITEM_VALIDATE_JS', PWWH_CORE_ITEM . '_validate_js');

/**
 * @brief     Intialize the validation of the Item.
 * @details   This AJAX validates an Item when publishing it.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_item_validate_init() {

  /* Enqueuing the script. */
  $id = PWWH_CORE_ITEM_VALIDATE_JS;
  $url = PWWH_CORE_ITEM_URL . '/js/pwwh.item.validate.js';
  $deps = array(PWWH_CORE_JS);
  $ver = '20201125';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Localizing the script. */
  $data = array('ui' => pwwh_core_item_api_get_ui_facts(),
                'post' => pwwh_core_item_api_get_item_facts());
  wp_localize_script($id, 'pwwh_core_item_val_obj', $data);
}
add_action('admin_enqueue_scripts', 'pwwh_core_item_validate_init');

/**
 * @brief     This handler is used by JS.validate to check uniqueness of item
 *            title.
 *
 * @hooked    wp_ajax_validate_item
 *
 * @return    void
 */
function pwwh_core_item_validate_title_handler() {
  global $wpdb;
  /* This handler is launched as POST method and receives the title of the item
     we are currently going to validate. */
  $item_title = sanitize_text_field($_POST['item_title']);
  $item_id = intval($_POST['item_id']);

  /* Looking for an item having the same title of the one which user is trying
     to add. */
  $query = "SELECT * FROM $wpdb->posts WHERE (post_title='$item_title') " .
           "AND (post_type='" . PWWH_CORE_ITEM . "') AND (post_status='publish');";

  $res = $wpdb->get_results($query);

  /* If an item with this title already exists the handler return false unless
     the db entry and the current item have the same ID: this condition
     occurs when we are editing an item. */
  if(count($res) === 1) {
    $res = $res[0];
    if($res->ID == $item_id) {
      echo json_encode(true);
    }
    else {
      /* This launches an error which asks user to choose a new unique title. */
      echo json_encode(false);
    }
  }

  else if(count($res) === 0) {
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
  $ui_facts = pwwh_core_item_api_get_ui_facts();
  $inputs = $ui_facts['input'];
  foreach($inputs as $input) {
    if(isset($input['rule']) && isset($input['rule']['remote'])) {
      add_action('wp_ajax_' . $input['rule']['remote']['action'],
                 $input['rule']['remote']['callback']);
    }
  }
}

/*===========================================================================*/
/* AJAX related to Item fields validation in the edit tag screen.            */
/*===========================================================================*/

/**
 * @brief     Item's Location validate script identifier.
 * @details   This script validates an item on insertion.
 */
define('PWWH_CORE_ITEM_VALIDATE_LOCATION_JS',
       PWWH_CORE_ITEM . '_validate_location_js');

/**
 * @brief     Intialize the validation of the Item.
 * @details   This AJAX validates an Item when publishing it.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_item_location_validate_init() {

  /* Enqueuing the script. */
  $id = PWWH_CORE_ITEM_VALIDATE_LOCATION_JS;
  $url = PWWH_CORE_ITEM_URL . '/js/pwwh.item.location.validate.js';
  $deps = array(PWWH_CORE_JS);
  $ver = '20201213';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Localizing the script. */
  $data = array('ui' => pwwh_core_item_api_get_ui_edit_tag_facts(),
                'post' => pwwh_core_item_api_get_item_facts());
  wp_localize_script($id, 'pwwh_core_item_loc_val_obj', $data);
}
add_action('admin_enqueue_scripts', 'pwwh_core_item_location_validate_init');

/**
 * @brief     This handler is used by JS.validate to check uniqueness of item's
 *            location tag name.
 *
 * @hooked    wp_ajax_validate_location
 *
 * @return    void
 */
function pwwh_core_item_validate_location_handler() {
  global $wpdb;
  /* This handler is launched as POST method and receives the title of the item
     we are currently going to validate. */
  $loc_title = sanitize_text_field(stripslashes_deep($_POST['loc_tag_name']));
  $loc = pwwh_core_item_get_location_by_name($loc_title);

  /* If a location with this title already exists the handler return false. */
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
  $ui_facts = pwwh_core_item_api_get_ui_edit_tag_facts();
  $inputs = $ui_facts['input'];
  foreach($inputs as $input) {
    if(isset($input['rule']) && isset($input['rule']['remote'])) {
      add_action('wp_ajax_' . $input['rule']['remote']['action'],
                 $input['rule']['remote']['callback']);
    }
  }
}

/*===========================================================================*/
/* AJAX related to Item delete alert.                                        */
/*===========================================================================*/

/**
 * @brief     Item delete alert script identifier.
 * @details   This script throws an alert on Item permanent delete.
 */
define('PWWH_CORE_ITEM_DELETE_ALERT_JS', PWWH_CORE_ITEM . '_delete_alert_js');

/**
 * @brief     Intialize the delete alert for the Item.
 * @details   This AJAX triggers a message while trying to delete permanently
 *            an Item.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_item_delete_alert() {

  /* Item alert on delete mechanism. */
  $id = PWWH_CORE_ITEM_DELETE_ALERT_JS;
  $url = PWWH_CORE_ITEM_URL . '/js/pwwh.item.delete.alert.js';
  $deps = array(PWWH_CORE_JS);
  $ver = '20201122';
  wp_enqueue_script($id, $url, $deps, $ver);

  $data = array('msg_alert' => __('This would delete this Item and all its ' .
                                  'related Movement and Purchase ' .
                                  'for good. Are you sure you want to do ' .
                                  'this?', 'piwi-warehouse'),
                'ui' => pwwh_core_item_api_get_ui_facts(),
                'post' => pwwh_core_item_api_get_item_facts());
  wp_localize_script($id, 'pwwh_core_item_del_obj', $data);
}
add_action('admin_enqueue_scripts', 'pwwh_core_item_delete_alert');

/*===========================================================================*/
/* AJAX related to Item post submit metabox.                                 */
/*===========================================================================*/

/**
 * @brief     Item Post Submit Div script identifier.
 * @details   This script handles the submit post metabox extending it.
 */
define('PWWH_CORE_ITEM_SUBMITDIV_JS', PWWH_CORE_ITEM . '_submitdiv_js');

/**
 * @brief     Modifies the Post Submit Div removing extra buttons.
 * @details   This AJAX is associated to some CSS that early hides the buttons
 *            before this JS is able to remove them.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_item_manage_submit_div() {

  $post = get_post();
  $post_type = get_post_type($post);

  if($post_type ==  PWWH_CORE_ITEM) {
    /* Editing submit div box. */
    $id = PWWH_CORE_ITEM_SUBMITDIV_JS;
    $url = PWWH_CORE_ITEM_URL . '/js/pwwh.item.manage.submitdiv.js';
    $deps = array(PWWH_CORE_JS);
    $ver = '20201122';
    wp_enqueue_script($id, $url, $deps, $ver);

    /* Localizing the script. */
    $post_facts = pwwh_core_item_api_get_item_facts();
    $data = array('post_type' => PWWH_CORE_ITEM,
                  'statuses' => $post_facts['status'],
                  'future_status' => 'publish',
                  'allowed_visibility' => array('public'),
                  'remove_minor' => true);
    wp_localize_script($id, 'pwwh_core_item_sub_obj', $data);
  }
}
add_action('admin_enqueue_scripts', 'pwwh_core_item_manage_submit_div');
/** @} */