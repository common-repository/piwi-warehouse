<?php
/**
 * @file              piwi-warehouse.php
 * @brief             Plugin main file.
 *
 * @package           Piwi Plugin
 * @author            Piwi Graphics <http://piwi.graphics/>
 * @license
 * @link
 * @copyright
 *
 * @wordpress-plugin
 * Plugin Name:       Piwi Warehouse
 * Plugin URI:
 * Description:       Warehouse manager.
 * Version:           3.1.3
 * Author:            Piwi Graphics
 * Author URI:        http://piwi.graphics/
 * Text Domain:       piwi-warehouse
 * License:
 * License URI:
 * Domain Path:       /languages/
 *
 * @addtogroup        PWWH
 * @{
 */

/* Make sure we don't expose any info if called directly. */
if(!defined('ABSPATH'))
  exit;

/**
 * @brief     Prefix used to guarantee uniqueness of plugin related entries.
 */
define('PWWH_PREFIX', 'pwwh');

/**
 * @brief     Directories of this plugin.
 * @{
 */
define('PWWH_MAIN_DIR', plugin_dir_path( __FILE__ ));
define('PWWH_ADMIN_DIR', PWWH_MAIN_DIR . '/admin');
define('PWWH_CORE_DIR', PWWH_MAIN_DIR . '/core');
define('PWWH_LIB_DIR', PWWH_MAIN_DIR . '/lib');
/** @} */

/**
 * @brief     URL of this plugin.
 * @{
 */
define('PWWH_MAIN_URL', plugin_dir_url( __FILE__ ));
define('PWWH_ADMIN_URL', PWWH_MAIN_URL . 'admin');
define('PWWH_CORE_URL', PWWH_MAIN_URL . 'core');
define('PWWH_LIB_URL', PWWH_MAIN_URL . 'lib');
/** @} */

/**
 * @brief     Localization support.
 * @{
 */
define('PWWH_TEXTDOMAIN_PATH', basename(dirname(__FILE__)) . '/languages');
/** @} */

/**
 * @brief     Inclusion block.
 * @{
 */
/* Including Library. */
require_once(PWWH_LIB_DIR . '/lib.php');

/* Including Warehouse Core. */
require_once(PWWH_CORE_DIR . '/core.php');

/* Including Admin Menu handler. */
require_once(PWWH_ADMIN_DIR . '/admin.php');

/** @} */

/**
 * @brief     Activation function.
 * @details   This function is called by the activation hook. It creates new
 *            custom posts refreshing rewrite engine and creates some custom
 *            MySQL tables.
 * @note      Callback for register_activation_hook
 *
 * @return    void
 */
function pwwh_activation() {

  /* Registering custom post types. */
  pwwh_core_item_init();
  pwwh_core_movement_init();
  pwwh_core_purchase_init();

  /* Refreshing rewrite engine. */
  flush_rewrite_rules();

  /* Initializing movement history table. */
  $history = new pwwh_core_movement_history();
}
register_activation_hook(__FILE__, 'pwwh_activation');

/**
 * @brief     Deactivation function.
 * @note      Callback for register_deactivation_hook
 * @note      This does not destroys the MySQL custom tables nor deletes custom
 *            posts' entries.
 *
 * @return    void
 */
function pwwh_deactivation() {
  /* Refreshing rewrite engine. */
  flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'pwwh_deactivation');

/**
 * @brief     Load textdomain for this plugin.
 *
 * @hooked    plugins_loaded
 *
 * @return    void
 */
function pwwh_load_textdomain() {
  load_plugin_textdomain('piwi-warehouse', false, PWWH_TEXTDOMAIN_PATH);
}
add_action('plugins_loaded', 'pwwh_load_textdomain');
/** @} */