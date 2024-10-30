<?php

abstract class Blendee_Mos_View {
    private string $settings_permalink_url;
    protected bool $validPermalink;
    protected BlendeeManager $blendeeManager;

    public function __construct($blendeeManager) {
        $this->settings_permalink_url = admin_url( ) . "/options-permalink.php";
        $this->validPermalink = !!get_option('permalink_structure');
        $this->blendeeManager = $blendeeManager;
    }

    public abstract function render_view();

    protected function blendeeActiveStatusRender() {
        ?>
        <div class="blendee-container-flex">
            <div class="blendee-row-flex">
                <p class="blendee-subtitle">
                    <?php echo esc_html(__('connect_to_blendee_mos', 'blendee-mos')); ?>
                </p>
                <div class="status-container">
                    <p class="status-active">
                        <?php echo esc_html(__('active', 'blendee-mos')); ?>
                    </p>
                </div>
            </div>
        </div>
        <?php
    }

    protected function getPermissionsStatusRender() {
        $wcStatus = $this->blendeeManager->wcApiIsAllowed() ? 'active' : 'deactivated';
        if ($this->blendeeManager->isWoocommerce()){
            ?>
            <div class="blendee-row-flex">
                <p class="blendee-subtitle">
                    <?php
                    if ($wcStatus != 'active') { ?><a
                            href="<?php echo esc_url($this->blendeeManager->getWcAuthManager()->generateAuthUrl()); ?>"><?php
                        }
                        echo esc_html(__('permissions_woocommerce', 'blendee-mos')); ?></a>
                </p>
                <div class="status-container">
                    <p class="status-<?php echo esc_html($wcStatus); ?>">
                        <?php
                        if ($wcStatus == 'active') echo esc_html(__('active' , 'blendee-mos'));
                        else echo esc_html(__('deactivated', 'blendee-mos'));
                        ?>
                    </p>
                </div>
            </div>
            <?php if ($wcStatus == 'deactivated') {?>
                <div class="container-small-description">
                    <p class="blendee-small-description">
                        <?php echo esc_html(__('permissions_woocommerce_instruction', 'blendee-mos'));?>
                    </p>
                </div>
                <?php
            }
        }
    }

    protected function changePermalinkRender() {
        ?>
        <div class="blendee-inner-container-big">
            <p class="blendee-subtitle" style="margin-bottom: 0;">
                <?php echo esc_html(__('inadequate_permalink', 'blendee-mos')); ?>
            </p>
            <p class="blendee-small-description">
                <?php echo esc_html(__('change_permalink_instruction', 'blendee-mos')); ?>
            </p>
            <a class="blendee-button" style="margin: 0;" target="_blank" href="<?php echo esc_url($this->settings_permalink_url); ?>">
                <?php echo esc_html(__('change_permalink', 'blendee-mos')); ?>
            </a>
        </div>
        <?php
    }

    protected function getLoader() {
        ?>
        <div id="blendee-loader-container">
            <div id="loader"></div>
        </div>
        <?php
    }
}
