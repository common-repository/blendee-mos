<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @package    Blendee_Mos
 * @subpackage Blendee_Mos/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 * @package    Blendee_Mos
 * @subpackage Blendee_Mos/includes
 * @author     Your Name <email@example.com>
 */
class Blendee_Mos_i18n {
	public function load_plugin_textdomain() {
		load_plugin_textdomain('blendee-mos', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/');
	}
}
