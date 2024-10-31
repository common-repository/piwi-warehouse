{
/*===========================================================================*/
/* Local Scope.                                                              */
/*===========================================================================*/

  /**
   * @brief   The localize object.
   */
  let loc_obj = pwwh_core_item_loc_val_obj;

  /**
   * @brief     Initializes the validation on the Location tag form.
   *
   * @return    bool the operation status.
   */
  let pwwhLocationValidateInit = function() {

    /* Composing form Selector from the post type. */
    var tag_form = '.post-type-' + loc_obj.post.type +
                   '.taxonomy-' + loc_obj.post.taxonomy.location + ' #addtag';

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
   * @brief     Adds validation rules to a the Location tag name Box.
   *
   * @return    void.
   */
  let pwwhItemAddRulesToLocationTagName = function() {

    let tag_name_input = "#" + loc_obj.ui.input.location.id;

    var cfg;

    cfg = {
      remote: {
        url: loc_obj.ui.input.location.rule.remote.url,
        type: 'POST',
        dataType: 'json',
        data: {
          action: loc_obj.ui.input.location.rule.remote.action,
          loc_tag_name: function(){
            return $(tag_name_input).val();
          }
        }
      },
      messages: {
        remote: loc_obj.ui.input.location.msg.remote
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
    let screen = 'edit-' + loc_obj.post.taxonomy.location;
    if($(input).val() == screen) {
      /* Initializing the validate. */
      if(pwwhLocationValidateInit()) {
        /* Adding rules to Location tag name field. */
        pwwhItemAddRulesToLocationTagName();
      }
    }
  });
}