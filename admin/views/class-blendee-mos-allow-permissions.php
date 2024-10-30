<?php

class Blendee_Mos_Allow_Permissions extends Blendee_Mos_View {
    public function render_view() {
        ?>
            <div class="blendee-margin-esterno">
                <h1>
                    <?php echo esc_html(__('blendee_settings', 'blendee-mos')); ?>
                </h1>
                <div class="inner-container">
                    <?php $this->blendeeActiveStatusRender(); ?>
                    <?php $this->getPermissionsStatusRender(); ?>
                    <div class="blendee-container-flex">
                        <?php
                        if ($this->validPermalink) {
                            ?>
                            <div class="allow_api_instruction" style="margin-top: 10px">
                                <?php echo esc_html(__('allow_api_instruction', 'blendee-mos')); ?> 
                            </div>
                            <?php
                        } else {
                            $this->changePermalinkRender();
                        } ?>
                    </div>
                </div>
            </div>
        <?php
        $this->getLoader();
    }
}
