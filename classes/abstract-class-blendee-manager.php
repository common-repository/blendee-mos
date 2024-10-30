<?php

class BlendeeManager {
    protected string $storeUrl;
    protected string $adapterUrl;
    protected string $ref;
    private array|null $adapterOptions;
    protected Blendee_Mos_View $view;
    protected BlendeeWCAuthManager $wcAuthManager;
    protected BlendeeWPAuthManager $wpAuthManager;

    public function __construct($adapterUrl, $ref) {
        $this->storeUrl = get_site_url();
        $this->adapterUrl = $adapterUrl;
        $this->ref = $ref;
        $this->adapterOptions = null;
        $this->wcAuthManager = new BlendeeWCAuthManager($adapterUrl, $ref);
        $this->wpAuthManager = new BlendeeWPAuthManager($adapterUrl, $ref);
    }

    /**
     * @return string
     */
    public function getRef(): string {
        return $this->ref;
    }

    /**
     * @param string $ref
     * @return BlendeeManager
     */
    public function setRef(string $ref): BlendeeManager {
        $this->ref = $ref;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getAdapterOptions(): ?array {
        if (!$this->adapterOptions) {
            $this->setAdapterOptions($this->getAdapterOptionsAPI());
        }
        return $this->adapterOptions;
    }

    /**
     * @param array|null $adapterOptions
     * @return BlendeeManager
     */
    public function setAdapterOptions(?array $adapterOptions): BlendeeManager {
        $this->adapterOptions = $adapterOptions;
        return $this;
    }

    /**
     * @return BlendeeWCAuthManager
     */
    public function getWcAuthManager(): BlendeeWCAuthManager {
        return $this->wcAuthManager;
    }

    /**
     * @return BlendeeWPAuthManager
     */
    public function getWpAuthManager(): BlendeeWPAuthManager {
        return $this->wpAuthManager;
    }

    public function isWoocommerce() {
        return function_exists('is_plugin_active') && is_plugin_active('woocommerce/woocommerce.php');
    }

    public function wpApiIsAllowed() {
        return !empty($this->adapterOptions) && $this->adapterOptions["wp_allowed_api"];
    }

    public function wcApiIsAllowed() {
        return !empty($this->adapterOptions) && $this->adapterOptions["wc_allowed_api"];
    }

    public function updateAdapterOptionsAPI($adapterOptions_64) {
        $url = esc_url($this->adapterUrl . '/wordpress-bridge/options');
        $this->updateAdapterOptions($url, $this->ref, $adapterOptions_64);
    }

    private function updateAdapterOptions($url, $ref, $adapterOptions_64) {
        wp_remote_post(
            $url,
            [
                'method' => 'POST',
                'headers' => ["Content-Type" => "application/json; charset=utf-8"],
                'body' => wp_json_encode(["ref" => $ref, "adapterOptions_64" => $adapterOptions_64])
            ]
        );
    }

    public function getAdapterOptionsAPI() {
        $response = wp_remote_get(esc_url($this->adapterUrl . '/wordpress-bridge/options?ref=' . $this->ref));
        $response_body = wp_remote_retrieve_response_code($response) > 300 ? null : sanitize_text_field(wp_remote_retrieve_body($response));
        return json_decode($response_body, true);
    }

    public function getLastSyncDate() {
        $response = wp_remote_get($this->adapterUrl . '/wordpress-bridge/last-sync-date?ref=' . $this->ref);
        $response_body = wp_remote_retrieve_body($response);
        return json_decode($response_body, true);
    }

    public function getViewToRender(): Blendee_Mos_View {
        switch (true) {
            case empty($this->adapterOptions):
                $view = new Blendee_Mos_Connect_Blendee($this);
                break;
            case !empty($this->adapterOptions) && ($this->adapterOptions["allowed_api"] || !$this->isWoocommerce()):
                $view = new Blendee_Mos_Blendee_Settings($this);
                break;
            default:
                $view = new Blendee_Mos_Allow_Permissions($this);
                break;
        }
        return $view;
    }

    public function removeCredentials() {
        $this->wpAuthManager->removeCredentials($this->adapterOptions);
        if ($this->isWoocommerce()) {
            $this->wcAuthManager->removeCredentials($this->adapterOptions);
        }
    }
}
