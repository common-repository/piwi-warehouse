{
/*===========================================================================*/
/* Local Scope.                                                              */
/*===========================================================================*/

/*===========================================================================*/
/* Global Scope.                                                             */
/*===========================================================================*/

  /**
   * @brief     Return current lent value of a specific item instance.
   *
   * @param[in] int instance        The instance ID.
   *
   * @return    int the current lent value.
   */
  var pwwhMovementGetLent = function(instance) {

    let loc_obj = pwwh_core_movement_comm_obj;

    var moved_input = "#" + instance + "\\:" + loc_obj.ui.input.moved.id;
    var returned_input = "#" + instance + "\\:" + loc_obj.ui.input.returned.id;
    var donated_input = "#" + instance + "\\:" + loc_obj.ui.input.donated.id;
    var lost_input = "#" + instance + "\\:" + loc_obj.ui.input.lost.id;

    var moved = parseFloat($(moved_input).val());
    var ret = parseFloat($(returned_input).val());
    if(isNaN(ret)) {
      ret = 0;
    }
    var don = parseFloat($(donated_input).val());
    if(isNaN(don)) {
      don = 0;
    }
    var lost = parseFloat($(lost_input).val());
    if(isNaN(lost)) {
      lost = 0;
    }
    /* Computing lent value. */
    var lent = moved - ret - don - lost;

    return lent;
  }

  /**
   * @brief     Checks if the current movement is concluded.
   *
   * @return    int the current lent value.
   */
  var pwwhMovementIsConcluded = function() {

    let loc_obj = pwwh_core_movement_comm_obj;

    let flag = true;

    /* Getting instances. */
    let instances_val = ($('#' + loc_obj.ui.input.collector.id).val());
    if(instances_val) {
      instances = instances_val.split(':');

      instances.forEach(function(instance) {
        if(pwwhMovementGetLent(instance) != 0) {
          flag = false;
        }
      });
    }
    return flag;
  }

  /**
   * @brief     Forces the status of the movement to active
   *
   * @return    void.
   */
  var pwwhMovementSetActive = function() {

    let loc_obj = pwwh_core_movement_comm_obj;
    let stauses_id = Object.keys(loc_obj.post.status);
    let active = stauses_id[1];

    /* Computing future label. */
    let status = pwwhCoreGetStatus(loc_obj.post.type);
    let label;
    if(status == 'concluded') {
      label = loc_obj.ui.button.publish.label.activate;
    }
    else if((status == 'draft') || (status == 'auto-draft')){
      label = loc_obj.ui.button.publish.label.confirm;
    }
    else {
      label = loc_obj.ui.button.publish.label.update
    }

    /* Updating the status and the publish button. */
    pwwhCoreChangeStatus(loc_obj.post.type, active, label);
  }

  /**
   * @brief     Forces the status of the movement to active
   *
   * @return    void.
   */
  var pwwhMovementSetConcluded = function() {

    let loc_obj = pwwh_core_movement_comm_obj;
    let stauses_id = Object.keys(loc_obj.post.status);
    let concluded = stauses_id[2];

    /* Computing future label. */
    let status = pwwhCoreGetStatus(loc_obj.post.type);
    let label;
    if(status == 'active') {
      label = loc_obj.ui.button.publish.label.conclude;
    }
    else if((status == 'draft') || (status == 'auto-draft')) {
      label = loc_obj.ui.button.publish.label.confirm;
    }
    else {
      label = loc_obj.ui.button.publish.label.update
    }

    /* Updating the status and the publish button. */
    pwwhCoreChangeStatus(loc_obj.post.type, concluded, label);
  }

  /**
   * @brief     Manages the status of the Movement.
   *
   * @return    void.
   */
  var pwwhMovementManageStatus = function() {

    let loc_obj = pwwh_core_movement_comm_obj;
    let stauses_id = Object.keys(loc_obj.post.status);
    let active = stauses_id[1];
    let concluded = stauses_id[2];

    /* Computing the current status. */
    let status = pwwhCoreGetStatus(loc_obj.post.type);
    let label = loc_obj.ui.button.publish.label.update;
    if(!pwwhMovementIsConcluded()) {
      pwwhMovementSetActive();
    }
    else if(pwwhMovementIsConcluded()) {
      pwwhMovementSetConcluded();
    }
    else {
      /* Nothing to do. */
    }
  }

  /**
   * @brief     Updates the Locations datalist depending on the value of the
   *            Item name.
   * @details   If the Item name is valid it fills the datalist with those
   *            location where the item availability is greater than 0.
   *            If the Item name is invalidvalid it fills the datalist with all
   *            the location entries.
   *
   * @param[in] int instance        The instance ID.
   *
   * @return void.
   */
  var pwwhMovementUpdateLocations = function(instance) {

    let loc_obj = pwwh_core_movement_comm_obj;

    var item_input = "#" + instance + "\\:" + loc_obj.ui.input.item.id;
    var location_input = "#" + instance + "\\:" + loc_obj.ui.input.location.id;
    var location_datalist = "#" + instance + "\\:" +
                            loc_obj.ui.input.location.datalist;
    var data = {
      url: loc_obj.ajax.url,
      type: 'POST',
      dataType: 'json',
      async: true,
      data: {
        action: loc_obj.ajax.action.upd_locs,
        item_title: function() {
          return $(item_input).val();
        },
        instance: function() {
          return instance;
        }
      },
      success: function(data) {
        if(data) {
          $(location_datalist).replaceWith(data);
        }
      }
    };
    $.ajax(data);
  }

/*===========================================================================*/
/* Application entry point.                                                  */
/*===========================================================================*/

  jQuery(document).ready(function($) {

  });
}