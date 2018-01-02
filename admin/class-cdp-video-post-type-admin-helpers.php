<?php
if ( !class_exists( 'Cdp_Video_Post_Type_Admin_Helpers' ) ) {
  
  class Cdp_Video_Post_Type_Admin_Helpers {
    
    public static function cmb2_video_post_type_language_metabox($cmb, $group, $id) {
      $cmb->add_group_field($group, array(
        'name'             => 'Language',
        'desc'             => 'Select the language.',
        'id'               => $id,
        'type'             => 'select',
        'default'          => 'english',
        'options'          => Cdp_Video_Post_Type::get_languages()
      ) );
    }

  }
}