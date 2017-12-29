<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/MaxOrelus
 * @since      1.0.0
 *
 * @package    Cdp_Video_Post_Type
 * @subpackage Cdp_Video_Post_Type/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cdp_Video_Post_Type
 * @subpackage Cdp_Video_Post_Type/admin
 * @author     Max Orelus <orelusm@america.gov>
 */
class Cdp_Video_Post_Type_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cdp_Video_Post_Type_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cdp_Video_Post_Type_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cdp-video-post-type-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cdp_Video_Post_Type_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cdp_Video_Post_Type_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cdp-video-post-type-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function cmb2_video_post_type_language_metabox($cmb, $group, $id) {
		$cmb->add_group_field($group, array(
			'name'             => 'Language',
			'desc'             => 'Select the language.',
			'id'               => $id,
			'type'             => 'select',
			'default'          => 'english',
			'options'          => array(
				'english' => __( 'English', 'cmb2' ),
				'french'   => __( 'French', 'cmb2' ),
				'spanish'     => __( 'Spanish', 'cmb2' ),
			),
		) );
	}

	public function cmb2_video_post_type_metaboxes() {
		$prefix = '_cdp_video_';

    foreach( glob( plugin_dir_path( __FILE__ ) . 'metaboxes/*.php') as $video_file ) {
      require_once $video_file;
    }
	}

}
