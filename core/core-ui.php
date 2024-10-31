<?php
/**
 * @file      core/core-ui.php
 * @brief     Common User Interface.
 *
 * @addtogroup PWWH_CORE
 * @{
 */

/**
 * @brief     Creates a postbox containing the title.
 *
 * @param[in] mixed $post         A Post object or the Post ID
 * @param[in] boolean $echo       Echoes on true
 *
 * @return    string the wrapped title as HTML.
 */
function pwwh_core_ui_post_title($post = null, $echo = true) {

  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }

  $output = '<div id="titlediv">
              <div class="pwwh-core-title">
                <h1>' . get_the_title($post) . '</h1>
              </div>
            </div>';

  if($echo) {
    echo $output;
  }
  else {
    return $output;
  }
}

/**
 * @brief     Returns the taxonomy list of this item.
 * @api
 *
 * @param[in] string $tax         The taxonomy name.
 * @param[in] mixed $post         The item as Post object or Post ID
 * @param[in] array $args         An array of arguments.
 * @paramkey{separator}           The taxonomy separator. @default{' > '}
 * @paramkey{linked}              Add edit links if true. @default{false}
 * @paramkey{echo}                Echoes on true. @default{true}
 *
 * @return    mixed the UI or void depending on echo.
 */
function pwwh_core_ui_tax_list($tax, $post = null, $args = array()) {

  /* Validating arguments array. */
  $separator = pwwh_lib_utils_validate_array_field($args, 'separator', ' > ',
                                                   array(), 'string');
  $linked = pwwh_lib_utils_validate_array_field($args, 'linked', false, array(),
                                                'boolean');
  $echo = pwwh_lib_utils_validate_array_field($args, 'echo', true, array(),
                                              'boolean');

  /* Cheching Post consistency. */
  if(!is_a($post, 'WP_Post')) {
    $post = get_post($post);
  }

  /* Retriving post taxs. */
  $taxs = pwwh_core_api_get_taxs($tax, $post->ID);

  if(count($taxs)) {
    $_list = '';
    $separator = '<span class="separator">' . $separator . '</span>';
    foreach($taxs as $sub_taxs) {

      /* Defining utils variables. */
      $len = count($sub_taxs);
      $counter = 1;
      $inner = '';
      foreach($sub_taxs as $elem) {

        /* Composing inner. */
        if($linked) {
          $inner .= '<span class="element">
                      <a href="'. $elem['edit_url'] . '"
                         title="' . $elem['name'] . '">' .
                        $elem['name'] .
                      '</a>
                    </span>';
        }
        else {
          $inner .= '<span class="element">' .
                      $elem['name'] .
                    '</span>';
        }

        /* Deciding whether adding separator or not. */
        if($counter != $len)
          $inner .= $separator;
        $counter++;
      }

      /* Adding inner to output. */
      $_list .= '<div class="' . $tax . '">
                  <span class="elements">' .
                    $inner .
                  '</span>
                </div>';
    }
  }
  else {
    $_list = '&mdash;';
  }

  /* Deciding whether echo or not. */
  if($echo) {
    echo $_list;
  }
  else {
    return $_list;
  }
}

/**
 * @brief     Returns the taxonomy list of this item.
 * @api
 *
 * @param[in] string $action      The action of the nonce.
 * @param[in] mixed $post         A Post object or a Post ID.
 * @param[in] bool $echo          Echoes on true.
 *
 * @return    mixed the UI or void depending on echo.
 */
function pwwh_core_ui_nonce($action, $post = null, $echo = true) {

  /* Generating Nonce input box. */
  $args = array('type' => 'hidden',
                'id' => $action,
                'value' => pwwh_core_api_create_nonce($action, $post),
                'echo' => true);
  pwwh_lib_ui_form_input($args);
}
/** @} */