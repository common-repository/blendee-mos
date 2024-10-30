<?php

class BlendeeWCAuthManager extends BlendeeAuth {
    const BLENDEE_WC_AUTH_ENDPOINT = '/wc-auth/v1/authorize';

    public function generateAuthUrl() {
        $params = [
            'app_name' => 'Blendee M.O.S.',
            'scope' => 'read',
            'user_id' => sanitize_text_field($this->ref),
            'return_url' => esc_url(admin_url('admin.php?page=blendee-mos')),
            'callback_url' => esc_url($this->adapterUrl . '/wordpress-bridge/callbackurl')
        ];
        return trailingslashit($this->storeUrl) . ltrim(self::BLENDEE_WC_AUTH_ENDPOINT, '/') . '?' . http_build_query($params);
    }

    public function removeCredentials($adapterOptions){
        $key_id = intval($adapterOptions['key_id']);
        if ($key_id){
            global $wpdb;
            $table_name = $wpdb->prefix . 'woocommerce_api_keys';
            $wpdb->delete($table_name, array('key_id' => $key_id), array('%d')); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        }
    }
}
