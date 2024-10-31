{
/*===========================================================================*/
/* Local Scope.                                                              */
/*===========================================================================*/

/*===========================================================================*/
/* Global Scope.                                                             */
/*===========================================================================*/

  /**
   * @brief     Check if this field is not equal to a comparison value.
   * @param[in] mixed value         The value of the checking element (assigned
   *                                by plugin).
   * @param[in] mixed element     The checking element (assigned by plugin).
   * @param[in] mixed compare     The comparison value
   *
   * @return    bool the validity of this field.
   */
  var pwwhCoreValidateNotEqual = function (value, element, compare) {
    return (this.optional(element) || (value != compare));
  }

  /**
   * @brief     Check if this item is duplicated or not.
   * @param[in] mixed value       The value of the checking element (assigned
   *                              by plugin).
   * @param[in] mixed element     The checking element (assigned by plugin).
   * @param[in] array param       An array of parameters.
   * @paramkey[0]                 The collector input identifier.
   * @paramkey[1]                 The item input identifier.
   * @paramkey[2]                 The current instance value.
   *
   * @return    bool the validity of this field.
   */
  var pwwhCoreValidateCheckDuplicate = function(value, element, param) {

    var collector_id = param[0];
    var item_id = param[1];
    var loc_id = param[2];
    var curr_inst = param[3];
    if(Number.isInteger(curr_inst)) {
      curr_inst = curr_inst.toString();
    }
    var collector = $('#' + collector_id).val();
    var instances = collector.split(':');
    if(!$.isArray(instances)) {
      instances = [instances];
    }
    var index = instances.indexOf(curr_inst);
    if(index > -1) {
      instances.splice(index, 1);
    }
    var curr_item_input = "#" + curr_inst + "\\:" + item_id;
    var curr_loc_input = "#" + curr_inst + "\\:" + loc_id;

    try {
      instances.forEach(function(inst) {
        var item_input = "#" + inst + "\\:" + item_id;
        var item_value = $(item_input).val();
        var loc_input = "#" + inst + "\\:" + loc_id;
        var loc_value = $(loc_input).val();

        var curr_item = $(curr_item_input).val();
        var curr_loc = $(curr_loc_input).val();
        if((curr_item == item_value) &&
           (curr_loc == loc_value)) {
          throw(inst);
        }
      });
    }
    catch(inst) {
      return false;
    }

    return true;
  }

  /**
   * @brief     Check if the equation (ret + don + lost <= moved) is respected.
   * @param[in] mixed value       The value of the checking element (assigned
   *                              by plugin).
   * @param[in] mixed element     The checking element (assigned by plugin).
   * @param[in] array param       An array of parameters.
   * @paramkey[0]                 The moved input identifier.
   * @paramkey[1]                 The returned input identifier.
   * @paramkey[2]                 The donated input identifier.
   * @paramkey[3]                 The lost input identifier.
   *
   * @return    bool the validity of this field.
   */
  var pwwhCoreValidateCheckEquation = function(value, element, param) {
    var moved_input = param[0];
    var ret_input = param[1];
    var don_input = param[2];
    var lost_input = param[3];

    var moved = parseFloat($(moved_input).val());
    if(isNaN(moved)) {
      moved = 0;
    }
    var ret = parseFloat($(ret_input).val());
    if(isNaN(ret)) {
      ret = 0;
    }
    var don = parseFloat($(don_input).val());
    if(isNaN(don)) {
      don = 0;
    }
    var lost = parseFloat($(lost_input).val());
    if(isNaN(lost)) {
      lost = 0;
    }
    if((ret + don + lost) > moved)
      return false;
    else
      return true;
  }

/*===========================================================================*/
/* Application entry point.                                                  */
/*===========================================================================*/

  jQuery(function($){

    /* Adding custom methods. */
    $.validator.addMethod("notEqual", pwwhCoreValidateNotEqual);
    $.validator.addMethod("checkDuplicate", pwwhCoreValidateCheckDuplicate);
    $.validator.addMethod("checkEquation", pwwhCoreValidateCheckEquation);
  });
}