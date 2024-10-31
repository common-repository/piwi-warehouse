<?php
/**
 * @file      item/item-ui.php
 * @brief     User Interface for Item post type.
 *
 * @addtogroup PWWH_CORE_ITEM
 * @{
 */

/**
 * @brief     Returns the content of Records postbox as HTML.
 *
 * @param[in] mixed $post         A Post object or the Post ID
 * @param[in] boolean $echo       Echoes on true
 *
 * @return    mixed the Warehouse Status postbox as HTML or void.
 */
function pwwh_core_item_ui_metabox_records($post = null, $echo = true) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }

  $post_id = $post->ID;

  /* Generating records description. */
  $description = __('This box reports the current status of the Item in the ' .
                    'Warehouse.', 'piwi-warehouse');
  $_desc = '<span class="description">' . $description . '</span>';

  /* Generating records sections. */
  $_sections = '';

  /* Generating the Amount section. */
  {
    /* Section title. */
    $label = __('Item Total Amount' , 'piwi-warehouse');
    $_section_title = '<h2 class="title">' . $label . '</h2>';

    /* Section description. */
    $desc = sprintf(__('The <b>%s</b> is the quantity purchased over time. ' .
                       'Ideally this is the quantity of item stored in our ' .
                       'facilites if there were Item loss, donation or loan.',
                       'piwi-warehouse'), $label);
    $_section_desc = '<span class="description">' .
                        $desc .
                     '</span>';

    /* Section content. */
    $amount_value = pwwh_core_item_api_get_amount($post_id);
    $args = array('description' => $label,
                  'value' => $amount_value,
                  'icon' => 'dashicons-clipboard',
                  'class' => 'pwwh-amount');
    $_content_box = pwwh_lib_ui_admin_info_chunk($args, false);

    $_section_content = '<div class="content">' .
                          $_content_box .
                        '</div>';

    /* Assembling section. */
    $_sections .= '<section class="amount-record">' .
                    $_section_title . $_section_desc . $_section_content .
                  '</section>';
  }

  /* Generating the Total Availability section. */
  {
    /* Section title. */
    $label = __('Item Total Availability', 'piwi-warehouse');
    $_section_title = '<h2 class="title">' . $label . '</h2>';

    /* Section description. */
    $desc = sprintf(__('The <b>%s</b> is the quantity currently available ' .
                       'in our facilities. This quantity could be ' .
                       'distributed over multiple locations.',
                       'piwi-warehouse'), $label);
    $_section_desc = '<span class="description">' .
                        $desc .
                     '</span>';

    /* Section content. */
    $avails = pwwh_core_item_api_get_avails($post_id);
    $args = array('description' => $label,
                  'value' => $avails['0'],
                  'icon' => 'dashicons-chart-bar',
                  'class' => 'pwwh-tot-avail');
    $_content_box = pwwh_lib_ui_admin_info_chunk($args, false);

    $_section_content = '<div class="content">' .
                          $_content_box .
                        '</div>';

    /* Assembling section. */
    $_sections .= '<section class="avail-record">' .
                    $_section_title . $_section_desc . $_section_content .
                  '</section>';
  }

  /* Generating the Availability per Location section. */
  {
    /* Section title. */
    $label = __('Item Availability per Location', 'piwi-warehouse');
    $_section_title = '<h2 class="title">' . $label . '</h2>';

    /* Section description. */
    $desc = sprintf(__('The <b>%s</b> is the quantity currently available ' .
                       'in our locations.', 'piwi-warehouse'), $label);
    $_section_desc = '<span class="description">' .
                        $desc .
                     '</span>';

    /* Retrieving locations list. */
    $args = array('classes' => 'pwwh-info-list',
                  'depth' => 0,
                  'avail' => true);
    $list = pwwh_core_item_api_get_location_list($post, $args);

    if($list) {
      $_content_box = '<span class="pwwh-avail-loc">
                         <span class="pwwh-lib-icon dashicons-location"></span>' .
                         $list .
                      '</span>';
    }
    else {
      $_content_box =  __('This Item has no Availability per any Location.',
                   'piwi-warehouse');
    }

    $_section_content = '<div class="content">' .
                          $_content_box .
                        '</div>';

    /* Assembling section. */
    $_sections .= '<section class="avail-record">' .
                    $_section_title . $_section_desc . $_section_content .
                  '</section>';
  }

  /* Composing output. */
  $output = $_desc . $_sections;

  if($echo) {
    echo $output;
  }
  else {
    return $output;
  }
}

/**
 * @brief     Returns the content of item info as HTML.
 *
 * @param[in] mixed $post         A Post object or the Post ID
 * @param[in] array $data         An array of data.
 * @paramkey{args}                An array of parameters sent while adding
 *                                metabox.
 * @paramkey{args purchase}       Add the quick purchase on true.
 *                                @default{false}
 * @paramkey{args movement}       Add the quick movement on true.
 *                                @default{false}
 * @paramkey{args echo}           Echoes on true @default{true}.
 *
 * @return    mixed the item info as HTML or void.
 */
function pwwh_core_item_ui_metabox_quick_ops($post = null, $data = array()) {

  $args = $data['args'];
  $movement = pwwh_lib_utils_validate_array_field($args, 'movement', false);
  $purchase = pwwh_lib_utils_validate_array_field($args, 'purchase', false);
  $echo = pwwh_lib_utils_validate_array_field($args, 'echo', true);

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }

  $post_id = $post->ID;
  $item_name = $post->post_title;
  $output = '';
  if($purchase) {
    $url = pwwh_core_purchase_api_url_purchase_item($post_id);
    $label = sprintf(__('Purchase <strong>%s</strong>',
                        'piwi-warehouse'), $item_name);
    $args = array('value' => $label,
                  'link' => $url,
                  'icon' => 'dashicons-cart',
                  'class' => 'pwwh-quick-purchase');
    $output .= pwwh_lib_ui_admin_info_chunk($args, false);
  }

  if($movement) {
    $url = pwwh_core_movement_api_url_movement_item($post_id);
    $label = sprintf(__('Move <strong>%s</strong>',
                        'piwi-warehouse'), $item_name);
    $args = array('value' => $label,
                  'link' => $url,
                  'icon' => 'dashicons-migrate',
                  'class' => 'pwwh-quick-movement');
    $output .= pwwh_lib_ui_admin_info_chunk($args, false);
  }

  if($echo) {
    echo $output;
  }
  else {
    return $output;
  }
}

/**
 * @brief     Prints the info of an item as read only.
 *
 * @param[in] mixed $post         A Post object or the Post ID
 * @param[in] array $data         An array of data.
 * @paramkey{args}                An array of parameters sent while adding
 *                                metabox.
 * @paramkey{args show_title}     Shows the title name on true. @default{false}
 * @paramkey{args linked_title}   Title comes with link to admin page. @default{false}
 * @paramkey{args show_thumb}     Shows the thumbnail on true. @default{false}
 * @paramkey{args show_loc}       Shows the loaction on true. @default{true}
 * @paramkey{args show_type}      Shows the type on true. @default{true}
 * @paramkey{args show_avail}     Shows the availability on true. @default{true}
 * @paramkey{args show_amount}    Shows the amount on true. @default{true}
 * @paramkey{args meta_hook}      Use this hook to populate extra content
 *                                in the post meta. The expected value is
 *                                HTML. @default{''}
 * @paramkey{args pre_hook}       Use this post to populate extra content in
 *                                the pre-header area. The expected value is
 *                                HTML. @default{''}
 * @paramkey{args section_hook}   Use this post to populate extra content in
 *                                the section area. The expected value is
 *                                HTML or an array of ID => HTML. Each element
 *                                will be wrappered in a section tag.
 *                                @default{''}
 * @paramkey{args footer_hook}    Use this post to populate extra content in
 *                                the footer area. The expected value is
 *                                HTML. @default{''}
 * @paramkey{args echo}           Echoes on true. @default{true}
 *
 * @return    mixed the item info box as HTML or void.
 */
function pwwh_core_item_ui_metabox_item_summary($post = null, $data = array()) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }
  $post_type = get_post_type($post);

  if($post_type === PWWH_CORE_ITEM) {

    $args = $data['args'];
    $show_title = pwwh_lib_utils_validate_array_field($args, 'show_title', false);
    $linked_title = pwwh_lib_utils_validate_array_field($args, 'linked_title', false);
    $show_thumb = pwwh_lib_utils_validate_array_field($args, 'show_thumb', false);
    $show_loc = pwwh_lib_utils_validate_array_field($args, 'show_loc', true);
    $show_type = pwwh_lib_utils_validate_array_field($args, 'show_type', true);
    $show_avail = pwwh_lib_utils_validate_array_field($args, 'show_avail', true);
    $show_amount = pwwh_lib_utils_validate_array_field($args, 'show_amount', true);
    $pre_hook = pwwh_lib_utils_validate_array_field($args, 'pre_hook', '');
    $meta_hook = pwwh_lib_utils_validate_array_field($args, 'meta_hook', '');
    $section_hook = pwwh_lib_utils_validate_array_field($args, 'section_hook', '');
    $footer_hook = pwwh_lib_utils_validate_array_field($args, 'footer_hook', '');
    $echo = pwwh_lib_utils_validate_array_field($args, 'echo', true);
    $post_id = $post->ID;

    /* Get UI facts. */
    $ui_facts = pwwh_core_item_api_get_ui_facts();

    $_classes = array('item-' . $post_id,
                      'status-' . get_post_status($post_id),
                      $ui_facts['box']['item_summary']['id']);

    /* Generating Image box. */
    if($show_thumb) {
      array_push($_classes, 'has-thumbnail');
      $thumbnail_id = get_post_thumbnail_id($post_id);
      $thumbnail_url = wp_get_attachment_url($thumbnail_id);
      if($thumbnail_url) {
        $_thumbnail = '<div class="thumbnail">
                        <img src="' . $thumbnail_url . '"
                             title="' . get_the_title($post_id) . '"/>
                      </div>';
      }
      else {
        $_thumbnail = '';
      }
    }

    /* Generating Item title. */
    if($show_title) {
      array_push($_classes, 'has-title');
      if($linked_title) {
        array_push($_classes, 'has-linked-title');
        $args = array('post' => $post_id,
                      'action' => 'edit');
        $url = pwwh_core_api_admin_url_post($args);
      }
      else {
        $url = false;
      }

      if(get_post_status($post_id) != 'publish') {
        $title = get_the_title($post_id) . __(' (Disabled)', 'piwi-warehouse');
      }
      else {
        $title = get_the_title($post_id);
      }

      $args = array('value' => $title,
                    'link' => $url,
                    'target' => '_blank',
                    'class' => 'item-title');
      $_item_title = pwwh_lib_ui_admin_info_chunk($args, false);
    }
    else {
      $_item_title = '';
    }

    /* Meta info generation .*/
    {
      $_metas = '';

      /* Generating a Location info. */
      if($show_loc) {
        $args = array('linked' => false, 'echo' => false);
        $loc = pwwh_core_ui_tax_list(PWWH_CORE_ITEM_LOCATION, $post_id, $args);

        $args = array('description' => __('Locations' , 'piwi-warehouse'),
                      'value' => $loc,
                      'icon' => 'dashicons-location',
                      'class' => PWWH_CORE_ITEM_LOCATION);
        $_metas .= pwwh_lib_ui_admin_info_chunk($args, false);
      }

      /* Generating a Type info. */
      if($show_type) {
        $args = array('linked' => false, 'echo' => false);
        $type = pwwh_core_ui_tax_list(PWWH_CORE_ITEM_TYPE, $post_id, $args);

        $args = array('description' => __('Types' , 'piwi-warehouse'),
                      'value' => $type,
                      'icon' => 'dashicons-tag',
                      'class' => PWWH_CORE_ITEM_TYPE);
        $_metas .= pwwh_lib_ui_admin_info_chunk($args, false);
      }

      /* Generating a Availability info. */
      if($show_avail) {
        $avail = pwwh_core_item_api_get_avail($post_id);

        $args = array('description' => __('Availability' , 'piwi-warehouse'),
                      'value' => $avail,
                      'icon' => 'dashicons-chart-bar',
                      'class' => 'pwwh-avail');
        $_metas .= pwwh_lib_ui_admin_info_chunk($args, false);
      }

      /* Generating a Amount info. */
      if($show_avail) {
        $amount = pwwh_core_item_api_get_amount($post_id);

        $args = array('description' => __('Amount' , 'piwi-warehouse'),
                      'value' => $amount,
                      'icon' => 'dashicons-clipboard',
                      'class' => 'pwwh-amount');
        $_metas .= pwwh_lib_ui_admin_info_chunk($args, false);
      }

      /* Adding extra metas. */
      if(is_string($meta_hook)) {
        $_metas .= $meta_hook;
      }

      /* Wrapping metas. */
      $_metas = '<div class="meta-info">' . $_metas . '</div>';
    }

    /* Generating Pre-Header. */
    if(is_string($pre_hook)) {
      $_pre = $pre_hook;
    }
    else {
      $_pre = '';
    }

    /* Generating Sections. */
    if($section_hook && is_array($section_hook)) {
      $_sections = '';
      foreach($section_hook as $id => $section)  {
        if (is_string($section)) {
          $_sections .= '<section id="section-' . $id . '">' .
                          $section .
                        '</section>';
        }
      }
    }
    else if($section_hook && is_string($section_hook)) {
      $_sections = '<section>' . $section_hook .'</section>';
    }
    else {
      $_sections = '';
    }

    /* Generating Footer. */
    if(is_string($footer_hook)) {
      $_footer = '<footer>' . $footer_hook .'</footer>';
    }
    else {
      $_footer = '';
    }

    /* Generating Info box. */
    $_text = '<div class="content">' .
                $_item_title . $_metas .
              '</div>';

    $_classes = pwwh_lib_ui_form_attribute('class', implode(' ', $_classes));
    $output = '<article ' . $_classes . '>' .
                $_pre .
                '<header>' .
                  $_thumbnail . $_text .
                '</header>' .
                $_sections .
                $_footer .
              '</article>';
  }
  else {
    $msg = sprintf('Unexpected post type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $output = '';
  }

  if($echo) {
    echo $output;
  }
  else {
    return $output;
  }
}
/** @} */