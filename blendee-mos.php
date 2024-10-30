<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 * @package           Blendee_Mos
 *
 * @wordpress-plugin
 * Plugin Name:       Blendee M.O.S.
 * Description:       Blendee maximizes sales through real-time page personalization, using advanced analytics and marketing automation for an optimal user experience
 * Version:           1.0.22
 * Author:            Blendee S.r.l.
 * Author URI:        https://www.blendee.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       blendee-mos
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'BLENDEE_MOS_VERSION', '1.0.22' );
define( 'BLENDEE_MOS_SLUG', 'blendee-mos' );
define( 'BLENDEE_ADAPTER_URL', 'https://hub-woocommerce.blendee.com' );

/**
 * The code that runs during plugin activation.
 */
function blendee_mos_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-blendee-mos-activator.php';
	Blendee_Mos_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-blendee-mos-deactivator.php
 */
function blendee_mos_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-blendee-mos-deactivator.php';
	Blendee_Mos_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'blendee_mos_activate' );
register_deactivation_hook( __FILE__, 'blendee_mos_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-blendee-mos.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
function blendee_mos_run() {
	$plugin = new Blendee_Mos();
	$plugin->run();

}
blendee_mos_run();