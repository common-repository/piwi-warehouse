<?php
/**
 * @file        admin/consistency/consistency-ui.php
 * @brief       This file contains all the code related to Consistency checker
 *              page UI.
 *
 * @ingroup     PWWH_CONSISTENCY
 * @{
 */

/* Query values. */
define('PWWH_ADMIN_CONSISTENCY_QUERY_ITEM', 'item_id');

/**
 * @brief     Creates the consistency flexbox of an Item.
 *
 * @param[in] int $item_id        The item ID.
 *
 * @return    The flexbox inner.
 */
function pwwh_admin_consistency_ui_item_operations($item_id) {

  /* Initializing accumulators. */
  $computed_avail = 0;
  $computed_amount = 0;

  $entries = array();

  /* Computing purchase related accounts. */
  $purchases = pwwh_core_purchase_api_get_by_item($item_id);
  foreach($purchases as $purchase) {

    $qnts = pwwh_core_purchase_api_get_quantities_by_item($purchase, $item_id);

    $total_qnt = 0;
    foreach($qnts as $loc_id => $qnt) {
      $computed_avail += floatval($qnt);
      $computed_amount += floatval($qnt);
      $total_qnt += floatval($qnt);
    }

    $entry = array('date' => $purchase->post_date,
                   'id' => $purchase->ID,
                   'type' => __('Purchase', 'piwi-warehouse'),
                   'purchased' => $total_qnt);
    array_push($entries, $entry);
  }

  /* Computing movement related accounts. */
  $movements = pwwh_core_movement_api_get_by_item($item_id);
  foreach($movements as $movement) {

    $qnts = pwwh_core_movement_api_get_quantities_by_item($movement, $item_id);

    $total_moved = 0;
    $total_returned = 0;
    $total_donated = 0;
    $total_lost = 0;
    $total_lent = 0;

    foreach($qnts as $loc_id => $qnt) {
      $computed_avail += floatval($qnt['returned']);
      $computed_avail -= floatval($qnt['moved']);
      $total_moved += floatval($qnt['moved']);
      $total_returned += floatval($qnt['returned']);
      $total_donated += floatval($qnt['donated']);
      $total_lost += floatval($qnt['lost']);
      $total_lent += floatval($qnt['lent']);
    }

    $entry = array('date' => $movement->post_date,
                   'id' => $movement->ID,
                   'type' => __('Movement', 'piwi-warehouse'),
                   'moved' => $total_moved,
                   'returned' => $total_returned,
                   'donated' => $total_donated,
                   'lost' => $total_lost,
                   'lent' => $total_lent);
    array_push($entries, $entry);
  }

  /* Composing table Head. */
  $columns = array('type' => __('Type', 'piwi-warehouse'),
                   'id' => __('ID', 'piwi-warehouse'),
                   'date' => __('Date', 'piwi-warehouse'),
                   'purchased' => __('Total Purchased', 'piwi-warehouse'),
                   'moved' => __('Total Moved', 'piwi-warehouse'),
                   'returned' => __('Total Returned', 'piwi-warehouse'),
                   'donated' => __('Total Donated', 'piwi-warehouse'),
                   'lost' => __('Total Lost', 'piwi-warehouse'),
                   'lent' => __('Total Lent', 'piwi-warehouse'));
  $thead = '';
  foreach($columns as $id => $column) {
    $thead .= '<th scope="col" id="' . $id . '"
                   class="column-' . $id . '">' .
                $column .
              '</th>';
  }
  $_thead = '<thead><tr>' . $thead . '</tr></thead>';

  /* Composing table Body. */
  $_trows = '';
  foreach($entries as $entry) {
    $_trows .= '<tr id="' . $entry['id'] . '">';
    foreach($columns as $id => $column) {
      if(isset($entry[$id]))
        $value = $entry[$id];
      else
        $value = '&mdash;';
      $_trows .= '<td class="' . $id . ' column-' . $id . '"
                      data-colname="' . $column . '">'.
                    $value .
                  '</td>';
    }
  }
  $_tbody = '<tbody id="the-list">' .
              $_trows .
            '</tbody>';

  /* Composing table. */
  $_table = '<table class="wp-list-table widefat fixed striped posts">' .
              $_thead . $_tbody .
            '</table>';

  /* Generating Availability info. */
  $args = array('description' => __('Computed Availability' , 'piwi-warehouse'),
                'value' => $computed_avail,
                'icon' => 'dashicons-chart-bar',
                'class' => 'pwwh-avail');
  $_avail = pwwh_lib_ui_admin_info_chunk($args, false);

  /* Generating Amount info. */
  $args = array('description' => __('Computed Amount' , 'piwi-warehouse'),
                'value' => $computed_amount,
                'icon' => 'dashicons-clipboard',
                'class' => 'pwwh-amount');
  $_amount = pwwh_lib_ui_admin_info_chunk($args, false);

  /* Generating Result. */
  $amount = floatval(pwwh_core_item_api_get_amount($item_id));
  $avail = floatval(pwwh_core_item_api_get_avail($item_id));
  if(($amount == $computed_amount) && ($avail == $computed_avail)) {
    $args = array('description' => __('Consistency check', 'piwi-warehouse'),
                  'value' => __('success', 'piwi-warehouse'),
                  'icon' => 'dashicons-yes',
                  'class' => 'pwwh-consistency-result pwwh-success');
  }
  else {
    $args = array('description' => __('Consistency check', 'piwi-warehouse'),
                  'value' => __('fail', 'piwi-warehouse'),
                  'icon' => 'dashicons-no',
                  'class' => 'pwwh-consistency-result pwwh-fail');
  }
  $_result = pwwh_lib_ui_admin_info_chunk($args, false);

  /* Composing additional info. */
  $_additional = '<span class="pwwh-footer">' .
                    '<span class="pwwh-amount-avail">' .
                      $_amount . $_avail .
                    '</span>
                    <span class="pwwh-result">' .
                    $_result .
                 '</span>';

  /* Composing output. */
  $output = $_table . $_additional;
  return $output;
}

/**
 * @brief     Creates the consistency flexbox of an Item.
 *
 * @param[in] int $item_id        The item ID.
 *
 * @return    The flexbox inner.
 */
function pwwh_admin_page_consistency_item_flexbox($item_id) {

  /* Initializing accumulators. */
  $computed_avail = 0;
  $computed_amount = 0;

  /* Computing purchase related accounts. */
  $purchases = pwwh_core_purchase_api_get_by_item($item_id);
  foreach($purchases as $purchase) {
    $qnts = pwwh_core_purchase_api_get_quantities_by_item($purchase, $item_id);

    foreach($qnts as $loc_id => $qnt) {
      $computed_avail += floatval($qnt);
      $computed_amount += floatval($qnt);
    }
  }

  /* Computing movement related accounts. */
  $movements = pwwh_core_movement_api_get_by_item($item_id);
  foreach($movements as $movement) {
    $qnts = pwwh_core_movement_api_get_quantities_by_item($movement, $item_id);
    foreach($qnts as $loc_id => $qnt) {
      $computed_avail += floatval($qnt['returned']);
      $computed_avail -= floatval($qnt['moved']);
    }
  }

  /* Generating Amount info. */
  $amount = floatval(pwwh_core_item_api_get_amount($item_id));
  $value = sprintf(__('Computed %s, Stored %s'), $computed_amount, $amount);
  $args = array('description' => __('Item Total Amount' , 'piwi-warehouse'),
                'value' => $value,
                'icon' => 'dashicons-clipboard',
                'class' => 'pwwh-amount');
  $_amount_box = pwwh_lib_ui_admin_info_chunk($args, false);

  /* Generating Availability info. */
  $avail = floatval(pwwh_core_item_api_get_avail($item_id));
  $value = sprintf(__('Computed %s, Stored %s'), $computed_avail, $avail);
  $args = array('description' => __('Item Availability' , 'piwi-warehouse'),
                'value' => $value,
                'icon' => 'dashicons-chart-bar',
                'class' => 'pwwh-avail');
  $_avail_box = pwwh_lib_ui_admin_info_chunk($args, false);

  /* Composing main box. */
  $_content = '<span class="pwwh-content">' .
                $_amount_box . $_avail_box .
              '</span>';

  /* Composing More detail link. */
  $queries = array(PWWH_ADMIN_CONSISTENCY_QUERY_ITEM => $item_id);
  $url = pwwh_admin_common_get_admin_url(PWWH_ADMIN_CONSISTENCY_PAGE_ID,
                                            $queries);
  $label = __('More details', 'piwi-warehouse');
  $_more = '<span class="pwwh-consistency-more">
              <a href="' . $url . '" target="_blank"
                 title="' . $label . '">' .
                $label .
              '</a>
            </span>';

  /* Composing Result. */
  if(($amount == $computed_amount) && ($avail == $computed_avail)) {
    $args = array('value' => __('success', 'piwi-warehouse'),
                  'icon' => 'dashicons-yes',
                  'class' => 'pwwh-consistency-result pwwh-success');
  }
  else {
    $args = array('value' => __('fail', 'piwi-warehouse'),
                  'icon' => 'dashicons-no',
                  'class' => 'pwwh-consistency-result pwwh-fail');
  }
  $_result = pwwh_lib_ui_admin_info_chunk($args, false);

  /* Composing footer box. */
  $_footer = '<span class="pwwh-footer">' .
                $_more . $_result .
             '</span>';

  /* Composing output. */
  $output = $_content . $_footer;
  return $output;
}

/**
 * @brief     Displays the movement consistency movements tool page.
 *
 * @return    void.
 */
function pwwh_admin_consistency_ui_page() {

  /* Checking whereas is the main page or a detail page. */
  if(isset($_GET[PWWH_ADMIN_CONSISTENCY_QUERY_ITEM])) {
    $item_id = $_GET[PWWH_ADMIN_CONSISTENCY_QUERY_ITEM];
    if(get_post_type($item_id) == PWWH_CORE_ITEM) {
      $item_title = get_the_title($item_id);

      /* Generating title of this page. */
      $label = sprintf(__('Details about consistency of %s', 'piwi-warehouse'),
                       $item_title);
      $_title = pwwh_lib_ui_admin_page_title($label, false);
      $flexbox_id = PWWH_ADMIN_CONSISTENCY_PAGE_ID . '_item_page';

      /* Adding general info. */
      $id = 'pwwh-item';
      $label = sprintf(__('General info about % s', 'piwi-warehouse'),
                       $item_title);
      $call = 'pwwh_core_item_ui_metabox_item_summary';
      $args = array('echo' => false,
                    'show_link' => true,
                    'show_loc' => true,
                    'show_type' => true,
                    'show_thumb' => true,
                    'avail' => pwwh_core_item_api_get_avail($item_id),
                    'amount' => pwwh_core_item_api_get_amount($item_id));
      /* Encapsulating data to be compliant with postbox callbacks. */
      $args = array('args' => $args);
      pwwh_lib_ui_flexboxes_add_flexbox($id, $label, $call,
                                        array($item_id, $args), $flexbox_id);

      /* Adding details table flexbox. */
      $id = 'pwwh-table';
      $label = sprintf(__('Purchases and Movements of %s',
                          'piwi-warehouse'), $item_title);
      $call = 'pwwh_admin_consistency_ui_item_operations';
      pwwh_lib_ui_flexboxes_add_flexbox($id, $label, $call, $item_id, $flexbox_id);

      /* generating flexbox area. */
      $_inner = pwwh_lib_ui_flexboxes_do_flexbox_area($flexbox_id, false);

      /* Composing output and echoing. */
      $output = '<span id="pwwh_concistency_checker">' .
                  $_title . $_inner .
                '</span>';
    }
    else {
      $url = pwwh_admin_common_get_admin_url(PWWH_ADMIN_SLUG_PAGE_MAIN);
      wp_redirect($url);
      exit;
    }
  }
  else {
    /* Generating title of this page. */
    $label = __('Item Consistency Checker', 'piwi-warehouse');
    $_title = pwwh_lib_ui_admin_page_title($label, false);

    /* Generating description. */
    $desc = __('This tool performs Item accounts re-count to verify if they are ' .
               'consistent. This is a testing tool used during development.',
               'piwi-warehouse');
    $_description = '<span class="pwwh-page-description">' . $desc . '</span>';

    /* Searching all the items. */
    $args = array('post_type' => PWWH_CORE_ITEM,
                  'post_status' => array('publish'),
                  'nopaging' => true);
    $query = new WP_Query($args);

    /* Checking whereas there are some results. */
    if($query->have_posts())
      $items = $query->get_posts();
    else
      $items = array();

    foreach($items as $item) {
      $item_id = $item->ID;
      $item_title = get_the_title($item_id);

      $id = PWWH_ADMIN_CONSISTENCY_PAGE_ID . '_' . $item_id;
      pwwh_lib_ui_flexboxes_add_flexbox($id, $item_title,
                                        'pwwh_admin_page_consistency_item_flexbox',
                                        $item_id, PWWH_ADMIN_CONSISTENCY_PAGE_ID);
    }

    wp_reset_postdata();

    $_inner = pwwh_lib_ui_flexboxes_do_flexbox_area(PWWH_ADMIN_CONSISTENCY_PAGE_ID,
                                                false);
    /* Composing output. */
    $output = '<span id="pwwh_concistency_checker">' .
                $_title . $_description . $_inner .
              '</span>';
  }

  echo $output;
}