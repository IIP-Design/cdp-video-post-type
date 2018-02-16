<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/MaxOrelus
 * @since      1.0.0
 *
 * @package    Cdp_Video_Post_Type
 * @subpackage Cdp_Video_Post_Type/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Cdp_Video_Post_Type
 * @subpackage Cdp_Video_Post_Type/includes
 * @author     Max Orelus <orelusm@america.gov>
 */
class Cdp_Video_Post_Type
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Cdp_Video_Post_Type_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{

		$this->plugin_name = 'cdp-video-post-type';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->register_post_type();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Cdp_Video_Post_Type_Loader. Orchestrates the hooks of the plugin.
	 * - Cdp_Video_Post_Type_i18n. Defines internationalization functionality.
	 * - Cdp_Video_Post_Type_Admin. Defines all hooks for the admin area.
	 * - Cdp_Video_Post_Type_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cdp-video-post-type-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cdp-video-post-type-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-cdp-video-post-type-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-cdp-video-post-type-public.php';

		# create rest route
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cdp-video-post-type-rest-controller.php';

		$this->loader = new Cdp_Video_Post_Type_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Cdp_Video_Post_Type_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Cdp_Video_Post_Type_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

	}

	/**
	 * Register the post type.
	 *
	 *
	 * @since    1.0.0
	 * @access   private
	 */

	private function register_post_type() {

		// initiates video post type
		$this->loader->add_action('init', $this, 'create_custom_video_post_type', 0);

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new Cdp_Video_Post_Type_Admin($this->get_plugin_name(), $this->get_version());

		// iniate cmb2
		$this->loader->add_action('cmb2_admin_init', $plugin_admin, 'cmb2_video_post_type_metaboxes');

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		$plugin_public = new Cdp_Video_Post_Type_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

	}

	public function create_custom_video_post_type() {
		$labels = array(
			'name' => _x('Videos', 'Post Type General Name', 'cdp-video-post-type'),
			'singular_name' => _x('Video', 'Post Type Singular Name', 'cdp-video-post-type'),
			'menu_name' => __('Videos', 'cdp-video-post-type'),
			'name_admin_bar' => __('Video', 'cdp-video-post-type'),
			'archives' => __('Video Archives', 'cdp-video-post-type'),
			'attributes' => __('Video Attributes', 'cdp-video-post-type'),
			'parent_item_colon' => __('Parent Video:', 'cdp-video-post-type'),
			'all_items' => __('All Videos', 'cdp-video-post-type'),
			'add_new_item' => __('Add New Video', 'cdp-video-post-type'),
			'add_new' => __('Add New', 'cdp-video-post-type'),
			'new_item' => __('New Video', 'cdp-video-post-type'),
			'edit_item' => __('Edit Video', 'cdp-video-post-type'),
			'update_item' => __('Update Video', 'cdp-video-post-type'),
			'view_item' => __('View Video', 'cdp-video-post-type'),
			'view_items' => __('View Videos', 'cdp-video-post-type'),
			'search_items' => __('Search Video', 'cdp-video-post-type'),
			'not_found' => __('Not found', 'cdp-video-post-type'),
			'not_found_in_trash' => __('Not found in Trash', 'cdp-video-post-type'),
			'featured_image' => __('Featured Image', 'cdp-video-post-type'),
			'set_featured_image' => __('Set featured image', 'cdp-video-post-type'),
			'remove_featured_image' => __('Remove featured image', 'cdp-video-post-type'),
			'use_featured_image' => __('Use as featured image', 'cdp-video-post-type'),
			'insert_into_item' => __('Insert into video', 'cdp-video-post-type'),
			'uploaded_to_this_item' => __('Uploaded to this video', 'cdp-video-post-type'),
			'items_list' => __('Videos list', 'cdp-video-post-type'),
			'items_list_navigation' => __('Videos list navigation', 'cdp-video-post-type'),
			'filter_items_list' => __('Filter videos list', 'cdp-video-post-type'),
		);
		$args = array(
			'label' => __('Video', 'cdp-video-post-type'),
			'description' => __('CDP video custom post-type', 'cdp-video-post-type'),
			'labels' => $labels,
			'supports' => array('title'),
			//'taxonomies' => array('category', 'post_tag'),
			'taxonomies' => array(),
			'hierarchical' => false,
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_position' => 5,
			'menu_icon' => 'dashicons-video-alt3',
			'show_in_admin_bar' => true,
			'show_in_nav_menus' => true,
			'can_export' => true,
			'has_archive' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'capability_type' => 'post',
		);
		register_post_type('video', $args);
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Cdp_Video_Post_Type_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}

	public static function get_languages() {
		$languages = Language_Helper::LANGUAGE_HASH;
		$langArray = array();

		foreach( $languages as $key=>$value ) {
			$langArray[$key] = $value['display_name'];
		}

		return $langArray;
	}

}
