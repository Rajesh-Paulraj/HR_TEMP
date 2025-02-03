<?php

namespace GeminiLabs\SiteReviews\Addon\Forms\Controllers;

use GeminiLabs\SiteReviews\Addon\Forms\Application;
use GeminiLabs\SiteReviews\Addon\Forms\Builder\AssignedPosts;
use GeminiLabs\SiteReviews\Addon\Forms\Builder\AssignedTerms;
use GeminiLabs\SiteReviews\Addon\Forms\Builder\AssignedUsers;
use GeminiLabs\SiteReviews\Addon\Forms\ColumnFilterForm;
use GeminiLabs\SiteReviews\Addon\Forms\Defaults\FieldTypeSanitizerDefaults;
use GeminiLabs\SiteReviews\Addon\Forms\FormFields;
use GeminiLabs\SiteReviews\Addon\Forms\ReviewTemplate;
use GeminiLabs\SiteReviews\Addon\Forms\Validation;
use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Defaults\CustomFieldsDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\ReviewHtml;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;

class FieldController
{
    /**
     * @return array
     * @filter site-reviews/enqueue/admin/localize
     */
    public function filterAdminLocalizedVariables(array $variables)
    {
        $variables['addons'][Application::ID] = [
            'defaults' => $this->getFieldDefaults(),
            'formats' => $this->getFieldFormats(),
            'labels' => [
                'format' => _x('Display Value As', 'admin-text', 'site-reviews-forms'),
                'label' => _x('Field Label', 'admin-text', 'site-reviews-forms'),
                'name' => _x('Field Name', 'admin-text', 'site-reviews-forms'),
                'options' => _x('Field Options', 'admin-text', 'site-reviews-forms'),
                'placeholder' => _x('Placeholder', 'admin-text', 'site-reviews-forms'),
                'posttypes' => _x('Post Type', 'admin-text', 'site-reviews-forms'),
                'required' => _x('Required', 'admin-text', 'site-reviews-forms'),
                'roles' => _x('User Role', 'admin-text', 'site-reviews-forms'),
                'tag' => _x('Template Tag', 'admin-text', 'site-reviews-forms'),
                'tag_label' => _x('Template Tag Label', 'admin-text', 'site-reviews-forms'),
                'terms' => _x('Category', 'admin-text', 'site-reviews-forms'),
                'type' => _x('Field Type', 'admin-text', 'site-reviews-forms'),
                'users' => _x('User', 'admin-text', 'site-reviews-forms'),
                'value' => _x('Default Value', 'admin-text', 'site-reviews-forms'),
            ],
            'messages' => [
                'between' => _x('The %s must be between %d and %d', 'admin-text', 'site-reviews-forms'),
                'number' => _x('The %s must be a number', 'admin-text', 'site-reviews-forms'),
                'required' => _x('The %s is required', 'admin-text', 'site-reviews-forms'),
                'reserved' => _x('The "%s" value is reserved', 'admin-text', 'site-reviews-forms'),
                'slug' => _x('The %s must be an alphabetic (a-z) lowercase word with no spaces. Underscores are allowed.', 'admin-text', 'site-reviews-forms'),
                'unique' => _x('The %s must be unique', 'admin-text', 'site-reviews-forms'),
            ],
            'options' => $this->getFieldOptions(),
            'reserved_names' => $this->getReservedNames(),
            'reserved_tags' => glsr(ReviewTemplate::class)->reservedTags(),
            'validation' => $this->getFieldValidation(),
        ];
        $variables['filters']['form'] = glsr(ColumnFilterForm::class)->options();
        $variables['nonce']['filter-form'] = wp_create_nonce('filter-form');
        $variables['nonce']['metabox-details'] = wp_create_nonce('metabox-details');
        return $variables;
    }

    /**
     * @return string
     * @filter site-reviews/builder/field/review_assigned_posts
     */
    public function filterBuilderFieldAssignedPosts()
    {
        return AssignedPosts::class;
    }

    /**
     * @return string
     * @filter site-reviews/builder/field/review_assigned_terms
     */
    public function filterBuilderFieldAssignedTerms()
    {
        return AssignedTerms::class;
    }

    /**
     * @return string
     * @filter site-reviews/builder/field/review_assigned_users
     */
    public function filterBuilderFieldAssignedUsers()
    {
        return AssignedUsers::class;
    }

    /**
     * Default fields are keyed, custom fields are indexed to allow multiple assigned_posts fields.
     * @return array
     * @filter site-reviews/review-form/fields
     */
    public function filterFormFields(array $fields, Arguments $args)
    {
        if ($indexedCustomFields = glsr(FormFields::class)->normalizedFieldsIndexed($args->form)) {
            foreach ($indexedCustomFields as &$field) {
                if (Str::startsWith(Arr::get($field, 'type'), 'review_')) {
                    $field['type'] = Str::removePrefix($field['type'], 'review_');
                }
                if ('images' === Arr::get($field, 'name')) {
                    glsr()->store('use_dropzone', true); // override the images hide option
                }
                if (Arr::get($field, 'hidden') || 'hidden' === Arr::get($field, 'type')) {
                    $field['is_raw'] = true; // do not wrap hidden fields
                }
                if ('textarea' === Arr::get($field, 'type')) {
                    $field['rows'] = 5;
                }
            }
            return $indexedCustomFields;
        }
        return $fields;
    }

    /**
     * @return array
     * @filter site-reviews/config/forms/metabox-fields
     */
    public function filterMetaboxFieldsConfig(array $config)
    {
        $formId = get_post_meta(get_the_ID(), '_custom_form', true);
        return glsr(FormFields::class)->metaboxConfig($formId, $config);
    }

    /**
     * @return array
     * @filter site-reviews/review-form/fields/normalized
     */
    public function filterMultiFields(array $fields, Arguments $args)
    {
        $multiFieldKeys = ['assigned_posts', 'assigned_terms', 'assigned_users'];
        $names = array_count_values(
            wp_list_pluck(glsr(FormFields::class)->indexedFields($args->form), 'name')
        );
        foreach ($multiFieldKeys as $key) {
            if (Cast::toInt(Arr::get($names, $key)) < 2) {
                continue;
            }
            array_walk($fields, function ($field) use ($key) {
                if ($field->path === $key) {
                    $field->name = Str::suffix($field->name, '[]');
                }
            });
        }
        return $fields;
    }

    /**
     * Set the default values for custom fields in the review.
     * @return void
     * @action site-reviews/review/build/before
     */
    public function filterReviewCustomDefaults(Review $review, ReviewHtml $reviewHtml)
    {
        $formId = Arr::get($reviewHtml, 'args.form');
        $fields = glsr(FormFields::class)->customFields($formId);
        if (!empty($fields)) {
            array_walk($fields, function (&$field) {
                $field = Arr::get($field, 'value');
            });
            $custom = wp_parse_args($review->custom->toArray(), $fields);
            $review->set('custom', glsr()->args($custom));
        }
    }

    /**
     * @param string $type
     * @param string $shortcode
     * @return array
     * @filter site-reviews/shortcode/atts
     */
    public function filterShortcodeAttributes(array $atts, $type, $shortcode)
    {
        if ('site_reviews_form' === $shortcode) {
            $fields = glsr(FormFields::class)->normalizedFieldsIndexed(glsr()->args($atts)->form);
            if (!empty($fields)) {
                $atts['hide'] = ''; // use the custom form configuration instead of the hide option
            }
        }
        return $atts;
    }

    /**
     * @return array
     * @filter site-reviews/validation/rules
     */
    public function filterValidationRules(array $rules, Request $request)
    {
        return glsr(Validation::class)->rules($rules, $request->form);
    }

    /**
     * @return void
     * @action admin_footer-post.php
     * @action admin_footer-post-new.php
     */
    public function renderFieldTemplates()
    {
        if (Application::POST_TYPE === get_post_type()) {
            glsr(Application::class)->render('views/templates', [
                'customFields' => $this->getFields('Custom'),
                'reviewFields' => $this->getFields('Review'),
            ]);
        }
    }

    /**
     * @param \GeminiLabs\SiteReviews\Contracts\DefaultsContract $defaults
     * @param string $hook
     * @param string $method
     * @param array $values
     * @return void
     * @action site-reviews/defaults
     */
    public function setFieldSanitizers($defaults, $hook, $method, $values)
    {
        if ('custom-fields' !== $hook) {
            return;
        }
        if (empty($values['form'])) {
            return;
        }
        $fields = glsr(FormFields::class)->customFields($values['form']);
        if (!empty($fields)) {
            $sanitizers = glsr(FieldTypeSanitizerDefaults::class)->defaults();
            $sanitize = array_map(function ($field) use ($sanitizers) {
                return Arr::get($sanitizers, Arr::get($field, 'type'), 'text');
            }, $fields);
            $defaults->sanitize = wp_parse_args($sanitize, $defaults->sanitize);
        }
    }

    /**
     * @return array
     */
    protected function getFieldDefaults()
    {
        $fields = $this->getFields();
        $defaults = [];
        foreach ($fields as $field) {
            $defaults[] = $field->defaults;
        }
        return $defaults;
    }

    /**
     * @return array
     */
    protected function getFieldFormats()
    {
        $fields = $this->getFields();
        $formats = [];
        foreach ($fields as $field) {
            $formats[$field->type] = $field->formats();
        }
        return $formats;
    }

    /**
     * @return array
     */
    protected function getFieldOptions()
    {
        $fields = $this->getFields();
        $options = [];
        foreach ($fields as $field) {
            $options[$field->type] = $field->options;
        }
        return $options;
    }

    /**
     * @param string $startsWith
     * @return array
     */
    protected function getFields($startsWith = 'Custom,Review')
    {
        $fields = [];
        $dir = glsr(Application::class)->path('plugin/Fields');
        if (!is_dir($dir)) {
            return $fields;
        }
        $iterator = new \DirectoryIterator($dir);
        foreach ($iterator as $fileinfo) {
            $file = $fileinfo->getFilename();
            if ($fileinfo->isFile() && Str::startsWith($file, $startsWith)) {
                $file = str_replace('.php', '', $file);
                $field = glsr(Helper::buildClassName($file, 'Addon\Forms\Fields'));
                if ($field->isActive()) {
                    $fields[$field->handle] = $field;
                }
            }
        }
        ksort($fields);
        return $fields;
    }

    /**
     * @return array
     */
    protected function getFieldValidation()
    {
        $fields = $this->getFields();
        $validation = [];
        foreach ($fields as $field) {
            $validation[$field->type] = $field->parsedValidation();
        }
        return $validation;
    }

    /**
     * @return array
     */
    protected function getReservedNames()
    {
        $names = array_merge(['form'], glsr(CustomFieldsDefaults::class)->guarded);
        sort($names);
        return $names;
    }
}
