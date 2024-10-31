{
/*===========================================================================*/
/* Local Scope.                                                              */
/*===========================================================================*/

  /**
   * @brief   The localize object.
   */
  let loc_obj = pwwh_core_movement_add_obj;

  /**
   * @brief   The localize object.
   */
  let box_id = loc_obj.ui.box.add_item.id;

  /**
   * @brief     Appends an Item box identified by its instance.
   *
   * @param[in] int instance        The instance ID
   *
   * @return    void.
   */
  let pwwhMovementAddItemBox = function(instance) {
    instance = parseFloat(instance);
    var data = {
      url: loc_obj.ajax.url,
      type: 'POST',
      dataType: 'json',
      async: true,
      data: {
        action: loc_obj.ajax.action.add_item,
        instance: function(){
          return instance;
        }
      },
      success: function(data) {
        /* Adding new boxes. */
        $(data).appendTo('#' + box_id + ' .pwwh-main').slideDown(200);

        /* Updating instance collector. */
        var value = $('#' + loc_obj.ui.input.collector.id).val();
        $('#' + loc_obj.ui.input.collector.id).val(value + ':' + instance);

        /* Updating button value. */
        $('#' + box_id + ' #pwwh-add').val(instance + 1);

        /* Adding rules for the new box. */
        pwwhMovementValidateAddRulesToItemBox(instance);

        /* Reactivating the movement. */
        pwwhMovementSetActive();
        
        /* Re-Enabling the button. */
        $('#' + box_id + ' #pwwh-add').prop('disabled', false);
        $('#' + box_id + ' #pwwh-add').removeClass('disabled');
      }
    };
    $.ajax(data);
  }

  /**
   * @brief     Removes an Item box identified by its instance.
   *
   * @param[in] int instance        The instance ID
   *
   * @return    void.
   */
  let pwwhMovementRemoveItemBox = function(instance) {
    if(instance > 0) {
      var fade = {opacity: 0, transition: 'opacity 0.4s'};
      var curr_box = '#' + box_id + '-' + instance;

      /* Updating instance collector. */
      var istances = $('#' + loc_obj.ui.input.collector.id).val().split(':');
      var index = istances.indexOf(instance);
      if (index > -1) {
        istances.splice(index, 1);
      }
      $('#' + loc_obj.ui.input.collector.id).val(istances.join(':'));

      /* Removing box. */
      $(curr_box).css(fade).slideUp(400, function(){$(this).remove();});

      /* Removing rules for the new box. */
      pwwhMovementValidateRemoveRulesToItemBox(instance);

      /* Recomputing post status. */
      pwwhMovementManageStatus();
    }
  }

/*===========================================================================*/
/* Global Scope.                                                             */
/*===========================================================================*/

/*===========================================================================*/
/* Application entry point.                                                  */
/*===========================================================================*/

  jQuery(document).ready(function($) {

    /* On add button index. */
    $('#' + box_id + ' #pwwh-add').click(function(event) {
      
      /* Disabling the button. */
      $('#' + box_id + ' #pwwh-add').prop('disabled', true);
      $('#' + box_id + ' #pwwh-add').addClass('disabled');
      
      /* Adding another itembox */
      var instance = $(this).val();
      pwwhMovementAddItemBox(instance);
    });

    /* Adding event listener for dynamically created remove buttons. */
    $('#' + box_id).on('click', '#pwwh-remove', function(event) {
      /* Adding another itembox */
      var instance = $(this).val();
      pwwhMovementRemoveItemBox(instance);
    });
  });
}