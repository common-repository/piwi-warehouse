{
/*===========================================================================*/
/* Local Scope.                                                              */
/*===========================================================================*/

  /**
   * @brief   The localize object.
   */
  let loc_obj = pwwh_core_purchase_edit_obj;

  /**
   * @brief   The localize object.
   */
  let box_id = loc_obj.ui.box.item_summary.id;

/*===========================================================================*/
/* Global Scope.                                                             */
/*===========================================================================*/

/*===========================================================================*/
/* Application entry point.                                                  */
/*===========================================================================*/

  jQuery(document).ready(function($) {

    var edit_id = loc_obj.ui.box.item_summary.button.edit;
    var confirm_id = loc_obj.ui.box.item_summary.button.confirm;
    var abort_id = loc_obj.ui.box.item_summary.button.abort;
    var note = '<span class="pwwh-qnt-note">* ' +
                  loc_obj.note +
               '</span>';
    var old_qnt = {};
    var old_avail = {};
    var old_amount = {};

    /* Edit button click handler. */
    $('#' + box_id + ' #' + edit_id).click(function(event) {
      /* Click prevented. */
      event.preventDefault();

      /* Saving box old value. */
      var inst = $(this).val();
      if(!old_qnt.hasOwnProperty(inst)) {
        old_qnt[inst] = parseFloat($('#' + inst + '\\:pwwh_purchase_qnt').val());
      }
      if(!old_avail.hasOwnProperty(inst)) {
        var selector = '#' + box_id + '.inst-' + inst +
                       ' .pwwh-avail .pwwh-lib-value';
        old_avail[inst] = parseFloat($(selector).text());
      }
      if(!old_amount.hasOwnProperty(inst)) {
        var selector = '#' + box_id + '.inst-' + inst +
                       ' .pwwh-amount .pwwh-lib-value';
        old_amount[inst] = parseFloat($(selector).text());
      }

      /* Showing editing area. */
      var selector = '#pwwh-qnt-editarea.inst-' + inst;
      $(selector).slideDown(200);

      /* Button now will seems not clickable. */
      $('#' + box_id + ' #' + edit_id).css('cursor', 'default');
      $('#' + box_id + ' #' + edit_id).css('color', '#124964');
    });

    $('#' + box_id + ' #' + confirm_id).click(function(event){
      /* Click prevented. */
      event.preventDefault();

      var inst = $(this).val();
      /* Checking quantity input validity. */
      var is_valid = $('#' + inst + '\\:pwwh_purchase_qnt').valid();

      if(is_valid) {
        /* Updating quantity info chunk. */
        var new_qnt = parseFloat($('#' + inst + '\\:pwwh_purchase_qnt').val());
        var selector = '#' + box_id + '.inst-' + inst +
                       ' .pwwh-quantity .pwwh-lib-value';
        if(new_qnt != old_qnt[inst]) {
          $(selector).html(new_qnt + ' *');
        }
        else {
          $(selector).html(new_qnt);
        }

        /* Updating available info chunk. */
        var new_avail = old_avail[inst] - old_qnt[inst] + new_qnt;
        var selector = '#' + box_id + '.inst-' + inst +
                       ' .pwwh-avail .pwwh-lib-value';
        if(new_qnt != old_qnt[inst]) {
          $(selector).html(new_avail + ' *');
        }
        else {
          $(selector).html(new_avail);
        }

        /* Updating amount info chunk. */
        var new_amount = old_amount[inst] - old_qnt[inst] + new_qnt;
        var selector = '#' + box_id + '.inst-' + inst +
                       ' .pwwh-amount .pwwh-lib-value';
        if(new_qnt != old_qnt[inst]) {
          $(selector).html(new_amount + ' *');
        }
        else {
          $(selector).html(new_amount);
        }

        /* Hiding editing area. */
        var selector = '#pwwh-qnt-editarea.inst-' + inst;
        $(selector).slideUp(200);

        /* Restoring button. */
        $('#' + box_id + ' #' + edit_id).css('cursor', '');
        $('#' + box_id + ' #' + edit_id).css('color', '');

        /* Appending note. */
        var selector = '#' + box_id + '.inst-' + inst +
                       ' footer';
        if(new_qnt != old_qnt[inst]) {
          if(!$(selector + ' .pwwh-qnt-note').length) {
            $(note).appendTo(selector);
          }
        }
        else {
          $(selector).remove();
        }
      }
    });

    $('#' + box_id + ' #' + abort_id).click(function(event){
      /* Click prevented. */
      event.preventDefault();

      var inst = $(this).val();

      /* Restoring quantity info chunk. */
      var selector = '#' + box_id + '.inst-' + inst +
                     ' .pwwh-quantity .pwwh-lib-value';
      $(selector).html(old_qnt[inst]);
      $('#' + inst + '\\:pwwh_purchase_qnt').val(old_qnt[inst]);

      /* Restoring available info chunk. */
      var selector = '#' + box_id + '.inst-' + inst +
                     ' .pwwh-avail .pwwh-lib-value';
      $(selector).html(old_avail[inst]);

      /* Restoring amount info chunk. */
      var selector = '#' + box_id + '.inst-' + inst +
                     ' .pwwh-amount .pwwh-lib-value';
      $(selector).html(old_amount[inst]);

      /* Hiding editing area. */
      var selector = '#pwwh-qnt-editarea.inst-' + inst;
      $(selector).slideUp(200);

      /* Restoring button. */
      $('#' + box_id + ' #' + edit_id).css('cursor', '');
      $('#' + box_id + ' #' + edit_id).css('color', '');

      /* Removing note. */
      var selector = '#' + box_id + '.inst-' + inst +
                     ' footer';
      if($(selector).length)
        $(selector).remove();
    });
  });
}
