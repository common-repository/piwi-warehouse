{
/*===========================================================================*/
/* Local Scope.                                                              */
/*===========================================================================*/

  /**
   * @brief   The localize object.
   */
  let loc_obj = pwwh_core_purchase_rem_obj;

  /**
   * @brief   The localize object.
   */
  let box_id = loc_obj.ui.box.item_summary.id;

  /**
   * @brief     Removes a specific box identified through the instance.
   *
   * @param[in] mixed instance      The instance.
   *
   * @return    void.
   */
  let pwwhRemoveItemBox = function(instance) {
    var fade = {opacity: 0, transition: 'opacity 0.4s'};
    var selector = '#' + box_id + '.inst-' + instance
    $(selector).css(fade).slideUp(400, function(){$(this).remove();});
  }

  /**
   * @brief     Updates the instance collector removing a specific istance.
   *
   * @param[in] mixed instance      The instance.
   *
   * @return    void.
   */
  let pwwhRemoveFromCollector = function(instance) {
    /* Updating instance collector. */
    let instances_val = ($('#' + loc_obj.ui.input.collector.id).val());
    if(instances_val) {
      instances = instances_val.split(':');
      var index = instances.indexOf(instance);
      if (index > -1) {
        instances.splice(index, 1);
      }
      $('#' + loc_obj.ui.input.collector.id).val(instances.join(':'));
    }
  }

/*===========================================================================*/
/* Global Scope.                                                             */
/*===========================================================================*/

/*===========================================================================*/
/* Application entry point.                                                  */
/*===========================================================================*/

  jQuery(document).ready(function($) {

    /* Adding an event listener for each box. */
    $('#' + box_id + ' #pwwh-remove').click(function(event) {

      /* Getting instances. */
      let instances_val = ($('#' + loc_obj.ui.input.collector.id).val());
      if(instances_val) {
        instances = instances_val.split(':');

        /* Counting how many instances are available. */
        let count = instances.length;

        /* Removing box. */
        if(count > 1) {
          /* Getting current instance */
          var instance = $(this).val();
          pwwhRemoveItemBox(instance);
          pwwhRemoveFromCollector(instance);
          count--;
        }
        /* Removing delete buttons. */
        if(count == 1) {
          $('#' + box_id + ' #pwwh-remove').remove();
        }
      }
    });
  });
}
