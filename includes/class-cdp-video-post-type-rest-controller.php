<?php
if ( ! defined( 'WPINC' ) ) {
  die;
}

// Include dependencies.
require_once ABSPATH . 'wp-admin/includes/plugin.php';

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendors/james-heinrich/getid3/getid3/getid3.php';

// Required for this to execute correctly.
$required_plugin = 'wp-elasticsearch-feeder/wp-es-feeder.php';

// Check if the feeder exists, if it does create the controller route.
if ( is_plugin_active( $required_plugin ) ) {
  class ES_Feeder_REST_VIDEO_Controller extends ES_Feeder\Admin\API\REST_Controller {
    public function baseline( $post ) {
      $api_helper = new ES_Feeder\Admin\Helpers\API_Helper();

      $document = array();

      // Fields to apply to video json document.
      $document['post_id']   = (int) $post->ID;
      $document['site']      = $api_helper->get_site();
      $document['type']      = $this->type;
      $document['published'] = get_the_date( 'c', $post->ID );
      $document['modified']  = get_the_modified_date( 'c', $post->ID );
      $document['owner']     = $post->_cdp_video_owner ? $post->_cdp_video_owner : '';
      $document['author']    = $post->_cdp_video_author ? $post->_cdp_video_author : '';
      $document['duration']  = $this->duration_to_seconds( $post );
      $document['unit']      = $this->get_units( $post );
      $document['thumbnail'] = $api_helper->get_image_metadata( get_post_thumbnail_id( $post ) );

      return $document;
    }

    private function duration_to_seconds( $post ) {

      $duration = $post->_cdp_video_duration ? $post->_cdp_video_duration : '';

      if ( '' !== $duration ) {
        list($hours, $minutes, $seconds) = explode( ':', $duration );
        $duration                        = ( $hours * 3600 ) + ( $minutes * 60 ) + $seconds;
      } else {
        $duration = 0;
      }

      return $duration;
    }

    private function get_units( $post ) {
      $lang_helper = new ES_Feeder\Admin\Helpers\Language_Helper();

      $units       = array();
      $srts        = $this->get_srts( $post ) ?: array();
      $transcripts = $this->get_transcripts( $post ) ?: array();
      $categories  = $this->get_categories( $post ) ?: array();
      $tags        = $this->get_tags( $post ) ?: array();
      $videos      = $this->get_videos( $post ) ?: array();
      $headers     = $this->get_headers( $post ) ?: array();

      $languages = $this->filter_languages( $srts, $transcripts, $categories, $tags, $videos, $headers );

      foreach ( $languages as $key => $value ) {
        $unit = new stdClass();

        $unit->transcript = new stdClass();
        $unit->language   = $lang_helper->get_language_by_code( $key );

        foreach ( $headers as $header ) {
          if ( in_array( $key, $header, true ) ) {
            $unit->title = isset( $header['_cdp_video_headers_title'] ) ? $header['_cdp_video_headers_title'] : '';
            $unit->desc  = isset( $header['_cdp_video_headers_description'] ) ? $header['_cdp_video_headers_description'] : '';
          }
        }

        $unit->source = array();

        foreach ( $videos as $video ) {
          if ( in_array( $key, $video, true ) ) {
            $vidObj = new stdClass();

            $filesrc  = isset( $video['_cdp_video_videos_video_file'] ) ? $video['_cdp_video_videos_video_file'] : '';
            $fileinfo = array();

            if ( $filesrc !== '' ) {
              $path = parse_url( $filesrc, PHP_URL_PATH );
              $file = $_SERVER['DOCUMENT_ROOT'] . $path;

              if ( file_exists( $file ) ) {
                $getID3      = new getID3();
                $fileinfo    = $getID3->analyze( $file );
                $vidObj->md5 = md5_file( $file );
              }
            }

            $vidObj->burnedInCaptions = ( strtolower( $video['_cdp_video_videos_video_captions'] ) == 'yes' ? true : false );
            $vidObj->downloadUrl      = $filesrc ?: '';
            $vidObj->streamUrl        = array();
            if ( isset( $video['_cdp_video_videos_video_streaming_url'] ) ) {
              $vidObj->streamUrl[] = array(
              'site' => 'youtube',
              'url'  => $video['_cdp_video_videos_video_streaming_url'],
              );
            }
            $vidObj->filetype = isset( $fileinfo['fileformat'] ) ? $fileinfo['fileformat'] : '';
            if ( array_key_exists( '_cdp_video_videos_video_quality', $video ) ) {
              $vidObj->video_quality = $video['_cdp_video_videos_video_quality'];
            }

            $size = new stdClass();
            if ( count( $fileinfo ) > 0 ) {
              $size->width    = $fileinfo['video']['resolution_x'];
              $size->height   = $fileinfo['video']['resolution_y'];
              $size->filesize = $fileinfo['filesize'];
              $size->bitrate  = $fileinfo['bitrate'];
            } else {
              $size = null;
            }

            $vidObj->size = $size;

            array_push( $unit->source, $vidObj );
          }
        }

        foreach ( $transcripts as $transcript ) {
          $transObj = new stdClass();
          if ( in_array( $key, $transcript, true ) ) {
            $transObj->srcUrl = isset( $transcript['_cdp_video_transcripts_transcript_file'] ) ? $transcript['_cdp_video_transcripts_transcript_file'] : '';
            $transObj->text   = isset( $transcript['_cdp_video_transcripts_transcript_text'] ) ? $transcript['_cdp_video_transcripts_transcript_text'] : '';
            if ( $transObj->srcUrl ) {
              $transObj->md5 = $this->get_md5_from_url( $transObj->srcUrl );
            }
            $unit->transcript = $transObj;
          }
        }

        foreach ( $srts as $srt ) {
          if ( in_array( $key, $srt, true ) && isset( $srt['_cdp_video_srts_srt_file'] ) && $srt['_cdp_video_srts_srt_file'] ) {
            $unit->srt = (object) array( 'srcUrl' => $srt['_cdp_video_srts_srt_file'] );
            if ( $unit->srt->srcUrl ) {
              $unit->srt->md5 = $this->get_md5_from_url( $unit->srt->srcUrl );
            }
          }
        }

        array_push( $units, $unit );
      }

      return $units;
    }

    private function get_srts( $post ) {
      if ( ! $post->_cdp_video_srts_srt ) {
        return array();
      }
      $filter = array_filter(
        $post->_cdp_video_srts_srt,
        function ( $srt ) {
          return ! empty( $srt['_cdp_video_srts_srt_file'] );
        }
      );
      return array_map(
        function ( $srt ) {
          return $srt;
        },
        $filter
      );
    }

    private function get_transcripts( $post ) {
      if ( ! $post->_cdp_video_transcripts_transcript ) {
        return array();
      }
      $filter = array_filter(
        $post->_cdp_video_transcripts_transcript,
        function ( $transcript ) {
          return ( ! empty( $transcript['_cdp_video_transcripts_transcript_file'] )
          || ! empty( $transcript['_cdp_video_transcripts_transcript_text'] ) );
        }
      );
      return array_map(
        function ( $transcript ) {
          return $transcript;
        },
        $filter
      );
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
      if ( ! $post->_cdp_video_videos_video ) {
        return array();
      }
      $filter = array_filter(
        $post->_cdp_video_videos_video,
        function ( $video ) {
          return ( ! empty( $video['_cdp_video_videos_video_file'] )
          || ! empty( $video['_cdp_video_videos_video_streaming_url'] ) );
        }
      );
      return array_map(
        function ( $video ) {
          return $video;
        },
        $filter
      );
    }

    private function get_headers( $post ) {
      if ( ! $post->_cdp_video_headers ) {
        return array();
      }
      $filter = array_filter(
        $post->_cdp_video_headers,
        function ( $header ) {
          return ( ! empty( $header['_cdp_video_headers_title'] )
          || ! empty( $header['_cdp_video_headers_description'] ) );
        }
      );
      return array_map(
        function ( $header ) {
          return $header;
        },
        $filter
      );
    }

    private function filter_languages( $srts, $transcripts, $categories, $tags, $videos, $headers ) {
      $languages = Cdp_Video_Post_Type::get_languages();
      $langarray = array();
      foreach ( $languages as $key => $value ) {
        if ( array_search( $key, array_column( $srts, '_cdp_video_srts_srt_language' ) ) !== false
          || array_search( $key, array_column( $transcripts, '_cdp_video_transcripts_transcript_language' ) ) !== false
          || array_search( $key, array_column( $categories, '_cdp_video_categories_language_language' ) ) !== false
          || array_search( $key, array_column( $tags, '_cdp_video_tags_language_language' ) ) !== false
          || array_search( $key, array_column( $videos, '_cdp_video_videos_video_language' ) ) !== false
          || array_search( $key, array_column( $headers, '_cdp_video_headers_language' ) ) !== false ) {
            $langarray[ $key ] = $value;
        }
      }
      return $langarray;
    }

    private function get_md5_from_url( $url ) {
      $path = parse_url( $url, PHP_URL_PATH );
      $file = $_SERVER['DOCUMENT_ROOT'] . $path;
      $md5  = null;
      if ( file_exists( $file ) ) {
        $md5 = md5_file( $file );
      }
      return $md5;
    }
  }

  function register_video_post_type_rest_routes() {
    $controller = new ES_Feeder_REST_VIDEO_Controller( 'video' );
    $controller->register_routes();
  }

  add_action( 'rest_api_init', 'register_video_post_type_rest_routes' );
}
