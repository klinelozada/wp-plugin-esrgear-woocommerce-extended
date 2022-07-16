<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://esrgear.com/
 * @since      1.0.0
 *
 * @package    Esr_Woo_Extended
 * @subpackage Esr_Woo_Extended/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Esr_Woo_Extended
 * @subpackage Esr_Woo_Extended/includes
 * @author     ESRGear <admin@esrgear.com>
 */
class Esr_Woo_Extended_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'esr-woo-extended',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
