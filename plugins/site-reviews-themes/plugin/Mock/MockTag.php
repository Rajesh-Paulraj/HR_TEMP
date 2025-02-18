<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Mock;

use GeminiLabs\SiteReviews\Addon\Themes\Application;
use GeminiLabs\SiteReviews\Addon\Themes\Defaults\MockFieldDefaults;
use GeminiLabs\SiteReviews\Addon\Themes\Defaults\TypeDefaults;
use GeminiLabs\SiteReviews\Addon\Themes\Mock\Tags\Tag;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;

class MockTag
{
    /**
     * @var \GeminiLabs\SiteReviews\Arguments
     */
    public $tag;

    public function __construct(array $field = [])
    {
        $this->tag = glsr()->args(glsr(MockFieldDefaults::class)->restrict($field));
        $this->setType();
        $this->setValue(); // do this last!
    }

    /**
     * @return void
     */
    public function setType()
    {
        $defaults = glsr(TypeDefaults::class)->defaults();
        $type = $this->tag->type;
        if ('assigned_links' === $this->tag->tag) {
            // this is a special tag created from assigned_posts which is also a tag
            $type = 'review_assigned_links';
        }
        $this->tag->set('type', Arr::get($defaults, $type, 'text'));
    }

    /**
     * @return void
     */
    public function setValue()
    {
        $type = $this->tag->type;
        $className = Helper::buildClassName($type.'-tag', __NAMESPACE__.'\Tags');
        $className = glsr(Application::class)->filter('mock/tag/'.$type, $className, $this->tag);
        if (!class_exists($className)) {
            $className = Tag::class;
        }
        $params = [
            'args' => $this->toArray(),
            'tag' => $this->tag->tag,
        ];
        $value = glsr($className, $params)->handleFor('review', $this->tag->value);
        $this->tag->set('value', $value);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->tag->toArray();
    }
}
