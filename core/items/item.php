<?php
/**
 * @file      items/item.php
 * @brief     Hooks and function related to Item post type.
 *
 * @addtogroup PWWH_CORE_ITEM
 * @{
 */

/**
 * @brief     Item defines.
 * @{
 */
define('PWWH_CORE_ITEM', PWWH_PREFIX . '_item');

/* Taxonomy identifiers. */
define('PWWH_CORE_ITEM_LOCATION', PWWH_PREFIX . '_location');
define('PWWH_CORE_ITEM_TYPE', PWWH_PREFIX . '_type');

/* Labels identifiers. */
define('PWWH_CORE_ITEM_LABEL_SINGULAR', __('Item', 'piwi-warehouse'));
define('PWWH_CORE_ITEM_LABEL_PLURAL', __('Items', 'piwi-warehouse'));
define('PWWH_CORE_ITEM_LOCATION_LABEL_SINGULAR', __('Location',
                                                    'piwi-warehouse'));
define('PWWH_CORE_ITEM_LOCATION_LABEL_PLURAL', __('Locations',
                                                  'piwi-warehouse'));
define('PWWH_CORE_ITEM_TYPE_LABEL_SINGULAR', __('Type', 'piwi-warehouse'));
define('PWWH_CORE_ITEM_TYPE_LABEL_PLURAL', __('Types', 'piwi-warehouse'));
/** @} */

/**
 * @brief     Inclusion block.
 * @{
 */
require_once(PWWH_CORE_ITEM_DIR . '/item-api.php');
require_once(PWWH_CORE_ITEM_DIR . '/item-ajax.php');
require_once(PWWH_CORE_ITEM_DIR . '/item-caps.php');
require_once(PWWH_CORE_ITEM_DIR . '/item-hook.php');
require_once(PWWH_CORE_ITEM_DIR . '/item-list.php');
require_once(PWWH_CORE_ITEM_DIR . '/item-ui.php');
require_once(PWWH_CORE_ITEM_DIR . '/class/walker-locations.php');
/** @} */

/** @} */