<?php

$duration = new_cmb2_box(array(
  'id' => $prefix . 'duration_metabox',
  'title' => __('Video Duration', 'cmb2'),
  'object_types' => array('video', ),
  'context' => 'normal',
  'priority' => 'low',
  'show_names' => true
));

$duration->add_field( array(
  'id'          => $prefix . 'duration',
  'name'        => 'Video Duration',
  'desc' => 'Duration of the video',
  'type'        => 'text_time',
  'attributes' => array(
    'data-timepicker' => json_encode( array(
            'stepMinute' => 1,
            'timeFormat' => 'HH:mm:ss',
            'showButtonPanel' => false
    ) ),
  ),
) );

?>