<?php

namespace GeminiLabs\SiteReviews\Addon\Images\Database;

use GeminiLabs\SiteReviews\Addon\Images\Defaults\GridImagesDefaults;
use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Multilingual;

/**
 * @property int[] $assigned_posts;
 * @property string[] $assigned_posts_types;
 * @property int[] $assigned_terms;
 * @property int[] $assigned_users;
 * @property int $offset;
 * @property int $page;
 * @property string $pagination;
 * @property int $per_page;
 * @property int[] $post__in;
 * @property int[] $post__not_in;
 * @property int $rating;
 * @property string $rating_field;
 * @property string $status;
 * @property string $terms;
 * @property string $type;
 * @property int[] $user__in;
 * @property int[] $user__not_in;
 */
class NormalizeQueryArgs extends Arguments
{
    public function __construct(array $args = [])
    {
        $args = glsr(GridImagesDefaults::class)->restrict($args);
        $args['assigned_posts'] = glsr(Multilingual::class)->getPostIds($args['assigned_posts']);
        // this also ensures we always join the query with the posts table!
        $args['status'] = $this->normalizeStatus($args['status']);
        parent::__construct($args);
    }

    /**
     * @param string $value
     * @return string
     */
    protected function normalizeStatus($value)
    {
        $statuses = [
            'all' => '-1',
            'approved' => '1',
            'pending' => '0',
            'publish' => '1',
            'unapproved' => '0',
        ];
        $status = Str::restrictTo(array_keys($statuses), $value, 'approved', $strict = true);
        return $statuses[$status];
    }
}
