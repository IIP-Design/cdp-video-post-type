<?php

$tags = new_cmb2_box(array(
  'id' => $prefix . 'tags_metabox',
  'title' => __('Tags', 'cmb2'),
  'object_types' => array('video', ),
  'context' => 'side',
  'priority' => 'core',
  'show_names' => true
));

$language = $tags->add_field( array(
  'id'          => $prefix . 'tags_language',
  'type'        => 'group',
  'description' => __( 'Add your tags for each language', 'cmb2' ),
  'repeatable'  => true,
  'options'     => array(
    'group_title'   => __( 'Language {#}', 'cmb2' ), // since version 1.1.4, {#} gets replaced by row number
    'add_button'    => __( 'Add Another Language', 'cmb2' ),
    'remove_button' => __( 'Remove Language', 'cmb2' ),
    'sortable'      => false, // beta
    // 'closed'     => true, // true to have the groups closed by default
  ),
) );

Cdp_Video_Post_Type_Admin::cmb2_video_post_type_language_metabox($tags, $language, $prefix . 'tags_language_language');

$tags->add_group_field($language, array(
  'name'    => 'Tags',
  'desc'    => 'Tags (separated by comma)',
  'id'      => $prefix . 'tags_language_tags',
  'type'    => 'text',
) );

?>