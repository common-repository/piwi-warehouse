{
/*===========================================================================*/
/* Local Scope.                                                              */
/*===========================================================================*/

  /**
   * @brief   The localize object.
   */
  let loc_obj = pwwh_core_purchase_val_obj;

  /**
   * @brief     Initializes the validation on the Purchase form.
   *
   * @return    bool the operation status.
   */
  let pwwhPurchaseValidateInit = function() {

    /* Composing form Selector from the post type. */
    var post_form = '.post-type-' + loc_obj.post.type + ' #post';

    /* Creating validate configuration for first instance. */
    var config = new Object();
    config.rules = new Object();
    config.messages = new Object();
    config.errorClass = "pwwh-invalid-field";

    /* Adding validation to purchase targeting instance #0. */
    if($(post_form).length) {
      $(post_form).validate(config);
      return true;
    }
    else {
      return false;
    }
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
  var pwwhPurchaseValidateItemLocation = function() {

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
   * @brief     Adds validation rules to a generic Item Box.
   * @details   This is used on dynamic insertion of Item Boxes.
   *
   * @param[in] int instance        The instance ID.
   *
   * @return    void.
   */
  var pwwhPurchaseValidateAddRulesToItemBox = function(instance) {

    let item_input = "#" + instance + "\\:" + loc_obj.ui.input.item.id;
    let location_input = "#" + instance + "\\:" + loc_obj.ui.input.location.id;
    let quantity_input = "#" + instance + "\\:" + loc_obj.ui.input.quantity.id;

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
      pwwhPurchaseValidateItemLocation();
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
      pwwhPurchaseValidateItemLocation();
    });

    cfg = {
      required: loc_obj.ui.input.quantity.rule.required,
      number: loc_obj.ui.input.quantity.rule.number,
      notEqual: loc_obj.ui.input.quantity.rule.not_equal,
      step: loc_obj.ui.input.quantity.rule.step,
      messages: {
        required: loc_obj.ui.input.quantity.msg.required,
        number: loc_obj.ui.input.quantity.msg.number,
        notEqual: loc_obj.ui.input.quantity.msg.not_equal,
        step:  loc_obj.ui.input.quantity.msg.step
      }
    };
    $(quantity_input).rules("add", cfg);
  }

  /**
   * @brief     Removes validation rules to a generic Item Box.
   * @details   This is used on dynamic deletion of Item Boxes.
   *
   * @param[in] int instance        The instance ID.
   *
   * @return    void.
   */
  var pwwhPurchaseValidateRemoveRulesToItemBox = function(instance) {

    let item_input = "#" + instance + "\\:" + loc_obj.ui.input.item.id;
    $(item_input).rules("remove");
    $(item_input).off('input');

    let location_input = "#" + instance + "\\:" + loc_obj.ui.input.location.id;
    $(location_input).rules("remove");
    $(location_input).off('input');

    let quantity_input = "#" + instance + "\\:" + loc_obj.ui.input.quantity.id;
    $(quantity_input).rules("remove");
  }

/*===========================================================================*/
/* Application entry point.                                                  */
/*===========================================================================*/

  jQuery(document).ready(function($) {

    if($('#post_type').val() == loc_obj.post.type) {
      /* Initializing the validate. */
      if(pwwhPurchaseValidateInit()) {
        /* Adding rules to the initial static box. */
        pwwhPurchaseValidateAddRulesToItemBox(0);
      }
    }
  });
}