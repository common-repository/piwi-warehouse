<?php
/**
 * @file    lib/utils/utils.php
 * @brief   This file contains a set of utility functions.
 *
 * @addtogroup PWWH_UTILS
 * @{
 */

/**
 * @brief     Validates an array field checking its existency and, in case, if
 *            it belongs to an ensemble of allowed values.
 *
 * @param[in] mixed $array       the array which contains the field to validate.
 * @param[in] mixed $key         the key of the field to validate.
 * @param[in] mixed $default     the fallback value.
 * @param[in] array $values      an array of allowed values. This check is
 *                               skipped if this array is empty.
 * @param[in] mixes $types       a string or an array of string representing
 *                               allowed types. This check is skipped if this
 *                               is false.
 *
 * @return    mixed the value of $array[$key] if it is valid, $default
 *            otherwise.
 */
function pwwh_lib_utils_validate_array_field($array, $key, $default = '',
                                             $values = array(), $types = false) {
  if(!is_array($values)) {
    /* $values or have been improperly passed. */
    return $default;
  }

  if(is_array($array) && array_key_exists($key, $array)) {
    if($types) {
      /* Checking type. */
      $type = gettype($array[$key]);
      if(is_array($types)) {
        /* Checking between an array of types. */
        if(!in_array($type, $types)) {
          /* Invalid type. */
          return $default;
        }
      }
      else if($type != $types) {
        /* Invalid type. */
        return $default;
      }
    }
    if(count($values)) {
      /* Checking value. */
      if(!in_array($array[$key], $values)) {
        /* Invalid value. */
        return $default;
      }
    }
  }
  else {
    /* $key does not exists or $array is not an array. */
    return $default;
  }
  return $array[$key];
}

/**
 * @brief     Gets the IP address of the current user.
 *
 * @return    string the IP address.
 */
function pwwh_lib_utils_get_user_ip_address() {

  if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
    /* The IP comes from share internet. */
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  }
  else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    /* The IP comes through a proxy. */
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  }
  else {
    /* The IP comes frome remote. */
    $ip = $_SERVER['REMOTE_ADDR'];
  }
  return $ip;
}
/** @} */