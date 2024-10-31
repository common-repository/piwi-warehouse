jQuery( function($) {

  /* Avoiding unload. */
  $('#pwwh-submitpost .pwwh-lib-button.pwwh-primary').click(function(event) {
    $(window).off('beforeunload');
  });

  /* Managing Status block. */
  {
    /* Visual link. */
    var status_edit_btn = '#pwwh-submitpost #pwwh-status.pwwh-status-edit a';

    /* Primary button. */
    var primary_btn = '#pwwh-submitpost .pwwh-lib-button.pwwh-primary';

    /* Getting old selected status. */
    var old_status = $('#pwwh-submitpost #hidden_post_status').val();

    /* Managing click on Edit status button. */
    $(status_edit_btn).click(function(event) {
      /* Click prevented. */
      event.preventDefault();

      /* Showing editing area. */
      $('#pwwh-submitpost #post-status-fieldset').slideDown(200);

      /* Anchor now will seems not clickable. */
      $(status_edit_btn).css('cursor', 'default');
      $(status_edit_btn).css('color', '#23282d');
    });

    /* Managing click on Confirm status. */
    $('#pwwh-submitpost #pwwh-status-confirm').click(function(event) {
      /* Click prevented. */
      event.preventDefault();

      /* Getting current selected status. */
      var status_label = $('#pwwh-submitpost #post_status').find(':selected').text();
      var next_status = $('#pwwh-submitpost #post_status').find(':selected').val();

      /* Updating visual anchor. */
      $(status_edit_btn).text(status_label);
      $(status_edit_btn).attr('title', status_label);

      /* Hiding editing area. */
      $('#pwwh-submitpost #post-status-fieldset').slideUp(200);

      /* Managing publish button. */
      if(next_status == 'publish') {
        $(primary_btn).attr('id', 'publish');
        $(primary_btn).attr('name', 'publish');
        if(next_status != old_status) {
          $(primary_btn).text(pwwh_submitdiv_obj.publish_label);
        }
        else {
          $(primary_btn).text(pwwh_submitdiv_obj.update_label);
        }
      }
      else {
        $(primary_btn).attr('id', 'save-post');
        $(primary_btn).attr('name', 'save');
        if(next_status != old_status) {
          $(primary_btn).text(pwwh_submitdiv_obj.saveas_label.replace(/%s/g, status_label));
        }
        else {
          $(primary_btn).text(pwwh_submitdiv_obj.save_label);
        }
      }

      /* Restoring Anchor. */
      $(status_edit_btn).css('cursor', '');
      $(status_edit_btn).css('color', '');
    });

    /* Managing click on Abort status. */
    $('#pwwh-submitpost #pwwh-status-abort').click(function(event) {
      /* Click prevented. */
      event.preventDefault();

      var status_label = $(post_status).find('option[value="' + old_status + '"]').text();

      /* Updating select. */
      $(post_status).val(old_status);

      /* Updating visual anchor. */
      $(status_edit_btn).text(status_label);
      $(status_edit_btn).attr('title', status_label);

      /* Showing editing area. */
      $('#pwwh-submitpost #post-status-fieldset').slideUp(200);

      /* Managing publish button. */
      if(old_status == 'publish') {
        $(primary_btn).attr('id', 'publish');
        $(primary_btn).attr('name', 'publish');
        $(primary_btn).text(pwwh_submitdiv_obj.update_label);
      }
      else {
        $(primary_btn).attr('id', 'save-post');
        $(primary_btn).attr('name', 'save');
        $(primary_btn).text(pwwh_submitdiv_obj.save_label);
      }

      /* Restoring Anchor. */
      $(status_edit_btn).css('cursor', '');
      $(status_edit_btn).css('color', '');
    });
  }

  /* Managing Date block. */
  {
    /* Visual link. */
    var date_edit_btn = '#pwwh-submitpost #pwwh-date.pwwh-date-edit a';

    /* Managing click on Edit date button. */
    $(date_edit_btn).click(function(event) {
      /* Click prevented. */
      event.preventDefault();

      /* Showing editing area. */
      $('#pwwh-submitpost #post-date-fieldset').slideDown(200);

      /* Anchor now will seems not clickable. */
      $(date_edit_btn).css('cursor', 'default');
      $(date_edit_btn).css('color', '#23282d');
    });

    /* Managing click on Confirm date. */
    $('#pwwh-submitpost #pwwh-date-confirm').click(function(event) {
      /* Click prevented. */
      event.preventDefault();

      /* Launching AJAX to remotely validate date. */
      $.ajax({
        url: pwwh_submitdiv_obj.validate_ajax_url,
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'pwwh_validate_date',
          pwwh_month: function() {
            return $('#pwwh-submitpost #mm').val();
          },
          pwwh_day: function() {
            return $('#pwwh-submitpost #jj').val();
          },
          pwwh_year: function() {
            return $('#pwwh-submitpost #aa').val();
          },
          pwwh_hour: function() {
            return $('#pwwh-submitpost #hh').val();
          },
          pwwh_minute: function() {
            return $('#pwwh-submitpost #mn').val();
          },
          pwwh_format: function() {
            return $('#pwwh-submitpost #date_format').val();
          }
        },
        success: function(data) {
          if(data == 'BOTH_INVALID') {
            /* Signaling error to both. */
            $('#pwwh-submitpost #mm').attr('aria-invalid', 'true');
            $('#pwwh-submitpost #jj').attr('aria-invalid', 'true');
            $('#pwwh-submitpost #aa').attr('aria-invalid', 'true');
            $('#pwwh-submitpost #hh').attr('aria-invalid', 'true');
            $('#pwwh-submitpost #mn').attr('aria-invalid', 'true');
          }
          else if(data == 'DATE_INVALID') {
            /* Signaling error to date. */
            $('#pwwh-submitpost #mm').attr('aria-invalid', 'true');
            $('#pwwh-submitpost #jj').attr('aria-invalid', 'true');
            $('#pwwh-submitpost #aa').attr('aria-invalid', 'true');
            $('#pwwh-submitpost #hh').attr('aria-invalid', 'false');
            $('#pwwh-submitpost #mn').attr('aria-invalid', 'false');
          }
          else if(data == 'TIME_INVALID') {
            /* Signaling error to time. */
            $('#pwwh-submitpost #mm').attr('aria-invalid', 'false');
            $('#pwwh-submitpost #jj').attr('aria-invalid', 'false');
            $('#pwwh-submitpost #aa').attr('aria-invalid', 'false');
            $('#pwwh-submitpost #hh').attr('aria-invalid', 'true');
            $('#pwwh-submitpost #mn').attr('aria-invalid', 'true');
          }
          else {
            /* Changing post date. */
            $(date_edit_btn).text(data);
            $(date_edit_btn).attr('title', data);

            /* Restoring error flag. */
            $('#pwwh-submitpost #mm').attr('aria-invalid', 'false');
            $('#pwwh-submitpost #jj').attr('aria-invalid', 'false');
            $('#pwwh-submitpost #aa').attr('aria-invalid', 'false');
            $('#pwwh-submitpost #hh').attr('aria-invalid', 'false');
            $('#pwwh-submitpost #mn').attr('aria-invalid', 'false');

            /* Sliding up area. */
            $('#pwwh-submitpost #post-date-fieldset').slideUp(200);

            /* Making anchor clickable again. */
            $(date_edit_btn).css('cursor', '');
            $(date_edit_btn).css('color', '');
          }
        }
      });
    });

    /* Managing click on Abort date. */
    $('#pwwh-submitpost #pwwh-date-abort').click(function(event) {
      /* Click prevented. */
      event.preventDefault();

      /* Changing post date. */
      var old_date = $('#pwwh-submitpost #cur_post_date').val();
      $(date_edit_btn).text(old_date);
      $(date_edit_btn).attr('title', old_date);
      $('#pwwh-submitpost #mm').val($('#pwwh-submitpost #hidden_mm').val());
      $('#pwwh-submitpost #jj').val($('#pwwh-submitpost #hidden_jj').val());
      $('#pwwh-submitpost #aa').val($('#pwwh-submitpost #hidden_aa').val());
      $('#pwwh-submitpost #hh').val($('#pwwh-submitpost #hidden_hh').val());
      $('#pwwh-submitpost #mn').val($('#pwwh-submitpost #hidden_mn').val());

      /* Restoring error flag. */
      $('#pwwh-submitpost #mm').attr('aria-invalid', 'false');
      $('#pwwh-submitpost #jj').attr('aria-invalid', 'false');
      $('#pwwh-submitpost #aa').attr('aria-invalid', 'false');
      $('#pwwh-submitpost #hh').attr('aria-invalid', 'false');
      $('#pwwh-submitpost #mn').attr('aria-invalid', 'false');

      /* Sliding up area. */
      $('#pwwh-submitpost #post-date-fieldset').slideUp(200);

      /* Making anchor clickable again. */
      $(date_edit_btn).css('cursor', '');
      $(date_edit_btn).css('color', '');
    });

    /* Input change. */
    $('#pwwh-submitpost #timestamp-wrap input').change(function(event) {
      /* Launching AJAX to remotely validate date. */
      $.ajax({
        url: pwwh_submitdiv_obj.validate_ajax_url,
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'pwwh_validate_date',
          pwwh_month: function() {
            return $('#pwwh-submitpost #mm').val();
          },
          pwwh_day: function() {
            return $('#pwwh-submitpost #jj').val();
          },
          pwwh_year: function() {
            return $('#pwwh-submitpost #aa').val();
          },
          pwwh_hour: function() {
            return $('#pwwh-submitpost #hh').val();
          },
          pwwh_minute: function() {
            return $('#pwwh-submitpost #mn').val();
          },
          pwwh_format: function() {
            return $('#pwwh-submitpost #date_format').val();
          }
        },
        success: function(data) {
          if(data == 'BOTH_INVALID') {
            /* Signaling error to both. */
            $('#pwwh-submitpost #mm').attr('aria-invalid', 'true');
            $('#pwwh-submitpost #jj').attr('aria-invalid', 'true');
            $('#pwwh-submitpost #aa').attr('aria-invalid', 'true');
            $('#pwwh-submitpost #hh').attr('aria-invalid', 'true');
            $('#pwwh-submitpost #mn').attr('aria-invalid', 'true');
          }
          else if(data == 'DATE_INVALID') {
            /* Signaling error to date. */
            $('#pwwh-submitpost #mm').attr('aria-invalid', 'true');
            $('#pwwh-submitpost #jj').attr('aria-invalid', 'true');
            $('#pwwh-submitpost #aa').attr('aria-invalid', 'true');
            $('#pwwh-submitpost #hh').attr('aria-invalid', 'false');
            $('#pwwh-submitpost #mn').attr('aria-invalid', 'false');
          }
          else if(data == 'TIME_INVALID') {
            /* Signaling error to time. */
            $('#pwwh-submitpost #mm').attr('aria-invalid', 'false');
            $('#pwwh-submitpost #jj').attr('aria-invalid', 'false');
            $('#pwwh-submitpost #aa').attr('aria-invalid', 'false');
            $('#pwwh-submitpost #hh').attr('aria-invalid', 'true');
            $('#pwwh-submitpost #mn').attr('aria-invalid', 'true');
          }
          else {
            /* Restoring error flag. */
            $('#pwwh-submitpost #mm').attr('aria-invalid', 'false');
            $('#pwwh-submitpost #jj').attr('aria-invalid', 'false');
            $('#pwwh-submitpost #aa').attr('aria-invalid', 'false');
            $('#pwwh-submitpost #hh').attr('aria-invalid', 'false');
            $('#pwwh-submitpost #mn').attr('aria-invalid', 'false');
          }
        }
      });
    });
  }

  var primary_btn = '#pwwh-submitpost #publishing-action .pwwh-primary';
  var allow_submit = false;
  $(primary_btn).click(function(event) {

    /* Launching AJAX to remotely validate date. */
    $.ajax({
      url: pwwh_submitdiv_obj.validate_ajax_url,
      type: 'POST',
      dataType: 'json',
      async: false,
      data: {
        action: 'pwwh_validate_date',
        pwwh_month: function() {
          return $('#pwwh-submitpost #mm').val();
        },
        pwwh_day: function() {
          return $('#pwwh-submitpost #jj').val();
        },
        pwwh_year: function() {
          return $('#pwwh-submitpost #aa').val();
        },
        pwwh_hour: function() {
          return $('#pwwh-submitpost #hh').val();
        },
        pwwh_minute: function() {
          return $('#pwwh-submitpost #mn').val();
        },
        pwwh_format: function() {
          return $('#pwwh-submitpost #date_format').val();
        }
      },
      success: function(data) {
        if(data == 'BOTH_INVALID') {
          /* Signaling error to both. */
          $('#pwwh-submitpost #mm').attr('aria-invalid', 'true');
          $('#pwwh-submitpost #jj').attr('aria-invalid', 'true');
          $('#pwwh-submitpost #aa').attr('aria-invalid', 'true');
          $('#pwwh-submitpost #hh').attr('aria-invalid', 'true');
          $('#pwwh-submitpost #mn').attr('aria-invalid', 'true');
        }
        else if(data == 'DATE_INVALID') {
          /* Signaling error to date. */
          $('#pwwh-submitpost #mm').attr('aria-invalid', 'true');
          $('#pwwh-submitpost #jj').attr('aria-invalid', 'true');
          $('#pwwh-submitpost #aa').attr('aria-invalid', 'true');
          $('#pwwh-submitpost #hh').attr('aria-invalid', 'false');
          $('#pwwh-submitpost #mn').attr('aria-invalid', 'false');
        }
        else if(data == 'TIME_INVALID') {
          /* Signaling error to time. */
          $('#pwwh-submitpost #mm').attr('aria-invalid', 'false');
          $('#pwwh-submitpost #jj').attr('aria-invalid', 'false');
          $('#pwwh-submitpost #aa').attr('aria-invalid', 'false');
          $('#pwwh-submitpost #hh').attr('aria-invalid', 'true');
          $('#pwwh-submitpost #mn').attr('aria-invalid', 'true');

        }
        else {
          /* Restoring error flag. */
          $('#pwwh-submitpost #mm').attr('aria-invalid', 'false');
          $('#pwwh-submitpost #jj').attr('aria-invalid', 'false');
          $('#pwwh-submitpost #aa').attr('aria-invalid', 'false');
          $('#pwwh-submitpost #hh').attr('aria-invalid', 'false');
          $('#pwwh-submitpost #mn').attr('aria-invalid', 'false');

          /* Allowing submit. */
          allow_submit = true;
        }
      }
    });

    if(allow_submit != true) {
      /* Preventing default action. */
      event.preventDefault();
    }
  });
});

