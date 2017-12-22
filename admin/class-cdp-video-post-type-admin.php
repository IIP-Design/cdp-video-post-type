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

	function create_custom_video_post_type()
	{
		$labels = array(
			'name' => _x('Videos', 'Post Type General Name', 'text_domain'),
			'singular_name' => _x('Video', 'Post Type Singular Name', 'text_domain'),
			'menu_name' => __('Videos', 'text_domain'),
			'name_admin_bar' => __('Video', 'text_domain'),
			'archives' => __('Video Archives', 'text_domain'),
			'attributes' => __('Video Attributes', 'text_domain'),
			'parent_item_colon' => __('Parent Video:', 'text_domain'),
			'all_items' => __('All Videos', 'text_domain'),
			'add_new_item' => __('Add New Video', 'text_domain'),
			'add_new' => __('Add New', 'text_domain'),
			'new_item' => __('New Video', 'text_domain'),
			'edit_item' => __('Edit Video', 'text_domain'),
			'update_item' => __('Update Video', 'text_domain'),
			'view_item' => __('View Video', 'text_domain'),
			'view_items' => __('View Videos', 'text_domain'),
			'search_items' => __('Search Video', 'text_domain'),
			'not_found' => __('Not found', 'text_domain'),
			'not_found_in_trash' => __('Not found in Trash', 'text_domain'),
			'featured_image' => __('Featured Image', 'text_domain'),
			'set_featured_image' => __('Set featured image', 'text_domain'),
			'remove_featured_image' => __('Remove featured image', 'text_domain'),
			'use_featured_image' => __('Use as featured image', 'text_domain'),
			'insert_into_item' => __('Insert into video', 'text_domain'),
			'uploaded_to_this_item' => __('Uploaded to this video', 'text_domain'),
			'items_list' => __('Videos list', 'text_domain'),
			'items_list_navigation' => __('Videos list navigation', 'text_domain'),
			'filter_items_list' => __('Filter videos list', 'text_domain'),
		);
		$args = array(
			'label' => __('Video', 'text_domain'),
			'description' => __('CDP video custom post-type', 'text_domain'),
			'labels' => $labels,
			'supports' => array('title', 'thumbnail'),
			'taxonomies' => array('category', 'post_tag'),
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

	function cmb2_video_post_type_language_metabox($cmb, $group, $id) {
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

	function cmb2_video_post_type_metaboxes()
	{
		$prefix = '_cdp_';

		$cmb = new_cmb2_box(array(
			'id' => $prefix . 'video_post_type_metabox',
			'title' => __('Videos', 'cmb2'),
			'object_types' => array('video', ),
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => true
		));

		$group_files = $cmb->add_field( array(
			'id'          => $prefix . 'video_post_type_group_files',
			'type'        => 'group',
			'description' => __( 'Add your video files and/or streaming URL.', 'cmb2' ),
			'repeatable'  => true,
			'options'     => array(
				'group_title'   => __( 'Video File {#}', 'cmb2' ), // since version 1.1.4, {#} gets replaced by row number
				'add_button'    => __( 'Add Another Video File', 'cmb2' ),
				'remove_button' => __( 'Remove File', 'cmb2' ),
				'sortable'      => true, // beta
				// 'closed'     => true, // true to have the groups closed by default
			),
		) );

		$cmb->add_group_field($group_files, array(
			'name'    => 'Video Title',
			'desc'    => 'Title of the video',
			'id'      => $prefix . 'video_post_type_group_info_title',
			'type'    => 'text',
		) );

		$cmb->add_group_field($group_files, array(
			'name' => 'Description',
			'desc' => 'Description for this context (optional)',
			'id' => $prefix . 'video_post_type_group_info_description',
			'type' => 'textarea_small'
		));

		$cmb->add_group_field($group_files, array(
			'name' => 'Video File',
			'desc' => 'Upload a video file if you would like it downloadable.',
			'id' => $prefix . 'video_post_type_group_files',
			'type' => 'file',
			'options' => array(
				'url' => false,
			),
			'text' => array(
				'add_upload_file_text' => 'Add Video'
			),
			'query_args' => array(
				'type' => array(
				  'video/mp4',
				  'video/mpeg',
				  'video/avi'
				)
			),
		));

		$cmb->add_group_field($group_files, array(
			'name' => 'Streaming URL',
			'desc' => 'Enter a streaming URL.',
			'id' => $prefix . 'video_post_type_video_url',
			'type' => 'oembed',
		));

		$cmb->add_group_field($group_files, array(
			'name'             => 'Captions',
			'desc'             => 'Does the video contain burned-in captions?',
			'id'               => $prefix . 'video_post_type_video_captions',
			'type'             => 'select',
			'default'          => 'no',
			'options'          => array(
				'no' => __( 'No', 'cmb2' ),
				'yes'   => __( 'Yes', 'cmb2' ),
			),
		) );

		Cdp_Video_Post_Type_Admin::cmb2_video_post_type_language_metabox($cmb, $group_files, $prefix . 'video_post_type_group_files_language');

		$group_srt_files = $cmb->add_field( array(
			'id'          => $prefix . 'video_post_type_group_srt_files',
			'type'        => 'group',
			'description' => __( 'Add your video SRT files.', 'cmb2' ),
			'repeatable'  => true,
			'options'     => array(
				'group_title'   => __( 'SRT File {#}', 'cmb2' ), // since version 1.1.4, {#} gets replaced by row number
				'add_button'    => __( 'Add Another SRT file', 'cmb2' ),
				'remove_button' => __( 'Remove SRT File', 'cmb2' ),
				'sortable'      => true, // beta
				// 'closed'     => true, // true to have the groups closed by default
			),
		) );

		$cmb->add_group_field($group_srt_files, array(
			'name' => 'SRT File',
			'desc' => 'Upload an SRT file.',
			'id' => $prefix . 'video_post_type_srt_files_file',
			'type' => 'file',
			'options' => array(
				'url' => false,
			),
			'text' => array(
				'add_upload_file_text' => 'Add SRT File'
			),
			'query_args' => array(
				'type' => 'application/srt',
			),
		));

		Cdp_Video_Post_Type_Admin::cmb2_video_post_type_language_metabox($cmb, $group_srt_files, $prefix . 'video_post_type_group_srt_files_language');

		$group_transcripts = $cmb->add_field( array(
			'id'          => $prefix . 'video_post_type_group_transcripts',
			'type'        => 'group',
			'description' => __( 'Add your video transcripts.', 'cmb2' ),
			'repeatable'  => true,
			'options'     => array(
				'group_title'   => __( 'Video Transcript {#}', 'cmb2' ), // since version 1.1.4, {#} gets replaced by row number
				'add_button'    => __( 'Add Another Video Transcript', 'cmb2' ),
				'remove_button' => __( 'Remove Transcript', 'cmb2' ),
				'sortable'      => true, // beta
				// 'closed'     => true, // true to have the groups closed by default
			),
		) );

		$cmb->add_group_field($group_transcripts, array(
			'name' => 'Transcript File',
			'desc' => 'Upload an transcript file if you would like transcripts downloadable.',
			'id' => $prefix . 'video_post_type_transcript_file',
			'type' => 'file',
			'options' => array(
				'url' => false,
			),
			'text' => array(
				'add_upload_file_text' => 'Add Transcript'
			),
			'query_args' => array(
				'type' => 'application/pdf',
			),
		));

		$cmb->add_group_field($group_transcripts, array(
			'name' => 'Transcript Text',
			'desc' => 'Type everything that\'s spoken in the video here.',
			'id' => $prefix . 'video_post_type_transcript',
			'type' => 'wysiwyg',
			'options' => array(
				'teeny' => true,
				'media_buttons' => false
			),
		));

		Cdp_Video_Post_Type_Admin::cmb2_video_post_type_language_metabox($cmb, $group_transcripts, $prefix . 'video_post_type_group_transcripts_language');

	}

}
