<?php

abstract class BlendeeAuth {
    protected string $storeUrl;
    protected string $adapterUrl;
    protected string $ref;
    protected Blendee_Mos_View $view;

    public function __construct($adapterUrl, $ref) {
        $this->storeUrl = get_site_url();
        $this->adapterUrl = $adapterUrl;
        $this->ref = $ref;
	}

    public abstract function generateAuthUrl();
    public abstract function removeCredentials($adapterOptions);

    public function getAdapterOptionsAPI() {
        $response = wp_remote_get($this->adapterUrl . '/wordpress-bridge/options?ref=' . $this->ref);
        $response_body = wp_remote_retrieve_body($response);
        return json_decode($response_body, true);
    }
}
