<?php
if (!defined('WPINC')) {
  die;
}

// include dependencies
include_once (ABSPATH . 'wp-admin/includes/plugin.php');
include_once (WP_PLUGIN_DIR . '/wp-elasticsearch-feeder/wp-es-feeder.php');

// required for this to execute correctly
$required_plugin = 'wp-elasticsearch-feeder/wp-es-feeder.php';

// check if the feeder exists, if it does create the controller route
if (is_plugin_active($required_plugin)) {
  class WP_ES_FEEDER_EXT_VIDEO_Controller extends WP_ES_FEEDER_REST_Controller {
    public function prepare_item_for_response($post, $request) {
      $document = array();

      // fields to apply to video json document
      $document['id'] = (int)$post->ID;
      $document['site'] = $this->index_name;
      $document['type'] = $this->type;
      $document['title'] = $post->post_title;
      $document['slug'] = $post->post_name;
      $document['published'] = get_the_date('c', $post->ID);
      $document['modified'] = get_the_modified_date('c', $post->ID);
      $document['author'] = ES_API_HELPER::get_author($post->post_author);
      $document['categories'] = ES_API_HELPER::get_categories($post->ID);
      $document['tags'] = ES_API_HELPER::get_tags($post->ID);

      $opt = get_option($this->plugin_name);
      $opt_url = $opt['es_wpdomain'];
      $document['link'] = str_replace(site_url(), $opt_url, get_permalink($post->ID));

      $feature_image_exists = has_post_thumbnail($post->ID);
      if ($feature_image_exists) {
        $document['featured_image'] = ES_API_HELPER::get_featured_image(get_post_thumbnail_id($post->ID));
      }
      else {
        $document['featured_image'] = new stdClass();
      }

      // custom fields
      $document['video_url'] = get_post_meta($post->ID, 'cdp_video_type_video_url', true);
      $document['description'] = get_post_meta($post->ID, 'cdp_video_type_description', true);
      $document['transcript'] = get_post_meta($post->ID, 'cdp_video_type_transcript', true);
      $document['transcript_file_url'] = get_post_meta($post->ID, 'cdp_video_type_transcript_file', true);

      return rest_ensure_response($document);
    }
  }

  function register_video_post_type_rest_routes() {
    $controller = new WP_ES_FEEDER_EXT_VIDEO_Controller('video');
    $controller->register_routes();
  }

  add_action('rest_api_init', 'register_video_post_type_rest_routes');
}
