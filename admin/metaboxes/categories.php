<?php

$categories = new_cmb2_box(array(
  'id' => $prefix . 'categories_metabox',
  'title' => __('Categories', 'cmb2'),
  'object_types' => array('video', ),
  'context' => 'side',
  'priority' => 'core',
  'show_names' => true
));

$language = $categories->add_field( array(
  'id'          => $prefix . 'categories_language',
  'type'        => 'group',
  'description' => __( 'Add your categories for each language', 'cmb2' ),
  'repeatable'  => true,
  'options'     => array(
    'group_title'   => __( 'Language {#}', 'cmb2' ), // since version 1.1.4, {#} gets replaced by row number
    'add_button'    => __( 'Add Another Language', 'cmb2' ),
    'remove_button' => __( 'Remove Language', 'cmb2' ),
    'sortable'      => false, // beta
    //'closed'     => true, // true to have the groups closed by default
  ),
) );

Cdp_Video_Post_Type_Admin::cmb2_video_post_type_language_metabox($categories, $language, $prefix . 'categories_language_language');

$categories->add_group_field($language, array(
  'name'    => 'Categories',
  'desc'    => 'Categories (separated by comma)',
  'id'      => $prefix . 'categories_language_categories',
  'type'    => 'text',
) );

?>