<?php

class Blendee_Mos_Connect_Blendee extends Blendee_Mos_View {
    const BLENDEE_APP_BLENDEE_URL = 'https://app.blendee.com';

    public function render_view() {
        $siteUrl = get_site_url();
        $siteUrlObj = wp_parse_url($siteUrl);
        $ref = sanitize_text_field($siteUrlObj["host"]);
?>
        <div class="blendee-container">
            <div>
                <?php
                echo '<img src = ' . esc_html(get_site_url()) . '/wp-content/plugins/blendee-mos/assets/Blendee_Marketing_Operating_System.svg' . ' class="logo-esteso" alt="logo">';
                ?>
                <h2>
                    <?php echo esc_html(__('connect_account_with_blendee', 'blendee-mos')); ?>
                </h2>
                <p>
                    <?php echo esc_html(__('description_connection_to_blendee', 'blendee-mos')); ?>
                </p>
                <div class="list-block">
                    <div class="connect-row">
                        <div class="blendee-row-flex">
                            <div class="number-circle"><span>1</span></div>
                            <p class="instruction">
                                <?php echo esc_html(__('instruction_1', 'blendee-mos')); ?>
                            </p>
                        </div>
                        <div class="copia-url">
                            <span id="textToCopy">
                                <?php echo esc_html($ref); ?>
                            </span>
                            <button id="copy_button" class="button-blendee-1 button-boreder-0">
                                <?php echo esc_html(__('copy', 'blendee-mos')); ?>
                            </button>
                        </div>
                    </div>
                    <div class="connect-row">
                        <div class="blendee-row-flex">
                            <div class="number-circle"><span>2</span></div>
                            <p class="instruction">
                                <?php echo esc_html(__('instruction_2', 'blendee-mos')); ?>
                            </p>
                        </div>
                        <div>
                            <a href="<?php echo esc_url(self::BLENDEE_APP_BLENDEE_URL); ?>" target="_blank" class="button-blendee-1">
                                <?php echo esc_html(__('connect_to_platform', 'blendee-mos')); ?>
                            </a>
                        </div>
                    </div>
                    <div class="connect-row">
                        <div class="blendee-row-flex">
                            <div class="number-circle"><span>3</span></div>
                            <p class="instruction">
                                <?php echo esc_html(__('instruction_3', 'blendee-mos')); ?>
                            </p>
                        </div>
                        <div>
                            <a href="<?php echo esc_url(sanitize_url($_SERVER['REQUEST_URI'])); ?>" class="button-blendee-2">
                                <?php echo esc_html(__('reload_page', 'blendee-mos')); ?> &#8635
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="video_container">
                <video width="100%" style="border-bottom: 1px solid #a9a9a9;" controls poster="<?php echo esc_url(get_site_url()) . '/wp-content/plugins/blendee-mos/assets/Blendee_thumbnail.jpeg'; ?>">
                    <?php
                    $video_url = esc_url(get_site_url() . '/wp-content/plugins/blendee-mos/assets/Flusso_Configurazione_Connettore.mp4');
                    echo '<source src="' . esc_url($video_url) . '">';
                    ?>
                    Your browser does not support the video tag.
                </video>
            </div>

        </div>
<?php
        $this->getLoader();
    }
}
