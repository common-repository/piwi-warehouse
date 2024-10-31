<?php
/**
 * @file      movement/movement-ui.php
 * @brief     User Interface for Movement post type.
 *
 * @addtogroup PWWH_CORE_MOVEMENT
 * @{
 */

/**
 * @brief     Returns the Instant Collector as HTML.
 * @notes     The collector is an hidden input field that contains the
 *            instance ids of all the items of a movement. It is used to
 *            access the $_POST array and also as reference from many JS.
 *
 * @param[in] mixed $post         A Post object or the Post ID
 * @param[in] boolean $echo       Echoes on true
 *
 * @return    mixed the Movement Instance Collector as HTML or void.
 */
function pwwh_core_movement_ui_collector($post = null, $echo = true) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }

  /* Getting UI Facts. */
  $ui_facts = pwwh_core_movement_api_get_ui_facts();

  $data = pwwh_core_movement_api_get_quantities($post);
  if($data && count($data)) {
    $item_count = count($data);
    $instances = range(0, count($data) - 1);
  }
  else {
    $item_count = 1;
    $instances = array('0');
  }

  $args = array('type' => 'hidden',
                'id' => $ui_facts['input']['collector']['id'],
                'value' => implode(':', $instances));
  $output = pwwh_lib_ui_form_input($args);

  if($echo) {
    echo $output;
  }
  else {
    return $output;
  }
}

/**
 * @brief     Returns the content of movement item info as HTML.
 *
 * @param[in] mixed $post         A Post object or the Post ID.
 * @param[in] array $data         An array of data.
 * @paramkey{args}                An array of parameters sent while adding
 *                                metabox.
 * @paramkey{args instance}       An instance identifier. @default{0}
 * @paramkey{args item_id}        The item id used to fill the box.
 *                                @default{empty}
 * @paramkey{args loc_id}         The location id of this movement.
 *                                @default{empty}
 * @paramkey{args show_history}   Show the history on true. @default{false}
 * @paramkey{args show_man_ui}    Show management UI on true. @default{false}
 * @paramkey{args col_man_ui}     Collapse management UI on true. @default{false}
 * @paramkey{args show_del_ui}    Show delete item UI on true. @default{false}
 * @paramkey{args echo}           Echoes on true. @default{true}
 *
 * @return    mixed the movement option box as HTML or void.
 */
function pwwh_core_movement_ui_metabox_item_summary($post = null, $data = array()) {

  $args = $data['args'];
  $item_id = pwwh_lib_utils_validate_array_field($args, 'item_id', null);
  $loc_id = pwwh_lib_utils_validate_array_field($args, 'loc_id', null);
  $instance = pwwh_lib_utils_validate_array_field($args, 'instance', 0);
  $show_history = pwwh_lib_utils_validate_array_field($args, 'show_history', false);
  $show_man_ui = pwwh_lib_utils_validate_array_field($args, 'show_man_ui', false);
  $col_man_ui = pwwh_lib_utils_validate_array_field($args, 'col_man_ui', false);
  $show_del_ui = pwwh_lib_utils_validate_array_field($args, 'show_del_ui', false);
  $echo = pwwh_lib_utils_validate_array_field($args, 'echo', true);

  $data['args']['echo'] = false;

  /* Getting UI Facts. */
  $ui_facts = pwwh_core_movement_api_get_ui_facts();

  /* Getting meta values. */
  $moved_val = pwwh_core_movement_api_get_moved_by_item($post, $item_id,
                                                        $loc_id);
  $returned_val = pwwh_core_movement_api_get_returned_by_item($post, $item_id,
                                                              $loc_id);
  $donated_val = pwwh_core_movement_api_get_donated_by_item($post, $item_id,
                                                            $loc_id);
  $lost_val = pwwh_core_movement_api_get_lost_by_item($post, $item_id,
                                                      $loc_id);
  $lent_val = pwwh_core_movement_api_get_lent_by_item($post, $item_id,
                                                      $loc_id);

  /* Composing metas. */
  {
    /* Hiding availability and amount. */
    $data['args']['show_avail'] = false;
    $data['args']['show_amount'] = false;

    $_metas = '';

    /* Adding Item hidden input. */
    $id = $instance . ':' . $ui_facts['input']['item']['id'];
    $args = array('type' => 'hidden',
                  'id' => $id,
                  'value' => get_the_title($item_id));
    $_metas .= pwwh_lib_ui_form_input($args);

    /* Adding Location hidden input. */
    $id = $instance . ':' . $ui_facts['input']['location']['id'];
    $args = array('type' => 'hidden',
                  'id' => $id,
                  'value' => pwwh_core_item_api_get_location_name($loc_id));
    $_metas .= pwwh_lib_ui_form_input($args);

    /* Generating Moved info. */
    $args = array('description' => __('Moved', 'piwi-warehouse'),
                  'value' => $moved_val,
                  'icon' => 'dashicons-migrate',
                  'class' => 'pwwh-moved');
    $_metas .= pwwh_lib_ui_admin_info_chunk($args, false);
    $args = array('type' => 'hidden',
                  'id' => $instance . ':' . $ui_facts['input']['moved']['id'],
                  'value' => $moved_val);
    $_metas .= pwwh_lib_ui_form_input($args);

    /* Generating Holder info. */
    $args = array('linked' => false, 'echo' => false);
    $holder = pwwh_core_ui_tax_list(PWWH_CORE_MOVEMENT_HOLDER, $post->ID,
                                    $args);

    $args = array('description' =>__('Holder', 'piwi-warehouse'),
                  'value' => $holder,
                  'icon' => 'dashicons-admin-users',
                  'class' => 'pwwh-holder');
    $_metas .= pwwh_lib_ui_admin_info_chunk($args, false);

    /* Adding Item Availability after the Holder. */
    $avail = pwwh_core_item_api_get_avail($item_id);

    $args = array('description' => __('Availability' , 'piwi-warehouse'),
                  'value' => $avail,
                  'icon' => 'dashicons-chart-bar',
                  'class' => 'pwwh-avail');
    $_metas .= pwwh_lib_ui_admin_info_chunk($args, false);

    $data['args']['meta_hook'] = $_metas;
  }

  /* Managing extra sections. */
  {
    $_sections = array();

    /* Location visual box. */
    if(pwwh_core_item_api_sanitize_location($loc_id)) {
      $title = __('Movement Origin' , 'piwi-warehouse');
      $_title = '<div class="section-title">' . $title . '</div>';
      /* Getting Location name. */
      $loc = pwwh_core_item_api_get_location_name($loc_id);

      $args = array('description' => __('Origin of the Item' ,
                                        'piwi-warehouse'),
                    'value' => $loc,
                    'icon' => 'dashicons-location',
                    'class' => 'pwwh-location');
      $_loc_chunk = pwwh_lib_ui_admin_info_chunk($args, false);

      /* Composing history. */
      $_location = '<div class="location">' .
                      $_title .
                      '<span class="history-wrap">' .
                        $_loc_chunk .
                      '</span>
                    </div>';
      $_sections[$instance . ':pwwh-movement-location'] = $_location;
    }

    /* Managing history. */
    if($show_history) {
      $title = __('Movement History' , 'piwi-warehouse');
      $_title = '<div class="section-title">' . $title . '</div>';

      /* Adding history table. */
      $history = new pwwh_core_movement_history_list($post->ID, $item_id,
                                                     $loc_id);

      if($lent_val) {
        $_history_class = 'history active';
      }
      else {
        $_history_class = 'history concluded';
      }

      /* Composing history. */
      $_history = '<div class="' . $_history_class . '">' .
                    $_title .
                    '<span class="history-wrap">' .
                      $history->get_history() .
                    '</span>
                  </div>';
      $_sections[$instance . ':pwwh-movement-history'] = $_history;
    }

    if($show_man_ui) {
      if(get_post_status($item_id) != 'publish') {
        $disabled = true;
      }
      else {
        $disabled = false;
      }

      if($col_man_ui) {
        $args = array('id' => 'pwwh-hideshow',
                      'value' => strval($instance),
                      'name' => 'pwwh-uncollapse',
                      'disabled' => $disabled,
                      'label' => __('Show', 'piwi-warehouse'),
                      'title' => __('Show Management area', 'piwi-warehouse'));
        $_man_button = pwwh_lib_ui_form_button($args);
        $_man_class = 'hide-if-js pwwh-movement-management';
      }
      else {
        $args = array('id' => 'pwwh-hideshow',
                      'value' => strval($instance),
                      'name' => 'pwwh-collapse',
                      'disabled' => $disabled,
                      'label' => __('Hide', 'piwi-warehouse'),
                      'title' => __('Hide Management area', 'piwi-warehouse'));
        $_man_button = pwwh_lib_ui_form_button($args);
        $_man_class = 'pwwh-movement-management';
      }

      $title = __('Movement Operations' , 'piwi-warehouse');
      $_title = '<div class="section-title">' .
                  $title . $_man_button .
                '</div>';
     /* Generating description. */
      $desc = __('An Item on loan can be marked as Returned, Donated or ' .
                 'Lost. The Movement can be updated indefinitely until all ' .
                 'the Items on Loan returns back, are marked as Donated or ' .
                 'Lost. Each operation is stored in the history. When all ' .
                 'the Items on loan are justified the movement is marked as ' .
                 'concluded and cannot be edited anymore.', 'piwi-warehouse');
      $_desc = '<p style="margin-top: 0;"><i>' . $desc . '</p></i>';

      /* Generating returned box. */
      $placeholder = __('Enter the Item amount to mark as Returned',
                        'piwi-warehouse');
      $args = array('label' => __('Amount to mark as Returned', 'piwi-warehouse'),
                    'icon' => 'dashicons-yes',
                    'type' => 'number',
                    'id' => $instance . ':' . $ui_facts['input']['returned']['id'],
                    'value' => $returned_val,
                    'placeholder' => $placeholder,
                    'min' => $ui_facts['input']['returned']['rule']['min'],
                    'step' => $ui_facts['input']['returned']['rule']['step'],
                    'disabled' => $disabled);
      $_ret = pwwh_lib_ui_form_input($args);

      /* Generating donated box. */
      $placeholder = __('Enter the Item amount to mark as Donated',
                        'piwi-warehouse');
      $args = array('label' => __('Amount to mark as Donated', 'piwi-warehouse'),
                    'icon' => 'dashicons-heart',
                    'type' => 'number',
                    'id' => $instance . ':' . $ui_facts['input']['donated']['id'],
                    'value' => $donated_val,
                    'placeholder' => $placeholder,
                    'min' => $ui_facts['input']['donated']['rule']['min'],
                    'step' => $ui_facts['input']['donated']['rule']['step'],
                    'disabled' => $disabled);
      $_don = pwwh_lib_ui_form_input($args);

      /* Generating lost box. */
      $placeholder = __('Enter the Item amount to mark as Lost',
                        'piwi-warehouse');
      $args = array('label' => __('Amount to mark as Lost', 'piwi-warehouse'),
                    'icon' => 'dashicons-no',
                    'type' => 'number',
                    'id' => $instance . ':' . $ui_facts['input']['lost']['id'],
                    'value' => $lost_val,
                    'placeholder' => $placeholder,
                    'min' =>  $ui_facts['input']['lost']['rule']['min'],
                    'step' =>  $ui_facts['input']['lost']['rule']['step'],
                    'disabled' => $disabled);
      $_lost = pwwh_lib_ui_form_input($args);

      /* Generating lent box. */
      if($lent_val) {
        $after_lent_class =  $ui_facts['field']['new_lent']['id'] .
                            ' pwwh-active';
      }
      else {
        $after_lent_class = $ui_facts['field']['new_lent']['id'] .
                            ' pwwh-concluded';
      }
      $args = array('description' =>__('Amount currently on Loan',
                                       'piwi-warehouse'),
                    'value' => $lent_val,
                    'icon' => 'dashicons-archive',
                    'id' => $instance . ':' . 'pwwh_curr_lent',
                    'class' => 'pwwh_curr_lent');
      $_lent = pwwh_lib_ui_admin_info_chunk($args, false);
      $args = array('description' =>__('Amount foreseen on Loan',
                                       'piwi-warehouse'),
                    'value' => $lent_val,
                    'icon' => 'dashicons-visibility',
                    'id' => $instance . ':' . $ui_facts['field']['new_lent']['id'],
                    'class' => $after_lent_class);
      $_lent .= pwwh_lib_ui_admin_info_chunk($args, false);

      /* Composing management. */
      $_manager = '<div id="pwwh-movement-manager-area">' .
                    $_title .
                    '<div class="' . $_man_class .'">' .
                     $_desc . $_lent . $_ret . $_don . $_lost .
                    '</div>
                  </div>';
      $_sections[$instance . ':pwwh-movement-management'] = $_manager;
    }
    else {

      $args = array('type' => 'hidden',
                    'id' => $instance . ':' . $ui_facts['input']['returned']['id'],
                    'value' => $returned);
      $_ret = pwwh_lib_ui_form_input($args);

      $args = array('type' => 'hidden',
                    'id' => $instance . ':' . $ui_facts['input']['donated']['id'],
                    'value' => $donated);
      $_don = pwwh_lib_ui_form_input($args);

      $args = array('type' => 'hidden',
                    'id' => $instance . ':' . $ui_facts['input']['lost']['id'],
                    'value' => $lost);
      $_lost = pwwh_lib_ui_form_input($args);

      /* Composing management. */
      $_manager = '<div id="pwwh-movement-manager-area">
                    <div class="pwwh-movement-management">' .
                      $_ret . $_don . $_lost .
                    '</div>
                  </div>';
      $_sections[$instance . ':pwwh-movement-management'] = $_manager;
    }
    $data['args']['section_hook'] = $_sections;
  }

  /* Generating and pushing delete button. */
  if($show_del_ui) {
    $label = sprintf(__('Remove %', 'piwi-warehouse'),
                     get_the_title($item_id));
    $args = array('id' => 'pwwh-remove',
                  'classes' => 'hide-if-no-js',
                  'value' => strval($instance),
                  'name' => 'pwwh-remove',
                  'icon' => 'dashicons-dismiss',
                  'title' => $label);
    $data['args']['pre_hook'] = pwwh_lib_ui_form_button($args);
  }

  $output = pwwh_core_item_ui_metabox_item_summary($item_id, $data);

  if($echo) {
    echo $output;
  }
  else {
    return $output;
  }
}

/**
 * @brief     Returns the content of movement option box as HTML.
 *
 * @param[in] mixed $post         A Post object or the Post ID.
 * @param[in] array $data         An array of data.
 * @paramkey{args}                An array of parameters sent while adding
 *                                metabox.
 * @paramkey{args show_item_ui}   Show item UI on true. @default{true}
 * @paramkey{args item_id}        The item id used to fill the UI.
 *                                @default{empty}
 * @paramkey{args show_loc_ui}    Show location UI on true. @default{true}
 * @paramkey{args loc_id}         The location id used to fill the UI.
 *                                @default{empty}
 * @paramkey{args show_mov_ui}    Show mov UI on true. @default{true}
 * @paramkey{args mov_value}      The quantity value used to fill the UI.
 *                                @default{0}
 * @paramkey{args instance}       An instance identifier. @default{0}
 * @paramkey{args is_primary}     If true adds extra code. @default{true}
 * @paramkey{args echo}           Echoes on true. @default{true}
 *
 * @return    mixed the movement option box as HTML or void.
 */
function pwwh_core_movement_ui_metabox_add_item($post = null, $data = array()) {

  $args = $data['args'];
  $instance = pwwh_lib_utils_validate_array_field($args, 'instance', 0);
  $show_item_ui = pwwh_lib_utils_validate_array_field($args, 'show_item_ui', true);
  $item_id = pwwh_lib_utils_validate_array_field($args, 'item_id', null);
  $show_loc_ui = pwwh_lib_utils_validate_array_field($args, 'show_loc_ui', true);
  $loc_id = pwwh_lib_utils_validate_array_field($args, 'loc_id', null);
  $show_mov_ui = pwwh_lib_utils_validate_array_field($args, 'show_mov_ui', true);
  $mov_value = pwwh_lib_utils_validate_array_field($args, 'mov_value', null);
  $is_primary = pwwh_lib_utils_validate_array_field($args, 'is_primary', true);
  $echo = pwwh_lib_utils_validate_array_field($args, 'echo', true);

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }

  /* Getting UI Facts. */
  $ui_facts = pwwh_core_movement_api_get_ui_facts();

  /* Generating Item box. */
  $item_box = '';
  if($show_item_ui) {

    /* Getting Item title to pre-fill the item box. */
    $item_title = '';
    if($item_id) {
      $item = get_post($item_id);
      if(($item) && ($item->post_type == PWWH_CORE_ITEM)) {
        $item_title = esc_attr($item->post_title);
      }
    }

    /* Generating Item box. */
    $args = array('post_type' => PWWH_CORE_ITEM, 'posts_per_page' => -1,
                  'post_status' => 'publish');
    $id = $instance . ':' . $ui_facts['input']['item']['id'];
    $dl_data  = get_posts($args);
    $dl_id = $instance . ':' . $ui_facts['input']['item']['datalist'];

    $placeholder = __('Enter item by name (Required)', 'piwi-warehouse');
    $args = array('label' => __('Item name', 'piwi-warehouse'),
                  'icon' => 'dashicons-carrot',
                  'id' => $id,
                  'classes' => $ui_facts['input']['item']['id'],
                  'placeholder' => $placeholder,
                  'value' => $item_title,
                  'dl-id' => $dl_id,
                  'dl-data' => $dl_data,
                  'dl-fillwith' => 'post_title');
    $item_box = pwwh_lib_ui_form_input($args);
  }

  /* Generating Location box. */
  $loc_box = '';
  if($show_loc_ui) {

    /* Getting Location title to pre-fill the loc box. */
    $loc_title = '';
    if($loc_id) {
      $loc = pwwh_core_item_api_sanitize_location($loc_id);
      if($loc) {
        $loc_title = esc_attr($loc->name);
      }
    }

    /* Generating Location box. */
    $args = array('taxonomy' => PWWH_CORE_ITEM_LOCATION,
                  'hide_empty' => false);
    $id = $instance . ':' . $ui_facts['input']['location']['id'];
    $dl_data  = get_terms($args);
    $dl_id = $instance . ':' . $ui_facts['input']['location']['datalist'];

    $placeholder = __('Enter location by name (Required)', 'piwi-warehouse');
    $args = array('label' => __('Location name', 'piwi-warehouse'),
                  'icon' => 'dashicons-location',
                  'id' => $id,
                  'classes' => $ui_facts['input']['location']['id'],
                  'placeholder' => $placeholder,
                  'value' => $loc_title,
                  'dl-id' => $dl_id,
                  'dl-data' => $dl_data,
                  'dl-fillwith' => 'name');
    $loc_box = pwwh_lib_ui_form_input($args);
  }

  /* Generating Moved input box. */
  $mov_box = '';
  if($show_mov_ui) {
    $placeholder = __('Enter the Item amount to displace (Required)',
                      'piwi-warehouse');

    $id = $instance . ':' . $ui_facts['input']['moved']['id'];
    $args = array('label' => __('Amount to displace', 'piwi-warehouse'),
                  'icon' => 'dashicons-archive',
                  'type' => 'number',
                  'id' => $id,
                  'classes' => $ui_facts['input']['moved']['id'],
                  'placeholder' => $placeholder,
                  'value' => $mov_value,
                  'min' => $ui_facts['input']['moved']['rule']['min'],
                  'step' => $ui_facts['input']['moved']['rule']['step']);
    $mov_box = pwwh_lib_ui_form_input($args);
  }

  if($is_primary) {
    /* Generating a description. */
    $description = __('Choose the Item you want to manage by name. Note that ' .
                      'the whole displaced amount would be marked as lent to ' .
                      'the Holder. You can decide to donate or take back items ' .
                      'at a later time.', 'piwi-warehouse');
    /* Composing main box. */
    $main = '<div id="pwwh-main" class="pwwh-main">
              <span class="pwwh-description">' .
                $description .
              '</span>
              <span id="' . $ui_facts['box']['add_item']['id'] . '-primary"
                    class="primary">' .
                $item_box . $loc_box . $mov_box .
              '</span>
            </div>';

    /* Generating the footer with add button. */
    $args = array('id' => 'pwwh-add',
                  'classes' => 'hide-if-no-js',
                  'value' => strval($instance + 1),
                  'name' => 'pwwh-add',
                  'label' => __('Add another Item', 'piwi-warehouse'));
    $add_button = pwwh_lib_ui_form_button($args);

    $footer = '<div id="pwwh-footer" class="pwwh-footer">' .
                $add_button .
              '</div>';
  }
  else {
    /* Generating the delete button. */
    $args = array('id' => 'pwwh-remove',
                  'classes' => 'hide-if-no-js',
                  'value' => strval($instance),
                  'name' => 'pwwh-remove',
                  'icon' => 'dashicons-dismiss',
                  'title' => __('Remove this Item', 'piwi-warehouse'));
    $delete_button = pwwh_lib_ui_form_button($args);

    /* Composing main box. */
    $main = '<div id="' . $ui_facts['box']['add_item']['id'] . '-' . $instance . '"
                   class="additional" style="display: none;">' .
              $delete_button . $item_box . $loc_box . $mov_box .
            '</div>';
    $_footer = '';
  }

  $output = $main . $footer;

  if($echo) {
    echo $output;
  }
  else {
    return $output;
  }
}

/**
 * @brief     Returns the content for holder postbox as HTML.
 *
 * @param[in] mixed $post         A Post object or the Post ID
 * @param[in] boolean $echo       Echoes on true
 *
 * @return    string the content as HTML.
 */
function pwwh_core_movement_ui_metabox_holder($post = null, $echo = true) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }

  /* Getting UI Facts. */
  $ui_facts = pwwh_core_movement_api_get_ui_facts();

  /* Managing args for holder input*/
  $post_id = $post->ID;
  $post_status = get_post_status($post);

  /* Getting the holder name. */
  $holder_name = '';
  if(($post_status == 'new') || ($post_status == 'auto-draft')) {
    /* Getting data from GET. */
    if(isset($_GET[PWWH_CORE_MOVEMENT_QUERY_HOLDER])) {
      $holder_id = floatval($_GET[PWWH_CORE_MOVEMENT_QUERY_HOLDER]);
      $holder = get_term($holder_id, PWWH_CORE_MOVEMENT_HOLDER);
      if($holder) {
        $holder_name = $holder->name;
      }
    }
  }

  /* Generating a description. */
  $desc = __('The Holder is the person or the entity which can borrow an ' .
              'Item or receive it as a gift.', 'piwi-warehouse');
  $_desc = '<p><i>' . $desc . '</i></p>';

  /* Generating holder box. */
  $args = array('taxonomy' => PWWH_CORE_MOVEMENT_HOLDER,
                'orderby' => 'id',
                'order' => 'ASC',
                'show_count' => 0,
                'hide_empty' => false,
                'echo' => false,
                'selected' => 0,
                'hierarchical' => 1);
  $dl_data = get_terms($args);
  $dl_id = $ui_facts['input']['holder']['datalist'];

  $placeholder = __('Enter Holder by name (Required)',
                    'piwi-warehouse');
  $args = array('label' => __('Holder name', 'piwi-warehouse'),
                'icon' => 'dashicons-admin-users',
                'id' => $ui_facts['input']['holder']['id'],
                'placeholder' => $placeholder,
                'value' => $holder_name,
                'dl-id' => $dl_id,
                'dl-data' => $dl_data,
                'dl-fillwith' => 'name');
  $_holder = pwwh_lib_ui_form_input($args);

  $output = $_desc . $_holder;

  if($echo) {
    echo $output;
  }
  else {
    return $output;
  }
}
/** @} */