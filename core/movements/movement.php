<?php
/**
 * @file      movements/movement.php
 * @brief     Hooks and function related to Movement post type.
 *
 * @addtogroup PWWH_CORE_MOVEMENT
 * @{
 */

/**
 * @brief     Movement defines.
 * @{
 */
define('PWWH_CORE_MOVEMENT', PWWH_PREFIX . '_movement');

/* Taxonomy identifiers. */
define('PWWH_CORE_MOVEMENT_HOLDER', PWWH_PREFIX . '_holder');

/* Custom statuses. */
define('PWWH_CORE_MOVEMENT_STATUS_ACTIVE', 'active');
define('PWWH_CORE_MOVEMENT_STATUS_CONCLUDED', 'concluded');

/* Labels identifiers. */
define('PWWH_CORE_MOVEMENT_LABEL_SINGULAR', __('Movement', 'piwi-warehouse'));
define('PWWH_CORE_MOVEMENT_LABEL_PLURAL', __('Movements', 'piwi-warehouse'));
define('PWWH_CORE_MOVEMENT_HOLDER_LABEL_SINGULAR', __('Holder', 'piwi-warehouse'));
define('PWWH_CORE_MOVEMENT_HOLDER_LABEL_PLURAL', __('Holders', 'piwi-warehouse'));

/* Nonces actions. */
define('PWWH_CORE_MOVEMENT_NONCE_EDIT', PWWH_CORE_MOVEMENT . '_nonce_edit');
/** @} */

/**
 * @brief     Inclusion block.
 * @{
 */
require_once(PWWH_CORE_MOVEMENT_DIR . '/movement-api.php');
require_once(PWWH_CORE_MOVEMENT_DIR . '/class/movement-history.php');
require_once(PWWH_CORE_MOVEMENT_DIR . '/class/movement-history-list.php');
require_once(PWWH_CORE_MOVEMENT_DIR . '/movement-ajax.php');
require_once(PWWH_CORE_MOVEMENT_DIR . '/movement-caps.php');
require_once(PWWH_CORE_MOVEMENT_DIR . '/movement-hook.php');
require_once(PWWH_CORE_MOVEMENT_DIR . '/movement-list.php');
require_once(PWWH_CORE_MOVEMENT_DIR . '/movement-ui.php');
/** @} */

/** @} */