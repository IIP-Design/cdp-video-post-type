<?php

$srts = new_cmb2_box( array(
  'id' => $prefix . 'srts_metabox',
  'title' => __('SRTs', 'cmb2'),
  'object_types' => array('video', ),
  'context' => 'normal',
  'priority' => 'default',
  'show_names' => true
));

$srt = $srts->add_field( array(
  'id'          => $prefix . 'srts_srt',
  'type'        => 'group',
  'description' => __( 'Add your video SRT files.', 'cmb2' ),
  'repeatable'  => true,
  'options'     => array(
    'group_title'   => __( 'SRT File {#}', 'cmb2' ), // since version 1.1.4, {#} gets replaced by row number
    'add_button'    => __( 'Add Another SRT file', 'cmb2' ),
    'remove_button' => __( 'Remove SRT File', 'cmb2' ),
    'sortable'      => true, // beta
    // 'closed'     => true, // true to have the groups closed by default
  ),
));

$srts->add_group_field( $srt, array(
  'name' => 'SRT File',
  'desc' => 'Upload an SRT file.',
  'id' => $prefix . 'srts_srt_file',
  'type' => 'file',
  'options' => array(
    'url' => false,
  ),
  'text' => array(
    'add_upload_file_text' => 'Add SRT File'
  ),
  'query_args' => array(
    'type' => 'application/srt',
  ),
));

Cdp_Video_Post_Type_Admin_Helpers::cmb2_video_post_type_language_metabox($srts, $srt, $prefix . 'srts_srt_language');

?>