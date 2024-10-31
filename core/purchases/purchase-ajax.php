<?php
/**
 * @file      purchase/purchase-ajax.php
 * @brief     Ajax related to Purchase.
 *
 * @addtogroup PWWH_CORE_PURCHASE
 * @{
 */

/*===========================================================================*/
/* AJAX related to Purchase fields validation.                               */
/*===========================================================================*/

/**
 * @brief     Purchase validation script ID.
 */
define('PWWH_CORE_PURCHASE_VALIDATE_JS', 'pwwh_core_purchase_validate_js');

/**
 * @brief     Intialize the validation of the Purchase.
 * @details   This AJAX validates an Purchase when publishing it.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_purchase_validate_init() {

  /* Enqueuing the script. */
  $id = PWWH_CORE_PURCHASE_VALIDATE_JS;
  $url = PWWH_CORE_PURCHASE_URL . '/js/pwwh.purchase.validate.js';
  $deps = array(PWWH_CORE_JS);
  $ver = '20201125';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Localizing the script. */
  $data = array('ui' => pwwh_core_purchase_api_get_ui_facts(),
                'post' => pwwh_core_purchase_api_get_purchase_facts());
  wp_localize_script($id, 'pwwh_core_purchase_val_obj', $data);
}
add_action('admin_enqueue_scripts', 'pwwh_core_purchase_validate_init');

/**
 * @brief     This handler is used by JS.validate to check if the selected item
 *            exists.
 *
 * @hooked    wp_ajax_[PWWH_CORE_PURCHASE_ACTION_VALIDATE_ITEM]
 *
 * @return    void
 */
function pwwh_core_purchase_validate_item_handler() {

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
 * @hooked    wp_ajax_[PWWH_CORE_PURCHASE_ACTION_VALIDATE_LOCATION]
 *
 * @return    void
 */
function pwwh_core_purchase_validate_location_handler() {

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

/* Adding all the remote action from the validate rules. */
{
  $ui_facts = pwwh_core_purchase_api_get_ui_facts();
  $inputs = $ui_facts['input'];
  foreach($inputs as $input) {
    if(isset($input['rule']) && isset($input['rule']['remote'])) {
      add_action('wp_ajax_' . $input['rule']['remote']['action'],
                 $input['rule']['remote']['callback']);
    }
  }
}

/*===========================================================================*/
/* AJAX related to add an Item to a Purchase (Purchase an Item box).         */
/*===========================================================================*/

/**
 * @brief     Add Item script ID.
 */
define('PWWH_CORE_PURCHASE_ADD_ITEM_JS', PWWH_CORE_PURCHASE . '_add_item_js');

/**
 * @brief     Ajax action triggered on Add Item event.
 */
define('PWWH_CORE_PURCHASE_ACTION_ADD_ITEM', PWWH_CORE_PURCHASE . '_add_item');

/**
 * @brief     Enqueues scripts needed to add an Item to a purchase.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_purchase_add_items() {

  /* Enqueuing the script. */
  $id = PWWH_CORE_PURCHASE_ADD_ITEM_JS;
  $url = PWWH_CORE_PURCHASE_URL . '/js/pwwh.purchase.add.items.js';
  $deps = array(PWWH_CORE_JS);
  $ver = '20230926';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Localizing the script. */
  $actions = array('add_item' => PWWH_CORE_PURCHASE_ACTION_ADD_ITEM);
  $data = array('ajax' => array('url' => admin_url('admin-ajax.php'),
                                'action' => $actions),
                'ui' => pwwh_core_purchase_api_get_ui_facts(),
                'post' => pwwh_core_purchase_api_get_purchase_facts());
  wp_localize_script($id, 'pwwh_core_purchase_add_obj', $data);
}
add_action('admin_enqueue_scripts', 'pwwh_core_purchase_add_items');

/**
 * @brief     This handler is used to generate a new Item box.
 *
 * @hooked    wp_ajax_[PWWH_CORE_PURCHASE_ACTION_ADD_ITEM]
 *
 * @return    void
 */
function pwwh_core_purchase_add_item_handler() {

  /* This handler is launched as POST method and receives the title of the item
     we are going to validate currently. */
  $args = array('instance' => intval($_POST['instance']),
                'is_primary' => false,
                'show_item_ui' => true,
                'show_loc_ui' => true,
                'show_qnt_ui' => true,
                'echo' => false);
  $out = pwwh_core_purchase_ui_metabox_add_item(null, array('args' => $args));

  echo json_encode($out);

  /* All ajax handlers should die when finished */
  wp_die();
}
add_action('wp_ajax_' . PWWH_CORE_PURCHASE_ACTION_ADD_ITEM,
           'pwwh_core_purchase_add_item_handler');

/*===========================================================================*/
/* AJAX related to Item removal from a Purchase.                             */
/*===========================================================================*/

/**
 * @brief     Remove Item script ID.
 */
define('PWWH_CORE_PURCHASE_REMOVE_ITEM_JS',
       PWWH_CORE_PURCHASE . '_remove_item_js');

/**
 * @brief     Script required to manage the removal of an Item from a purchase.
 * @details   This AJAX validates an Purchase when publishing it.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_purchase_remove_item_init() {

  /* Enqueuing the script. */
  $id = PWWH_CORE_PURCHASE_REMOVE_ITEM_JS;
  $url = PWWH_CORE_PURCHASE_URL . '/js/pwwh.purchase.remove.items.js';
  $deps = array(PWWH_CORE_JS);
  $ver = '20201125';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Localizing the script. */
  $data = array('ui' => pwwh_core_purchase_api_get_ui_facts(),
                'post' => pwwh_core_purchase_api_get_purchase_facts());
  wp_localize_script($id, 'pwwh_core_purchase_rem_obj', $data);
}
add_action('admin_enqueue_scripts', 'pwwh_core_purchase_remove_item_init');

/*===========================================================================*/
/* AJAX related to Edit quantities in the Item Summary box.                  */
/*===========================================================================*/

/**
 * @brief     Manage Edit box Item script ID.
 */
define('PWWH_CORE_PURCHASE_EDIT_QNTS_JS',
       PWWH_CORE_PURCHASE . '_edit_quantities_js');

/**
 * @brief     Intialize the validation of the Purchase.
 * @details   This AJAX validates an Purchase when publishing it.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_purchase_manage_edit_box() {

  /* Enqueuing the script. */
  $id = PWWH_CORE_PURCHASE_EDIT_QNTS_JS;
  $url = PWWH_CORE_PURCHASE_URL . '/js/pwwh.purchase.edit.qnts.js';
  $deps = array(PWWH_CORE_JS);
  $ver = '20201125';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Localizing the script. */
  $data = array('ui' => pwwh_core_purchase_api_get_ui_facts(),
                'post' => pwwh_core_purchase_api_get_purchase_facts(),
                'note' => __('Note that any change will be in place after ' .
                             'an Update', 'piwi-warehouse'));
  wp_localize_script($id, 'pwwh_core_purchase_edit_obj', $data);
}
add_action('admin_enqueue_scripts', 'pwwh_core_purchase_manage_edit_box');

/*===========================================================================*/
/* AJAX related to Purchase delete alert.                                    */
/*===========================================================================*/

/**
 * @brief     Manage Operations script ID.
 */
define('PWWH_CORE_PURCHASE_DELETE_ALERT_JS',
       PWWH_CORE_PURCHASE . '_delete_alert_js');
/**
 * @brief     Intialize the delete alert for the Purchase.
 * @details   This AJAX triggers a message while trying to delete permanently
 *            an Purchase.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_purchase_delete_alert() {

  /* Enqueuing the script. */
  $id = PWWH_CORE_PURCHASE_DELETE_ALERT_JS;
  $url = PWWH_CORE_PURCHASE_URL . '/js/pwwh.purchase.delete.alert.js';
  $deps = array(PWWH_CORE_JS);
  $ver = '20201125';
  wp_enqueue_script($id, $url, $deps, $ver);

  /* Localizing the script. */
  $data = array('msg_alert' => __('This would delete Purchase for good. ' .
                                  'Are you sure you want to do this?',
                                  'piwi-warehouse'),
                'ui' => pwwh_core_purchase_api_get_ui_facts(),
                'post' => pwwh_core_purchase_api_get_purchase_facts());
  wp_localize_script($id, 'pwwh_core_purchase_del_obj', $data);
}
add_action('admin_enqueue_scripts', 'pwwh_core_purchase_delete_alert');

/*===========================================================================*/
/* AJAX related to Purchase post submit metabox.                             */
/*===========================================================================*/

/**
 * @brief     Manage Operations script ID.
 */
define('PWWH_CORE_PURCHASE_MANAGE_SUBMITDIV_JS',
       PWWH_CORE_PURCHASE . '_manage_submitdiv_js');
/**
 * @brief     Modifies the Post Submit Div removing extra buttons.
 * @details   This AJAX is associated to some CSS that early hides the buttons
 *            before this JS is able to remove them.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_purchase_manage_submit_div() {

  $post = get_post();
  $post_type = get_post_type($post);

  if($post_type == PWWH_CORE_PURCHASE) {

    /* Enqueuing the script. */
    $id = PWWH_CORE_PURCHASE_MANAGE_SUBMITDIV_JS;
    $url = PWWH_CORE_PURCHASE_URL . '/js/pwwh.purchase.manage.submitdiv.js';
    $deps = array(PWWH_CORE_JS);
    $ver = '20201125';
    wp_enqueue_script($id, $url, $deps, $ver);

    /* Localizing the script. */
    $post_facts = pwwh_core_purchase_api_get_purchase_facts();
    $data = array('post_type' => PWWH_CORE_PURCHASE,
                  'statuses' => $post_facts['status'],
                  'future_status' => 'publish',
                  'allowed_visibility' => array('public'),
                  'remove_minor' => true);
    wp_localize_script($id, 'pwwh_core_purchase_sub_obj', $data);
  }
}
add_action('admin_enqueue_scripts', 'pwwh_core_purchase_manage_submit_div');
/** @} */