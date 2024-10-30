<?php

/**
 * The core plugin class.
 *
 * @package    Blendee_Mos
 * @subpackage Blendee_Mos/includes
 */
class Blendee_Mos {

	/**
	 *
	 * @access   protected
	 * @var Blendee_Mos_Loader $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * @access   protected
	 * @var string $blendee_mos The string used to uniquely identify this plugin.
	 */
	protected $blendee_mos;

	/**
	 * @access   protected
	 * @var string $version The current version of the plugin.
	 */
	protected $version;
	/**
	 * @access   protected
	 * @var string $slug Plugin page slug
	 */
	private string $slug;
	/**
	 * @access   protected
	 * @var string $blendee_mos_adapter Blendee adapter url
	 */
	private string $blendee_mos_adapter;

	public function __construct() {
		$this->version = defined('BLENDEE_MOS_VERSION') ? BLENDEE_MOS_VERSION : '0.0.0';
		$this->blendee_mos = 'blendee-mos';
		$this->slug = defined('BLENDEE_MOS_SLUG') ? BLENDEE_MOS_SLUG : 'blendee-mos';
		$this->blendee_mos_adapter = defined('BLENDEE_ADAPTER_URL') ? BLENDEE_ADAPTER_URL : "https://hub-woocommerce.blendee.com";
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 * @access   private
	 */
	private function load_dependencies() {
		if (defined('ABSPATH')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			require_once( ABSPATH . 'wp-includes/class-wp-application-passwords.php' );
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-blendee-mos-loader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-blendee-mos-i18n.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'classes/abstract-class-blendee-auth.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'classes/class-blendee-wc-auth-manager.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'classes/class-blendee-wp-auth-manager.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'classes/abstract-class-blendee-manager.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/views/abstract-class-blendee-mos-view.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/views/class-blendee-mos-connect-blendee.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/views/class-blendee-mos-allow-permissions.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/views/class-blendee-mos-blendee-settings.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-blendee-mos-admin.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-blendee-mos-public.php';

		$this->loader = new Blendee_Mos_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Blendee_Mos_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 * @access   private
	 */
	private function define_admin_hooks() {
		$siteUrl = get_site_url();
		$siteUrlObj = wp_parse_url($siteUrl);
		$ref = $siteUrlObj["host"];

		$plugin_admin = new Blendee_Mos_Admin($this->get_blendee_mos() . '-admin', $this->get_version(), $this->get_slug(), $this->blendee_mos_adapter);
		$blendeeManager = new BlendeeManager($this->blendee_mos_adapter, $ref);
		$blendee_settings_view = new Blendee_Mos_Blendee_Settings($blendeeManager);

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('admin_enqueue_scripts', $blendee_settings_view, 'enqueue_scripts_reload_page');
		$this->loader->add_action('admin_menu', $plugin_admin, 'blendee_mos_admin_menu');
		$this->loader->add_action('wp_ajax_blendee_mos_update_options', $plugin_admin, 'blendee_mos_update_options_callback');
		$this->loader->add_filter('plugin_action_links_blendee-mos/blendee-mos.php', $plugin_admin, 'blendee_mos_add_plugin_settings_link');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Blendee_Mos_Public($this->get_blendee_mos() . '-public', $this->get_version(), $this->blendee_mos_adapter);
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
		$this->loader->add_action('user_register', $plugin_public, 'blendee_mos_user_register_cb', 10, 2);
		$this->loader->add_action('init', $plugin_public, 'blendee_mos_delete_cookie_cb');
		if (function_exists('is_plugin_active') && is_plugin_active('blendee-mos/blendee-mos.php')) {
			$callback = is_plugin_active('woocommerce/woocommerce.php') ? 'blendee_mos_wp_head_woocommerce_cb' : 'blendee_mos_wp_head_no_commerce_cb';
            $this->loader->add_action('wp_head', $plugin_public, $callback);
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * @return string The name of the plugin.
	 */
	public function get_blendee_mos() {
		return $this->blendee_mos;
	}

	/**
	 * @return Blendee_Mos_Loader Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * @return string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * @return string Plugin page slug
	 */
	public function get_slug(): string {
		return $this->slug;
	}
}