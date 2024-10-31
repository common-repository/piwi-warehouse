{
/*===========================================================================*/
/* Local Scope.                                                              */
/*===========================================================================*/

  /**
   * @brief     Manages the publish button.
   *
   * @param[in] string post_type    The post type to target
   * @param[in] string future       The future status ID
   * @param[in] string current      The current status ID
   * @param[in] string label        The button label (leave empty for auto
   *                                management)
   *
   * @return    void
   */
  let pwwhCoreManagePublishButton = function(post_type, current, future,
                                             label = '') {
    let submit_div = '.post-type-' + post_type + ' #submitdiv';
    let publish_button = submit_div + ' #publish';

    /* Chaning publish button action. */
    if((future === 'publish') && (current !== 'publish')) {
      $(publish_button).attr('name', 'publish');
      if(!label) {
        label = pwwh_core_obj.publish;
      }
    }
    else {
      $(publish_button).attr('name', 'save');
      if(!label) {
        label = pwwh_core_obj.update;
      }
    }

    $(publish_button).val(label);
  }
/*===========================================================================*/
/* Global Scope.                                                             */
/*===========================================================================*/

  /**
   * @brief     jQuery object.
   */
  var $ = jQuery;

  /**
   * @brief     Modifies the submit div according to the configuration.
   *
   * @param[in] obj cfg             A configuration object.
   * @paramkey{post_type}           The post type to target
   * @paramkey{statuses}            An array of statuses. If empty statuses are
   *                                not impacted.
   * @paramkey{future_status}       The future status ID. If statuses is empty
   *                                this is not affecting the div.
   * @paramkey{allowed_visibility}  An array of allowed visibilities
   * @paramkey{remove_minor}        If true removes the minor publishing actions
   * @paramkey{publish_label}       A replacement for the Submit button
   *
   * @return    void
   */
  var pwwhCoreManageSubmitDiv = function(cfg) {

    var submit_div = '.post-type-' + cfg.post_type + ' #submitdiv';
    var post_status_select = submit_div + ' #post_status';
    var post_status_options = post_status_select + ' option';
    var post_status_hidden = submit_div + ' #hidden_post_status';
    var publish_button = submit_div + ' #publish';
    var post_status_display = submit_div + ' #post-status-display';

    /* The script could be called in the wrong context. If there is no submit
       div it does not perform any action. */
    if($(submit_div).length == 0) {
      return;
    }

    /* Storing the current Status info. */
    var curr_status_label = $(post_status_display).html().trim();
    var curr_status = $(post_status_hidden).val();

    /* Removing unallowed statuses. */
    if((cfg.statuses !== null) && (typeof(cfg.statuses) === 'object')) {

      $(submit_div + ' a.save-post-status').remove();
      $(submit_div + ' a.cancel-post-status').remove();

      /* Removing existing statuses. */
      $(post_status_options).remove();

      /* Appending new statuses. */
      for (const [value, label] of Object.entries(cfg.statuses)) {
        if(value == cfg.future_status) {
          $(post_status_select).append(new Option(label, value, true, true));
          //$(post_status_hidden).val(cfg.future_status);
          $(submit_div + ' #post-status-display').html(label);

          var future_status_label = label;
        }
        else {
          $(post_status_select).append(new Option(label, value));
        }
      }
      $(post_status_select).val(cfg.future_status);

      /* Saving current status label. */
      if(curr_status_label) {
        $(post_status_display).html(curr_status_label);
      }
      else {
        $(post_status_display).html(future_status_label);
      }
    }

    /* Removing status edit button. */
    var sel_edit = submit_div + ' .edit-post-status';
    $(sel_edit).remove();

    /* Removing unallowed visibility. */
    var allowed = cfg.allowed_visibility;
    var post_status_options = submit_div + ' #post-visibility-select input[type="radio"]';

    $(post_status_options).each(function(key, item) {

      var curr_val = $(item).val();

      if(!allowed.includes(curr_val)) {
        var label = 'label[for="visibility-radio-' + curr_val + '"]';
        var next = $(label).next();
        if(next.prop("tagName").toLowerCase() == 'br') {
          next.remove();
        }
        $(label).remove();
        $(item).remove();
      }
    });

    /* Removing visibility edit button. */
    $(submit_div + ' .edit-visibility').remove();

    /* Removing timestamp edit button. */
    $(submit_div + ' .edit-timestamp').remove();

    if(cfg.remove_minor) {
      /* Removing minor publishing actions. */
      $(submit_div + ' #minor-publishing-actions').html('');
    }

    /* Changing action and label of the publish button. */
    pwwhCoreManagePublishButton(cfg.post_type, curr_status, cfg.future_status,
                                cfg.publish_label);
    /* Enabling the publish button. */
    $(publish_button).css('display', 'inline-block');
    $(publish_button).css('opacity', '1');

    $(submit_div + ' .inside').animate({opacity: '1'}, 400);
  }

  /**
   * @brief     Changes the current status of the submit div.
   *
   * @param[in] string post_type    The current post type.
   * @param[in] string status       The status id.
   * @param[in] string label        The label for the publish button.
   *
   * @return    void
   */
  var pwwhCoreChangeStatus = function(post_type, status, label = '') {

    var submit_div = '.post-type-' + post_type + ' #submitdiv';

    var post_status_select = submit_div + ' #post_status';
    var current_status = post_status_select + ' option:selected';
    var future_status = post_status_select + ' option[value="' + status + '"]';
    var post_status_hidden = submit_div + ' #hidden_post_status';
    var publish_button = submit_div + ' #publish';
    var post_status_display = submit_div + ' #post-status-display';

    /* Storing the current Status label: if not available getting it from the
       currently selected status. */
    var curr_status_label = $(post_status_display).html().trim();
    var curr_status = $(post_status_hidden).val();

    if($(future_status)) {
      $(current_status).removeAttr('selected');
      $(future_status).attr('selected','selected');
      $(post_status_select).val(status);
      //$(post_status_hidden).val(status);

      var future_status_label = $(future_status).html();

      /* Saving current status label. */
      if(curr_status_label) {
        $(post_status_display).html(curr_status_label);
      }
      else {
        $(post_status_display).html(future_status_label);
      }

      /* Chaning publish button action. */
      pwwhCoreManagePublishButton(post_type, curr_status, status, label);
    }
  }

  /**
   * @brief     Changes the current status of the submit div.
   *
   * @param[in] string post_type    The current post type.
   *
   * @return    string status       The status id.
   */
  var pwwhCoreGetStatus = function(post_type) {

    var submit_div = '.post-type-' + post_type + ' #submitdiv';
    var post_status_hidden = submit_div + ' #hidden_post_status';

    return $(post_status_hidden).val();
  }

  /**
   * @brief     Triggers an alarm when trying to delete a post.
   *
   * @param[in] obj cfg             A configuration object.
   * @paramkey{post_type}           The post type to target
   * @paramkey{msg_alert}           The message to populate the alert
   *
   * @return    void
   */
  var pwwhCoreDeleteAlert = function (cfg) {

    /* Composing the selector. */
    var sel = '.post-type-' + cfg.post.type + ' a.submitdelete';

    /* Checking if there is any element. */
    if($(sel).length) {

      /* Searching for the action in the url link. */
      var action =  $(sel).attr('href').match(/action=([^&]+)/)[1]
      /* Prompting the alert only on delete permanently. */
      if(action == 'delete') {
        $(sel).click(function(event) {
          if(!confirm(cfg.msg_alert)) {
            event.preventDefault();
          }
        });
      }
    }
  }

/*===========================================================================*/
/* Application entry point.                                                  */
/*===========================================================================*/

  jQuery(document).ready(function($) {

  });
}