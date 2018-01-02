<?php
$videos = new_cmb2_box(array(
  'id' => $prefix . 'videos_metabox',
  'title' => __('Videos', 'cdp-video-post-type'),
  'object_types' => array('video', ),
  'context' => 'normal',
  'priority' => 'core',
  'show_names' => true
));

$video = $videos->add_field( array(
  'id'          => $prefix . 'videos_video',
  'type'        => 'group',
  'description' => __( 'Add your video files and/or streaming URL.', 'cdp-video-post-type' ),
  'repeatable'  => true,
  'options'     => array(
    'group_title'   => __( 'Video File {#}', 'cdp-video-post-type' ), // since version 1.1.4, {#} gets replaced by row number
    'add_button'    => __( 'Add Another Video File', 'cdp-video-post-type' ),
    'remove_button' => __( 'Remove File', 'cdp-video-post-type' ),
    'sortable'      => true, // beta
    // 'closed'     => true, // true to have the groups closed by default
  ),
) );

$videos->add_group_field($video, array(
  'name' => 'Video File',
  'desc' => 'Upload a video file if you would like it downloadable.',
  'id' => $prefix . 'videos_video_file',
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

$videos->add_group_field($video, array(
  'name' => 'Streaming URL',
  'desc' => 'Enter a streaming URL.',
  'id' => $prefix . 'videos_video_streaming_url',
  'type' => 'oembed',
));

$videos->add_group_field($video, array(
  'name'             => 'Captions',
  'desc'             => 'Does the video contain burned-in captions?',
  'id'               => $prefix . 'videos_video_captions',
  'type'             => 'select',
  'default'          => 'no',
  'options'          => array(
    'no' => __( 'No', 'cdp-video-post-type' ),
    'yes'   => __( 'Yes', 'cdp-video-post-type' ),
  ),
) );

Cdp_Video_Post_Type_Admin_Helpers::cmb2_video_post_type_language_metabox($videos, $video, $prefix . 'videos_video_language');
?>