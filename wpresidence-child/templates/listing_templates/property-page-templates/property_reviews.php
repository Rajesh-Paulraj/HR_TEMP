<?php

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if(class_exists( 'Elementor\Plugin')){
    if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
        $property_id = $post->ID;
    }
} else {
    $property_id = $post->ID; 
}
?>


