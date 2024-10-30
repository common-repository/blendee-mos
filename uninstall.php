<?php

/**
 * Fired when the plugin is uninstalled.
 * @package    Blendee_Mos
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

require_once plugin_dir_path(__FILE__) . 'classes/abstract-class-blendee-auth.php';
require_once plugin_dir_path(__FILE__) . 'classes/abstract-class-blendee-manager.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-blendee-wc-auth-manager.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-blendee-wp-auth-manager.php';

define('BLENDEE_ADAPTER_URL', 'https://hub-woocommerce.blendee.com');

$option_name = 'wporg_option';
delete_option($option_name);

function blendee_mos_uninstall() {
	$blendee_mos_adapter = defined('BLENDEE_ADAPTER_URL') ? BLENDEE_ADAPTER_URL : "https://hub-woocommerce.blendee.com";
	$siteUrlObj = wp_parse_url(get_site_url());
	$ref = $siteUrlObj["host"];
    $blendeeManager = new BlendeeManager($blendee_mos_adapter, $ref);
    $blendeeManager->removeCredentials();

	wp_remote_post(
		$blendee_mos_adapter . '/wordpress-bridge/uninstall',
		[
			'method' => 'POST',
			'headers' => ["Content-Type" => "application/json; charset=utf-8"],
			'body' => wp_json_encode(["ref" => $ref])
		]
	);
}

blendee_mos_uninstall();
