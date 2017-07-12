<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://github.com/MaxOrelus
 * @since      1.0.0
 *
 * @package    Cdp_Video_Post_Type
 * @subpackage Cdp_Video_Post_Type/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Cdp_Video_Post_Type
 * @subpackage Cdp_Video_Post_Type/includes
 * @author     Max Orelus <orelusm@america.gov>
 */
class Cdp_Video_Post_Type_Loader
{

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{

		$this->actions = array();
		$this->filters = array();

	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress action that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the action is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. he priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1)
	{
		$this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. he priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
	 */
	public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1)
	{
		$this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array                $hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         The priority at which the function should be fired.
	 * @param    int                  $accepted_args    The number of arguments that should be passed to the $callback.
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function add($hooks, $hook, $component, $callback, $priority, $accepted_args)
	{

		$hooks[] = array(
			'hook' => $hook,
			'component' => $component,
			'callback' => $callback,
			'priority' => $priority,
			'accepted_args' => $accepted_args
		);

		return $hooks;

	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{

		foreach ($this->filters as $hook) {
			add_filter($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
		}

		foreach ($this->actions as $hook) {
			add_action($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
		}

		// initiates video post type
		add_action('init', array($this, 'create_custom_video_post_type'), 0);

		// iniate cmb2
		add_action('cmb2_admin_init', array($this, 'cmb2_video_post_type_metaboxes'));
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

	function cmb2_video_post_type_metaboxes()
	{
		$prefix = '_cdp_';

		$cmb = new_cmb2_box(array(
			'id' => 'video_post_type_metabox',
			'title' => __('Video Options', 'cmb2'),
			'object_types' => array('video', ),
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => true
		));

		$cmb->add_field(array(
			'name' => 'Youtube URL',
			'desc' => 'Enter a youtube URL.',
			'id' => 'cdp_video_type_video_url',
			'type' => 'oembed',
		));

		$cmb->add_field(array(
			'name' => 'Description',
			'desc' => 'Description for this context (optional)',
			'id' => 'cdp_video_type_description',
			'type' => 'textarea_small'
		));

		$cmb->add_field(array(
			'name' => 'Transcript File',
			'desc' => 'Upload an transcript file if you would like transcripts downloadable.',
			'id' => 'cdp_video_type_transcript_file',
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

		$cmb->add_field(array(
			'name' => 'Transcript',
			'desc' => 'Type everything that\'s spoken in the video here.',
			'id' => 'cdp_video_type_transcript',
			'type' => 'wysiwyg',
			'options' => array(
				'teeny' => true,
				'media_buttons' => false
			),
		));
	}
}
