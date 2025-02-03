<?php

namespace GeminiLabs\SiteReviews\Addon\Images\Database;

use GeminiLabs\SiteReviews\Addon\Images\Database\NormalizeQueryArgs;
use GeminiLabs\SiteReviews\Addon\Images\GridImage;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Sql;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Review;

/**
 * @property array $args
 * @property \wpdb $db
 */
class Query
{
    use Sql;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
    }

    /**
     * @param int $offset
     * @return GridImage|false
     */
    public function reviewImage($offset, array $args = [])
    {
        $offset = Cast::toInt($offset);
        if ($offset < 0) {
            return false;
        }
        $this->setArgs(wp_parse_args(['offset' => $offset, 'per_page' => 1], $args));
        $results = glsr(Database::class)->dbGetResults($this->queryReviewImages(), ARRAY_A);
        foreach ($results as &$image) {
            $image = wp_parse_args(['index' => $this->args['offset']], $image);
            $image = new GridImage($image);
        }
        return !empty($results[0])
            ? $results[0]
            : false;
    }

    /**
     * @return array
     */
    public function reviewImages(array $args = [])
    {
        $this->setArgs($args);
        $results = [];
        if ($this->args['per_page'] > 0) { // allow zero results
            $results = glsr(Database::class)->dbGetResults($this->queryReviewImages(), ARRAY_A);
        }
        $total = $this->totalReviewImages($args);
        $currentIndex = (($this->args['page'] - 1) * $this->args['per_page']) + $this->args['offset'];
        foreach ($results as &$image) {
            $image = wp_parse_args(['index' => $currentIndex++], $image);
            $image = new GridImage($image);
        }
        return [
            'count' => count($results),
            'max_pages' => (int) ceil($total / max(1, $this->args['per_page'])),
            'page' => $this->args['page'],
            'per_page' => $this->args['per_page'],
            'results' => $results,
            'total' => $total,
        ];
    }

    /**
     * @return void
     */
    public function setArgs(array $args = [], array $unset = [])
    {
        $args = (new NormalizeQueryArgs($args))->toArray();
        foreach ($unset as $key) {
            $args[$key] = '';
        }
        $this->args = $args;
    }

    /**
     * @return int
     */
    public function totalReviewImages(array $args = [])
    {
        $this->setArgs($args);
        return (int) glsr(Database::class)->dbGetVar($this->queryTotalReviewImages());
    }

    /**
     * @return string
     */
    protected function clauseJoinStatus()
    {
        return "INNER JOIN {$this->db->posts} AS p ON r.review_id = p.ID";
    }

    /**
     * @return string
     */
    protected function queryReviewImages()
    {
        return $this->sql("
            SELECT img.ID, r.review_id
            FROM {$this->table('posts')} AS img
            INNER JOIN {$this->table('ratings')} AS r ON r.review_id = img.post_parent
            {$this->sqlJoin()}
            {$this->sqlWhere()}
            AND img.post_type = 'attachment'
            ORDER BY p.post_date DESC, img.menu_order ASC
            {$this->sqlLimit()}
            {$this->sqlOffset()}
        ");
    }

    /**
     * @return string
     */
    protected function queryTotalReviewImages()
    {
        return $this->sql("
            SELECT COUNT(DISTINCT img.ID) AS count
            FROM {$this->table('posts')} AS img
            INNER JOIN {$this->table('ratings')} AS r ON r.review_id = img.post_parent
            {$this->sqlJoin()}
            {$this->sqlWhere()}
            AND img.post_type = 'attachment'
        ");
    }
}
