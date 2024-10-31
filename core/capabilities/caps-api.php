<?php
/**
 * @file       common/common-caps.php
 * @brief      Capability Engine APIs.
 *
 * @addtogroup PWWH_CAPS
 * @{
 */

/*===========================================================================*/
/* Local Database related APIs and local functions.                          */
/*===========================================================================*/

/**
 * @brief      The name of the option which stores capability revision.
 * @notapi
 */
define('PWWH_CORE_CAPS_OPTION_REV', PWWH_PREFIX . '_caps_rev');

/**
 * @brief     Gets the current capabilities database revision.
 *
 * @api
 * @return    string The capabilities database revision.
 */
function pwwh_core_caps_api_db_get_revision() {

  return get_option(PWWH_CORE_CAPS_OPTION_REV);
}

/**
 * @brief     Sets the current capabilities database revision.
 *
 * @param[in] string $rev         The capabilities database revision.
 *
 * @api
 * @return    void
 */
function pwwh_core_caps_api_db_set_revision($rev) {

  update_option(PWWH_CORE_CAPS_OPTION_REV, $rev);
}

/*===========================================================================*/
/* Generic APIs                                                              */
/*===========================================================================*/

/**
 * @brief    Modules global array key identifiers.
 * @{
 */
/* Main keys. */
define('PWWH_CORE_CAPS_KEY_CNTXS', 'contexts');
define('PWWH_CORE_CAPS_KEY_CAPS', 'capabilities');

/* Contexts fields. */
define('PWWH_CORE_CAPS_KEY_CNTX_LABEL', 'label');
define('PWWH_CORE_CAPS_KEY_CNTX_DESC', 'description');

/* Capabilities fields. */
define('PWWH_CORE_CAPS_KEY_CAP_LABEL', 'label');
define('PWWH_CORE_CAPS_KEY_CAP_DESC', 'description');
define('PWWH_CORE_CAPS_KEY_CAP_CTX', 'context');
define('PWWH_CORE_CAPS_KEY_CAP_DEP', 'dependency');
/** @} */

/**
 * @brief     Initializes the system creating the main structure.
 *
 * @return    void.
 */
function pwwh_core_caps_api_init() {

  global $PWWH_CORE_CAPS;
  $PWWH_CORE_CAPS = array(PWWH_CORE_CAPS_KEY_CNTXS => array(),
                          PWWH_CORE_CAPS_KEY_CAPS => array());

  /* Registering a default context. */
  $args = array('label' => __('Generic', 'piwi-warehouse'),
                'description' => __('Generic capabilites',
                                    'piwi-warehouse'));
  pwwh_core_caps_api_register_context('_default', $args);
}

/*===========================================================================*/
/* APIs related to contexts registry.                                        */
/*===========================================================================*/

/**
 * @brief     Registers a capability context providing its information.
 * @details   The context is such a taxonomy for the capabilities. Its main
 *            purpose is to group capabilities togheter according to their
 *            purpose
 * @note      Already registered contexts will be updated.
 *
 * @param[in] string $slug        The context slug.
 * @param[in] array $args         An array of argument.
 * @paramkey{label}               The context label @default{Empty}.
 * @paramkey{description}         The context description @default{Empty}.
 *
 * @return    void
 */
function pwwh_core_caps_api_register_context($slug, $args = array()) {
  $label = pwwh_lib_utils_validate_array_field($args, 'label', null);
  $desc = pwwh_lib_utils_validate_array_field($args, 'description', null);

  $data = array(PWWH_CORE_CAPS_KEY_CNTX_LABEL => $label,
                PWWH_CORE_CAPS_KEY_CNTX_DESC => $desc);

  global $PWWH_CORE_CAPS;
  $PWWH_CORE_CAPS[PWWH_CORE_CAPS_KEY_CNTXS][$slug] = $data;
}

/**
 * @brief     Unregisters a capability context.
 *
 * @param[in] string $slug        The slug of the context to unregister.
 *
 * @return    void
 */
function pwwh_core_caps_api_unregister_context($slug) {

  global $PWWH_CORE_CAPS;
  if(pwwh_core_caps_api_exists_context($slug)) {
    unset($PWWH_CORE_CAPS[PWWH_CORE_CAPS_KEY_CNTXS][$slug]);
  }
}

/**
 * @brief     Checks a capability context existency.
 *
 * @param[in] string $slug        The slug of the context to check.
 *
 * @return    bool the operation status.
 */
function pwwh_core_caps_api_exists_context($slug) {

  global $PWWH_CORE_CAPS;

  return isset($PWWH_CORE_CAPS[PWWH_CORE_CAPS_KEY_CNTXS][$slug]);
}

/**
 * @brief     Gets an an array of all the capability slugs.
 *
 * @return    array the capability slugs.
 */
function pwwh_core_caps_api_get_all_contexts() {

  global $PWWH_CORE_CAPS;

  return array_keys($PWWH_CORE_CAPS[PWWH_CORE_CAPS_KEY_CNTXS]);
}

/**
 * @brief     Retrieve capability data by a given field.
 *
 * @param[in] string $field       The data field to retrieve.
 * @param[in] string $ctx         The slug of the context.
 *
 * @return    mixed the capability info or false.
 */
function pwwh_core_caps_api_get_context_data_by($field, $ctx) {
  global $PWWH_CORE_CAPS;

  $allowed_fields = array(PWWH_CORE_CAPS_KEY_CNTX_LABEL,
                          PWWH_CORE_CAPS_KEY_CNTX_DESC);

  if(!in_array($field, $allowed_fields)) {
    $msg = sprintf('Invalid field in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $msg = sprintf('The field is %s', $field);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    return false;
  }

  if(pwwh_core_caps_api_exists_context($ctx)) {
    return $PWWH_CORE_CAPS[PWWH_CORE_CAPS_KEY_CNTXS][$ctx][$field];
  }
  else {
    $msg = sprintf('Trying to get data of an unregistered context in %s()',
                   __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $msg = sprintf('The context is %s', $ctx);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    return false;
  }
}

/*===========================================================================*/
/* APIs related to capabilties registry.                                     */
/*===========================================================================*/

/**
 * @brief     Registers a capability providing its information.
 * @note      Already registered capability will be updated.
 * @note      The context shall be already registered.
 * @note      The dependency shall be already registered in the same context.
 *
 * @param[in] string $slug        The capability slug.
 * @param[in] array $args         An array of argument.
 * @paramkey{label}               The capability label @default{Empty}.
 * @paramkey{description}         The capability description @default{Empty}.
 * @paramkey{context}             The capability context @default{Empty}.
 * @paramkey{dependency}          The identifier of the core capability which
 *                                this capability depends on @default{Empty}.
 *
 * @return    void
 */
function pwwh_core_caps_api_register_cap($slug, $args = array()) {
  $label = pwwh_lib_utils_validate_array_field($args, 'label', null);
  $desc = pwwh_lib_utils_validate_array_field($args, 'description', null);
  $context = pwwh_lib_utils_validate_array_field($args, 'context', '_default');
  $dep = pwwh_lib_utils_validate_array_field($args, 'dependency', false);

  if(pwwh_core_caps_api_exists_context($context)) {

    /* Checking the data validity.*/
    if(($dep == false) ||
       ($context == pwwh_core_caps_api_get_cap_data_by(PWWH_CORE_CAPS_KEY_CAP_CTX,
                                                       $dep))) {

      /* No dependency or valid dependency: the dependency is another
         capability that shall be already registered in the same context. */
      $data = array(PWWH_CORE_CAPS_KEY_CAP_LABEL => $label,
                    PWWH_CORE_CAPS_KEY_CAP_DESC => $desc,
                    PWWH_CORE_CAPS_KEY_CAP_CTX => $context,
                    PWWH_CORE_CAPS_KEY_CAP_DEP => $dep);
      global $PWWH_CORE_CAPS;
      $PWWH_CORE_CAPS[PWWH_CORE_CAPS_KEY_CAPS][$slug] = $data;
    }
    else {
      $msg = sprintf('Trying to register a capability with an invalid ' .
                     'dependency in %s()', __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      $msg = sprintf('The capability is %s, the dependency %s', $slug, $dep);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    }
  }
  else {
    $msg = sprintf('Trying to register a capability in an unexistent ' .
                   ' context in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $msg = sprintf('The capability is %s, the context %s', $slug, $context);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
}

/**
 * @brief     Unregisters a capability.
 *
 * @param[in] string $slug        The slug of the capability to unregister.
 *
 * @return    void
 */
function pwwh_core_caps_api_unregister_cap($slug) {

  global $PWWH_CORE_CAPS;
  if(pwwh_core_caps_api_exists_cap($slug)) {
    unset($PWWH_CORE_CAPS[PWWH_CORE_CAPS_KEY_CAPS][$slug]);
  }
}

/**
 * @brief     Checks a capability existency.
 *
 * @param[in] string $slug        The slug of the capability to check.
 *
 * @return    bool the operation status.
 */
function pwwh_core_caps_api_exists_cap($slug) {

  global $PWWH_CORE_CAPS;

  return isset($PWWH_CORE_CAPS[PWWH_CORE_CAPS_KEY_CAPS][$slug]);
}

/**
 * @brief     Gets an an array of all the capability slugs.
 *
 * @return    array the capability slugs.
 */
function pwwh_core_caps_api_get_all_caps() {

  global $PWWH_CORE_CAPS;

  return array_keys($PWWH_CORE_CAPS[PWWH_CORE_CAPS_KEY_CAPS]);
}

/**
 * @brief     Retrieve capability data by a given field.
 *
 * @param[in] string $field       The data field to retrieve.
 * @param[in] string $cap         The slug of the capability.
 *
 * @return    mixed the capability info or false.
 */
function pwwh_core_caps_api_get_cap_data_by($field, $cap) {
  global $PWWH_CORE_CAPS;

  $allowed_fields = array(PWWH_CORE_CAPS_KEY_CAP_LABEL,
                          PWWH_CORE_CAPS_KEY_CAP_DESC,
                          PWWH_CORE_CAPS_KEY_CAP_CTX,
                          PWWH_CORE_CAPS_KEY_CAP_DEP);

  if(!in_array($field, $allowed_fields)) {
    $msg = sprintf('Invalid field in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $msg = sprintf('The field is %s', $field);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    return false;
  }

  if(pwwh_core_caps_api_exists_cap($cap)) {
    return $PWWH_CORE_CAPS[PWWH_CORE_CAPS_KEY_CAPS][$cap][$field];
  }
  else {
    $msg = sprintf('Trying to get data of an unregistered capability in %s()',
                   __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $msg = sprintf('The capability is %s', $cap);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    return false;
  }
}

/**
 * @brief     Gets an array of capability slug filtered by context.
 *
 * @param[in] string $ctx         The slug of the context.
 *
 * @return    mixed the capability info or false.
 */
function pwwh_core_caps_api_get_caps_by_context($ctx) {
  global $PWWH_CORE_CAPS;

  if(pwwh_core_caps_api_exists_context($ctx)) {

    /* Filtering the capabilities. */
    $caps = $PWWH_CORE_CAPS[PWWH_CORE_CAPS_KEY_CAPS];
    foreach($caps as $slug => $data) {

      if($data[PWWH_CORE_CAPS_KEY_CAP_CTX] != $ctx) {
        unset($caps[$slug]);
      }

    }
    return array_keys($caps);
  }
  else {
    $msg = sprintf('Trying to get capabilities of an unregistered context ' .
                   'in %s()',  __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    $msg = sprintf('The context is %s', $ctx);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    return array();
  }
}

/*===========================================================================*/
/* APIs related to capability management.                                    */
/*===========================================================================*/

/**
 * @brief     Grants a capability for a role.
 * @note      Works only on registered capabilities.
 *
 * @param[in] mixed $role         The role object or the slug to edit.
 * @param[in] string $cap         The capability to add.
 *
 * @return    void
 */
function pwwh_core_caps_api_add($role, $cap) {

  if(!is_a($role, 'WP_Role')) {
    $role = get_role($role);
  }

  if($role) {
    /* Getting role slug. */
    $role_name = $role->name;

    if(pwwh_core_caps_api_exists_cap($cap)) {

      /* Assigning the capability to the role. */
      $role->add_cap($cap);
    }
    else {
      $msg = sprintf('Trying to assign an unregistered capability in %s()',
                     __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      $msg = sprintf('The capability is %s', $cap);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    }
  }
  else {
    $msg = sprintf('Unexpected $role type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
}

/**
 * @brief     Ungrants a capability for a role.
 * @note      If capability is marked as core will be removed in core too.
 *
 * @param[in] mixed $role         The role object or the slug to edit.
 * @param[in] string $cap         The capability to remove.
 *
 * @return    void
 */
function pwwh_core_caps_api_revoke($role, $cap) {

  if(!is_a($role, 'WP_Role')) {
    $role = get_role($role);
  }

  if($role) {
    if(pwwh_core_caps_api_exists_cap($cap)) {

      /* Removing the capability from the role. */
      $role->remove_cap($cap);
    }
    else {
      $msg = sprintf('Trying to assign an unregistered capability in %s()',
                     __FUNCTION__);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
      $msg = sprintf('The capability is %s', $cap);
      pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
    }
  }
  else {
    $msg = sprintf('Unexpected $role type in %s()', __FUNCTION__);
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
}

/**
 * @brief     Ungrants all the context's capabilities for a role.
 * @note      If capability is marked as core will be removed in core too.
 * @note      When $role is NULL all the roles are configured.
 *
 * @param[in] mixed $role         The role object or the slug to edit.
 * @param[in] string $ctx         The slug of the context.
 *
 * @return    void
 */
function pwwh_core_caps_api_revoke_by_context($role = null, $ctx) {

  if($role === null) {

    /* Retrieving the list of all the roles from the DB. */
    global $wpdb;
    $table_name = $wpdb->prefix;
    $roles = get_option($table_name . 'user_roles');

    /* Recalling this function per each role. */
    foreach($roles as $role => $value) {
      pwwh_core_caps_api_revoke_by_context($role, $ctx);
    }
  }
  else {
    /* Getting all the capabilities for this context. */
    $caps = pwwh_core_caps_api_get_caps_by_context($ctx);

    /* Revoking capabilities. */
    foreach($caps as $cap) {
      pwwh_core_caps_api_revoke($role, $cap);
    }
  }
}

/**
 * @brief     Ungrants all the capabilities for a role.
 * @note      If capability is marked as core will be removed in core too.
 * @note      When $role is NULL all the roles are configured.
 *
 * @param[in] mixed $role         The role object or the slug to edit.
 *
 * @return    void
 */
function pwwh_core_caps_api_revoke_all($role = null) {

  if($role === null) {

    /* Retrieving the list of all the roles from the DB. */
    global $wpdb;
    $table_name = $wpdb->prefix;
    $roles = get_option($table_name . 'user_roles');

    /* Recalling this function per each role. */
    foreach($roles as $role => $value) {
      pwwh_core_caps_api_revoke_all($role);
    }
  }
  else {

    /* Getting all the capabilities for this context. */
    $caps = pwwh_core_caps_api_get_all_caps();

    /* Revoking capabilities. */
    foreach($caps as $cap) {
      pwwh_core_caps_api_revoke($role, $cap);
    }
  }
}

/**
 * @brief     Checks whereas a role has or not a capability.
 * @note      By default while checking user capabilities the dependency is
 *            taken in account: if the user can $cap but cannot its dependency
 *            the function return false.
 * @note      The depencency check can be skipped: this is used to manage the
 *            interface.
 *
 * @param[in] mixed $role         The role object or the slug to check.
 * @param[in] string $cap         The capability to check.
 * @param[in] bool $skip_dep      Skips dependency check on true.
 *
 * @return    boolean true if user can, false otherwise
 */
function pwwh_core_caps_api_role_can($role, $cap, $skip_dep = false) {

  if(!is_a($role, 'WP_Role')) {
    $role = get_role($role);
  }

  if($role) {
    /* Getting role slug. */
    $role_name = $role->name;

    /* Controlling if the capabilty is registered. */
    if(pwwh_core_caps_api_exists_cap($cap)) {

      /* Getting the dependency. */
      $dep = pwwh_core_caps_api_get_cap_data_by(PWWH_CORE_CAPS_KEY_CAP_DEP, $cap);

      /* Checking dependency capability. */
      if(!$skip_dep && $dep && !pwwh_core_caps_api_role_can($role, $dep)) {
        /* The dependency capability is not granted. */
        return false;
      }
      else {
        /* Checking the capability. */
        return $role->has_cap($cap);
      }
    }
    else {
      /* This could be a non registered core capability. This allows the
         function to assess also core capabilities that are
         not registered. */
      return $role->has_cap($cap);
    }
  }
  else {
    $msg = 'Unexpected $role type in pwwh_core_caps_api_role_can()';
    pwwh_logger_append($msg, PWWH_LIB_LOGGER_EFLAG_CRITICAL);
  }
}

/**
 * @brief     Checks whereas a user has or not a capability.
 * @note      By default while checking user capabilities the dependency is
 *            taken in account: if the user can $cap but cannot its dependency
 *            the function return false.
 * @note      The depencency check can be skipped: this is used to manage the
 *            interface.
 *
 * @param[in] mixed $user         The user id to check.
 * @param[in] string $cap         The capability to check.
 * @param[in] bool $skip_dep      Skips dependency check on true.
 *
 * @return    boolean true if user can, false otherwise
 */
function pwwh_core_caps_api_user_can($user, $cap, $skip_dep = false) {
  $data = get_userdata($user);
  $roles = $data->roles;
  foreach($roles as $role) {
    if(pwwh_core_caps_api_role_can($role, $cap, $skip_dep)) {
      return true;
    }
  }
  return false;
}

/**
 * @brief     Checks whereas a current user has or not a capability.
 * @note      By default while checking user capabilities the dependency is
 *            taken in account: if the user can $cap but cannot its dependency
 *            the function return false.
 * @note      The depencency check can be skipped: this is used to manage the
 *            interface.
 *
 * @param[in] string $cap         The capability to check.
 * @param[in] bool $skip_dep      Skips dependency check on true.
 *
 * @return    boolean true if user can, false otherwise
 */
function pwwh_core_caps_api_current_user_can($cap, $skip_dep = false) {
  $user = get_current_user_id();

  return pwwh_core_caps_api_user_can($user, $cap, $skip_dep);
}
/** @} */