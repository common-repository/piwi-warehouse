{
/*===========================================================================*/
/* Local Scope.                                                              */
/*===========================================================================*/

  /**
   * @brief   The localize object.
   */
  let loc_obj = pwwh_core_movement_val_obj;

  /**
   * @brief     Initializes the validation on the Movement form.
   *
   * @return    bool the operation status.
   */
  let pwwhMovementValidateInit = function() {

    /* Composing form Selector from the post type. */
    var post_form = '.post-type-' + loc_obj.post.type + ' #post';

    /* Creating validate configuration for first instance. */
    var config = new Object();
    config.rules = new Object();
    config.messages = new Object();
    config.errorClass = "pwwh-invalid-field";

    /* Adding an empty configuration structure. */
    if($(post_form).length) {
      $(post_form).validate(config);
      return true;
    }
    else {
      return false;
    }
  }

  /**
   * @brief     Adds validation rules to the holder box.
   *
   * @return    void.
   */
  let pwwhMovementValidateAddRulesToHolderBox = function() {

    var holder_id = "#" + loc_obj.ui.input.holder.id;
    var cfg;

    cfg = {
      required: loc_obj.ui.input.holder.rule.required,
      remote: {
        url: loc_obj.ui.input.holder.rule.remote.url,
        type: 'POST',
        dataType: 'json',
        data: {
          action: loc_obj.ui.input.holder.rule.remote.action,
          holder_name: function(){
            return $(holder_id).val();
          }
        }
      },
      messages: {
        required: loc_obj.ui.input.holder.msg.required,
        remote: loc_obj.ui.input.holder.msg.remote
      }
    };
    $(holder_id).rules("add", cfg);
  }

/*===========================================================================*/
/* Global Scope.                                                             */
/*===========================================================================*/

  /**
   * @brief     Triggers the validation of the location-item couple
   * @details   Only those couple that are not empty are validated
   *
   * @return    void.
   */
  var pwwhMovementValidateItemLocation = function() {

    /* Updating instance collector. */
    let istances = $('#' + loc_obj.ui.input.collector.id).val().split(':');
    $.each(istances, function(index, instance) {
      let item_input = "#" + instance + "\\:" + loc_obj.ui.input.item.id;
      let location_input = "#" + instance + "\\:" + loc_obj.ui.input.location.id;

      /* Triggering the validation if both the input are populated. */
      if($(item_input).val() && $(location_input).val()) {
        $(item_input).valid();
        $(location_input).valid();
      }
    });
  }

  /**
   * @brief     Triggers the validation of the moved field.
   * @details   Only moved associated to a non-empty location-item are validated.
   *
   * @param[in] int instance        The instance ID.
   *
   * @return    void.
   */
  var pwwhMovementValidateMoved = function(instance) {

    let item_input = "#" + instance + "\\:" + loc_obj.ui.input.item.id;
    let location_input = "#" + instance + "\\:" + loc_obj.ui.input.location.id;
    let moved_input = "#" + instance + "\\:" + loc_obj.ui.input.moved.id;

    if($(item_input).val() && $(location_input).val() && $(moved_input).val()) {
      $(moved_input).valid();
    }
  }

  /**
   * @brief     Adds validation rules to a generic Item Box.
   * @details   This is used on dynamic insertion of Item Boxes.
   *
   * @param[in] int instance        The instance ID.
   *
   * @return    void.
   */
  var pwwhMovementValidateAddRulesToItemBox = function(instance) {

    let item_input = "#" + instance + "\\:" + loc_obj.ui.input.item.id;
    let location_input = "#" + instance + "\\:" + loc_obj.ui.input.location.id;
    let moved_input = "#" + instance + "\\:" + loc_obj.ui.input.moved.id;

    var cfg;

    cfg = {
      required: loc_obj.ui.input.item.rule.required,
      checkDuplicate: [loc_obj.ui.input.collector.id, loc_obj.ui.input.item.id,
                       loc_obj.ui.input.location.id, instance],
      remote: {
        url: loc_obj.ui.input.item.rule.remote.url,
        type: 'POST',
        dataType: 'json',
        data: {
          action: loc_obj.ui.input.item.rule.remote.action,
          item_title: function(){
            return $(item_input).val();
          }
        }
      },
      messages: {
        required: loc_obj.ui.input.item.msg.required,
        checkDuplicate: loc_obj.ui.input.item.msg.check_duplicate,
        remote: loc_obj.ui.input.item.msg.remote
      }
    };
    $(item_input).rules("add", cfg);

    $(item_input).on('input', function() {
      pwwhMovementValidateItemLocation();
      pwwhMovementValidateMoved(instance);
      pwwhMovementUpdateLocations(instance);
    });

    cfg = {
      required: loc_obj.ui.input.location.rule.required,
      checkDuplicate: [loc_obj.ui.input.collector.id, loc_obj.ui.input.item.id,
                       loc_obj.ui.input.location.id, instance],
      remote: {
        url: loc_obj.ui.input.location.rule.remote.url,
        type: 'POST',
        dataType: 'json',
        data: {
          action: loc_obj.ui.input.location.rule.remote.action,
          loc_name: function(){
            return $(location_input).val();
          }
        }
      },
      messages: {
        required: loc_obj.ui.input.location.msg.required,
        checkDuplicate: loc_obj.ui.input.location.msg.check_duplicate,
        remote: loc_obj.ui.input.location.msg.remote
      }
    };
    $(location_input).rules("add", cfg);

    $(location_input).on('input', function() {
      pwwhMovementValidateItemLocation();
      pwwhMovementValidateMoved(instance);
    });

    cfg = {
      required: loc_obj.ui.input.moved.rule.required,
      number: loc_obj.ui.input.moved.rule.number,
      min: loc_obj.ui.input.moved.rule.min,
      step: loc_obj.ui.input.moved.rule.step,
      remote: {
        url: loc_obj.ui.input.moved.rule.remote.url,
        type: 'POST',
        dataType: 'json',
        data: {
          action: loc_obj.ui.input.moved.rule.remote.action,
          item_title: function(){
            return $(item_input).val();
          },
          loc_name: function(){
            return $(location_input).val();
          },
          moved: function(){
            return $(moved_input).val();
          }
        }
      },
      messages: {
        required: loc_obj.ui.input.moved.msg.required,
        number: loc_obj.ui.input.moved.msg.number,
        min: loc_obj.ui.input.moved.msg.min,
        step:  loc_obj.ui.input.moved.msg.step,
        remote: loc_obj.ui.input.moved.msg.remote
      }
    };
    $(moved_input).rules("add", cfg);

    $(moved_input).on('input', function() {
      pwwhMovementValidateMoved(instance);
    });
  }

  /**
   * @brief     Removes validation rules to a generic Item Box.
   * @details   This is used on dynamic deletion of Item Boxes.
   *
   * @param[in] int instance        The instance ID.
   *
   * @return    void.
   */
  var pwwhMovementValidateRemoveRulesToItemBox = function(instance) {

    let item_input = "#" + instance + "\\:" + loc_obj.ui.input.item.id;
    $(item_input).rules("remove");
    $(item_input).off('input');

    let location_input = "#" + instance + "\\:" + loc_obj.ui.input.location.id;
    $(location_input).rules("remove");
    $(location_input).off('input');

    let moved_input = "#" + instance + "\\:" + loc_obj.ui.input.moved.id;
    $(moved_input).rules("remove");
    $(moved_input).off('input');
  }

  /**
   * @brief     Triggers the validation on all the input boxes that are not
   *            empty.
   * @details   This is used on dynamic deletion of Item Boxes.
   *
   * @param[in] int instance        The instance ID.
   *
   * @return    void.
   */
  var pwwhMovementValidateQuantities = function(instance) {

    let returned_input = "#" + instance + "\\:" + loc_obj.ui.input.returned.id;
    let donated_input = "#" + instance + "\\:" + loc_obj.ui.input.donated.id;
    let lost_input = "#" + instance + "\\:" + loc_obj.ui.input.lost.id;

    if($(returned_input).length && $(donated_input).length &&
       $(lost_input).length) {

      /* Cross checking all the inputs. */
      $(returned_input).valid();
      $(donated_input).valid();
      $(lost_input).valid();
    }
  }

  /**
   * @brief     Adds validation rules to a generic Movement operations box.
   *
   * @param[in] int instance        The instance ID.
   *
   * @return    void.
   */
  var pwwhMovementValidateAddRulesToOperations = function(instance) {

    let moved_input = "#" + instance + "\\:" + loc_obj.ui.input.moved.id;
    let returned_input = "#" + instance + "\\:" + loc_obj.ui.input.returned.id;
    let donated_input = "#" + instance + "\\:" + loc_obj.ui.input.donated.id;
    let lost_input = "#" + instance + "\\:" + loc_obj.ui.input.lost.id;

    var cfg;
    cfg = {
      checkEquation: [moved_input, returned_input, donated_input, lost_input],
      min: loc_obj.ui.input.returned.rule.min,
      step: loc_obj.ui.input.returned.rule.step,
      messages: {
        checkEquation: loc_obj.ui.input.returned.msg.check_eq,
        min: loc_obj.ui.input.returned.msg.min,
        step: loc_obj.ui.input.returned.msg.step,
      }
    };
    $(returned_input).rules("add", cfg);

    $(returned_input).on('input', function() {
      pwwhMovementValidateQuantities(instance);
    });

    cfg = {
      checkEquation: [moved_input, returned_input, donated_input, lost_input],
      min: loc_obj.ui.input.donated.rule.min,
      step: loc_obj.ui.input.donated.rule.step,
      messages: {
        checkEquation: loc_obj.ui.input.donated.msg.check_eq,
        min: loc_obj.ui.input.donated.msg.min,
        step: loc_obj.ui.input.donated.msg.step,
      }
    };
    $(donated_input).rules("add", cfg);

    $(donated_input).on('input', function() {
      pwwhMovementValidateQuantities(instance);
    });

    cfg = {
      checkEquation: [moved_input, returned_input, donated_input, lost_input],
      min: loc_obj.ui.input.lost.rule.min,
      step: loc_obj.ui.input.lost.rule.step,
      messages: {
        checkEquation: loc_obj.ui.input.lost.msg.check_eq,
        min: loc_obj.ui.input.lost.msg.min,
        step: loc_obj.ui.input.lost.msg.step,
      }
    };
    $(lost_input).rules("add", cfg);

    $(lost_input).on('input', function() {
      pwwhMovementValidateQuantities(instance);
    });
  }

/*===========================================================================*/
/* Application entry point.                                                  */
/*===========================================================================*/

  jQuery(function($){

    if($('#post_type').val() == loc_obj.post.type) {
      /* Initializing the validate. */
      if(pwwhMovementValidateInit()) {
        /* Adding validation rules to the Holder Box. */
        pwwhMovementValidateAddRulesToHolderBox();

        /* Adding rules to the initial static box. */
        pwwhMovementValidateAddRulesToItemBox(0);

        /* Appending rule to all the option boxes. */
        var collector = loc_obj.ui.input.collector.id;
        var instances_val = ($('#' + collector).val());
        if(instances_val) {
          instances = instances_val.split(':');
          instances.forEach(function(instance) {
            pwwhMovementValidateAddRulesToOperations(instance);
          });
        }
      }
    }
  });
}