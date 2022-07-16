<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://esrgear.com/
 * @since             1.0.0
 * @package           Esr_Woo_Extended
 *
 * @wordpress-plugin
 * Plugin Name:       ESR - WooCommerce Extended
 * Plugin URI:        https://esrgear.com/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Kline Lozada (Cross Border Digital)
 * Author URI:        https://crossborderdigital.cn
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       esr-woo-extended
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ESR_WOO_EXTENDED_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-esr-woo-extended-activator.php
 */
function activate_esr_woo_extended() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-esr-woo-extended-activator.php';
	Esr_Woo_Extended_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-esr-woo-extended-deactivator.php
 */
function deactivate_esr_woo_extended() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-esr-woo-extended-deactivator.php';
	Esr_Woo_Extended_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_esr_woo_extended' );
register_deactivation_hook( __FILE__, 'deactivate_esr_woo_extended' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-esr-woo-extended.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_esr_woo_extended() {

	$plugin = new Esr_Woo_Extended();
	$plugin->run();

}
run_esr_woo_extended();
