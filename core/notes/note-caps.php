<?php
/**
 * @file      notes/notes-caps.php
 * @brief     Function related to the Notes capabilies.
 *
 * @addtogroup PWWH_CORE_NOTE
 * @{
 */

/**
 * @brief     Notes capabilites defines.
 * @{
 */
define('PWWH_CORE_NOTE_CAPS_ADD_NOTES', 'add_notes');
define('PWWH_CORE_NOTE_CAPS_EDIT_NOTES', 'edit_notes');
define('PWWH_CORE_NOTE_CAPS_DELETE_NOTES', 'delete_notes');
define('PWWH_CORE_NOTE_CAPS_EDIT_OTHER_NOTES', 'edit_other_notes');
define('PWWH_CORE_NOTE_CAPS_DELETE_OTHER_NOTES', 'delete_other_notes');
/** @} */

/**
 * @brief     Defines the capabilites related to the core and returns
 *            the array required to create the post holder.
 *
 * @return    array the array of capabilities required by the
 *            register_post_holder().
 */
function pwwh_core_note_caps_register() {

  /* Registering a note related context. */
  $label = __('Note related', 'piwi-warehouse');
  $desc = __('Capabilities associated to Notes.',
             'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc);
  pwwh_core_caps_api_register_context(PWWH_CORE_NOTE, $args);

  /* Registering Add Notes capability. */
  $label = __('Add Notes', 'piwi-warehouse');
  $desc = __('Allows to add Notes into the warehouse operations.',
             'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc,
                'context' => PWWH_CORE_NOTE);
  pwwh_core_caps_api_register_cap(PWWH_CORE_NOTE_CAPS_ADD_NOTES, $args);

  /* Registering Edit Notes capability. */
  $label = __('Edit Notes', 'piwi-warehouse');
  $desc = __('Allows the user to edit his own notes.', 'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc,
                'context' => PWWH_CORE_NOTE,
                'dependency' => PWWH_CORE_NOTE_CAPS_ADD_NOTES);
  pwwh_core_caps_api_register_cap(PWWH_CORE_NOTE_CAPS_EDIT_NOTES, $args);

  /* Registering Delete Notes capability. */
  $label = __('Delete Notes', 'piwi-warehouse');
  $desc = __('Allows the user to delete his own notes.', 'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc,
                'context' => PWWH_CORE_NOTE,
                'dependency' => PWWH_CORE_NOTE_CAPS_ADD_NOTES);
  pwwh_core_caps_api_register_cap(PWWH_CORE_NOTE_CAPS_DELETE_NOTES, $args);

  /* Registering Edit Notes capability. */
  $label = __('Edit Other Notes', 'piwi-warehouse');
  $desc = __('Allows the user to edit notes from other Users.',
             'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc,
                'context' => PWWH_CORE_NOTE,
                'dependency' => PWWH_CORE_NOTE_CAPS_EDIT_NOTES);
  pwwh_core_caps_api_register_cap(PWWH_CORE_NOTE_CAPS_EDIT_OTHER_NOTES, $args);

  /* Registering Delete Notes capability. */
  $label = __('Delete Other Notes', 'piwi-warehouse');
  $desc = __('Allows the user to delete notes from other Users.',
             'piwi-warehouse');
  $args = array('label' => $label,
                'description' => $desc,
                'context' => PWWH_CORE_NOTE,
                'dependency' => PWWH_CORE_NOTE_CAPS_DELETE_NOTES);
  pwwh_core_caps_api_register_cap(PWWH_CORE_NOTE_CAPS_DELETE_OTHER_NOTES, $args);
}

/**
 * @brief     Automatically configures capabilities for Core post holder.
 * @details   There are tree level of capabilities:
 *            - Administrator: can do everything.
 *            - Manager: can do everything except perform advanced operation on
 *              core.
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
function pwwh_core_note_caps_autoset($role = NULL) {

  if($role === NULL) {

    /* Retrieving the list of all the roles from the DB. */
    global $wpdb;
    $table_name = $wpdb->prefix;
    $roles = get_option($table_name . 'user_roles');

    /* Recalling this function per each role. */
    foreach($roles as $role => $value) {
      pwwh_core_note_caps_autoset($role);
    }
  }
  else {
    /* Revoking all the capabilites related to Core for this role. */
    pwwh_core_caps_api_revoke_by_context($role, PWWH_CORE_NOTE);

    /* Auto-assigning capabilities depending on the user role. */
    if(pwwh_core_caps_api_role_can($role, 'update_core')) {
      /* Considering this role an administrator. */
      pwwh_core_caps_api_add($role, PWWH_CORE_NOTE_CAPS_ADD_NOTES);
      pwwh_core_caps_api_add($role, PWWH_CORE_NOTE_CAPS_EDIT_NOTES);
      pwwh_core_caps_api_add($role, PWWH_CORE_NOTE_CAPS_DELETE_NOTES);
      pwwh_core_caps_api_add($role, PWWH_CORE_NOTE_CAPS_EDIT_OTHER_NOTES);
      pwwh_core_caps_api_add($role, PWWH_CORE_NOTE_CAPS_DELETE_OTHER_NOTES);
    }
    else if(pwwh_core_caps_api_role_can($role, 'edit_others_posts')) {
      /* Considering this role a manager. */
      pwwh_core_caps_api_add($role, PWWH_CORE_NOTE_CAPS_ADD_NOTES);
      pwwh_core_caps_api_add($role, PWWH_CORE_NOTE_CAPS_EDIT_NOTES);
      pwwh_core_caps_api_add($role, PWWH_CORE_NOTE_CAPS_DELETE_NOTES);
    }
    else if(pwwh_core_caps_api_role_can($role, 'edit_published_posts')) {
      /* Considering this role a collaborator. */
      pwwh_core_caps_api_add($role, PWWH_CORE_NOTE_CAPS_ADD_NOTES);
      pwwh_core_caps_api_add($role, PWWH_CORE_NOTE_CAPS_EDIT_NOTES);
    }
    else {
      /* Standard user. */
    }
  }
}