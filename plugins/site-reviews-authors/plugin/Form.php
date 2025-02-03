<?php

namespace GeminiLabs\SiteReviews\Addon\Authors;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Defaults\SiteReviewsFormDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Field;
use GeminiLabs\SiteReviews\Modules\Html\Form as FormFields;
use GeminiLabs\SiteReviews\Modules\Html\Tags\FormFieldsTag;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Modules\Style;
use GeminiLabs\SiteReviews\Review;

class Form
{
    /**
     * @var array
     */
    public $args;
    /**
     * @var Review
     */
    public $review;
    /**
     * @var Arguments
     */
    public $with;

    public function __construct(Review $review, array $args = [])
    {
        $this->args = glsr(SiteReviewsFormDefaults::class)->merge($args);
        $this->review = $review;
        $this->with = $this->with();
    }

    public function buildTemplate(array $args = []): string
    {
        $form = new FormFields($this->visibleFields(), $this->hiddenFields());
        return glsr(Template::class)->build('templates/reviews-form', [
            'args' => $this->args,
            'context' => [
                'fields' => (string) $form,
                'class' => $this->getClasses(),
                'response' => $this->buildResponseTag(),
                'submit_button' => $this->buildSubmitButtonTag(),
            ],
            'form' => $form,
        ]);
    }

    public function images(): array
    {
        $attachmentIds = Arr::consolidate($this->review->images);
        $images = [];
        foreach ($attachmentIds as $attachmentId) {
            $file = wp_get_original_image_path($attachmentId);
            if (!$file) {
                continue;
            }
            $size = wp_getimagesize($file);
            $url = wp_get_original_image_url($attachmentId);
            $images[] = [
                'accepted' => true,
                'caption' => wp_get_attachment_caption($attachmentId),
                'dataURL' => $url,
                'file' => $file,
                'height' => $size[0],
                'id' => $attachmentId,
                'lastModified' => '',
                'name' => wp_basename($file),
                'processing' => true,
                'size' => wp_filesize($file),
                'status' => 'success',
                'type' => $size['mime'],
                'width' => $size[0],
            ];
        }
        return $images;
    }

    public function toString(): string
    {
        return $this->buildTemplate($this->args);
    }

    protected function buildSubmitButtonTag(): string
    {
        return glsr(Template::class)->build('templates/form/submit-button', [
            'context' => [
                'class' => glsr(Style::class)->classes('button'),
                'loading_text' => __('Updating, please wait...', 'site-reviews-authors'),
                'text' => __('Update review', 'site-reviews-authors'),
            ],
        ]);
    }

    protected function buildResponseTag(): string
    {
        $classes = [glsr(Style::class)->validation('form_message')];
        if (!empty($this->with->errors)) {
            $classes[] = glsr(Style::class)->validation('form_message_failed');
        }
        return glsr(Template::class)->build('templates/form/response', [
            'context' => [
                'class' => implode(' ', array_filter($classes)),
                'message' => wpautop($this->with->message),
            ],
            'has_errors' => !empty($this->with->errors),
        ]);
    }

    protected function getClasses(): string
    {
        $classes = ['glsr-review-form'];
        $classes[] = glsr(Style::class)->classes('form');
        $classes[] = $this->args['class'];
        if (!empty($this->with->errors)) {
            $classes[] = glsr(Style::class)->validation('form_error');
        }
        $classes = implode(' ', $classes);
        return glsr(Sanitizer::class)->sanitizeAttrClass($classes);
    }

    protected function hiddenFields(): array
    {
        do_action('litespeed_nonce', 'update-review'); // @litespeedcache
        $fields = [];
        $hiddenFields = [
            '_action' => 'update-review',
            '_nonce' => wp_create_nonce('update-review'),
            'review_id' => $this->review->ID,
            'theme' => Arr::get($this->args, 'theme'),
            'form' => Arr::get($this->args, 'form'),
        ];
        foreach ($hiddenFields as $name => $value) {
            $fields[$name] = new Field([
                'name' => $name,
                'type' => 'hidden',
                'value' => $value,
            ]);
        }
        return $fields;
    }

    protected function visibleFields(): array
    {
        $fields = [];
        $parameters = [
            'args' => $this->args,
            'tag' => 'fields',
        ];
        $formFields = glsr(FormFieldsTag::class, $parameters)->handleFor('form', null, $this->with);
        foreach ($formFields->visible() as $field) {
            $field->value = $this->review[$field->path];
            $fields[] = $field;
        }
        return $fields;
    }

    protected function with(): Arguments
    {
        return glsr()->args([
            'errors' => glsr()->sessionPluck('form_errors', []),
            'message' => glsr()->sessionPluck('form_message', ''),
            'required' => glsr_get_option('forms.required', []),
            'values' => glsr()->sessionPluck('form_values', []),
        ]);
    }
}
