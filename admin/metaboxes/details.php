<?php

$details = new_cmb2_box(array(
  'id' => $prefix . 'details_metabox',
  'title' => __('Video Details', 'cmb2'),
  'object_types' => array('video', ),
  'context' => 'normal',
  'priority' => 'high',
  'show_names' => true
));

$headers = $details->add_field( array(
  'id'          => $prefix . 'headers',
  'type'        => 'group',
  'description' => __( 'Add your video header data for each language.', 'cdp-video-post-type' ),
  'repeatable'  => true,
  'options'     => array(
    'group_title'   => __( 'Headers Language {#}', 'cdp-video-post-type' ), // since version 1.1.4, {#} gets replaced by row number
    'add_button'    => __( 'Add Another Headers Language', 'cdp-video-post-type' ),
    'remove_button' => __( 'Remove Headers', 'cdp-video-post-type' ),
    'sortable'      => true, // beta
    // 'closed'     => true, // true to have the groups closed by default
  ),
) );

$details->add_group_field($headers, array(
  'name'    => 'Video Title',
  'desc'    => 'Title of the video',
  'id'      => $prefix . 'headers_title',
  'type'    => 'text',
) );

$details->add_group_field($headers, array(
  'name' => 'Description',
  'desc' => 'Description of the video',
  'id' => $prefix . 'headers_description',
  'type' => 'textarea_small'
));

Cdp_Video_Post_Type_Admin_Helpers::cmb2_video_post_type_language_metabox($details, $headers, $prefix . 'headers_language');

$details->add_field( array(
  'id'          => $prefix . 'duration',
  'name'        => 'Video Duration',
  'desc' => 'Duration of the video',
  'type'        => 'text_time',
  'time_format' => 'H:m:s',
  'attributes' => array(
    'data-timepicker' => json_encode( array(
            'timeOnlyTitle' => 'Choose duration',
            'timeText' => 'Duration',
            'stepMinute' => 1,
            'timeFormat' => 'HH:mm:ss',
            'showButtonPanel' => false
    ) ),
  ),
) );

$details->add_field( array(
  'name'    => 'Video Author',
  'desc'    => 'Author of the video',
  'id'      => $prefix . 'author',
  'type'    => 'text',
) );

$details->add_field( array(
  'name'    => 'Video Owner',
  'desc'    => 'Owner of the video',
  'id'      => $prefix . 'owner',
  'type'    => 'text',
) );

?>