<?php

class Blendee_Mos_Blendee_Settings extends Blendee_Mos_View {
    public function render_view() {
?>
        <div class="blendee-margin-esterno">
            <h1> <?php echo esc_html(__('blendee_settings', 'blendee-mos')); ?> </h1>
            <div class="inner-container">
                <?php $this->blendeeActiveStatusRender(); ?>
                <?php $this->getPermissionsStatusRender(); ?>
                <?php $this->syncOptionsRender(); ?>
            </div>
        </div>
    <?php
        $this->getLoader();
    }

    private function syncOptionsRender() {
        $adapterOption = $this->blendeeManager->getAdapterOptions();
        $ref = $this->blendeeManager->getRef();
        $lastSyncList = $this->blendeeManager->getLastSyncDate();
    ?>
        <div class="blendee-margin-esterno">
            <form class="blendee-sync-form" id="updateOptions" method="post" action="">
                <div class="div-form">
                    <div class="div-checkbox">
                        <label for="active" style="font-weight: bold; margin-bottom: 10px">
                            <?php echo esc_html(__('enable_tracking', 'blendee-mos')); ?>
                        </label>
                        <input type="checkbox" id="active" name="active" <?php echo $adapterOption['enableTracking'] == "true" ? 'checked' : '' ?>>
                        <label class="toggle-switch" for="active"></label>
                    </div>
                </div>
                <?php if ($this->blendeeManager->isWoocommerce()) { ?>
                    <div class="synchronization-title">
                        <p class="blendee-subtitle" style="margin: 0">
                            <?php echo esc_html(__('synchronization', 'blendee-mos')); ?>
                        </p>
                        <p class="blendee-small-description">
                            <?php echo esc_html(__('enable_import_precesses', 'blendee-mos')); ?>
                        </p>
                    </div>
                    <div>
                        <div class="div-form">
                            <div class="div-checkbox">
                                <div class="colonna">
                                    <label class="grassetto" <?php if (!$this->blendeeManager->wcApiIsAllowed()) echo 'style="opacity:0.4"'; ?> for="products"><?php echo esc_html(__('sync_prods', 'blendee-mos')); ?></label>
                                    <?php if (isset($lastSyncList["products"])) : ?> <span><?php echo esc_html(__('last_sync', 'blendee-mos')) . ' ' . esc_html($lastSyncList["products"]); ?></span> <?php endif; ?>
                                </div>
                                <input <?php if (!$this->blendeeManager->wcApiIsAllowed()) echo 'disabled"'; ?> type="checkbox" id="products" name="products" <?php echo esc_attr($adapterOption['syncProds'] == "true" ? 'checked' : '') ?>>
                                <label <?php if (!$this->blendeeManager->wcApiIsAllowed()) echo 'style="opacity:0.4"'; ?> class="toggle-switch" for="products"></label>
                            </div>
                            <div class="div-checkbox">
                                <div class="colonna">
                                    <label class="grassetto" <?php if (!$this->blendeeManager->wcApiIsAllowed()) echo 'style="opacity:0.4"'; ?> for="orders"><?php echo esc_html(__('sync_orders', 'blendee-mos')); ?></label>
                                    <?php if (isset($lastSyncList["orders"])) : ?> <span><?php echo esc_html(__('last_sync', 'blendee-mos')) . ' ' . esc_html($lastSyncList["orders"]); ?></span> <?php endif; ?>
                                </div>
                                <input <?php if (!$this->blendeeManager->wcApiIsAllowed()) echo 'disabled"'; ?> type="checkbox" id="orders" name="orders" <?php echo esc_attr($adapterOption['syncOrders'] == "true" ? 'checked' : '') ?>>
                                <label <?php if (!$this->blendeeManager->wcApiIsAllowed()) echo 'style="opacity:0.4"'; ?> class="toggle-switch" for="orders"></label>
                            </div>
                            <div class="div-checkbox">
                                <div class="colonna">
                                    <label class="grassetto" for="categories"><?php echo esc_html(__('sync_categories', 'blendee-mos')); ?></label>
                                    <?php if (isset($lastSyncList["categories"])) : ?> <span><?php echo esc_html(__('last_sync', 'blendee-mos')) . ' ' . esc_html($lastSyncList["categories"]); ?></span> <?php endif; ?>
                                </div>
                                <input type="checkbox" id="categories" name="categories" <?php echo esc_attr($adapterOption['syncCollections'] == "true" ? 'checked' : ''); ?>>
                                <label class="toggle-switch" for="categories"></label>
                            </div>
                            <div class="div-checkbox">
                                <div class="colonna">
                                    <label class="grassetto" for="users"><?php echo esc_html(__('sync_users', 'blendee-mos')); ?></label>
                                    <?php if (isset($lastSyncList["users"])) : ?> <span><?php echo esc_html(__('last_sync', 'blendee-mos')) . ' ' . esc_html($lastSyncList["users"]); ?></span> <?php endif; ?>
                                </div>
                                <input type="checkbox" id="users" name="users" <?php echo esc_attr($adapterOption['syncCustomers'] == "true" ? 'checked' : '') ?>>
                                <label class="toggle-switch" for="users"></label>
                            </div>
                        </div>
                    <?php } ?>
                    <input type="hidden" id="ref" value="<?php echo esc_attr($ref); ?>">
                    <?php wp_nonce_field('blendee_nonce_action', 'blendee_nonce'); ?>
                    <input type="submit" value="<?php echo esc_html(__('save_changes_button', 'blendee-mos')); ?>">
                    </div>
            </form>

        </div>
    <?php
    }

    public function enqueue_scripts_reload_page($hook_suffix) {
        if ($hook_suffix === 'toplevel_page_blendee-mos') {
            wp_register_script('dummy-handle', '');
            wp_enqueue_script('dummy-handle');

            $inline_script = "
                (function() {
                    let currentURL = window.location.href;
                    let pageIndex = currentURL.indexOf('page=blendee-mos');
                    if (currentURL.length > (pageIndex + 'page=blendee-mos'.length)) {
                        let newURL = currentURL.substring(0, pageIndex + 'page=blendee-mos'.length);
                        window.location.href = newURL;
                    }
                })();
            ";
            wp_add_inline_script('dummy-handle', $inline_script);
        }
    }
}
