<?php
if (!defined('WPINC')) {
  die;
}

// include dependencies
include_once (ABSPATH . 'wp-admin/includes/plugin.php');
//include_once (WP_PLUGIN_DIR . '/wp-elasticsearch-feeder/wp-es-feeder.php');
include_once (plugin_dir_path(dirname(__FILE__)) . 'vendor/james-heinrich/getid3/getid3/getid3.php');

// required for this to execute correctly
$required_plugin = 'wp-elasticsearch-feeder/wp-es-feeder.php';

// check if the feeder exists, if it does create the controller route
if (is_plugin_active($required_plugin)) {
  class WP_ES_FEEDER_EXT_VIDEO_Controller extends WP_ES_FEEDER_REST_Controller {
    public function prepare_item_for_response( $post, $request ) {
      $document = array();

      // fields to apply to video json document
      $document['post_id'] = (int)$post->ID;
      $document['site'] = $this->get_site();
      $document['type'] = $this->type;
      $document['published'] = get_the_date('c', $post->ID);
      $document['modified'] = get_the_modified_date('c', $post->ID);
      $document['owner'] = $post->_cdp_video_owner?$post->_cdp_video_owner:'';
      $document['author'] = $post->_cdp_video_author?$post->_cdp_video_author:'';
      $document['duration'] = $this->duration_to_seconds($post);
      $document['unit'] = $this->get_units($post);

      return rest_ensure_response($document);
    }

    private function duration_to_seconds( $post ) {
      
      $duration = $post->_cdp_video_duration?$post->_cdp_video_duration:'';

      if ($duration !== '') {
        list($hours, $minutes, $seconds) = explode(':', $duration);
        $duration = ( $hours * 3600 ) + ( $minutes * 60 ) + $seconds;
      }

      return $duration;
    }

    private function get_units( $post ) {
      $units = array();
      $srts = $this->get_srts( $post ) ?: array();
      $transcripts = $this->get_transcripts( $post ) ?: array();
      $categories = $this->get_categories( $post ) ?: array();
      $tags = $this->get_tags( $post ) ?: array();
      $videos = $this->get_videos( $post ) ?: array();
      $headers = $this->get_headers( $post ) ?: array();

      $languages = $this->filter_languages($srts, $transcripts, $categories, $tags, $videos, $headers);

      foreach($languages as $key=>$value) {
        $unit = new stdClass();

        $unit->language = Language_Helper::get_language_by_locale( $key );

        foreach ($headers as $header) {
          if (in_array($key, $header, true)) {
            $unit->title = isset($header['_cdp_video_headers_title'])?$header['_cdp_video_headers_title']:'';
            $unit->desc = isset($header['_cdp_video_headers_description'])?$header['_cdp_video_headers_description']:'';
          }
        }

        $unit->categories = array();
        foreach ($categories as $category) {
          if (in_array($key, $category, true))
            $unit->categories = isset($category['_cdp_video_categories_language_categories'])?array_map('trim', explode(',', $category['_cdp_video_categories_language_categories'])):array();
        }

        $unit->tags = array();
        foreach ($tags as $tag) {
          if (in_array($key, $tag, true))
            $unit->tags = isset($tag['_cdp_video_tags_language_tags'])?array_map('trim', explode(',', $tag['_cdp_video_tags_language_tags'])):array();
        }

        $unit->source = array();

        foreach ($videos as $video) {
          if (in_array($key, $video, true)) {
            $vidObj = new stdClass();

            $filesrc = isset($video['_cdp_video_videos_video_file'])?$video['_cdp_video_videos_video_file']:'';
            $fileinfo = array();

            if ($filesrc !== '') {
              $path = parse_url($filesrc, PHP_URL_PATH);
              $file = $_SERVER['DOCUMENT_ROOT'] . $path;

              $getID3 = new getID3;
              $fileinfo = $getID3->analyze($file);
            }

            $vidObj->burnedInCaptions = $video['_cdp_video_videos_video_captions'];
            $vidObj->downloadUrl = $filesrc;
            $vidObj->streamUrl = isset($video['_cdp_video_videos_video_streaming_url'])?$video['_cdp_video_videos_video_streaming_url']:'';
            $vidObj->filetype = isset($fileinfo['fileformat'])?$fileinfo['fileformat']:'';

            $size = new stdClass();
            if ( count($fileinfo) > 0 ) {
              $size->width = $fileinfo['video']['resolution_x'];
              $size->height = $fileinfo['video']['resolution_y'];
              $size->filesize = $fileinfo['filesize'];
              $size->bitrate = $fileinfo['bitrate'];
            } else {
              $size = null;
            }

            $vidObj->size = $size;

            array_push($unit->source, $vidObj);
          }
        }

        foreach ($transcripts as $transcript) {
          $transObj = new stdClass();
          if (in_array($key, $transcript, true)) {
            $transObj->srcUrl = isset($transcript['_cdp_video_transcripts_transcript_file'])?$transcript['_cdp_video_transcripts_transcript_file']:'';
            $transObj->text = isset($transcript['_cdp_video_transcripts_transcript_text'])?$transcript['_cdp_video_transcripts_transcript_text']:'';
          }
          $unit->transcript = (count((array)$transObj) > 0)?$transObj:null;
        }

        foreach ($srts as $srt) {
          if ( in_array($key, $srt, true) && isset($srt['_cdp_video_srts_srt_file']) ) {
            $unit->srt = $srt['_cdp_video_srts_srt_file'];
          } else {
            $unit->srt = '';
          }
        }

        array_push($units, $unit);
      }


      return $units;
    }

    private function get_srts( $post ) {
      $srts = $post->_cdp_video_srts_srt;
      return $srts;
    }

    private function get_transcripts( $post ) {
      $transcripts = $post->_cdp_video_transcripts_transcript;
      return $transcripts;
    }

    private function get_categories( $post ) {
      $categories = $post->_cdp_video_categories_language;
      return $categories;
    }

    private function get_tags( $post ) {
      $tags = $post->_cdp_video_tags_language;
      return $tags;
    }

    private function get_videos( $post ) {
      $videos = $post->_cdp_video_videos_video;
      return $videos;
    }
    
    private function get_headers( $post ) {
      $headers = $post->_cdp_video_headers;
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
