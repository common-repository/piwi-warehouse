<?php
/**
 * @file    lib/ui/ui-form.php
 * @brief   This file contains a set of functions used to generate forms.
 *
 * @addtogroup PWWH_LIB_UI_FORM
 * @{
 */

/**
 * @brief     Creates an HTML attribute if is not empty.
 *
 * @param[in] mixed $attribute   the attribute name.
 * @param[in] mixed $value       the attribute value.
 * @param[in] bool $echo         echoes on true.
 *
 * @return    mixed string if echo is false or void.
 */
function pwwh_lib_ui_form_attribute($attribute, $value, $echo = false) {

  if(is_bool($value) || is_integer($value) || is_double($value) ||
     is_string($value)) {
    $output = ' ' . $attribute . '="' . esc_attr($value) . '"';
  }
  else {
    $output = '';
  }

  if($echo)
    echo $output;
  else
    return $output;
}

/**
 * @brief     Checks if the parameters are equal in that case prints a string.
 *
 * @param[in] mixed $par1        first value to check.
 * @param[in] mixed $par2        second value to check.
 * @param[in] string $string     string to print
 * @param[in] bool $echo         echoes on true.
 *
 * @return    mixed string if echo is false or void.
 */
function pwwh_lib_ui_form_helper($par1, $par2, $string, $echo) {
  if(($par1 == $par2) && $echo)
    echo $string;
  else if(($par1 == $par2))
    return $string;
}

/**
 * @brief     Checks if the parameters are equal in that case generated
 *            selected attribute.
 *
 * @param[in] mixed $par1        first value to check.
 * @param[in] mixed $par2        second value to check.
 * @param[in] bool $echo         echoes on true.
 *
 * @return    mixed the attribute if echo is false or void.
 */
function pwwh_lib_ui_form_selected($par1, $par2, $echo = false) {
  $attr = pwwh_lib_ui_form_attribute('selected', 'selected');
  return pwwh_lib_ui_form_helper($par1, $par2, $attr, $echo);
}

/**
 * @brief     Checks if the parameters are equal in that case generated
 *            checked attribute.
 *
 * @param[in] mixed $par1        first value to check.
 * @param[in] mixed $par2        second value to check.
 * @param[in] bool $echo         echoes on true.
 *
 * @return    mixed the attribute if echo is false or void.
 */
function pwwh_lib_ui_form_checked($par1, $par2 = true, $echo = false) {
  $attr = pwwh_lib_ui_form_attribute('checked', 'checked');
  return pwwh_lib_ui_form_helper($par1, $par2, $attr, $echo);
}

/**
 * @brief     Checks if the parameters are equal in that case generated
 *            disabled attribute.
 *
 * @param[in] mixed $par1        second value to check.
 * @param[in] mixed $par2        second value to check.
 * @param[in] bool  $echo        must echo?
 *
 * @return    mixed the attribute if echo is false or void.
 */
function pwwh_lib_ui_form_disabled($par1, $par2 = true, $echo = false) {
  $attr = pwwh_lib_ui_form_attribute('disabled', 'disabled');
  return pwwh_lib_ui_form_helper($par1, $par2, $attr, $echo);
}

/**
 * @brief     Checks if the parameters are equal in that case generated
 *            readonly attribute.
 *
 * @param[in] mixed $par1        second value to check.
 * @param[in] mixed $par2        second value to check.
 * @param[in] bool  $echo        must echo?
 *
 * @return    mixed the attribute if echo is false or void.
 */
function pwwh_lib_ui_form_readonly($par1, $par2 = true, $echo = false) {
  $attr = pwwh_lib_ui_form_attribute('readonly', 'readonly');
  return pwwh_lib_ui_form_helper($par1, $par2, $attr, $echo);
}

/**
 * @brief     Checks if the parameters are equal in that case generated
 *            required attribute.
 *
 * @param[in] mixed $par1        second value to check.
 * @param[in] mixed $par2        second value to check.
 * @param[in] bool  $echo        must echo?
 *
 * @return    mixed the attribute if echo is false or void.
 */
function pwwh_lib_ui_form_required($par1, $par2 = true, $echo = false) {
  $attr = pwwh_lib_ui_form_attribute('required', 'required');
  return pwwh_lib_ui_form_helper($par1, $par2, $attr, $echo);
}

/**
/**
 * @brief     Checks if the parameters are equal in that case generated
 *            hidden attribute.
 *
 * @param[in] mixed $par1        first value to check.
 * @param[in] mixed $par2        second value to check.
 * @param[in] bool $echo         echoes on true.
 *
 * @return    mixed the attribute if echo is false or void.
 */
function pwwh_lib_ui_form_hidden($par1, $par2 = false, $echo = false) {
  $attr = pwwh_lib_ui_form_attribute('hidden', 'hidden');
  return pwwh_lib_ui_form_helper($par1, $par2, $attr, $echo);
}

/**
 * @brief     Checks if the parameters are equal in that case generated
 *            autocomplete attribute.
 *
 * @param[in] mixed $par1        second value to check.
 * @param[in] mixed $par2        second value to check.
 * @param[in] bool  $echo        must echo?
 *
 * @return    mixed the attribute if echo is false or void.
 */
function pwwh_lib_ui_form_autocomplete($par1, $par2 = true, $echo = false) {
  $attr = pwwh_lib_ui_form_attribute('autocomplete', 'true');
  return pwwh_lib_ui_form_helper($par1, $par2, $attr, $echo);
}

/**
 * @brief     Creates a data-list starting an array of data.
 * @details   Data could be composed by sub-arrays or items: in that case
 *            $fillwith must be specified and it would represent the
 *            key/fieldname of the single sub-array/item.
 * @note      This function must not be used. See pwwh_lib_ui_form_input() instead.
 *
 * @param[in] array $data         An array of data.
 * @param[in] string $id          The datalist id.
 * @param[in] string $fillwith    The key/fieldname of the array/item.
 *
 * @return    the datalist as HTML string or FALSE.
 *
 * @notapi
 */
function pwwh_lib_ui_form_datalist($data, $id, $fillwith = '') {
  $output = '<datalist id="' . esc_attr($id) . '">';
  foreach($data as $item) {
    if(is_array($item))
      $output .= '<option value="' . esc_attr($item[$fillwith]) . '">';
    else if(is_object($item))
      $output .= '<option value="' . esc_attr($item->$fillwith) . '">';
    else
      $output .= '<option value="' . esc_attr($item) . '">';
  }
  $output .= '</datalist>';
  return $output;
}

/**
 * @brief     Creates an HTML input according to parameters.
 *
 * @param[in] array $args         An array of arguments to compose the input.
 * @paramkey{id}                  The input ID.
 * @paramkey{type}                The input type. @default{text}
 * @paramkey{classes}             An array of classes or a string.
 *                                @default{false}
 * @paramkey{icon}                The input icon. @default{false}
 * @paramkey{label}               The input label. @default{false}
 * @paramkey{name}                The input name. @default{same of ID}
 * @paramkey{placeholder}         The input placeholder. @default{false}
 * @paramkey{value}               The input value. @default{false}
 * @paramkey{disabled}            If true, the input is disabled.
 *                                @default{false}
 * @paramkey{readonly}            If true, the input is readonly.
 *                                @default{false}
 * @paramkey{required}            If true, the input is required.
 *                                @default{false}
 * @paramkey{size}                The size attribute. @default{false}
 * @paramkey{maxlenght}           The maxlenght attribute. @default{false}
 * @paramkey{autocomplete}        The autocomplete attribute. @default{false}
 * @paramkey{min}                 The min attribute. @default{false}
 * @paramkey{max}                 The max attribute. @default{false}
 * @paramkey{step}                The step attribute. @default{false}
 * @paramkey{dl-data}             See pwwh_lib_ui_form_datalist() documentation.
 *                                @default{false}
 * @paramkey{dl-id}               See pwwh_lib_ui_form_datalist() documentation.
 *                                @default{false}
 * @paramkey{dl-fillwith}         See pwwh_lib_ui_form_datalist() documentation.
 *                                @default{false}
 * @paramkey{echo}                Echoes if true return elsewhere.
 *                                @default{false}
 *
 * @return    mixed the list as HTML string or FALSE.
 */
function pwwh_lib_ui_form_input($args = array()) {
  /* Validating array keys. */
  $id = pwwh_lib_utils_validate_array_field($args, 'id', null);
  $type = pwwh_lib_utils_validate_array_field($args, 'type', 'text');
  $classes = pwwh_lib_utils_validate_array_field($args, 'classes', null);
  $icon = pwwh_lib_utils_validate_array_field($args, 'icon', null);
  $label = pwwh_lib_utils_validate_array_field($args, 'label', null);
  $name = pwwh_lib_utils_validate_array_field($args, 'name', $id);
  $placeholder = pwwh_lib_utils_validate_array_field($args, 'placeholder', null);
  $value = pwwh_lib_utils_validate_array_field($args, 'value', '');
  $disabled = pwwh_lib_utils_validate_array_field($args, 'disabled', false);
  $readonly = pwwh_lib_utils_validate_array_field($args, 'readonly', false);
  $required = pwwh_lib_utils_validate_array_field($args, 'required', false);
  $size = pwwh_lib_utils_validate_array_field($args, 'size', null);
  $maxlenght = pwwh_lib_utils_validate_array_field($args, 'maxlenght', null);
  $autocomplete = pwwh_lib_utils_validate_array_field($args, 'autocomplete');
  $min = pwwh_lib_utils_validate_array_field($args, 'min', null);
  $max = pwwh_lib_utils_validate_array_field($args, 'max', null);
  $step = pwwh_lib_utils_validate_array_field($args, 'step', null);
  $dl_data = pwwh_lib_utils_validate_array_field($args, 'dl-data');
  $dl_id = pwwh_lib_utils_validate_array_field($args, 'dl-id', false);
  $dl_fillwith = pwwh_lib_utils_validate_array_field($args, 'dl-fillwith', false);
  $echo = pwwh_lib_utils_validate_array_field($args, 'echo', false);

  /* Converting classes to array. */
  if($classes) {
    if(!is_array($classes)) {
      $classes = explode(' ', $classes);
    }
  }
  else {
    $classes = array();
  }

  /* Managing label. */
  if($label) {
    if($icon) {
      $icon = '<span class="pwwh-lib-icon ' . $icon .'"></span>';
    }
    $label = '<label for="' . esc_attr($name) . '" class="pwwh-lib-label">' .
                $icon .
                '<span class="pwwh-lib-text">' . $label . '</span>
              </label>';
  }

  /* Remapping base attributes. */
  $_type = pwwh_lib_ui_form_attribute('type', $type);
  $_id = pwwh_lib_ui_form_attribute('id', $id);
  $_name = pwwh_lib_ui_form_attribute('name', $name);
  $_value = pwwh_lib_ui_form_attribute('value', $value);
  $_placeholder = pwwh_lib_ui_form_attribute('placeholder', $placeholder);

  /* Pushing base classes. */
  array_push($classes, 'pwwh-lib-input', 'type-' . $type);

  /* Remapping extra attributes. */
  $_size = pwwh_lib_ui_form_attribute('size', $size);

  if(isset($size))
    array_push($classes, 'size-' . $size );

  $_maxlenght = pwwh_lib_ui_form_attribute('maxlenght', $maxlenght);
  if(isset($maxlenght))
    array_push($classes, 'maxlenght-' . $maxlenght );

  $_disabled = pwwh_lib_ui_form_disabled($disabled);
  if(isset($disabled) && $disabled == true)
    array_push($classes, 'disabled');

  $_readonly = pwwh_lib_ui_form_readonly($readonly);
  if(isset($readonly) && $readonly == true)
    array_push($classes, 'readonly');

  $_required = pwwh_lib_ui_form_required($required);
  if(isset($required) && $required == true)
    array_push($classes, 'required');

  $_autocomplete = pwwh_lib_ui_form_autocomplete($autocomplete);

  $_min = pwwh_lib_ui_form_attribute('min', $min);
  if(isset($min))
    array_push($classes, 'min-' . $min );

  $_max = pwwh_lib_ui_form_attribute('max', $max);
  if(isset($max))
    array_push($classes, 'max-' . $max );

  $_step = pwwh_lib_ui_form_attribute('step', $step);
  if(isset($step))
    array_push($classes, 'step-' . $step );

  /* Managing datalist. */
  if($dl_id && $dl_data) {
    $datalist = pwwh_lib_ui_form_datalist($dl_data, $dl_id, $dl_fillwith);
    $datalist_link = ' list="' . esc_attr($dl_id) . '"';
  }
  else {
    $datalist = '';
    $datalist_link = '';
  }

  /* Imploding classes. */
  $classes = implode(' ', $classes);
  $_classes = pwwh_lib_ui_form_attribute('class', $classes);

  /* Composing output. */
  $output = '<span' . $_classes . '>' .
               $label .
               '<input class="widefat"' .
                       $_type . $_id . $_name . $_value . $_placeholder .
                       $_size . $_maxlenght . $_disabled . $_readonly .
                       $_required . $_autocomplete . $datalist_link . $_min .
                       $_max . $_step . '>' .
               $datalist .
             '</span>';

  /* Deciding whether echo or not. */
  if($echo)
    echo $output;
  else
    return $output;
}

/**
 * @brief     Creates an HTML switch according to parameters.
 *
 * @param[in] array $args         An array of arguments to compose the input.
 * @paramkey{id}                  The switch id.
 * @paramkey{classes}             An array of classes or a string.
 *                                @default{false}
 * @paramkey{icon}                The input icon. @default{false}
 * @paramkey{label}               The input label. @default{false}
 * @paramkey{name}                The input name. @default{same of ID}
 * @paramkey{rounded}             Rounded switch if true. @default{true}
 * @paramkey{status}              The switch status. @default{false}
 * @paramkey{disabled}            If true, the input is disabled.
 *                                @default{false}
 * @paramkey{readonly}            If true, the input is readonly.
 *                                @default{false}
 * @paramkey{echo}                Echoes if true return elsewhere.
 *                                @default{false}
 *
 * @return    mixed the list as HTML string or FALSE.
 */
function pwwh_lib_ui_form_switch($args = array()) {
  /* Validating array keys. */
  $id = pwwh_lib_utils_validate_array_field($args, 'id', null);
  $classes = pwwh_lib_utils_validate_array_field($args, 'classes', null);
  $icon = pwwh_lib_utils_validate_array_field($args, 'icon', null);
  $label = pwwh_lib_utils_validate_array_field($args, 'label', null);
  $name = pwwh_lib_utils_validate_array_field($args, 'name', $id);
  $rounded = pwwh_lib_utils_validate_array_field($args, 'rounded', true);
  $checked = pwwh_lib_utils_validate_array_field($args, 'status', false);
  $disabled = pwwh_lib_utils_validate_array_field($args, 'disabled', false);
  $readonly = pwwh_lib_utils_validate_array_field($args, 'readonly', false);
  $echo = pwwh_lib_utils_validate_array_field($args, 'echo', false);

  /* Converting classes to array. */
  if($classes) {
    if(!is_array($classes)) {
      $classes = explode(' ', $classes);
    }
  }
  else {
    $classes = array();
  }

  /* Managing label. */
  if($label) {
    if($icon) {
      $icon = '<span class="pwwh-lib-icon ' . $icon .'"></span>';
    }
    $label = '<span class="pwwh-lib-label">' .
                $icon .
                '<span class="pwwh-lib-text">' . $label . '</span>
              </span>';
  }

  /* Remapping base attributes. */
  $_id = pwwh_lib_ui_form_attribute('id', $id);
  $_name = pwwh_lib_ui_form_attribute('name', $name);

  /* Pushing base classes. */
  array_push($classes, 'pwwh-lib-switch');

  /* Remapping extra attributes. */
  $_disabled = pwwh_lib_ui_form_disabled($disabled);
  if($disabled)
    array_push($classes, 'disabled');

  $_readonly = pwwh_lib_ui_form_readonly($readonly);
  if($readonly)
    array_push($classes, 'readonly');

  $_checked = pwwh_lib_ui_form_checked($checked);
  if($checked)
    array_push($classes, 'checked');

  if($rounded)
    array_push($classes, 'round');

  /* Imploding classes. */
  $classes = implode(' ', $classes);
  $_classes = pwwh_lib_ui_form_attribute('class', $classes);

  /* Compose output. */
  $output = '<span' . $_classes . '>' .
              $label .
              '<label class="pwwh-lib-switch-wrapper">
                <input type="checkbox" class="pwwh-lib-switch-input"' . $_id .
                       $_name . $_disabled . $_readonly . $_checked . '>
                <span class="pwwh-lib-switch-slider"></span>
              </label>
            </span>';

  /* Deciding whether echo or not. */
  if($echo)
    echo $output;
  else
    return $output;
}

/**
 * @brief     Creates an HTML button according to parameters.
 *
 * @param[in] array $args         An array of arguments to compose the input.
 * @paramkey{id}                  The button id.
 * @paramkey{type}                The button type. @default{'button'}
 * @paramkey{classes}             An array of classes or a string.
 *                                @default{false}
 * @paramkey{icon}                The button icon. @default{false}
 * @paramkey{label}               The button label. @default{false}
 * @paramkey{name}                The button name. @default{same of ID}
 * @paramkey{value}               The button value. @default{empty}
 * @paramkey{title}               The button title. @default{empty}
 * @paramkey{disabled}            If true, the button is disabled.
 *                                @default{false}
 * @paramkey{readonly}            If true, the button is readonly.
 *                                @default{false}
 * @paramkey{echo}                Echoes if true return elsewhere.
 *                                @default{false}
 *
 * @return    mixed the button as HTML.
 */
function pwwh_lib_ui_form_button($args = array()) {
  /* Validating array keys. */
  $id = pwwh_lib_utils_validate_array_field($args, 'id', null);
  $type = pwwh_lib_utils_validate_array_field($args, 'type', 'button');
  $classes = pwwh_lib_utils_validate_array_field($args, 'classes', null);
  $icon = pwwh_lib_utils_validate_array_field($args, 'icon', null);
  $label = pwwh_lib_utils_validate_array_field($args, 'label', null);
  $name = pwwh_lib_utils_validate_array_field($args, 'name', $id);
  $value = pwwh_lib_utils_validate_array_field($args, 'value', '');
  $title = pwwh_lib_utils_validate_array_field($args, 'title', '');
  $disabled = pwwh_lib_utils_validate_array_field($args, 'disabled', false);
  $readonly = pwwh_lib_utils_validate_array_field($args, 'readonly', false);
  $echo = pwwh_lib_utils_validate_array_field($args, 'echo', false);

  /* Additional check on type. */
  if($type) {
    $allowed = array('button', 'reset', 'submit');
    if(!in_array($type, $allowed)) {
      $type = 'button';
    }
  }

  /* Converting classes to array. */
  if($classes) {
    if(!is_array($classes)) {
      $classes = explode(' ', $classes);
    }
  }
  else {
    $classes = array();
  }

  /* Managing inner. */
  if($label || $icon) {
    if($icon) {
      $icon = '<span class="pwwh-lib-icon ' . $icon .'"></span>';
    array_push($classes, 'has-icon');
    }
    if($label) {
      $label = '<span class="pwwh-lib-label">' . $label . '</span>';
      array_push($classes, 'has-label');
    }
    $inner = $icon . $label;
  }

  /* Remapping base attributes. */
  $_type = pwwh_lib_ui_form_attribute('type', $type);
  $_id = pwwh_lib_ui_form_attribute('id', $id);
  $_name = pwwh_lib_ui_form_attribute('name', $name);
  $_value = pwwh_lib_ui_form_attribute('value', $value);
  $_title = pwwh_lib_ui_form_attribute('title', $title);

  /* Pushing base classes. */
  array_push($classes, 'pwwh-lib-button');
  if($title)
    array_push($classes, 'has-title');

  /* Remapping extra attributes. */
  $_disabled = pwwh_lib_ui_form_disabled($disabled);
  if($disabled)
    array_push($classes, 'disabled');

  $_readonly = pwwh_lib_ui_form_readonly($readonly);
  if($readonly)
    array_push($classes, 'readonly');

  /* Imploding classes. */
  $classes = implode(' ', $classes);
  $_classes = pwwh_lib_ui_form_attribute('class', $classes);

  /* Compose output. */
  $output = '<button' . $_type . $_id . $_name . $_classes . $_value .
                        $_title . $_disabled . $_readonly . '>' .
              $inner .
            '</button>';

  /* Deciding whether echo or not. */
  if($echo)
    echo $output;
  else
    return $output;
}

/**
 * @brief     Creates an HTML select according to parameters.
 *
 * @param[in] array $args         An array of arguments to compose the input.
 * @paramkey{data}                An array of data organized as
 *                                ['value'] => ['label'] as example
 *                                ('pippo' => 'Pippo', 'o-brian' => 'O Brian').
 * @paramkey{id}                  The select id.
 * @paramkey{classes}             An array of classes or a string.
 *                                @default{false}
 * @paramkey{icon}                The input icon. @default{false}
 * @paramkey{label}               The select label. @default{false}
 * @paramkey{label_class}         The class for label. @default{false}
 * @paramkey{value}               The value to mark as selected. @default{empty}
 * @paramkey{name}                The select name. @default{same of ID}
 * @paramkey{disabled}            If true, the button is disabled.
 *                                @default{false}
 * @paramkey{readonly}            If true, the button is readonly.
 *                                @default{false}
 * @paramkey{echo}                Echoes if true return elsewhere.
 *                                @default{false}
 *
 * @return    mixed the button as HTML.
 */
function pwwh_lib_ui_form_select($args = array()) {
  /* Validating array keys. */
  if(!is_array($args['data']))
    return '';
  $data = $args['data'];
  $id = pwwh_lib_utils_validate_array_field($args, 'id', null);
  $classes = pwwh_lib_utils_validate_array_field($args, 'classes', null);
  $icon = pwwh_lib_utils_validate_array_field($args, 'icon', null);
  $label = pwwh_lib_utils_validate_array_field($args, 'label', null);
  $label_class = pwwh_lib_utils_validate_array_field($args, 'label_classes', null);
  $name = pwwh_lib_utils_validate_array_field($args, 'name', $id);
  $value = pwwh_lib_utils_validate_array_field($args, 'value', '');
  $disabled = pwwh_lib_utils_validate_array_field($args, 'disabled', false);
  $readonly = pwwh_lib_utils_validate_array_field($args, 'readonly', false);
  $echo = pwwh_lib_utils_validate_array_field($args, 'echo', false);

  /* Converting classes to array. */
  if($classes) {
    if(!is_array($classes)) {
      $classes = explode(' ', $classes);
    }
  }
  else {
    $classes = array();
  }

  /* Managing label. */
  if($label) {
    if($icon) {
      $icon = '<span class="pwwh-lib-icon ' . $icon .'"></span>';
    }
    if($label_class) {
      if($label_class && !is_array($label_class)) {
        $label_class = explode(' ', $label_class);
      }
      array_push($label_class, 'pwwh-lib-label');
      /* Imploding classes. */
      $label_class = implode(' ', $label_class);
      $_label_class = pwwh_lib_ui_form_attribute('class', $label_class);
    }
    else {
      $_label_class = pwwh_lib_ui_form_attribute('class', 'pwwh-lib-label');
    }

    $label = '<label for="' . esc_attr($name) . '"' . $_label_class . '>' .
                $icon .
                '<span class="pwwh-lib-text">' . $label . '</span>
              </label>';
  }

  /* Remapping base attributes. */
  $_id = pwwh_lib_ui_form_attribute('id', $id);
  $_name = pwwh_lib_ui_form_attribute('name', $name);

  /* Pushing base classes. */
  array_push($classes, 'pwwh-lib-select');

  /* Remapping extra attributes. */
  $_disabled = pwwh_lib_ui_form_disabled($disabled);
  if($disabled)
    array_push($classes, 'disabled');

  $_readonly = pwwh_lib_ui_form_readonly($readonly);
  if($readonly)
    array_push($classes, 'readonly');

  /* Composing options. */
  $option = '';
  foreach($data as $_value => $_nicename) {
    $option .= '<option value="' . esc_attr($_value) . '"' .
                        pwwh_lib_ui_form_selected($_value, $value) . '>' .
                  $_nicename .
               '</option>';
  }

  /* Imploding classes. */
  $classes = implode(' ', $classes);
  $_classes = pwwh_lib_ui_form_attribute('class', $classes);

  $output = '<span' . $_classes . '>' .
              $label .
              '<select class="widefat"' . $_id . $_name . $_disabled .
                       $_readonly . '>' .
                $option .
              '</select>
            </span>';

  /* Deciding whether echo or not. */
  if($echo)
    echo $output;
  else
    return $output;
}

/**
 * @brief     Creates an HTML textare according to parameters.
 *
 * @param[in] array $args         An array of arguments to compose the input.
 * @paramkey{id}                  The input ID.
 * @paramkey{classes}             An array of classes or a string.
 *                                @default{false}
 * @paramkey{icon}                The input icon. @default{false}
 * @paramkey{label}               The input label. @default{false}
 * @paramkey{name}                The input name. @default{same of ID}
 * @paramkey{placeholder}         The input placeholder. @default{false}
 * @paramkey{value}               The input value. @default{false}
 * @paramkey{disabled}            If true, the button is disabled.
 *                                @default{false}
 * @paramkey{readonly}            If true, the button is readonly.
 *                                @default{false}
 * @paramkey{cols}                The column attribute. @default{80}
 * @paramkey{rows}                The rows attribute. @default{4}
 * @paramkey{maxlenght}           The maxlenght attribute. @default{false}
 * @paramkey{echo}                Echoes if true return elsewhere.
 *                                @default{false}
 *
 * @return    mixed the list as HTML string or FALSE.
 */
function pwwh_lib_ui_form_textarea($args = array()) {
  /* Validating array keys. */
  $id = pwwh_lib_utils_validate_array_field($args, 'id', null);
  $classes = pwwh_lib_utils_validate_array_field($args, 'classes', null);
  $icon = pwwh_lib_utils_validate_array_field($args, 'icon', null);
  $label = pwwh_lib_utils_validate_array_field($args, 'label', null);
  $name = pwwh_lib_utils_validate_array_field($args, 'name', $id);
  $placeholder = pwwh_lib_utils_validate_array_field($args, 'placeholder', null);
  $value = pwwh_lib_utils_validate_array_field($args, 'value', '');
  $disabled = pwwh_lib_utils_validate_array_field($args, 'disabled', false);
  $readonly = pwwh_lib_utils_validate_array_field($args, 'readonly', false);
  $cols = pwwh_lib_utils_validate_array_field($args, 'cols', 80);
  $rows = pwwh_lib_utils_validate_array_field($args, 'rows', 4);
  $echo = pwwh_lib_utils_validate_array_field($args, 'echo', false);

  /* Converting classes to array. */
  if($classes) {
    if(!is_array($classes)) {
      $classes = explode(' ', $classes);
    }
  }
  else {
    $classes = array();
  }

  /* Managing label. */
  if($label) {
    if($icon) {
      $icon = '<span class="pwwh-lib-icon ' . $icon .'"></span>';
    }
    $label = '<label for="' . esc_attr($name) . '" class="pwwh-lib-label">' .
                $icon .
                '<span class="pwwh-lib-text">' . $label . '</span>
              </label>';
  }

  /* Remapping base attributes. */
  $_id = pwwh_lib_ui_form_attribute('id', $id);
  $_name = pwwh_lib_ui_form_attribute('name', $name);
  $_placeholder = pwwh_lib_ui_form_attribute('placeholder', $placeholder);

  /* Pushing base classes. */
  array_push($classes, 'pwwh-lib-textarea');

  /* Remapping extra attributes. */
  $_cols = pwwh_lib_ui_form_attribute('cols', $cols);
  if($cols)
    array_push($classes, 'cols-' . $cols );

  $_rows = pwwh_lib_ui_form_attribute('rows', $rows);
  if($cols)
    array_push($classes, 'rows-' . $rows );

  $_maxlenght = pwwh_lib_ui_form_attribute('maxlenght', $maxlenght);
  if($maxlenght)
    array_push($classes, 'maxlenght-' . $maxlenght );

  $_disabled = pwwh_lib_ui_form_disabled($disabled);
  if($disabled)
    array_push($classes, 'disabled');

  $_readonly = pwwh_lib_ui_form_readonly($readonly);
  if($readonly)
    array_push($classes, 'readonly');

  /* Imploding classes. */
  $classes = implode(' ', $classes);
  $_classes = pwwh_lib_ui_form_attribute('class', $classes);

  /* Composing output. */
  $output = '<span' . $_classes . '>' .
               $label .
               '<textarea class="widefat"' . $_type . $_id . $_name .
                          $_placeholder . $_rows . $_cols .
                          $_maxlenght . $_disabled . $_readonly .'>' .
                $value .
              '</textarea>
            </span>';

  /* Deciding whether echo or not. */
  if($echo)
    echo $output;
  else
    return $output;
}
/** @} */