<?php

$details = new_cmb2_box(array(
  'id' => $prefix . 'details_metabox',
  'title' => __('Video Details', 'cmb2'),
  'object_types' => array('video', ),
  'context' => 'normal',
  'priority' => 'low',
  'show_names' => true
));

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