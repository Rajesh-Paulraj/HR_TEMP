<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Controllers;

use GeminiLabs\SiteReviews\Addon\Forms\Application;
use GeminiLabs\SiteReviews\Addon\Forms\FormFields;
use GeminiLabs\SiteReviews\Addon\Forms\ReviewTemplate;
use GeminiLabs\SiteReviews\Addon\Forms\Tags\CustomTag;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\ReviewHtml;
use GeminiLabs\SiteReviews\Review;

class TemplateController
{
    /**
     * @param string $stylesheet
     * @return string
     * @filter site-reviews/enqueue/public/inline-styles
     */
    public function filterInlineStyles($stylesheet)
    {
        $inlineStylesheetPath = glsr(Application::class)->path('assets/inline.css');
        $stylesheet .= file_get_contents($inlineStylesheetPath);
        if ('choices.js' === glsr_get_option('addons.'.Application::SLUG.'.dropdown_library')) {
            $inlinePath = glsr(Application::class)->path('assets/inline-choices.js.css');
            $stylesheet .= file_get_contents($inlinePath);
        }
        return $stylesheet;
    }

    /**
     * @param string $template
     * @return string
     * @filter site-reviews/build/template/review
     */
    public function filterReviewTemplate($template, array $data)
    {
        $formId = Arr::get($data, 'args.form');
        if ($customTemplate = glsr(ReviewTemplate::class)->template($formId)) {
            return $customTemplate;
        }
        return $template;
    }

    /**
     * Render the templates tags of custom fields in the review.
     * @param array $templateTags
     * @return array
     * @filter site-reviews/review/build/after
     */
    public function filterReviewTemplateTags(array $templateTags, Review $review, ReviewHtml $reviewHtml)
    {
        $args = $reviewHtml->args;
        $formId = Arr::get($reviewHtml->args, 'form');
        $fields = glsr(FormFields::class)->customFields($formId);
        $customTags = glsr(FormFields::class)->customTemplateTags($formId);
        foreach ($customTags as $name => $tag) {
            $field = glsr()->args(Arr::get($fields, $name));
            $value = Helper::ifEmpty($review->custom[$name], '');
            if (Helper::isNotEmpty($value) && !$field->isEmpty()) {
                $type = $field->type;
                if (is_array($value)) { // @todo refactor this mess!
                    $type = 'multi';
                    $list = array_reduce($value, function ($result, $item) {
                        return $result.'<li>'.$item.'</li>';
                    });
                    $value = '<ul>'.$list.'</ul>';
                }
                $className = Helper::buildClassName(['custom', $type, 'tag'], 'Addon\Forms\Tags');
                $className = glsr(Application::class)->filterString('custom/tag/'.$type, $className, $reviewHtml);
                $className = Helper::ifTrue(class_exists($className), $className, CustomTag::class);
                $value = glsr($className, compact('tag', 'args'))->handleFor('custom', $value, $field);
            }
            $templateTags[$tag] = $value;
        }
        return $templateTags;
    }

    /**
     * @param string $value
     * @param string $rawValue
     * @param mixed $tag
     * @return string
     * @filter site-reviews/custom/wrapped
     * @filter site-reviews/review/wrapped
     */
    public function filterWrappedTagValue($value, $rawValue, $tag)
    {
        $label = Arr::get($tag->with, 'tag_label');
        if (empty($label) && ($tag->with instanceof Review)) {
            $formId = Cast::toInt(Arr::get($tag->args, 'form'));
            $fields = glsr(FormFields::class)->indexedFields($formId);
            foreach ($fields as $field) {
                if ($tag->tag === Arr::get($field, 'tag')) {
                    $label = Arr::get($field, 'tag_label');
                    break;
                }
            }
        }
        if (empty($label)) {
            return $value;
        }
        $label = glsr(Builder::class)->span([
            'class' => 'glsr-tag-label',
            'text' => $label,
        ]);
        return $label.$value;
    }
}
