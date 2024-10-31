<?php
/**
 * @file      items/item-hook.php
 * @brief     Hooks and function related to Item post type.
 *
 * @addtogroup PWWH_CORE_ITEM
 * @{
 */

/**
 * @brief     Registers the Item custom post type and its taxonomies.
 *
 * @hooked    init
 *
 * @return    void
 */
function pwwh_core_item_init() {

  /* Registering the Item custom post type. */
  {
    $labels = pwwh_core_api_get_post_labels(PWWH_CORE_ITEM_LABEL_SINGULAR,
                                            PWWH_CORE_ITEM_LABEL_PLURAL);

    $supports = array('title', 'editor', 'thumbnail');

    $rewrite = array('slug' => 'item',
                     'with_front' => true);

    $caps = pwwh_core_item_caps_init();

    $args = array('labels' => $labels,
                  'public' => true,
                  'show_ui' => true,
                  'show_in_menu' => false,
                  'supports' => $supports,
                  'has_archive' => true,
                  'capability_type' => PWWH_CORE_ITEM,
                  'capabilities' => $caps,
                  'map_meta_cap' => true,
                  'rewrite' => $rewrite,
                  'exclude_from_search' => false,
                  'publicly_queryable' => true);
    register_post_type(PWWH_CORE_ITEM, $args);
  }

  /* Registering the Location taxonomy. */
  {
    $labels = pwwh_core_api_get_tax_labels(PWWH_CORE_ITEM_LOCATION_LABEL_SINGULAR,
                                           PWWH_CORE_ITEM_LOCATION_LABEL_PLURAL);

    $caps = pwwh_core_item_caps_location_init();

    $args = array('hierarchical' => true,
                  'labels' => $labels,
                  'show_ui' => true,
                  'update_count_callback' => '_update_post_term_count',
                  'show_admin_column' => true,
                  'query_var' => true,
                  'capabilities' => $caps,
                  'rewrite' => array('slug' => 'location'));
    register_taxonomy(PWWH_CORE_ITEM_LOCATION, PWWH_CORE_ITEM, $args);
  }

  /* Registering the Type taxonomy. */
  {
    $labels = pwwh_core_api_get_tax_labels(PWWH_CORE_ITEM_TYPE_LABEL_SINGULAR,
                                           PWWH_CORE_ITEM_TYPE_LABEL_PLURAL);

    $caps = pwwh_core_item_caps_type_init();

    $args = array('hierarchical' => true,
                  'labels' => $labels,
                  'show_ui' => true,
                  'update_count_callback' => '_update_post_term_count',
                  'show_admin_column' => true,
                  'query_var' => true,
                  'capabilities' => $caps,
                  'rewrite' => array('slug' => 'type'));
    register_taxonomy(PWWH_CORE_ITEM_TYPE, PWWH_CORE_ITEM, $args);
  }

  /* Configuring Item post list behaviour. */
  pwwh_core_item_lists_init();
}
add_action('init', 'pwwh_core_item_init');

/**
 * @brief     Item related style.
 */
define('PWWH_CORE_ITEM_CSS', PWWH_PREFIX . '_item_css');

/**
 * @brief     Enqueues item related admin style.
 *
 * @hooked    admin_enqueue_scripts
 *
 * @return    void
 */
function pwwh_core_item_enqueue_style() {

  /* Enqueue Item specific Custom Style. */
  $id = PWWH_CORE_ITEM_CSS;
  $url = PWWH_CORE_ITEM_URL . '/css/item.css';
  $deps = array();
  $ver = '20201029';
  wp_enqueue_style($id, $url, $deps, $ver);
}
add_action('admin_enqueue_scripts', 'pwwh_core_item_enqueue_style');

/**
 * @brief     Customizes the updated messages for the Item post type.
 *
 * @param[in] array $msgs         The array of messages.
 *
 * @hooked    post_updated_messages
 *
 * @return    array the filtered messages array.
 */
function pwwh_core_item_custom_post_updated_messages($msgs) {

  $_msgs = pwwh_core_api_post_updated_messages(PWWH_CORE_ITEM_LABEL_SINGULAR,
                                               PWWH_CORE_ITEM_LABEL_PLURAL);
  $msgs[PWWH_CORE_ITEM] = $_msgs;

  return $msgs;
}
add_action('post_updated_messages',
           'pwwh_core_item_custom_post_updated_messages');

/**
 * @brief     Customizes the updated messages for the location and Type
 *            taxonomy.
 *
 * @param[in] array $msgs         The array of messages.
 *
 * @hooked    term_updated_messages
 *
 * @return    array the filtered messages array.
 */
function pwwh_core_item_custom_tax_updated_messages($messages) {

  /* Updating messages for the Location taxonomy. */
  {
    $msgs = pwwh_core_api_tax_updated_messages(PWWH_CORE_ITEM_LOCATION_LABEL_SINGULAR);
    $messages[PWWH_CORE_ITEM_LOCATION] = $msgs;
  }

  /* Updating messages for the Type taxonomy. */
  {
    $msgs = pwwh_core_api_tax_updated_messages(PWWH_CORE_ITEM_TYPE_LABEL_SINGULAR);
    $messages[PWWH_CORE_ITEM_TYPE] = $msgs;
  }

  return $messages;
}
add_action('term_updated_messages',
           'pwwh_core_item_custom_tax_updated_messages');

/**
 * @brief     Customizes the bulk messages for the Item post type.
 *
 * @param[in] array $msgs         The array of messages.
 * @param[in] array $bulk_counts   The array of bulk counts.
 *
 * @hooked    bulk_post_updated_messages
 *
 * @return    array the filtered messages array.
 */
function pwwh_core_item_custom_bulk_messages($msgs, $bulk_counts) {

  $_msgs = pwwh_core_api_post_bulk_messages(PWWH_CORE_ITEM_LABEL_SINGULAR,
                                            PWWH_CORE_ITEM_LABEL_PLURAL,
                                            $bulk_counts);
  $msgs[PWWH_CORE_ITEM] = $_msgs;

  return $msgs;
}
add_action('bulk_post_updated_messages',
           'pwwh_core_item_custom_bulk_messages', 10, 2);

/**
 * @brief     Manages meta boxes for the Item custom post type.
 *
 * @hooked    add_meta_boxes
 *
 * @return    void
 */
function pwwh_core_item_manage_meta_boxes() {

  $post = get_post();
  $post_type = $post->post_type;
  $post_status = get_post_status($post);

  if($post_type === PWWH_CORE_ITEM) {

    /* Get UI facts. */
    $ui_facts = pwwh_core_item_api_get_ui_facts();

    /* Adding Custom postbox only if the Item is published. Note that the
       add_meta_box already filter by screen (4th paramether) and there is no
       need to check the post type. */
    if($post_status == 'publish') {

      /* Adding warehouse status. */
      $label = $ui_facts['box']['records']['label'];
      add_meta_box($ui_facts['box']['records']['id'], $label,
                   $ui_facts['box']['records']['callback'], PWWH_CORE_ITEM,
                   'normal');

      /* Adding quick operations. */
      $args = array();
      if(current_user_can(PWWH_CORE_MOVEMENT_CAPS_MANAGE_MOVEMENTS)) {
        $args['movement'] = true;
      }
      if(current_user_can(PWWH_CORE_PURCHASE_CAPS_MANAGE_PURCHASES)) {
        $args['purchase'] = true;
      }
      if(count($args)) {
        $label = $ui_facts['box']['quick_ops']['label'];
        add_meta_box($ui_facts['box']['quick_ops']['id'], $label,
                     $ui_facts['box']['quick_ops']['callback'], PWWH_CORE_ITEM,
                     'side', 'high', $args);
      }
    }

    /* Removing unwanted meta boxes. */
    remove_meta_box('slugdiv', PWWH_CORE_ITEM, 'normal');
    remove_meta_box(PWWH_CORE_ITEM_LOCATION . 'div', PWWH_CORE_ITEM, 'side');
  }
}
add_action('add_meta_boxes', 'pwwh_core_item_manage_meta_boxes');

/**
 * @brief     Deletes all the Item meta and removes the Item from all the
 *            movement and purchases.
 *
 * @param[in] int $post_id        Post ID
 *
 * @hooked    save_post
 *
 * @return    int the post ID
 */
function pwwh_core_item_on_delete($post_id) {

  $post_type = get_post_type($post_id);

  /* This must act only on items. */
  if($post_type == PWWH_CORE_ITEM) {

    /** @todo extend movement_facts */
    /* Deleting movements on this Item. */
    $post_status = array('draft',
                         PWWH_CORE_MOVEMENT_STATUS_ACTIVE,
                         PWWH_CORE_MOVEMENT_STATUS_CONCLUDED,
                         'trash');
    $movements = pwwh_core_movement_api_get_by_item($post_id, $post_status);
    foreach($movements as $movement) {
      pwwh_core_movement_api_remove_item($movement, $post_id);
    }

    /* Removing this item from purchases. */
    $post_status = array('draft', 'publish', 'trash');
    $purchases = pwwh_core_purchase_api_get_by_item($post_id, $post_status);
    foreach($purchases as $purchase) {
      pwwh_core_purchase_api_remove_item($purchase->ID, $post_id);
    }
  }
}
add_action('before_delete_post', 'pwwh_core_item_on_delete');

/**
 * @brief     Restore the title of an item to avoid wrong use in forms.
 * @todo      Check if this function is still of any use: possible solution
 *            do comparison between title and modified title.
 *
 * @param[in] string $post_title  Post title
 * @param[in] int $post_id        Post ID
 *
 * @hooked    the_title
 *
 * @return    int the post ID
 */
function pwwh_core_item_filter_title($post_title, $post_id) {

  $post = get_post($post_id);
  if($post) {
    $post_type = get_post_type($post);

    if($post_type == PWWH_CORE_ITEM) {
      $post_title = $post->post_title;
    }
  }
  return $post_title;
}
add_filter('the_title', 'pwwh_core_item_filter_title', 10, 2);

/**
 * @brief     Verifies the Location existency before the push in the database.
 * @details   This checks the location as enforcement of the JS Validate.
 *
 * @param[in] mixed $term_name    The name of the term to insert.
 * @param[in] array $taxonomy     The taxonomy slug.
 *
 * @hooked    pre_insert_term
 *
 * @return    void
 */
function pwwh_core_item_on_location_insert($term_name, $taxonomy) {

  if($taxonomy === PWWH_CORE_ITEM_LOCATION) {

    /* Checking if the location exists. */
    $loc = pwwh_core_item_get_location_by_name($term_name);

    if($loc) {
      /* Getting the facts. */
      $ui_facts = pwwh_core_item_api_get_ui_edit_tag_facts();

      /* Composing and returning an error. */
      $msg = $ui_facts['input']['location']['msg']['remote'];
      return new WP_Error('location_duplicated', $msg);
    }
  }
  return $term_name;
}
add_filter('pre_insert_term', 'pwwh_core_item_on_location_insert', 10, 2);
/** @} */