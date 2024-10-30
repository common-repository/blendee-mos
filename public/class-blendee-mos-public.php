<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Blendee_Mos
 * @subpackage Blendee_Mos/public
 */
class Blendee_Mos_Public {

    /**
     * @access   private
     * @var string $blendee_mos
     */
    private string $blendee_mos;

    /**
     * @access   private
     * @var string $version
     */
    private string $version;

    /**
     * @access   private
     * @var string $blendee_mos_adapter
     */
    private string $blendee_mos_adapter;
    private mixed $newUserData;

    /**
     * @param string $blendee_mos
     * @param string $version
     */
    public function __construct(string $blendee_mos, string $version, string $adapterUrl) {
        $this->blendee_mos = $blendee_mos;
        $this->version = $version;
        $this->blendee_mos_adapter = $adapterUrl;
        $this->newUserData = null;
        if (isset($_COOKIE["sbn_new_user_data"])) {
            $rawUserData = sanitize_text_field($_COOKIE["sbn_new_user_data"]);
            $decodedUserData = base64_decode($rawUserData);
            if ($decodedUserData !== false) {
                $userDataArray = json_decode($decodedUserData, true);
                if (is_array($userDataArray)) {
                    $sanitizedUserData = array();
                    if (isset($userDataArray["userId"])) $sanitizedUserData["userId"] = sanitize_text_field($userDataArray["userId"]);
                    if (isset($userDataArray["userEmail"])) $sanitizedUserData["userEmail"] = sanitize_email($userDataArray["userEmail"]);
                    $this->newUserData = $sanitizedUserData;
                }
            }
        }
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->blendee_mos, plugin_dir_url(__FILE__) . 'css/blendee-mos-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->blendee_mos, plugin_dir_url(__FILE__) . 'js/blendee-mos-public.js', array('jquery'), $this->version, false);
        wp_register_script('blendee-sbn-base-param', plugin_dir_url(__FILE__) . 'js/sbn-base-param.js', array('jquery'), $this->version, false);
        wp_register_script('blendee-sbn-ecommerce-param', plugin_dir_url(__FILE__) . 'js/sbn-ecommerce-param.js', array('jquery'), $this->version, false);
        wp_register_script('blendee-load-sbn', plugin_dir_url(__FILE__) . 'js/load-sbn.js', array('jquery'), $this->version, false);
    }

    public function blendee_mos_user_register_cb($user_id, $userdata) {
        $cookieData = base64_encode(wp_json_encode(["userId" => $user_id, "userEmail" => isset($userdata["user_email"]) ? $userdata["user_email"] : null]));
        $expired = time() + (24 * 60 * 60); // 1 day
        setcookie("sbn_new_user_data", $cookieData, $expired, "/", wp_parse_url(get_site_url())['host']);
    }

    public function blendee_mos_delete_cookie_cb() {
        if (isset($_COOKIE["sbn_new_user_data"])) {
            setcookie("sbn_new_user_data", "", time() - 3600, "/", wp_parse_url(get_site_url())['host']);
        }
    }

    public function blendee_mos_wp_head_woocommerce_cb() {
        $sbnData = [];
        $current_language = $this->get_current_language();
        $responseBody = $this->getAdpterOption();
        if ($responseBody) {
            $options = json_decode($responseBody, true);
            $trackingUrl = sanitize_text_field($options["urlTracking"]);
            $sbnData["siteId"] = sanitize_text_field($options["siteId"]);
            $sbnData["userIdExt"] = get_current_user_id();
            $sbnData["language"] = $current_language;
            foreach ($options['catalogIds'] as &$item) {
                foreach ($item as $key => &$value) {
                    $key = sanitize_key($key);
                    $value = sanitize_text_field($value);
                }
            }
            $catalog = current(array_filter($options["catalogIds"], function ($catalog) use ($sbnData) {
                return array_key_exists($sbnData["language"], $catalog);
            }));
            $sbnData["catalogId"] = !$catalog ? "AUTO" : current(array_values($catalog));
            switch (true) {
                case is_front_page() && !(is_archive() || is_author() || is_category() || is_single() || is_tag()):
                    $sbnData["pageType"] = 101;
                    break;
                case is_product_category():
                    $sbnData["pageType"] = 102;
                    $sbnData["itemId"] = get_queried_object_id();
                    break;
                case is_product():
                    $sbnData["pageType"] = 103;
                    $sbnData["itemId"] = get_the_ID();
                    break;
                case is_cart():
                    $sbnData["pageType"] = 104;
                    break;
                case is_404():
                    $sbnData["pageType"] = 107;
                    break;
                case is_single():
                    $sbnData["pageType"] = 111;
                    break;
                case is_checkout() && !is_order_received_page():
                    $sbnData["pageType"] = 110;
                    break;
                case is_front_page() && (is_archive() || is_author() || is_category() || is_single() || is_tag()):
                    $sbnData["pageType"] = 112;
                    break;
                case is_search():
                    $sbnData["pageType"] = 105;
                    break;
                default:
                    $sbnData["pageType"] = 108;
                    break;
            }
            $sbnData["currency"] = sanitize_text_field(get_woocommerce_currency());
            $sbnData["orderDetail"] = [];
            if (is_order_received_page()) {
                global $wp;
                $orderId = $wp->query_vars[WC()->query->query_vars['order-received']];
                $sbnData["orderDetail"]["orderId"] = sanitize_text_field($orderId);
                $sbnData["orderDetail"]["lines"] = [];
                $order = new WC_Order($orderId);
                $couponList = $order->get_coupon_codes();
                $coupon = sizeof($couponList) > 0 ? $couponList[0] : '';
                $deliveryPrice = array_reduce($order->get_items('shipping'), function ($acc, $item) {
                    return $acc + floatval($item->get_total());
                }, 0);
                foreach ($order->get_items() as $item) {
                    $product = $item->get_product();
                    $productId = $item->is_type('variable') ? $item->get_variation_id() : $product->get_id();
                    $sbnData["orderDetail"]["lines"][] = [
                        "productId" => sanitize_text_field($productId),
                        "quantity" => intval($item->get_quantity()),
                        "coupon" => sanitize_text_field($coupon),
                        "price" => floatval($item->get_total()) - floatval($item->get_total_tax()),
                        "priceWithTax" => floatval($item->get_total()),
                        "deliveryPrice" => floatval($deliveryPrice),
                        "currency" => sanitize_text_field($order->get_currency()),
                        "dateTime" => sanitize_text_field($order->get_date_created()->__toString())
                    ];
                }
            }
            $currentCartItems = WC()->cart->get_cart();
            $sbnData["currentCartProducts"] =  array_map(function ($key) use ($currentCartItems) {
                return [
                    "key" => $key,
                    "productId" => sanitize_text_field($currentCartItems[$key]["variation_id"] != 0 ? $currentCartItems[$key]["variation_id"] : $currentCartItems[$key]["product_id"]),
                    "quantity" => intval($currentCartItems[$key]["quantity"])
                ];
            }, array_keys($currentCartItems));

            if (isset($this->newUserData)) {
                $sbnData["newUserData"] = $this->newUserData;
            }
            
            if ($options["enableTracking"] == 'true') {
                wp_localize_script('blendee-sbn-base-param', 'sbnData', $sbnData);
                wp_enqueue_script('blendee-sbn-base-param');

                wp_localize_script('blendee-sbn-ecommerce-param', 'sbnData', $sbnData);
                wp_enqueue_script('blendee-sbn-ecommerce-param');

                wp_localize_script('blendee-load-sbn', 'trackingUrl', $trackingUrl);
                wp_enqueue_script('blendee-load-sbn');
            }
        }
    }

    public function blendee_mos_wp_head_no_commerce_cb() {
        $sbnData = [];
        $current_language = $this->get_current_language();
        $responseBody = $this->getAdpterOption();
        if ($responseBody) {
            $options = json_decode($responseBody, true);
            $trackingUrl = sanitize_text_field($options["urlTracking"]);
            $sbnData["siteId"] = sanitize_text_field($options["siteId"]);
            $sbnData["userIdExt"] = get_current_user_id();
            $sbnData["language"] = $current_language;
            foreach ($options['catalogIds'] as &$item) {
                foreach ($item as $key => &$value) {
                    $key = sanitize_key($key);
                    $value = sanitize_text_field($value);
                }
            }
            $catalog = current(array_filter($options["catalogIds"], function ($catalog) use ($sbnData) {
                return array_key_exists($sbnData["language"], $catalog);
            }));
            $sbnData["catalogId"] = !$catalog ? "AUTO" : current(array_values($catalog));
            switch (true) {
                case is_front_page() && !(is_archive() || is_author() || is_category() || is_single() || is_tag()):
                    $sbnData["pageType"] = 101;
                    break;
                case is_category():
                    $sbnData["pageType"] = 102;
                    $sbnData["itemId"] = get_queried_object_id();
                    break;
                case is_404():
                    $sbnData["pageType"] = 107;
                    break;
                case is_single():
                    $sbnData["pageType"] = 111;
                    $sbnData["itemId"] = get_the_ID();
                    break;
                case is_front_page() && (is_archive() || is_author() || is_category() || is_single() || is_tag()):
                    $sbnData["pageType"] = 112;
                    break;
                case is_search():
                    $sbnData["pageType"] = 105;
                    break;
                default:
                    $sbnData["pageType"] = 108;
                    break;
            }
            if (isset($this->newUserData)) {
                $sbnData["newUserData"] = $this->newUserData;
            }
            if ($options["enableTracking"] == 'true') {
                wp_localize_script('blendee-sbn-base-param', 'sbnData', $sbnData);
                wp_enqueue_script('blendee-sbn-base-param');

                wp_localize_script('blendee-load-sbn', 'trackingUrl', $trackingUrl);
                wp_enqueue_script('blendee-load-sbn');
            }
        }
    }

    public function get_current_language() {
        switch (true) {
            case is_plugin_active('sitepress-multilingual-cms/sitepress.php'):
                $current_language = apply_filters('wpml_current_language', NULL);
                break;
            case is_plugin_active('polylang/polylang.php'):
                $current_language = pll_current_language();
                break;
            default:
                $current_language = get_locale();
                break;
        }
        $current_language = sanitize_text_field($current_language);
        return substr($current_language, 0, 2);
    }

    private function getAdpterOption() {
        $siteUrl = get_site_url();
        $siteUrlObj = wp_parse_url($siteUrl);
        $ref = $siteUrlObj["host"];
        $response = wp_remote_get(esc_url($this->blendee_mos_adapter) . '/wordpress-bridge/options?ref=' . $ref);
        $response_body = wp_remote_retrieve_response_code($response) > 300 ? null : sanitize_text_field(wp_remote_retrieve_body($response));
        return $response_body;
    }
}
