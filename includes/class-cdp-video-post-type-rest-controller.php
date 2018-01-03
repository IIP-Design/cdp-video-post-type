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
    public function prepare_item_for_response( $post, $request ) {
      $document = array();

      // fields to apply to video json document
      $document['id'] = (int)$post->ID;
      $document['site'] = $this->index_name;
      $document['type'] = $this->type;
      $document['published'] = get_the_date('c', $post->ID);
      $document['modified'] = get_the_modified_date('c', $post->ID);
      $document['owner'] = get_post_meta($post->ID, '_cdp_video_owner', true);
      $document['author'] = get_post_meta($post->ID, '_cdp_video_author', true);
      $document['duration'] = $this->duration_to_seconds($post->ID);
      $document['unit'] = $this->get_units($post->ID);
      //$document['title'] = $post->post_title;
      //$document['slug'] = $post->post_name;
      //$document['categories'] = ES_API_HELPER::get_categories($post->ID);
      //$document['tags'] = ES_API_HELPER::get_tags($post->ID);

      // $opt = get_option($this->plugin_name);
      // $opt_url = $opt['es_wpdomain'];
      // $document['link'] = str_replace(site_url(), $opt_url, get_permalink($post->ID));

      // $feature_image_exists = has_post_thumbnail($post->ID);
      // if ($feature_image_exists) {
      //   $document['featured_image'] = ES_API_HELPER::get_featured_image(get_post_thumbnail_id($post->ID));
      // }
      // else {
      //   $document['featured_image'] = new stdClass();
      // }

      // // custom fields
      // $document['video_url'] = get_post_meta($post->ID, 'cdp_video_type_video_url', true);
      // $document['description'] = get_post_meta($post->ID, 'cdp_video_type_description', true);
      // $document['transcript'] = get_post_meta($post->ID, 'cdp_video_type_transcript', true);
      // $document['transcript_file_url'] = get_post_meta($post->ID, 'cdp_video_type_transcript_file', true);

      return rest_ensure_response($document);
    }

    private function duration_to_seconds( $id ) {
      
      $duration = get_post_meta($id, '_cdp_video_duration', true);
      list($hours, $minutes, $seconds) = explode(':', $duration);

      if ( $seconds ) {
        $duration = ( $hours * 3600 ) + ( $minutes * 60 ) + $seconds;
      } else {
        $duration = null;
      }

      return $duration;
    }

    private function get_units( $id ) {
      $units = array();
      $srts = $this->get_srts( $id );
      $transcripts = $this->get_transcripts( $id );
      $categories = array();
      $tags = array();
      $videos = $this->get_videos( $id );
      $headers = $this->get_headers( $id );

      $languages = $this->filter_languages($srts, $transcripts, $categories, $tags, $videos, $headers);

      foreach($languages as $key=>$value) {
        $unit = new stdClass();

        $unit->language = Language_Helper::get_language_by_locale( $key );

        foreach ($headers as $header) {
          if (in_array($key, $header, true)) {
            $unit->title = $header['_cdp_video_headers_title'];
            $unit->desc = $header['_cdp_video_headers_description'];
          }
        }

        $unit->categories = array();
        $unit->tags = array();
        $unit->source = array();

        foreach ($videos as $video) {
          if (in_array($key, $video, true)) {
            $vidObj = new stdClass();
            $vidObj->burnedInCaptions = $video['_cdp_video_videos_video_captions'];
            $vidObj->downloadUrl = '';
            $vidObj->streamUrl = '';
            $vidObj->filetype = '';
            $size = new stdClass();
            $size->width = 0;
            $size->height = 0;
            $size->filesize = 0;
            $size->speed = 0;
            $vidObj->size = $size;
            array_push($unit->source, $vidObj);
          }
        }

        foreach ($transcripts as $transcript) {
          if (in_array($key, $transcript, true)) {
            $transObj = new stdClass();
            $transObj->srcUrl = $transcript['_cdp_video_transcripts_transcript_file'];
            $transObj->text = $transcript['_cdp_video_transcripts_transcript_text'];
            $unit->transcript = $transObj;
          }
        }

        foreach ($srts as $srt) {
          if (in_array($key, $srt, true))
            $unit->srt = $srt['_cdp_video_srts_srt_file'];
        }

        array_push($units, $unit);
      }


      return $units;
    }

    private function get_srts( $id ) {
      $srts = get_post_meta($id, '_cdp_video_srts_srt', true);
      return $srts;
    }

    private function get_transcripts( $id ) {
      $transcripts = get_post_meta($id, '_cdp_video_transcripts_transcript', true);
      return $transcripts;
    }

    private function get_videos( $id ) {
      $videos = get_post_meta($id, '_cdp_video_videos_video', true);
      return $videos;
    }
    
    private function get_headers( $id ) {
      $headers = get_post_meta($id, '_cdp_video_headers', true);
      return $headers;
    }

    private function filter_languages($srts, $transcripts, $categories, $tags, $videos, $headers) {
      $languages = Cdp_Video_Post_Type::get_languages();
      $langarray = array();
      foreach ($languages as $key=>$value) {
        if ( array_search($key, array_column($srts, '_cdp_video_srts_srt_language')) !== false 
          || array_search($key, array_column($transcripts, '_cdp_video_transcripts_transcript_language')) !== false
          || array_search($key, array_column($categories, '_cdp_video_categories_language_language')) !== false
          || array_search($key, array_column($tags, '_cdp_video_tags_language_language')) !== false
          || array_search($key, array_column($videos, '_cdp_video_videos_video_language')) !== false
          || array_search($key, array_column($headers, '_cdp_video_headers_language')) !== false )
            $langarray[$key] = $value;
      }
      return $langarray;
    }
  }

  function register_video_post_type_rest_routes() {
    $controller = new WP_ES_FEEDER_EXT_VIDEO_Controller('video');
    $controller->register_routes();
  }

  add_action('rest_api_init', 'register_video_post_type_rest_routes');
}
