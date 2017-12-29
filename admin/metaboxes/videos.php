<?php
$videos = new_cmb2_box(array(
  'id' => $prefix . 'videos_metabox',
  'title' => __('Videos', 'cmb2'),
  'object_types' => array('video', ),
  'context' => 'normal',
  'priority' => 'high',
  'show_names' => true
));

$video = $videos->add_field( array(
  'id'          => $prefix . 'videos_video',
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

$videos->add_group_field($video, array(
  'name'    => 'Video Title',
  'desc'    => 'Title of the video',
  'id'      => $prefix . 'videos_video_title',
  'type'    => 'text',
) );

$videos->add_group_field($video, array(
  'name' => 'Description',
  'desc' => 'Description for this context (optional)',
  'id' => $prefix . 'videos_video_description',
  'type' => 'textarea_small'
));

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
    'no' => __( 'No', 'cmb2' ),
    'yes'   => __( 'Yes', 'cmb2' ),
  ),
) );

Cdp_Video_Post_Type_Admin::cmb2_video_post_type_language_metabox($videos, $video, $prefix . 'videos_video_language');
?>