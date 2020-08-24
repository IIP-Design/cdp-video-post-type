<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/MaxOrelus
 * @since             1.0.0
 * @package           Cdp_Video_Post_Type
 *
 * @wordpress-plugin
 * Plugin Name:       CDP Video Post Type
 * Plugin URI:        https://github.com/IIP-Design/cdp-video-post-type
 * Description:       A WordPress post type to create videos for use with the CDP.
 * Version:           2.0.3
 * Author:            IIP Design
 * Author URI:        https://github.com/IIP-Design
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cdp-video-post-type
 * Domain Path:       /languages
 */

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cdp-video-post-type-activator.php
 */
function activate_cdp_video_post_type()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-cdp-video-post-type-activator.php';
	Cdp_Video_Post_Type_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cdp-video-post-type-deactivator.php
 */
function deactivate_cdp_video_post_type()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-cdp-video-post-type-deactivator.php';
	Cdp_Video_Post_Type_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_cdp_video_post_type');
register_deactivation_hook(__FILE__, 'deactivate_cdp_video_post_type');


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cdp_video_post_type()
{

  /**
   * The core plugin class that is used to define internationalization,
   * admin-specific hooks, and public-facing site hooks.
   */
  require plugin_dir_path(__FILE__) . 'includes/class-cdp-video-post-type.php';
  require plugin_dir_path(__FILE__) . 'includes/class-cdp-video-post-type-rest-controller.php';
	$plugin = new Cdp_Video_Post_Type();
	$plugin->run();
}
add_action('plugins_loaded', 'run_cdp_video_post_type');
