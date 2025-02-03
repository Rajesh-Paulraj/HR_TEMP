<?php

// function wpestate_return_property_status($post_id, $return_type = '') {
//     $property_status = get_the_terms($post_id, 'property_status');
//     $top_rated = get_post_meta($post_id, 'top-rated', true); // Fetch the custom field value
//     $to_return = '';

//     if ($return_type == 'pin') {
//         if (!empty($property_status)) {
//             foreach ($property_status as $key => $term) {
//                 $to_return .= esc_html($term->name) . ',';
//             }
//         }
//         if ($top_rated === 'True') {
//             $to_return .= 'Top Rated,';
//         }
//         $to_return = substr($to_return, 0, -1);
//         return $to_return;

//     } else if ($return_type == 'verticalstatus' || $return_type == 'horizontalstatus') {
//         if (!empty($property_status)) {
//             foreach ($property_status as $key => $term) {
//                 if ($term->slug != 'normal') {
//                     $ribbon_class = str_replace(' ', '-', $term->name);
//                     $to_return .= '<div class="slider-property-status ' . esc_attr($return_type) . ' ribbon-wrapper-' . esc_attr($ribbon_class) . ' ' . esc_attr($ribbon_class) . '">' . esc_html($term->name) . '</div>';
//                 }
//             }
//         }
//         if ($top_rated === 'True') {
//             $to_return .= '<div class="slider-property-status ' . esc_attr($return_type) . ' ribbon-wrapper-top-rated top-rated">Top Rated</div>';
//         }
//         return '<div class="status-wrapper ' . esc_attr($return_type) . '">' . $to_return . '</div>';

//     } else if ($return_type == 'unit') {
//         if (!empty($property_status)) {
//             foreach ($property_status as $key => $term) {
//                 if ($term->slug != 'normal') {
//                     $ribbon_class = str_replace(' ', '-', $term->name);
//                     $to_return .= '<div class="ribbon-inside ' . esc_attr($ribbon_class) . '">' . esc_html($term->name) . '</div>';
//                 }
//             }
//         }
//         if ($top_rated === 'True') {
//             $to_return .= '<div class="ribbon-inside top-rated">Top Rated</div>';
//         }
//         return $to_return;

//     } else {
//         if (!empty($property_status)) {
//             foreach ($property_status as $key => $term) {
//                 if ($term->slug != 'normal') {
//                     $ribbon_class = str_replace(' ', '-', $term->name);
//                     $to_return .= '<div class="ribbon-wrapper-default ribbon-wrapper-' . $ribbon_class . '"><div class="ribbon-inside ' . $ribbon_class . '">' . esc_html($term->name) . '</div></div>';
//                 }
//             }
//         }
//         if ($top_rated === 'True') {
//             $to_return .= '<div class="ribbon-wrapper-default ribbon-wrapper-top-rated"><div class="ribbon-inside top-rated">Top Rated</div></div>';
//         }
//         return '<div class="status-wrapper">' . $to_return . '</div>';
//     }
// }