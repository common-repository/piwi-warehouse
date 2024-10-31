<?php
/**
 * @file      purchases/purchase-caps.php
 * @brief     Function related to the Purchase capabilies.
 *
 * @addtogroup PWWH_CORE_PURCHASE
 * @{
 */

/**
 * @brief     Purchase post type capabilites defines.
 * @{
 */
define('PWWH_CORE_PURCHASE_CAPS_MANAGE_PURCHASES', 'manage_purchases');
define('PWWH_CORE_PURCHASE_CAPS_MANAGE_OTHERS_PURCHASES',
       'manage_others_purchases');
define('PWWH_CORE_PURCHASE_CAPS_EDIT_QUANTITIES', 'edit_quantities');
define('PWWH_CORE_PURCHASE_CAPS_DELETE_PURCHASES', 'delete_purchases');
/** @} */

/**
 * @brief     Defines the capabilites related to the purchases and returns
 *            the array required to create the post type.
 *
 * @return    array the array of capabilities required by the
 *            register_post_type().
 */
function pwwh_core_purchase_caps_init() {

  /* Registering a purchase related context. */
  $label = __('Purchase related', 'piwi-warehouse');
  $desc = __('Capabilities associated to Purchases.',
             'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc);
  pwwh_core_caps_api_register_context(PWWH_CORE_PURCHASE, $args);

  /* Registering Manage Purchases capability. */
  $label = __('Manage Purchases', 'piwi-warehouse');
  $desc = __('Allows to manage Purchases. This capability allows to browse ' .
             'Purchases create new ones, edit user\'s own Purchases.',
             'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc,
                'context' => PWWH_CORE_PURCHASE);
  pwwh_core_caps_api_register_cap(PWWH_CORE_PURCHASE_CAPS_MANAGE_PURCHASES, $args);

  /* Registering Manage Other's Purchases capability. */
  $label = __('Manage Others Purchases', 'piwi-warehouse');
  $desc = __('Allows to manage Purchases from other users.', 'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc,
                'context' => PWWH_CORE_PURCHASE,
                'dependency' => PWWH_CORE_PURCHASE_CAPS_MANAGE_PURCHASES);
  pwwh_core_caps_api_register_cap(PWWH_CORE_PURCHASE_CAPS_MANAGE_OTHERS_PURCHASES,
                          $args);

  /* Registering Edit Quantities capability. */
  $label = __('Edit Quantities', 'piwi-warehouse');
  $desc = __('Allows to edit quantities to already published items.' .
             'This is usually forbidden to preserve Warehouse history.',
             'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc,
                'context' => PWWH_CORE_PURCHASE,
                'dependency' => PWWH_CORE_PURCHASE_CAPS_MANAGE_PURCHASES);
  pwwh_core_caps_api_register_cap(PWWH_CORE_PURCHASE_CAPS_EDIT_QUANTITIES, $args);

  /* Registering Delete Purchases capability. */
  $label = __('Delete Purchases', 'piwi-warehouse');
  $desc = __('Allows to delete Purchases. This is usually forbidden to ' .
             'preserve Warehouse history.', 'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc,
                'context' => PWWH_CORE_PURCHASE,
                'dependency' => PWWH_CORE_PURCHASE_CAPS_MANAGE_PURCHASES);
  pwwh_core_caps_api_register_cap(PWWH_CORE_PURCHASE_CAPS_DELETE_PURCHASES, $args);

  $caps = array('read' => 'read',
                'read_private_posts' => 'read',
                'create_posts' => PWWH_CORE_PURCHASE_CAPS_MANAGE_PURCHASES,
                'edit_posts' => PWWH_CORE_PURCHASE_CAPS_MANAGE_PURCHASES,
                'edit_others_posts' => PWWH_CORE_PURCHASE_CAPS_MANAGE_OTHERS_PURCHASES,
                'edit_private_posts' => PWWH_CORE_PURCHASE_CAPS_MANAGE_PURCHASES,
                'publish_posts' => PWWH_CORE_PURCHASE_CAPS_MANAGE_PURCHASES,
                'edit_published_posts' => PWWH_CORE_PURCHASE_CAPS_MANAGE_PURCHASES,
                'delete_posts' => PWWH_CORE_PURCHASE_CAPS_DELETE_PURCHASES,
                'delete_others_posts' => PWWH_CORE_PURCHASE_CAPS_DELETE_PURCHASES,
                'delete_private_posts' => PWWH_CORE_PURCHASE_CAPS_DELETE_PURCHASES,
                'delete_published_posts' => PWWH_CORE_PURCHASE_CAPS_DELETE_PURCHASES);

  return $caps;
}

/**
 * @brief     Automatically configures capabilities for Purchase post type.
 * @details   There are tree level of capabilities:
 *            - Administrator: can do everything.
 *            - Manager: can do everything except perform advanced operation on
 *              purchases.
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
function pwwh_core_purchase_caps_autoset($role = NULL) {

    if($role === NULL) {

    /* Retrieving the list of all the roles from the DB. */
    global $wpdb;
    $table_name = $wpdb->prefix;
    $roles = get_option($table_name . 'user_roles');

    /* Recalling this function per each role. */
    foreach($roles as $role => $value) {
      pwwh_core_purchase_caps_autoset($role);
    }
  }
  else {
    /* Revoking all the capabilites related to Purchase for this role. */
    pwwh_core_caps_api_revoke_by_context($role, PWWH_CORE_PURCHASE);

    /* Auto-assigning capabilities depending on the user role. */
    if(pwwh_core_caps_api_role_can($role, 'update_core')) {
      /* Considering this role an administrator. */
      pwwh_core_caps_api_add($role, PWWH_CORE_PURCHASE_CAPS_MANAGE_PURCHASES);
      pwwh_core_caps_api_add($role, PWWH_CORE_PURCHASE_CAPS_MANAGE_OTHERS_PURCHASES);
      pwwh_core_caps_api_add($role, PWWH_CORE_PURCHASE_CAPS_EDIT_QUANTITIES);
      pwwh_core_caps_api_add($role, PWWH_CORE_PURCHASE_CAPS_DELETE_PURCHASES);
    }
      else if(pwwh_core_caps_api_role_can($role, 'edit_others_posts')) {
      /* Considering this role a manager. */
      pwwh_core_caps_api_add($role, PWWH_CORE_PURCHASE_CAPS_MANAGE_PURCHASES);
      pwwh_core_caps_api_add($role, PWWH_CORE_PURCHASE_CAPS_MANAGE_OTHERS_PURCHASES);
    }
    else if(pwwh_core_caps_api_role_can($role, 'edit_published_posts')) {
      /* Considering this role a collaborator. */
      pwwh_core_caps_api_add($role, PWWH_CORE_PURCHASE_CAPS_MANAGE_PURCHASES);
    }
    else {
      /* Standard user. */
    }
  }
}