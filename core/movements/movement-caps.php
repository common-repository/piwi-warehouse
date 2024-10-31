<?php
/**
 * @file      movements/movement-caps.php
 * @brief     Function related to the Movement capabilies.
 *
 * @addtogroup PWWH_CORE_MOVEMENT
 * @{
 */

/**
 * @brief     Movement post holder capabilites defines.
 * @{
 */
define('PWWH_CORE_MOVEMENT_CAPS_MANAGE_MOVEMENTS', 'manage_movements');
define('PWWH_CORE_MOVEMENT_CAPS_MANAGE_OTHERS_MOVEMENTS',
       'manage_others_movements');
define('PWWH_CORE_MOVEMENT_CAPS_DELETE_MOVEMENTS', 'delete_movements');
/** @} */

/**
 * @brief     Defines the capabilites related to the movements and returns
 *            the array required to create the post holder.
 *
 * @return    array the array of capabilities required by the
 *            register_post_holder().
 */
function pwwh_core_movement_caps_init() {

  /* Registering a movement related context. */
  $label = __('Movement related', 'piwi-warehouse');
  $desc = __('Capabilities associated to Movements and Holders.',
             'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc);
  pwwh_core_caps_api_register_context(PWWH_CORE_MOVEMENT, $args);

  /* Registering Manage Movements capability. */
  $label = __('Manage Movements', 'piwi-warehouse');
  $desc = __('Allows to manage Movements. This capability allows to browse ' .
             'Movements create new ones, edit user\'s own Movements.',
             'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc,
                'context' => PWWH_CORE_MOVEMENT);
  pwwh_core_caps_api_register_cap(PWWH_CORE_MOVEMENT_CAPS_MANAGE_MOVEMENTS, $args);

  /* Registering Manage Other's Movements capability. */
  $label = __('Manage Others Movements', 'piwi-warehouse');
  $desc = __('Allows to manage Movements from other users.', 'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc,
                'context' => PWWH_CORE_MOVEMENT,
                'dependency' => PWWH_CORE_MOVEMENT_CAPS_MANAGE_MOVEMENTS);
  pwwh_core_caps_api_register_cap(PWWH_CORE_MOVEMENT_CAPS_MANAGE_OTHERS_MOVEMENTS,
                          $args);

  /* Registering Delete Movements capability. */
  $label = __('Delete Movements', 'piwi-warehouse');
  $desc = __('Allows to delete Movements. This is usually forbidden to ' .
             'preserve Warehouse history.', 'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc,
                'context' => PWWH_CORE_MOVEMENT,
                'dependency' => PWWH_CORE_MOVEMENT_CAPS_MANAGE_MOVEMENTS);
  pwwh_core_caps_api_register_cap(PWWH_CORE_MOVEMENT_CAPS_DELETE_MOVEMENTS, $args);

  $caps = array('read' => 'read',
                'read_private_posts' => 'read',
                'create_posts' => PWWH_CORE_MOVEMENT_CAPS_MANAGE_MOVEMENTS,
                'edit_posts' => PWWH_CORE_MOVEMENT_CAPS_MANAGE_MOVEMENTS,
                'edit_others_posts' => PWWH_CORE_MOVEMENT_CAPS_MANAGE_OTHERS_MOVEMENTS,
                'edit_private_posts' => PWWH_CORE_MOVEMENT_CAPS_MANAGE_MOVEMENTS,
                'publish_posts' => PWWH_CORE_MOVEMENT_CAPS_MANAGE_MOVEMENTS,
                'edit_published_posts' => PWWH_CORE_MOVEMENT_CAPS_MANAGE_MOVEMENTS,
                'delete_posts' => PWWH_CORE_MOVEMENT_CAPS_DELETE_MOVEMENTS,
                'delete_others_posts' => PWWH_CORE_MOVEMENT_CAPS_DELETE_MOVEMENTS,
                'delete_private_posts' => PWWH_CORE_MOVEMENT_CAPS_DELETE_MOVEMENTS,
                'delete_published_posts' => PWWH_CORE_MOVEMENT_CAPS_DELETE_MOVEMENTS);

  return $caps;
}

/**
 * @brief     Holder taxonomy capabilites defines.
 * @{
 */
define('PWWH_CORE_MOVEMENT_CAPS_MANAGE_HOLDERS', 'manage_holders');
define('PWWH_CORE_MOVEMENT_CAPS_DELETE_HOLDERS', 'delete_holders');
/** @} */

/**
 * @brief     Defines the capabilites related to the holder taxonomy and
 *            returns the array required to create the taxonomy.
 *
 * @return    array the array of capabilities required by the
 *            register_taxonomy().
 */
function pwwh_core_movement_caps_holder_init() {

  /* Registering Manage Movements capability. */
  $label = __('Manage Holders', 'piwi-warehouse');
  $desc = __('Allows to manage Holder. With this capability the user can
              create or edit holders.', 'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc,
                'context' => PWWH_CORE_MOVEMENT,
                'dependency' => PWWH_CORE_MOVEMENT_CAPS_MANAGE_MOVEMENTS);
  pwwh_core_caps_api_register_cap(PWWH_CORE_MOVEMENT_CAPS_MANAGE_HOLDERS, $args);

  /* Registering Delete Movements capability. */
  $label = __('Delete Holders', 'piwi-warehouse');
  $desc = __('Allows to delete holders.', 'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc,
                'context' => PWWH_CORE_MOVEMENT,
                'dependency' => PWWH_CORE_MOVEMENT_CAPS_MANAGE_MOVEMENTS);
  pwwh_core_caps_api_register_cap(PWWH_CORE_MOVEMENT_CAPS_DELETE_HOLDERS, $args);

  $caps = array('assign_terms'  => PWWH_CORE_MOVEMENT_CAPS_MANAGE_MOVEMENTS,
                'manage_terms'  => PWWH_CORE_MOVEMENT_CAPS_MANAGE_HOLDERS,
                'edit_terms'    => PWWH_CORE_MOVEMENT_CAPS_MANAGE_HOLDERS,
                'delete_terms'  => PWWH_CORE_MOVEMENT_CAPS_DELETE_HOLDERS);

  return $caps;
}

/**
 * @brief     Automatically configures capabilities for Movement post holder.
 * @details   There are tree level of capabilities:
 *            - Administrator: can do everything.
 *            - Manager: can do everything except perform advanced operation on
 *              movements.
 *            - User: can do nothing.
 * @note      This function assign capabilities based on already assigned
 *            capabilities:
 *            - A role which can update_core is considered Administrator.
 *            - A role which can delete_published_posts is considered Manger.
 *            - Others are considered User.
 * @api
 *
 * @param[in] mixed $role         The role object or the slug to edit. When
 *                                NULL all the roles are configured.
 *
 * @return    void.
 */
function pwwh_core_movement_caps_autoset($role = NULL) {

  if($role === NULL) {

    /* Retrieving the list of all the roles from the DB. */
    global $wpdb;
    $table_name = $wpdb->prefix;
    $roles = get_option($table_name . 'user_roles');

    /* Recalling this function per each role. */
    foreach($roles as $role => $value) {
      pwwh_core_movement_caps_autoset($role);
    }
  }
  else {
    /* Revoking all the capabilites related to Movement for this role. */
    pwwh_core_caps_api_revoke_by_context($role, PWWH_CORE_MOVEMENT);

    /* Auto-assigning capabilities depending on the user role. */
    if(pwwh_core_caps_api_role_can($role, 'update_core')) {
      /* Considering this role an administrator. */
      pwwh_core_caps_api_add($role, PWWH_CORE_MOVEMENT_CAPS_MANAGE_MOVEMENTS);
      pwwh_core_caps_api_add($role, PWWH_CORE_MOVEMENT_CAPS_MANAGE_OTHERS_MOVEMENTS);
      pwwh_core_caps_api_add($role, PWWH_CORE_MOVEMENT_CAPS_DELETE_MOVEMENTS);
      pwwh_core_caps_api_add($role, PWWH_CORE_MOVEMENT_CAPS_MANAGE_HOLDERS);
      pwwh_core_caps_api_add($role, PWWH_CORE_MOVEMENT_CAPS_DELETE_HOLDERS);
    }
    else if(pwwh_core_caps_api_role_can($role, 'edit_others_posts')) {
      /* Considering this role a manager. */
      pwwh_core_caps_api_add($role, PWWH_CORE_MOVEMENT_CAPS_MANAGE_MOVEMENTS);
      pwwh_core_caps_api_add($role, PWWH_CORE_MOVEMENT_CAPS_MANAGE_OTHERS_MOVEMENTS);
      pwwh_core_caps_api_add($role, PWWH_CORE_MOVEMENT_CAPS_MANAGE_HOLDERS);
    }
    else if(pwwh_core_caps_api_role_can($role, 'edit_published_posts')) {
      /* Considering this role a collaborator. */
      pwwh_core_caps_api_add($role, PWWH_CORE_MOVEMENT_CAPS_MANAGE_MOVEMENTS);
    }
    else {
      /* Standard user. */
    }
  }
}