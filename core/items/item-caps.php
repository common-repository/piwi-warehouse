<?php
/**
 * @file      items/item-caps.php
 * @brief     Function related to the Item capabilies.
 *
 * @addtogroup PWWH_CORE_ITEM
 * @{
 */

/**
 * @brief     Item post type capabilites defines.
 * @{
 */
define('PWWH_CORE_ITEM_CAPS_MANAGE_ITEMS', 'manage_items');
define('PWWH_CORE_ITEM_CAPS_MANAGE_OTHERS_ITEMS', 'manage_others_items');
define('PWWH_CORE_ITEM_CAPS_DELETE_ITEMS', 'delete_items');
/** @} */

/**
 * @brief     Defines the capabilites related to the items and returns
 *            the array required to create the post type.
 *
 * @return    array the array of capabilities required by the
 *            register_post_type().
 */
function pwwh_core_item_caps_init() {

  /* Registering a item related context. */
  $label = __('Item related', 'piwi-warehouse');
  $desc = __('Capabilities associated to Items, Locations and Types.',
             'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc);
  pwwh_core_caps_api_register_context(PWWH_CORE_ITEM, $args);

  /* Registering Manage Items capability. */
  $label = __('Manage Items', 'piwi-warehouse');
  $desc = __('Allows to manage Items. This capability allows to browse ' .
             'Items create new ones, edit user\'s own Items, assign ' .
             'Locations and Types.', 'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc,
                'context' => PWWH_CORE_ITEM);
  pwwh_core_caps_api_register_cap(PWWH_CORE_ITEM_CAPS_MANAGE_ITEMS, $args);

  /* Registering Manage Items capability. */
  $label = __('Manage Others Items', 'piwi-warehouse');
  $desc = __('Allows to manage Items from other users.', 'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc,
                'context' => PWWH_CORE_ITEM,
                'dependency' => PWWH_CORE_ITEM_CAPS_MANAGE_ITEMS);
  pwwh_core_caps_api_register_cap(PWWH_CORE_ITEM_CAPS_MANAGE_OTHERS_ITEMS, $args);

  /* Registering Delete Items capability. */
  $label = __('Delete Items', 'piwi-warehouse');
  $desc = __('Allows to delete Items. With this capability an user can ' .
             'delete any Item, even from other author. Note that deleting ' .
             'an Item all its related Movements and Purchases will be ' .
             'destroyed. This capability is considered of high privilege.',
             'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc,
                'context' => PWWH_CORE_ITEM,
                'dependency' => PWWH_CORE_ITEM_CAPS_MANAGE_ITEMS);
  pwwh_core_caps_api_register_cap(PWWH_CORE_ITEM_CAPS_DELETE_ITEMS, $args);

  $caps = array('read' => 'read',
                'read_private_posts' => 'read',
                'create_posts' => PWWH_CORE_ITEM_CAPS_MANAGE_ITEMS,
                'edit_posts' => PWWH_CORE_ITEM_CAPS_MANAGE_ITEMS,
                'edit_others_posts' => PWWH_CORE_ITEM_CAPS_MANAGE_OTHERS_ITEMS,
                'edit_private_posts' => PWWH_CORE_ITEM_CAPS_MANAGE_ITEMS,
                'publish_posts' => PWWH_CORE_ITEM_CAPS_MANAGE_ITEMS,
                'edit_published_posts' => PWWH_CORE_ITEM_CAPS_MANAGE_ITEMS,
                'delete_posts' => PWWH_CORE_ITEM_CAPS_DELETE_ITEMS,
                'delete_others_posts' => PWWH_CORE_ITEM_CAPS_DELETE_ITEMS,
                'delete_private_posts' => PWWH_CORE_ITEM_CAPS_DELETE_ITEMS,
                'delete_published_posts' => PWWH_CORE_ITEM_CAPS_DELETE_ITEMS);

  return $caps;
}

/**
 * @brief     Location taxonomy capabilites defines.
 * @{
 */
define('PWWH_CORE_ITEM_CAPS_MANAGE_LOCATIONS', 'manage_locations');
define('PWWH_CORE_ITEM_CAPS_DELETE_LOCATIONS', 'delete_locations');
/** @} */

/**
 * @brief     Defines the capabilites related to the location taxonomy and
 *            returns the array required to create the taxonomy.
 *
 * @return    array the array of capabilities required by the
 *            register_taxonomy().
 */
function pwwh_core_item_caps_location_init() {

  /* Registering Manage Items capability. */
  $label = __('Manage Locations', 'piwi-warehouse');
  $desc = __('Allows to manage Location. With this capability the user can
              create or edit locations.', 'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc,
                'context' => PWWH_CORE_ITEM,
                'dependency' => PWWH_CORE_ITEM_CAPS_MANAGE_ITEMS);
  pwwh_core_caps_api_register_cap(PWWH_CORE_ITEM_CAPS_MANAGE_LOCATIONS, $args);

  /* Registering Delete Items capability. */
  $label = __('Delete Locations', 'piwi-warehouse');
  $desc = __('Allows to delete locations.', 'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc,
                'context' => PWWH_CORE_ITEM,
                'dependency' => PWWH_CORE_ITEM_CAPS_MANAGE_ITEMS);
  pwwh_core_caps_api_register_cap(PWWH_CORE_ITEM_CAPS_DELETE_LOCATIONS, $args);

  $caps = array('assign_terms'  => PWWH_CORE_ITEM_CAPS_MANAGE_ITEMS,
                'manage_terms'  => PWWH_CORE_ITEM_CAPS_MANAGE_LOCATIONS,
                'edit_terms'    => PWWH_CORE_ITEM_CAPS_MANAGE_LOCATIONS,
                'delete_terms'  => PWWH_CORE_ITEM_CAPS_DELETE_LOCATIONS);

  return $caps;
}

/**
 * @brief     Type taxonomy capabilites defines.
 * @{
 */
define('PWWH_CORE_ITEM_CAPS_MANAGE_TYPES', 'manage_types');
define('PWWH_CORE_ITEM_CAPS_DELETE_TYPES', 'delete_types');
/** @} */

/**
 * @brief     Defines the capabilites related to the type taxonomy and
 *            returns the array required to create the taxonomy.
 *
 * @return    array the array of capabilities required by the
 *            register_taxonomy().
 */
function pwwh_core_item_caps_type_init() {

  /* Registering Manage Items capability. */
  $label = __('Manage Types', 'piwi-warehouse');
  $desc = __('Allows to manage Type. With this capability the user can
              create or edit types.', 'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc,
                'context' => PWWH_CORE_ITEM,
                'dependency' => PWWH_CORE_ITEM_CAPS_MANAGE_ITEMS);
  pwwh_core_caps_api_register_cap(PWWH_CORE_ITEM_CAPS_MANAGE_TYPES, $args);

  /* Registering Delete Items capability. */
  $label = __('Delete Types', 'piwi-warehouse');
  $desc = __('Allows to delete types.', 'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc,
                'context' => PWWH_CORE_ITEM,
                'dependency' => PWWH_CORE_ITEM_CAPS_MANAGE_ITEMS);
  pwwh_core_caps_api_register_cap(PWWH_CORE_ITEM_CAPS_DELETE_TYPES, $args);

  $caps = array('assign_terms'  => PWWH_CORE_ITEM_CAPS_MANAGE_ITEMS,
                'manage_terms'  => PWWH_CORE_ITEM_CAPS_MANAGE_TYPES,
                'edit_terms'    => PWWH_CORE_ITEM_CAPS_MANAGE_TYPES,
                'delete_terms'  => PWWH_CORE_ITEM_CAPS_DELETE_TYPES);

  return $caps;
}

/**
 * @brief     Automatically configures capabilities for Item post type.
 * @details   There are tree level of capabilities:
 *            - Administrator: can do everything.
 *            - Manager: can do everything except perform advanced operation on
 *              items.
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
function pwwh_core_item_caps_autoset($role = NULL) {

    if($role === NULL) {

    /* Retrieving the list of all the roles from the DB. */
    global $wpdb;
    $table_name = $wpdb->prefix;
    $roles = get_option($table_name . 'user_roles');

    /* Recalling this function per each role. */
    foreach($roles as $role => $value) {
      pwwh_core_item_caps_autoset($role);
    }
  }
  else {
    /* Revoking all the capabilites related to Item for this role. */
    pwwh_core_caps_api_revoke_by_context($role, PWWH_CORE_ITEM);

    /* Auto-assigning capabilities depending on the user role. */
    if(pwwh_core_caps_api_role_can($role, 'update_core')) {
      /* Considering this role an administrator. */
      pwwh_core_caps_api_add($role, PWWH_CORE_ITEM_CAPS_MANAGE_ITEMS);
      pwwh_core_caps_api_add($role, PWWH_CORE_ITEM_CAPS_MANAGE_OTHERS_ITEMS);
      pwwh_core_caps_api_add($role, PWWH_CORE_ITEM_CAPS_DELETE_ITEMS);
      pwwh_core_caps_api_add($role, PWWH_CORE_ITEM_CAPS_MANAGE_LOCATIONS);
      pwwh_core_caps_api_add($role, PWWH_CORE_ITEM_CAPS_DELETE_LOCATIONS);
      pwwh_core_caps_api_add($role, PWWH_CORE_ITEM_CAPS_MANAGE_TYPES);
      pwwh_core_caps_api_add($role, PWWH_CORE_ITEM_CAPS_DELETE_TYPES);
    }
    else if(pwwh_core_caps_api_role_can($role, 'edit_others_posts')) {
      /* Considering this role a manager. */
      pwwh_core_caps_api_add($role, PWWH_CORE_ITEM_CAPS_MANAGE_ITEMS);
      pwwh_core_caps_api_add($role, PWWH_CORE_ITEM_CAPS_MANAGE_OTHERS_ITEMS);
      pwwh_core_caps_api_add($role, PWWH_CORE_ITEM_CAPS_MANAGE_LOCATIONS);
      pwwh_core_caps_api_add($role, PWWH_CORE_ITEM_CAPS_MANAGE_TYPES);
    }
    else if(pwwh_core_caps_api_role_can($role, 'edit_published_posts')) {
      /* Considering this role a collaborator. */
      pwwh_core_caps_api_add($role, PWWH_CORE_ITEM_CAPS_MANAGE_ITEMS);
    }
    else {
      /* Standard user. */
    }
  }
}
/** @} */