{
/*===========================================================================*/
/* Local Scope.                                                              */
/*===========================================================================*/

  /**
   * @brief   The localize object.
   */
  let loc_obj = pwwh_core_item_val_obj;

  /**
   * @brief     Initializes the validation on the Item form.
   *
   * @return    bool the operation status.
   */
  let pwwhItemValidateInit = function() {

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
   * @brief     Adds validation rules to a the Item title Box.
   *
   * @return    void.
   */
  let pwwhItemAddRulesToTitle = function() {

    let loc_obj = pwwh_core_item_val_obj;

    let title_input = "#" + loc_obj.ui.input.title.id;

    var cfg;

    cfg = {
      required: loc_obj.ui.input.title.rule.required,
      remote: {
        url: loc_obj.ui.input.title.rule.remote.url,
        type: 'POST',
        dataType: 'json',
        data: {
          action: loc_obj.ui.input.title.rule.remote.action,
          item_title: function(){
            return $("#title").val();
          },
          item_id: function(){
            return $("#post_ID").val();
          }
        }
      },
      messages: {
        required: loc_obj.ui.input.title.msg.required,
        remote: loc_obj.ui.input.title.msg.remote
      }
    };
    $(title_input).rules("add", cfg);
  }

/*===========================================================================*/
/* Global Scope.                                                             */
/*===========================================================================*/

/*===========================================================================*/
/* Application entry point.                                                  */
/*===========================================================================*/

  jQuery(function($){

    if($('#post_type').val() == loc_obj.post.type) {
      /* Initializing the validate. */
      if(pwwhItemValidateInit()) {
        /* Adding rules to Item title field. */
        pwwhItemAddRulesToTitle();
      }
    }
  });
}