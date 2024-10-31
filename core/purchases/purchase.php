<?php
/**
 * @file      purchases/purchase.php
 * @brief     Hooks and function related to Purchase post type.
 *
 * @addtogroup PWWH_CORE_PURCHASE
 * @{
 */

/**
 * @brief     Purchase defines.
 * @{
 */
define('PWWH_CORE_PURCHASE', PWWH_PREFIX . '_purchase');

/* Labels identifiers. */
define('PWWH_CORE_PURCHASE_LABEL_SINGULAR', __('Purchase', 'piwi-warehouse'));
define('PWWH_CORE_PURCHASE_LABEL_PLURAL', __('Purchases', 'piwi-warehouse'));

/* Nonces actions. */
define('PWWH_CORE_PURCHASE_NONCE_EDIT', PWWH_CORE_PURCHASE . '_nonce_edit');
/** @} */

/**
 * @brief     Inclusion block.
 * @{
 */
require_once(PWWH_CORE_PURCHASE_DIR . '/purchase-api.php');
require_once(PWWH_CORE_PURCHASE_DIR . '/purchase-ajax.php');
require_once(PWWH_CORE_PURCHASE_DIR . '/purchase-caps.php');
require_once(PWWH_CORE_PURCHASE_DIR . '/purchase-hook.php');
require_once(PWWH_CORE_PURCHASE_DIR . '/purchase-list.php');
require_once(PWWH_CORE_PURCHASE_DIR . '/purchase-ui.php');
/** @} */

/** @} */