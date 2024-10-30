<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Blendee_Mos
 * @subpackage Blendee_Mos/admin
 */
class Blendee_Mos_Admin {

	/**
	 * @access   private
	 * @var string $blendee_mos The ID of this plugin.
	 */
	private $blendee_mos;

	/**
	 * @access private
	 * @var string $version The current version of this plugin.
	 */
	private string $version;
	private string $pluginBaseName;
	private string $slug;
	private string $blendee_mos_adapter;
	private ?BlendeeManager $blendeeManager;

	/**
	 * @param string $blendee_mos
	 * @param string $version
	 */
	public function __construct($blendee_mos, $version, $slug, $adapterUrl) {
		$this->blendee_mos = $blendee_mos;
		$this->version = $version;
		$this->slug = $slug;
		$this->pluginBaseName = plugin_basename(__FILE__);
		$this->blendee_mos_adapter = $adapterUrl;
		$this->blendeeManager = null;
	}

	/**
	 * @return string
	 */
	public function getPluginBaseName(): string {
		return $this->pluginBaseName;
	}

	/**
	 * @return string
	 */
	public function getSlug(): string {
		return $this->slug;
	}

	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() {
		wp_enqueue_style($this->blendee_mos, plugin_dir_url(__FILE__) . 'css/blendee-mos-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script($this->blendee_mos, plugin_dir_url(__FILE__) . 'js/blendee-mos-admin.js', array('jquery'), $this->version, false);
		wp_localize_script('blendee-mos-admin', 'updateOptions', array('ajaxurl' => admin_url('admin-ajax.php')));
	}

	/**
	 * Add plugin to menu
	 */
	public function blendee_mos_admin_menu() {
		add_menu_page(
			'Blendee M.O.S.',
			'Blendee M.O.S.',
			'manage_options',
			$this->getSlug(),
			array($this, 'blendee_mos_display_setting_page'),
			get_site_url() . '/wp-content/plugins/blendee-mos/assets/Blendee-logo.svg'
		);
	}

	/**
	 * Add settings link
	 */
	public function blendee_mos_add_plugin_settings_link($links) {
		array_unshift($links, '<a href="' . admin_url('admin.php?page=' . $this->getSlug()) . '">Impostazioni</a>');
		return $links;
	}

	/**
	 * Display admin view
	 */
	public function blendee_mos_display_setting_page() {
		$siteUrl = get_site_url();
		$siteUrlObj = wp_parse_url($siteUrl);
		$ref = $siteUrlObj["host"];

		$this->blendeeManager = new BlendeeManager($this->blendee_mos_adapter, $ref);

		$adapterOptions = $this->blendeeManager->getAdapterOptionsAPI();

		$this->blendeeManager->setAdapterOptions($adapterOptions);
		$this->blendeeManager->getViewToRender()->render_view();
	}

	public function blendee_mos_update_options_callback() {
		if (isset($_POST['blendee_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['blendee_nonce'])), 'blendee_nonce_action')) {
			$ref = isset($_POST['ref']) ? sanitize_text_field(wp_unslash($_POST['ref'])) : '';
			$adapterOptions_64 = isset($_POST['adapterOptions_64']) ? sanitize_text_field(wp_unslash($_POST['adapterOptions_64'])) : '';
	
			if (!$this->blendeeManager) {
				$this->blendeeManager = new BlendeeManager($this->blendee_mos_adapter, $ref);
			}
			$this->blendeeManager->updateAdapterOptionsAPI($adapterOptions_64);
		} else {
			wp_die('Nonce invalid, retry.');
		}
	}
	
}
