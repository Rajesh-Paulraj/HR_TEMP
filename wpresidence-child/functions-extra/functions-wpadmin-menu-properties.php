<?php

function hide_unwanted_sections_in_quick_edit() {
    global $pagenow, $post_type;

    // Check if we are on the `edit.php` page and the post type is `estate_property`
    if ($pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'estate_property') {
        ?>
        <style>
            ul.cat-checklist.property_action_category-checklist,
            ul.cat-checklist.property_area-checklist,
            ul.cat-checklist.property_county_state-checklist,
            ul.cat-checklist.property_features-checklist,
            ul.cat-checklist.property_status-checklist {
                display: none !important;
            }
        </style>
        <script>
            // Check for the specific text inside the span and hide it using JavaScript
            document.addEventListener('DOMContentLoaded', function () {
                const spans = document.querySelectorAll('span.title.inline-edit-categories-label');
                spans.forEach(function (span) {
                    if (span.textContent.trim() === 'Type' || span.textContent.trim() === 'Neighborhood'
                        || span.textContent.trim() === 'County / State' || span.textContent.trim() === 'Features & Amenities'
                        || span.textContent.trim() === 'Property Status') {
                        span.style.display = 'none';
                    }
                });
            });
        </script>
        <?php
    }
    ?>
    <style>
        .edit-post-meta-boxes-area.is-advanced {
            display: none !important;
        }
    </style>
    <?php
}
add_action('admin_head', 'hide_unwanted_sections_in_quick_edit');




?>
