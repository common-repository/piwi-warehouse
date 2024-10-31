{
/*===========================================================================*/
/* Local Scope.                                                              */
/*===========================================================================*/

  /**
   * @brief   The localize object.
   */
  let loc_obj = pwwh_core_movement_loc_val_obj;

  /**
   * @brief     Initializes the validation on the Holder tag form.
   *
   * @return    bool the operation status.
   */
  let pwwhHolderValidateInit = function() {

    /* Composing form Selector from the post type. */
    var tag_form = '.post-type-' + loc_obj.post.type +
                   '.taxonomy-' + loc_obj.post.taxonomy.holder + ' #addtag';

    /* Creating validate configuration for first instance. */
    var config = new Object();
    config.rules = new Object();
    config.messages = new Object();
    config.errorClass = "pwwh-invalid-field";

    /* Adding an empty configuration structure. */
    if($(tag_form).length) {
      $(tag_form).validate(config);
      return true;
    }
    else {
      return false;
    }
  }

  /**
   * @brief     Adds validation rules to a the Holder tag name Box.
   *
   * @return    void.
   */
  let pwwhMovementAddRulesToHolderTagName = function() {

    let tag_name_input = "#" + loc_obj.ui.input.holder.id;

    var cfg;

    cfg = {
      remote: {
        url: loc_obj.ui.input.holder.rule.remote.url,
        type: 'POST',
        dataType: 'json',
        data: {
          action: loc_obj.ui.input.holder.rule.remote.action,
          holder_tag_name: function(){
            return $(tag_name_input).val();
          }
        }
      },
      messages: {
        remote: loc_obj.ui.input.holder.msg.remote
      }
    };
    $(tag_name_input).rules("add", cfg);
  }

/*===========================================================================*/
/* Global Scope.                                                             */
/*===========================================================================*/

/*===========================================================================*/
/* Application entry point.                                                  */
/*===========================================================================*/

  jQuery(function($){

    let input = 'input[name=screen]';
    let screen = 'edit-' + loc_obj.post.taxonomy.holder;
    if($(input).val() == screen) {
      /* Initializing the validate. */
      if(pwwhHolderValidateInit()) {
        /* Adding rules to Holder tag name field. */
        pwwhMovementAddRulesToHolderTagName();
      }
    }
  });
}