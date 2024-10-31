<?php
/**
 * @file    lib/ui/ui-admin.php
 * @brief   This file contains a set of functions used to generate admin pages.
 *
 * @addtogroup PWWH_LIB_UI_ADMIN
 * @{
 */

/**
 * @brief     Wraps a main title for an admin page.
 *
 * @param[in] string $title       The title
 * @param[in] boolean $echo       Echoes on true
 *
 * @return    mixed string if echo is false or void.
 */
function pwwh_lib_ui_admin_page_title($title, $echo = false) {

  $output = '<h1 class="wp-heading-inline">' . $title . '</h1>';
  if($echo)
    echo $output;
  else
    return $output;
}

/**
 * @brief     Wraps a main title for an admin page.
 *
 * @param[in] string $title       The title
 * @param[in] boolean $echo       Echoes on true
 *
 * @return    mixed string if echo is false or void.
 */
function pwwh_lib_ui_admin_page_section_title($title, $echo = false) {

  $output = '<h2 class="title">' . $title . '</h2>';
  if($echo)
    echo $output;
  else
    return $output;
}

/**
 * @brief     Wraps a paragraph title for an admin page
 *
 * @param[in] string $title       The title
 * @param[in] boolean $echo       Echoes on true
 *
 * @return    mixed string if echo is false or void.
 */
function pwwh_lib_ui_admin_pararaph_title($title, $echo = false) {

  $output = '<h2 class="wp-heading-inline">' . $title . '</h2>';
  if($echo)
    echo $output;
  else
    return $output;
}

/**
 * @brief     Returns a chunk of information organized as identifier an value.
 *
 * @param[in] array $data         An array of data used to generate the chunk.
 * @paramkey{description}         The chunk description (as instance 'Fruit').
 * @paramkey{icon}                The Dashicon identifier.
 * @paramkey{value}               The specific value of this chunk (as instance
 *                                'Banana').
 * @paramkey{id}                  A string representing the ID. @default{empty}
 * @paramkey{class}               A string or an array of strings.
 *                                @default{empty}
 * @paramkey{link}                The link to banana page. @default{empty}
 * @paramkey{target}              The target of the link. @default{empty}
 * @paramkey{before}              An hook to add code before the icon.
 *                                @default{empty}
 * @paramkey{after}               An hook to add code after the value.
 *                                @default{empty}
 * @paramkey{cap}                 The capability required to see this chunk.
 *                                @default{read}
 * @param[in] boolean $echo       Echoes on true
 *
 * @return    string the wrapped title as HTML.
 */
function pwwh_lib_ui_admin_info_chunk($data = array(), $echo = false) {

  $description = pwwh_lib_utils_validate_array_field($data, 'description', null);
  $value = pwwh_lib_utils_validate_array_field($data, 'value', null);
  $icon = pwwh_lib_utils_validate_array_field($data, 'icon', null);
  $id = pwwh_lib_utils_validate_array_field($data, 'id', null);
  $class = pwwh_lib_utils_validate_array_field($data, 'class', null);
  $link = pwwh_lib_utils_validate_array_field($data, 'link', null);
  $target = pwwh_lib_utils_validate_array_field($data, 'target', null);
  $before = pwwh_lib_utils_validate_array_field($data, 'before', null);
  $after = pwwh_lib_utils_validate_array_field($data, 'after', null);
  $cap = pwwh_lib_utils_validate_array_field($data, 'cap', 'read');

  if(current_user_can($cap)) {

    $id = pwwh_lib_ui_form_attribute('id', $id);
    $target = pwwh_lib_ui_form_attribute('target', $target);

    if(!is_array($class))
      $class = explode(' ', $class);

    array_push($class, 'pwwh', 'pwwh-lib-info-chunk');

    if($link) {
      $link = esc_url($link);
      $title = esc_attr($value);
      $value = '<a href="' . $link . '"' . $target . '
                   title="' . $title . '">' .
                  $value .
               '</a>';
    }

    if($icon) {
      $icon = '<span class="pwwh-lib-icon ' . esc_attr($icon). '"></span>';
      array_push($class, 'has-icon');
    }

    $text = '';
    if($description) {
      $text = '<span class="pwwh-lib-text">' . $description . '</span>';
      array_push($class, 'has-description');
    }

    $desc = '';
    if($icon || $description) {
      $desc = '<span class="pwwh-lib-description">' .
                  $icon .
                  $text .
              '</span>';
    }

    $class = pwwh_lib_ui_form_attribute('class', trim(implode(' ', $class)));

    $output = '<span' . $id . $class . '>' .
                $before .
                $desc .
                '<span class="pwwh-lib-value">' . $value . '</span>' .
                $after .
              '</span>';
  }
  else {
    $output = '';
  }

  if($echo)
    echo $output;
  else
    return $output;
}
/** @} */