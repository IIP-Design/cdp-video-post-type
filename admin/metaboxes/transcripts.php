<?php

$transcripts = new_cmb2_box(array(
  'id' => $prefix . 'transcripts_metabox',
  'title' => __('Transcripts', 'cmb2'),
  'object_types' => array('video', ),
  'context' => 'normal',
  'priority' => 'default',
  'show_names' => true
));

$transcript = $transcripts->add_field( array(
  'id'          => $prefix . 'transcripts_transcript',
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

$transcripts->add_group_field($transcript, array(
  'name' => 'Transcript File',
  'desc' => 'Upload an transcript file if you would like transcripts downloadable.',
  'id' => $prefix . 'transcripts_transcript_file',
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

$transcripts->add_group_field($transcript, array(
  'name' => 'Transcript Text',
  'desc' => 'Type everything that\'s spoken in the video here.',
  'id' => $prefix . 'transcripts_transcript_text',
  'type' => 'wysiwyg',
  'options' => array(
    'teeny' => true,
    'media_buttons' => false
  ),
));

Cdp_Video_Post_Type_Admin::cmb2_video_post_type_language_metabox($transcripts, $transcript, $prefix . 'transcripts_transcript_language');

?>