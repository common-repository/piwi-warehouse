<?php
/**
 * @file      purchase/purchase-ui.php
 * @brief     User Interface for Purchase post type.
 *
 * @addtogroup PWWH_CORE_PURCHASE
 * @{
 */

/**
 * @brief     Returns the Instant Collector as HTML.
 * @notes     The collector is an hidden input field that contains the
 *            instance ids of all the items of a purchase. It is used to
 *            access the $_POST array and also as reference from many JS.
 *
 * @param[in] mixed $post         A Post object or the Post ID
 * @param[in] boolean $echo       Echoes on true
 *
 * @return    mixed the Purchase Instance Collector as HTML or void.
 */
function pwwh_core_purchase_ui_collector($post = null, $echo = true) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }

  /* Getting UI Facts. */
  $ui_facts = pwwh_core_purchase_api_get_ui_facts();

  $data = pwwh_core_purchase_api_get_quantities($post);
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
 * @brief     Returns the content of purchase item info as HTML.
 *
 * @param[in] mixed $post         A Post object or the Post ID.
 * @param[in] array $data         An array of data.
 * @paramkey{args}                An array of parameters sent while adding
 *                                metabox.
 * @paramkey{args instance}       An instance identifier. @default{0}
 * @paramkey{args item_id}        The item id used to fill the box.
 *                                @default{empty}
 * @paramkey{args loc_id}         The location id of this purchase.
 *                                @default{empty}
 * @paramkey{args show_del_ui}    Show delete item UI on true. @default{false}
 * @paramkey{args show_mod_ui}    Show modify item UI on true. @default{false}
 * @paramkey{args echo}           Echoes on true. @default{true}
 *
 * @return    mixed the purchase option box as HTML or void.
 */
function pwwh_core_purchase_ui_metabox_item_summary($post = null, $data = array()) {

  $args = $data['args'];
  $item_id = pwwh_lib_utils_validate_array_field($args, 'item_id', null);
  $loc_id = pwwh_lib_utils_validate_array_field($args, 'loc_id', null);
  $instance = pwwh_lib_utils_validate_array_field($args, 'instance', 0);
  $show_del_ui = pwwh_lib_utils_validate_array_field($args, 'show_del_ui', false);
  $show_mod_ui = pwwh_lib_utils_validate_array_field($args, 'show_mod_ui', false);
  $echo = pwwh_lib_utils_validate_array_field($args, 'echo', true);

  $data['args']['echo'] = false;

  /* Getting UI Facts. */
  $ui_facts = pwwh_core_purchase_api_get_ui_facts();

  /* Composing purchase summary. */
  {
    $title = __('Purchase Summary' , 'piwi-warehouse');
    $_title = '<div class="section-title">' . $title . '</div>';

    if(get_post_status($item_id) != 'publish') {
      $disabled = true;
    }
    else {
      $disabled = false;
    }

    /* Location visual box. */
    {
      if(pwwh_core_item_api_sanitize_location($loc_id)) {

        /* Getting Location name. */
        $loc = pwwh_core_item_api_get_location_name($loc_id);

        $args = array('description' => __('Purchase Destination' ,
                                          'piwi-warehouse'),
                      'value' => $loc,
                      'icon' => 'dashicons-location',
                      'class' => 'pwwh-location');
        $_loc_chunk = pwwh_lib_ui_admin_info_chunk($args, false);
      }
      else {
        $_loc_chunk = '';
      }

    }

    /* Quantity visual box. */
    {
      if($show_mod_ui) {
        $args = array('id' => $ui_facts['box']['item_summary']['button']['edit'],
                      'type' => 'button',
                      'classes' => 'pwwh-lib-button hide-if-no-js',
                      'name' => esc_attr('edit'),
                      'value' => esc_attr($instance),
                      'label' => __('Edit', 'piwi-warehouse'),
                      'title' => __('Edit quantity', 'piwi-warehouse'),
                      'disabled' => $disabled);
        $_edit_button = pwwh_lib_ui_form_button($args);
      }
      else {
        $_edit_button = '';
      }

      $qnt = pwwh_core_purchase_api_get_quantity_by_item($post, $item_id,
                                                         $loc_id);
      $args = array('description' => __('Purchased quantity' , 'piwi-warehouse'),
                    'value' => $qnt,
                    'icon' => 'dashicons-migrate',
                    'class' => 'pwwh-quantity',
                    'after' => $_edit_button);
      $_qnt_chunk = pwwh_lib_ui_admin_info_chunk($args, false);
    }

    /* Edit box: this contains the Item, Location and Quantity inputs. */
    {
      /* Composing Item Input. */
      $id = $instance . ':' . $ui_facts['input']['item']['id'];
      $args = array('type' => 'hidden',
                    'id' => $id,
                    'value' => get_the_title($item_id));
      $item_input = pwwh_lib_ui_form_input($args);

      /* Composing Location Input. */
      $id = $instance . ':' . $ui_facts['input']['location']['id'];
      $args = array('type' => 'hidden',
                    'id' => $id,
                    'value' => pwwh_core_item_api_get_location_name($loc_id));
      $loc_input = pwwh_lib_ui_form_input($args);

      if($show_mod_ui) {
        /* Composing Quantity Input. */
        $args = array('id' => $instance . ':' . $ui_facts['input']['quantity']['id'],
                      'type' => 'number',
                      'value' => $qnt,
                      'step' => 1,
                      'disabled' => $disabled);
        $qnt_input = pwwh_lib_ui_form_input($args);

        /* Composing Quantity edit buttons. */
        $args = array('id' => $ui_facts['box']['item_summary']['button']['confirm'],
                      'type' => 'button',
                      'classes' => 'pwwh-lib-button',
                      'name' => esc_attr('confirm'),
                      'value' => esc_attr($instance),
                      'label' => __('Ok', 'piwi-warehouse'),
                      'disabled' => $disabled);
        $confirm = pwwh_lib_ui_form_button($args);
        $args = array('id' => $ui_facts['box']['item_summary']['button']['abort'],
                      'type' => 'button',
                      'classes' => 'pwwh-lib-button',
                      'name' => esc_attr('abort'),
                      'value' => esc_attr($instance),
                      'label' => __('Cancel', 'piwi-warehouse'),
                      'disabled' => $disabled);
        $abort = pwwh_lib_ui_form_button($args);

        /* Composing edit box. */
        $_edit_box = '<div id="pwwh-qnt-editarea"
                        class="hide-if-js hide-if-no-js inst-' . $instance . '">
                        <span id="pwwh-item-input">' .
                          $item_input .
                        '</span>
                        <span id="pwwh-loc-input">' .
                          $loc_input .
                        '</span>
                        <span id="pwwh-qnt-input">' .
                          $qnt_input .
                        '</span>
                        <span id="pwwh-qnt-buttons">' .
                          $confirm . $abort .
                        '</span>
                      </div>';
      }
      else {
        /* Composing Quantity Input. */
        $id = $instance . ':' . $ui_facts['input']['quantity']['id'];
        $args = array('type' => 'hidden',
                      'id' => $id,
                      'value' => $qnt);
        $qnt_input = pwwh_lib_ui_form_input($args);

        /* Composing edit box. */
        $_edit_box = '<span id="pwwh-item-input">' .
                        $item_input .
                      '</span>
                      <span id="pwwh-loc-input">' .
                        $loc_input .
                      '</span>
                      <span id="pwwh-qnt-input">' .
                        $qnt_input .
                      '</span>';
      }
    }

    $_section = $_title .
               '<span class="purchase-meta">' .
                $_loc_chunk . $_qnt_chunk . $_edit_box .
               '</span>';
    $data['args']['section_hook'] = $_section;
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
 * @brief     Returns the content of purchase option box as HTML.
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
 * @paramkey{args show_qnt_ui}    Show qnt UI on true. @default{true}
 * @paramkey{args qnt_value}      The quantity value used to fill the UI.
 *                                @default{0}
 * @paramkey{args instance}       An instance identifier. @default{0}
 * @paramkey{args is_primary}     If true adds extra code. @default{true}
 * @paramkey{args echo}           Echoes on true. @default{true}
 *
 * @return    mixed the purchase option box as HTML or void.
 */
function pwwh_core_purchase_ui_metabox_add_item($post = null, $data = array()) {

  $args = $data['args'];
  $instance = pwwh_lib_utils_validate_array_field($args, 'instance', 0);
  $show_item_ui = pwwh_lib_utils_validate_array_field($args, 'show_item_ui', true);
  $item_id = pwwh_lib_utils_validate_array_field($args, 'item_id', null);
  $show_loc_ui = pwwh_lib_utils_validate_array_field($args, 'show_loc_ui', true);
  $loc_id = pwwh_lib_utils_validate_array_field($args, 'loc_id', null);
  $show_qnt_ui = pwwh_lib_utils_validate_array_field($args, 'show_qnt_ui', true);
  $qnt_value = pwwh_lib_utils_validate_array_field($args, 'qnt_value', null);
  $is_primary = pwwh_lib_utils_validate_array_field($args, 'is_primary', true);
  $echo = pwwh_lib_utils_validate_array_field($args, 'echo', true);

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }

  /* Getting UI Facts. */
  $ui_facts = pwwh_core_purchase_api_get_ui_facts();

  /* Generating Item box. */
  $item_box = '';
  if($show_item_ui) {

    /* Getting Item title to pre-fill the item box. */
    $item_title = '';
    if($item_id) {
      $item = get_post($item_id);
      if(get_post_type($item) === PWWH_CORE_ITEM) {
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

  /* Generating Quantity input box. */
  $qnt_box = '';
  if($show_qnt_ui) {
    $placeholder = __('Enter the Item amount to stock (Required)',
                      'piwi-warehouse');

    $id = $instance . ':' . $ui_facts['input']['quantity']['id'];
    $args = array('label' => __('Amount to stock', 'piwi-warehouse'),
                  'icon' => 'dashicons-archive',
                  'type' => 'number',
                  'id' => $id,
                  'classes' => $ui_facts['input']['quantity']['id'],
                  'placeholder' => $placeholder,
                  'value' => $qnt_value,
                  'step' => $ui_facts['input']['quantity']['rule']['step']);
    $qnt_box = pwwh_lib_ui_form_input($args);
  }

  if($is_primary) {
    /* Generating a description. */
    $description = __('Choose the Item you want to stock by name reporting the ' .
                      'amount to stock.', 'piwi-warehouse');
    /* Composing main box. */
    $main = '<div id="pwwh-main" class="pwwh-main">
              <span class="pwwh-description">' .
                $description .
              '</span>
              <div id="' . $ui_facts['box']['add_item']['id'] . '-primary"
                    class="primary">' .
                $item_box . $loc_box . $qnt_box .
              '</div>
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
              $delete_button . $item_box . $loc_box . $qnt_box .
            '</div>';
    $footer = '';
  }

  $output = $main . $footer;

  if($echo) {
    echo $output;
  }
  else {
    return $output;
  }
}
/** @} */