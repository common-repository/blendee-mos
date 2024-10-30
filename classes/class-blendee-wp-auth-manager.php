<?php

class BlendeeWPAuthManager extends BlendeeAuth {
    const BLENDEE_WP_AUTH_ENDPOINT = "/wp-admin/authorize-application.php";

    public function generateAuthUrl() {
        $uuid = wp_generate_uuid4();

        $params = [
            'success_url' => wp_nonce_url(admin_url('admin.php') . '?page=blendee-mos&app_id='.$uuid, 'blendee_nonce'),
            'reject_url' => esc_url(admin_url('admin.php') . '?page=blendee-mos'),
            'app_id' => $uuid,
        ];
        return trailingslashit($this->storeUrl) . ltrim(self::BLENDEE_WP_AUTH_ENDPOINT , '/') . '?' . http_build_query($params);
    }

    public function removeCredentials($adapterOptions){
        $user_id = intval($adapterOptions['user_id']);
        $appId = sanitize_text_field($adapterOptions['app_id']);
        $passList = WP_Application_Passwords::get_user_application_passwords($user_id);
        $password = current(array_filter($passList, function ($password) use ($appId) {
            return $password["app_id"] == $appId;
        }));
        $uuid = $password["uuid"];
        WP_Application_Passwords::delete_application_password($user_id, $uuid);
    }
}
