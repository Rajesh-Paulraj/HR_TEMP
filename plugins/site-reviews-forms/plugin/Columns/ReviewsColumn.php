<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Columns;

class ReviewsColumn extends Column
{
    /**
     * {@inheritdoc}
     */
    public function build($value = '')
    {
        global $wpdb;
        $reviewCount = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(DISTINCT m.post_id) as reviews
            FROM {$wpdb->postmeta} m
            INNER JOIN {$wpdb->posts} p on m.post_id = p.ID
            WHERE p.post_type = '%s'
            AND p.post_status = 'publish'
            AND m.meta_key = '_custom_form'
            AND m.meta_value = '%s'
        ", glsr()->post_type, $this->postId));
        $url = admin_url('edit.php?post_type='.glsr()->post_type.'&form='.$this->postId);
        return sprintf('<a href="%s">%s</a>', $url, $reviewCount);
    }
}
