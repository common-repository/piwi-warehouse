{
/*===========================================================================*/
/* Local Scope.                                                              */
/*===========================================================================*/

  /**
   * @brief   The localize object.
   */
  let loc_obj = pwwh_admin_cap_ui_obj;

  /**
   * @brief   The localize object.
   */
  let box_id = loc_obj.ui.box;

  let pwwhAdminCapsSubGroup = function(elem, enable) {

    var curr_status = enable;
    /* Current capability dependencies. */
    deps = $(elem).next('ul');

    /* If there is a sublist. */
    if(deps.length) {
      $.each(deps.children('li'), function() {
        if(curr_status) {
          $(this).removeClass('readonly');
        }
        else {
          $(this).addClass('readonly');
        }
        var cap_wrap = $(this).children().first();
        var cap_switch = $(cap_wrap).find('.' + loc_obj.ui.switch);
        var cap_input = $(cap_wrap).find('input');

        if(curr_status) {
          cap_wrap.removeClass('readonly');
          cap_switch.removeClass('readonly');
        }
        else {
          cap_wrap.addClass('readonly');
          cap_switch.addClass('readonly');
        }
        $(cap_input).prop('readonly', !curr_status);
        var enable = curr_status & $(cap_input).prop('checked');
        pwwhAdminCapsSubGroup(cap_wrap, enable);
      });
    }
  }

/*===========================================================================*/
/* Global Scope.                                                             */
/*===========================================================================*/

/*===========================================================================*/
/* Application entry point.                                                  */
/*===========================================================================*/

  jQuery(document).ready(function($) {

    var cap_input = '#' + box_id + ' .' + loc_obj.ui.switch + ' input';
    $(cap_input).on('change', function(){

      var enable = $(this).prop('checked');
      var cap_wrap = $(this).closest('.' + loc_obj.ui.wrap);
      pwwhAdminCapsSubGroup(cap_wrap, enable);
    });
  });
}