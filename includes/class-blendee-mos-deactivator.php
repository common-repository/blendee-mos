<?php

/**
 * Fired during plugin deactivation
 *
 * @package    Blendee_Mos
 * @subpackage Blendee_Mos/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @package    Blendee_Mos
 * @subpackage Blendee_Mos/includes
 * @author     Your Name <email@example.com>
 */
class Blendee_Mos_Deactivator {
	public static function deactivate() {
		$siteUrlObj = wp_parse_url(get_site_url());
        $blendee_mos_adapter = defined('BLENDEE_ADAPTER_URL') ? BLENDEE_ADAPTER_URL : "https://hub-woocommerce.blendee.com";
        $ref = $siteUrlObj["host"];
        $blendeeManager = new BlendeeManager($blendee_mos_adapter, $ref);
        $adapterOptions_64 = base64_encode(json_encode(array(
            "enableTracking" => false,
            "syncProds" => false,
            "syncCollections" => false,
            "syncOrders" => false,
            "syncCustomers" => false,
            "syncContents" => false,
        )));
        $blendeeManager->updateAdapterOptionsAPI($adapterOptions_64);
	}
}
