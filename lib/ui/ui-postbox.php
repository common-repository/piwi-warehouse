<?php
/**
 * @file      ui/ui-flexboxes.php
 * @brief     This file contains all the functions to manage flexbox mechanism.
 *
 * @addtogroup PWWH_LIB_UI_FLEXBOXES
 * @{
 */

/**
 * @brief     Returns a custom content for submit postbox as HTML.
 *
 * @param[in] mixed $post         A Post object or the Post ID.
 * @param[in] array $data         An array of data.
 * @paramkey{args}                An array of parameters sent while adding
 *                                metabox.
 * @paramkey{args status_show}    Show status box on true. @default{true}
 * @paramkey{args status_edit}    Adds UI to edit status. @default{false}
 * @paramkey{args status_list}    An array of statuses [slug] => [label] which
 *                                will be used to populate a select box.
 *                                @default{empty}
 * @paramkey{args date_show}      Show date box on true. @default{true}
 * @paramkey{args date_edit}      Adds UI to edit date. @default{false}
 * @paramkey{args delete_show}    Show delete box on true. @default{false}
 * @paramkey{args delete_forced}  Delete button forces deletion on true.
 *                                @default{false}
 * @paramkey{args delete_label}   Label of delete button. Default depends on
 *                                delete_forced.
 *                                @default{'Move to Trash' or 'Delete permanently'}
 * @paramkey{args submit_show}    Show submit button. @default{false}
 * @paramkey{args submit_label}   Label of submit button.
 * @paramkey{args submit_status}  The status which will be reached after submit.
 *                                This status can be changed at run time by the
 *                                status select box if enabled.
 *                                @default{current_status}
 * @paramkey{args echo}           Echoes on true @default{true}.
 *
 * @return    string the content as HTML.
 */
function pwwh_ui_postbox_submit($post = null, $data = array()) {

  /* Preparing arguments required to generate HTML. */
  {
    $args = $data['args'];

    $status_show = pwwh_lib_utils_validate_array_field($args, 'status_show', true);
    $status_edit = pwwh_lib_utils_validate_array_field($args, 'status_edit', false);
    $status_list = pwwh_lib_utils_validate_array_field($args, 'status_list', array());
    if(!is_array($status_list)) {
      $status_list = array();
    }
    $date_show = pwwh_lib_utils_validate_array_field($args, 'date_show', true);
    $date_edit = pwwh_lib_utils_validate_array_field($args, 'date_edit', false);
    $delete_show = pwwh_lib_utils_validate_array_field($args, 'delete_show', false);
    $delete_forced = pwwh_lib_utils_validate_array_field($args, 'delete_forced',
                                                         false);
    $submit_show = pwwh_lib_utils_validate_array_field($args, 'submit_show', true);
    $echo = pwwh_lib_utils_validate_array_field($args, 'echo', true);

      /* Delete label depends on forced. */
    if($delete_forced) {
      $def_del_lab = __('Delete Permanently', 'piwi-library');

    }
    else {
      $def_del_lab = __('Move to Trash', 'piwi-library');
    }

    $delete_label = pwwh_lib_utils_validate_array_field($args, 'delete_label',
                                                    $def_del_lab);

    /* Getting information from Post. */
    if(!is_a($post, 'WP_Post')) {
      $post = get_post($post);
    }
    $post_id = $post->ID;
    $cur_status = $post->post_status;

    /* Default submit status is current. */
    $submit_status = esc_attr(pwwh_lib_utils_validate_array_field($args,
                                                                  'submit_status',
                                                                   $cur_status));
    $submit_label = esc_attr(pwwh_lib_utils_validate_array_field($args,
                                                                 'submit_label',
                                                                 ''));

    /* Submit label depends on current status and next one. */
    if(!$submit_label) {
      if($submit_status == 'publish') {
        if($submit_status != $cur_status) {
          $submit_label = __('Publish', 'piwi-library');
        }
        else {
          $submit_label = __('Update', 'piwi-library');
        }
      }
      else {
        if($submit_status != $cur_status) {
          if(isset($status_list[$submit_status])) {
            $_label = $status_list[$submit_status];
          }
          else {
            $_label = ucwords($submit_status);
          }
          $submit_label = sprintf(__('Save as %s', 'piwi-library'), $_label);
        }
        else {
          $submit_label = __('Save', 'piwi-library');
        }
      }
    }

    /* Additional info required to show Status box. */
    if(isset($status_list[$cur_status])) {
      /* Trying to get status label from status list. */
      $cur_status_label = $status_list[$cur_status];
    }
    else {
      $cur_status_label = ucfirst($cur_status);
    }

    /* Additional info required to show date box. */
    $cur_mm = get_the_date('m', $post_id);
    $cur_jj = get_the_date('d', $post_id);
    $cur_aa = get_the_date('Y', $post_id);
    $cur_hh = get_the_date('H', $post_id);
    $cur_mn = get_the_date('i', $post_id);
    $cur_ss = get_the_date('m', $post_id);

    $format = 'M j, Y @ H:i';
    $post_date = get_the_date($format, $post);

    $months = array('01' => __('Jan', 'piwi-library'),
                    '02' => __('Feb', 'piwi-library'),
                    '03' => __('Mar', 'piwi-library'),
                    '04' => __('Apr', 'piwi-library'),
                    '05' => __('May', 'piwi-library'),
                    '06' => __('Jun', 'piwi-library'),
                    '07' => __('Jul', 'piwi-library'),
                    '08' => __('Aug', 'piwi-library'),
                    '09' => __('Sep', 'piwi-library'),
                    '10' => __('Oct', 'piwi-library'),
                    '11' => __('Nov', 'piwi-library'),
                    '12' => __('Dec', 'piwi-library'));
  }

  /* Common parts. */
  $clearer = '<div class="clear"></div>';

  /* Minor publishing action are printed only if are not empty. */

  if($status_show) {
    $args = array('description' => __('Status', 'piwi-library'),
                  'value' => $cur_status_label,
                  'icon' => 'dashicons-post-status',
                  'id' => 'pwwh-status');
    if($status_edit && count($status_list)) {
      $args['link'] = '#';
      $args['class'] = 'pwwh-status-edit';
    }
    $status_info = pwwh_lib_ui_admin_info_chunk($args, false);

    if($status_edit && count($status_list)) {
      /* Current status input. */
      $args = array('type' => 'hidden',
                    'id' => 'hidden_post_status',
                    'value' => $cur_status);
      $cur_status_input = pwwh_lib_ui_form_input($args);

      /* Status select box. */
      $args = array('description' => __('Status', 'piwi-library'),
                    'id' => 'post_status',
                    'classes' => 'pwwh-edit-status',
                    'label' => __('Set status', 'piwi-library'),
                    'label_classes' => 'screen-reader-text',
                    'value' => $submit_status,
                    'data' => $status_list);
      $status_input = pwwh_lib_ui_form_select($args);

      /* Status confirm button. */
      $args = array('id' => 'pwwh-status-confirm',
                    'type' => 'button',
                    'classes' => 'pwwh-lib-button hide-if-no-js',
                    'value' => esc_attr('confirm'),
                    'label' => __('Ok', 'piwi-library'));
      $status_confirm_btn = pwwh_lib_ui_form_button($args);

      /* Status cancel button. */
      $args = array('id' => 'pwwh-status-abort',
                    'type' => 'button',
                    'classes' => 'pwwh-lib-button hide-if-no-js',
                    'value' => esc_attr('abort'),
                    'label' => __('Cancel', 'piwi-library'));
      $status_cancel_btn = pwwh_lib_ui_form_button($args);

    }
    else {
      /* Current status input. */
      $args = array('type' => 'hidden',
                    'id' => 'hidden_post_status',
                    'value' => $cur_status);
      $cur_status_input = pwwh_lib_ui_form_input($args);

      /* Submit status input. */
      $args = array('type' => 'hidden',
                    'id' => 'post_status',
                    'value' => $submit_status);
      $status_input = pwwh_lib_ui_form_input($args);

      $status_confirm_btn = '';
      $status_cancel_btn = '';
    }
  }
  else {
    /* Current status input. */
    $args = array('type' => 'hidden',
                  'id' => 'hidden_post_status',
                  'value' => $cur_status);
    $cur_status_input = pwwh_lib_ui_form_input($args);

    /* Submit status input. */
    $args = array('type' => 'hidden',
                  'id' => 'post_status',
                  'value' => $submit_status);
    $status_input = pwwh_lib_ui_form_input($args);

    $status_confirm_btn = '';
    $status_cancel_btn = '';
  }
  /* Composing status fieldset. */
  $status_fieldset  = '<div id="post-status-fieldset"
                            class="hide-if-js">' .
                        $cur_status_input . $status_input .
                        $status_confirm_btn . $status_cancel_btn .
                      '</div>';

  /* Composing status box. */
  $status_box = $status_info . $status_fieldset;

  if($date_show) {
    /* Generating visual date box. */
    $args = array('description' => __('Published' , 'piwi-library'),
                  'value' => $post_date,
                  'icon' => 'dashicons-calendar',
                  'id' => 'pwwh-date');
    if($date_edit) {
      $args['link'] = '#';
      $args['class'] = 'pwwh-date-edit';
    }
    $date_info = pwwh_lib_ui_admin_info_chunk($args, false);

    /* Generating hidden input to store current visual date. */
    $args = array('type' => 'hidden',
                  'id' => 'cur_post_date',
                  'value' => $post_date);
    $date_info .= pwwh_lib_ui_form_input($args, false);
  }
  else {
    $date_info = '';
  }

  if($date_show && $date_edit) {
    /* Current date input. */
    $args = array('type' => 'hidden',
                  'id' => 'date_format',
                  'value' => $format);
    $cur_date_input = pwwh_lib_ui_form_input($args);
    $args = array('type' => 'hidden',
                  'id' => 'ss',
                  'value' => $cur_ss);
    $cur_date_input .= pwwh_lib_ui_form_input($args);
    $args = array('type' => 'hidden',
                  'id' => 'hidden_mm',
                  'value' => $cur_mm);
    $cur_date_input .= pwwh_lib_ui_form_input($args);
    $args = array('type' => 'hidden',
                  'id' => 'cur_mm',
                  'value' => $cur_mm);
    $cur_date_input .= pwwh_lib_ui_form_input($args);
    $args = array('type' => 'hidden',
                  'id' => 'hidden_jj',
                  'value' => $cur_jj);
    $cur_date_input .= pwwh_lib_ui_form_input($args);
    $args = array('type' => 'hidden',
                  'id' => 'cur_jj',
                  'value' => $cur_jj);
    $cur_date_input .= pwwh_lib_ui_form_input($args);
    $args = array('type' => 'hidden',
                  'id' => 'hidden_aa',
                  'value' => $cur_aa);
    $cur_date_input .= pwwh_lib_ui_form_input($args);
    $args = array('type' => 'hidden',
                  'id' => 'cur_aa',
                  'value' => $cur_aa);
    $cur_date_input .= pwwh_lib_ui_form_input($args);
    $args = array('type' => 'hidden',
                  'id' => 'hidden_hh',
                  'value' => $cur_hh);
    $cur_date_input .= pwwh_lib_ui_form_input($args);
    $args = array('type' => 'hidden',
                  'id' => 'cur_hh',
                  'value' => $cur_hh);
    $cur_date_input .= pwwh_lib_ui_form_input($args);
    $args = array('type' => 'hidden',
                  'id' => 'hidden_mn',
                  'value' => $cur_mn);
    $cur_date_input .= pwwh_lib_ui_form_input($args);
    $args = array('type' => 'hidden',
                  'id' => 'cur_mn',
                  'value' => $cur_mn);
    $cur_date_input .= pwwh_lib_ui_form_input($args);

    /* Date input box. */
    $date_input = '<div id="timestamp-wrap">';
    $args = array('id' => 'mm',
                  'classes' => 'pwwh-edit-date',
                  'label' => __('Month', 'piwi-library'),
                  'label_classes' => 'screen-reader-text',
                  'value' => $cur_mm,
                  'data' => $months);
    $date_input .= pwwh_lib_ui_form_select($args);
    $args = array('type' => 'text',
                  'id' => 'jj',
                  'size' => 2,
                  'maxlenght' => 2,
                  'value' => $cur_jj);
    $date_input .= pwwh_lib_ui_form_input($args);
    $date_input .= '<span class="pwwh-separator">, </span>';
    $args = array('type' => 'text',
                  'id' => 'aa',
                  'size' => 4,
                  'maxlenght' => 4,
                  'value' => $cur_aa);
    $date_input .= pwwh_lib_ui_form_input($args);
    $date_input .= '<span class="pwwh-separator"> @ </span>';
    $args = array('type' => 'text',
                  'id' => 'hh',
                  'size' => 2,
                  'maxlenght' => 2,
                  'value' => $cur_hh);
    $date_input .= pwwh_lib_ui_form_input($args);
    $date_input .= '<span class="pwwh-separator">:</span>';
    $args = array('type' => 'text',
                  'id' => 'mn',
                  'size' => 2,
                  'maxlenght' => 2,
                  'value' => $cur_mn);
    $date_input .= pwwh_lib_ui_form_input($args);
    $date_input .= '</div>';

    /* Date confirm button. */
    $args = array('id' => 'pwwh-date-confirm',
                  'type' => 'button',
                  'classes' => 'pwwh-lib-button hide-if-no-js',
                  'value' => esc_attr('confirm'),
                  'label' => __('Ok', 'piwi-library'));
    $date_confirm_btn = pwwh_lib_ui_form_button($args);

    /* Date cancel button. */
    $args = array('id' => 'pwwh-date-abort',
                  'type' => 'button',
                  'classes' => 'pwwh-lib-button hide-if-no-js',
                  'value' => esc_attr('abort'),
                  'label' => __('Cancel', 'piwi-library'));
    $date_cancel_btn = pwwh_lib_ui_form_button($args);

    /* Composing date fieldset. */
    $date_fieldset  = '<fieldset id="post-date-fieldset" class="hide-if-js">' .
                        $cur_date_input . $date_input .
                        $date_confirm_btn . $date_cancel_btn .
                      '</fieldset>';
  }
  else {
    /* Date input box. */
    $date_input = '<div id="timestamp-wrap">';
    $args = array('type' => 'hidden',
                  'id' => 'ss',
                  'value' => $cur_ss);
    $date_input .= pwwh_lib_ui_form_input($args);
    $args = array('type' => 'hidden',
                  'id' => 'mm',
                  'value' => $cur_mm);
    $date_input .= pwwh_lib_ui_form_input($args);
    $args = array('type' => 'hidden',
                  'id' => 'jj',
                  'value' => $cur_jj);
    $date_input .= pwwh_lib_ui_form_input($args);
    $args = array('type' => 'hidden',
                  'id' => 'aa',
                  'value' => $cur_aa);
    $date_input .= pwwh_lib_ui_form_input($args);
    $args = array('type' => 'hidden',
                  'id' => 'hh',
                  'value' => $cur_hh);
    $date_input .= pwwh_lib_ui_form_input($args);
    $args = array('type' => 'hidden',
                  'id' => 'mn',
                  'value' => $cur_mn);
    $date_input .= pwwh_lib_ui_form_input($args);
    $date_input .= '</div>';

    /* Composing date fieldset. */
    $date_fieldset  = '<fieldset id="post-date-fieldset" class="hide-if-js">' .
                          $date_input .
                      '</fieldset>';
  }

  /* Composing date box. */
  $date_box = $date_info . $date_fieldset;

  $misc_publishing = '<div id="misc-publishing-actions">' .
                        $status_box .
                        $date_box .
                     '</div>';
  $minor = '<div id="minor-publishing">' .
               $misc_publishing .
               $clearer .
           '</div>';

  /* Major publishing action are printed only if are not empty. */
  if($delete_show || $submit_show) {

    ob_start();
    do_action('post_submitbox_start', $post);
    $submit_box_action = ob_get_clean();

    /* Creating delete action. */
    if((current_user_can("delete_post", $post_id)) && $delete_show) {
      $url = get_delete_post_link($post_id, null, $delete_forced);
      $delete_action = '<div id="delete-action">
                          <a class="submitdelete deletion"
                             href="' . $url . '">' .
                            $delete_label .
                          '</a>
                        </div>';
    }
    else {
      $delete_action = '';
    }

    /* Creating publishing action. */
    if((current_user_can("publish_posts", $post_id)) && $submit_show) {
      $args = array('id' => 'original_publish',
                    'type' => 'hidden',
                    'value' => $submit_label);
      $submit = pwwh_lib_ui_form_input($args);

      $args = array('type' => 'submit',
                    'classes' => 'pwwh-lib-button pwwh-primary',
                    'value' => esc_attr($submit_label),
                    'label' => $submit_label);
      if($submit_status == 'publish' && $cur_status != 'publish') {
        $args['id'] = 'publish';
      }
      else if($submit_status == 'publish' && $cur_status == 'publish') {
        $args['id'] = 'publish';
        $args['name'] = 'save';
      }
      else {
        $args['id'] = 'save-post';
        $args['name'] = 'save';
      }
      $submit .= pwwh_lib_ui_form_button($args);

      $publish_action = '<div id="publishing-action">' .
                          $submit .
                        '</div>';
    }
    else {
      $publish_action = '';
    }

    $major = '<div id="major-publishing-actions">' .
                $delete_action .
                $publish_action .
                $clearer .
             '</div>';
  }
  else {
    $submit_box_action = '';
    $major = '';
  }

  /* Composing output. */
  $output = '<div id="pwwh-submitpost" class="pwwh-submitbox">' .
              $submit_box_action .
              $minor .
              $major .
            '</div>';

  if($echo)
    echo $output;
  else
    return $output;
}