<?php

namespace GeminiLabs\SiteReviews\Addon\Authors\Commands;

use GeminiLabs\SiteReviews\Addon\Authors\Application;
use GeminiLabs\SiteReviews\Addon\Authors\Defaults\UpdateReviewDefaults;
use GeminiLabs\SiteReviews\Addon\Authors\Validators\PermissionValidator;
use GeminiLabs\SiteReviews\Addon\Authors\Validators\ReviewValidator;
use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\CustomFieldsDefaults;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Avatar;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Modules\Validator\AkismetValidator;
use GeminiLabs\SiteReviews\Modules\Validator\BlacklistValidator;
use GeminiLabs\SiteReviews\Modules\Validator\CustomValidator;
use GeminiLabs\SiteReviews\Modules\Validator\DefaultValidator;
use GeminiLabs\SiteReviews\Modules\Validator\ValidateReview;
use GeminiLabs\SiteReviews\Request;

class UpdateReview implements Contract
{
    public $assigned_posts;
    public $assigned_terms;
    public $assigned_users;
    public $content;
    public $email;
    public $name;
    public $rating;
    public $title;
    public $type;
    public $url;

    protected $attributes;
    protected $blacklisted;
    protected $errors;
    protected $message;
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->setAttributes();
        $this->setProperties();
    }

    public function __get($property)
    {
        if (in_array($property, ['attributes', 'request'])) {
            return $this->$property;
        }
    }

    /**
     * @return static
     */
    public function handle()
    {
        if ($this->validate()) {
            $this->update();
        }
        return $this;
    }

    public function response(): array
    {
        $review = glsr_get_review($this->request->review_id);
        $rendered = [];
        foreach ($this->attributes as $args) {
            $rendered[] = (string) $review->build($args);
        }
        return [
            'errors' => $this->errors,
            'message' => $this->message,
            'rendered' => $rendered,
            'review' => Cast::toArray($review),
        ];
    }

    public function success(): bool
    {
        if (false === $this->errors) {
            glsr()->sessionClear();
            return true;
        }
        return false;
    }

    public function toArray(): array
    {
        $properties = (new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PUBLIC);
        $values = [];
        foreach ($properties as $property) {
            $values[$property->getName()] = $property->getValue($this);
        }
        $values = glsr(Application::class)->filterArray('update/review-values', $values, $this);
        $values = glsr(UpdateReviewDefaults::class)->merge($values);
        $values = array_merge($values, $this->custom());
        return $values;
    }

    public function validate(): bool
    {
        $validator = glsr(ValidateReview::class)->validate($this->request, [ // order is intentional
            ReviewValidator::class,
            PermissionValidator::class,
            DefaultValidator::class,
            CustomValidator::class,
            BlacklistValidator::class,
            AkismetValidator::class,
        ]);
        $this->blacklisted = $validator->blacklisted;
        $this->errors = $validator->errors;
        $this->message = $validator->message;
        return $validator->isValid();
    }

    protected function custom(): array
    {
        $values = $this->request->toArray();
        unset($values['attributes']);
        return glsr(CustomFieldsDefaults::class)->filter($values);
    }

    protected function update(): void
    {
        $review = glsr(ReviewManager::class)->update($this->request->review_id, $this->toArray());
        if ($review) {
            glsr(ReviewManager::class)->updateRating($review->ID, [
                'avatar' => glsr(Avatar::class)->generate($review),
            ]);
            glsr(Application::class)->action('review/updated', $review, $this);
            $this->message = $review->is_approved
                ? __('The review has been updated!', 'site-reviews-authors')
                : __('The review has been updated and is pending approval.', 'site-reviews-authors');
        } else {
            $this->errors = []; // not false
            $this->message = __('Your review could not be updated and the error has been logged. Please notify the site administrator.', 'site-reviews-authors');
        }
    }

    protected function setAttributes(): void
    {
        $this->attributes = [];
        $defaults = array_fill_keys(['form', 'hide', 'theme'], '');
        $values = glsr(Sanitizer::class)->sanitizeJson($this->request->attributes);
        foreach ($values as $value) {
            $this->attributes[] = is_array($value)
                ? shortcode_atts($defaults, $value)
                : $defaults;
        }
        if (empty($this->attributes)) {
            $this->attributes[] = $defaults;
        }
    }

    protected function setProperties(): void
    {
        $properties = (new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PUBLIC);
        $values = glsr(UpdateReviewDefaults::class)->restrict($this->request->toArray());
        foreach ($properties as $property) {
            $key = $property->getName();
            if (array_key_exists($key, $values)) {
                $this->$key = $values[$key];
            }
        }
    }
}
