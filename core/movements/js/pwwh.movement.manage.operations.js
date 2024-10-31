{
/*===========================================================================*/
/* Local Scope.                                                              */
/*===========================================================================*/

  /**
   * @brief   The localize object.
   */
  let loc_obj = pwwh_core_movement_operations_obj;

  /**
   * @brief   The localize object.
   */
  let box_id = loc_obj.ui.box.item_summary.id;

  /**
   * @brief     Updates current lent value of a specific item instance.
   *
   * @param[in] mixed instance      The instance.
   *
   * @return    void.
   */
  let pwwhUpdateLent = function(instance) {

    let moved_input = "#" + instance + "\\:" + loc_obj.ui.input.moved.id;

    /* Computing Values value. */
    var lent_value = pwwhMovementGetLent(instance);
    var moved_value = parseFloat($(moved_input).val());

    /* Updating lent value on the HTML. */
    var new_lent_field = "#" + instance + "\\:" + loc_obj.ui.field.new_lent.id;

    /* Updating new lent value. */
    $(new_lent_field + ' .pwwh-lib-value').text(lent_value);

    if((lent_value < 0) || (lent_value > moved_value)) {
      $(new_lent_field).removeClass('pwwh-active');
      $(new_lent_field).removeClass('pwwh-concluded');
      $(new_lent_field).addClass('pwwh-invalid');
      $(new_lent_field).attr('aria-invalid', 'true');
    }
    else if (lent_value == 0) {
      $(new_lent_field).removeClass('pwwh-active');
      $(new_lent_field).removeClass('pwwh-invalid');
      $(new_lent_field).addClass('pwwh-concluded');
      $(new_lent_field).attr('aria-invalid', 'false');
    }
    else {
      $(new_lent_field).removeClass('pwwh-concluded');
      $(new_lent_field).removeClass('pwwh-invalid');
      $(new_lent_field).addClass('pwwh-active');
      $(new_lent_field).attr('aria-invalid', 'false');
    }
  }

/*===========================================================================*/
/* Global Scope.                                                             */
/*===========================================================================*/

/*===========================================================================*/
/* Application entry point.                                                  */
/*===========================================================================*/

  jQuery(document).ready(function($) {

    /* Getting instances. */
    let instances_val = ($('#' + loc_obj.ui.input.collector.id).val());
    if(instances_val) {
      instances = instances_val.split(':');
      instances.forEach(function(instance) {
        var returned_input = "#" + instance + "\\:" + loc_obj.ui.input.returned.id;
        var donated_input = "#" + instance + "\\:" + loc_obj.ui.input.donated.id;
        var lost_input = "#" + instance + "\\:" + loc_obj.ui.input.lost.id;
        $(returned_input).on("input", function() {
          pwwhUpdateLent(instance);
          pwwhMovementManageStatus();
        });
        $(donated_input).on("input", function() {
          pwwhUpdateLent(instance);
          pwwhMovementManageStatus();
        });
        $(lost_input).on("input", function() {
          pwwhUpdateLent(instance);
          pwwhMovementManageStatus();
        });
      });

      /* Adding an event listener for uncollapse button. */
      $('#' + box_id + ' #pwwh-hideshow').click(function(event) {
        var instance = $(this).val();
        var action = $(this).attr('name')
        var management_area = "#section-" + instance +
                              "\\:pwwh-movement-management " +
                              ".pwwh-movement-management";
        if(action == 'pwwh-uncollapse') {
          $(this).attr('name', 'pwwh-collapse');
          $(this).attr('title', loc_obj.title.hide);
          $(this).find('span').html(loc_obj.label.hide);
          $(management_area).slideDown(200);
        }
        else if(action == 'pwwh-collapse') {
          $(this).attr('name', 'pwwh-uncollapse');
          $(this).attr('title', loc_obj.title.show);
          $(this).find('span').html(loc_obj.label.show);
          $(management_area).slideUp(200);
        }
      });
    }
  });
}