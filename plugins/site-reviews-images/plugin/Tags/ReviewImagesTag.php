<?php

namespace GeminiLabs\SiteReviews\Addon\Images\Tags;

use GeminiLabs\SiteReviews\Addon\Images\Application;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewTag;

class ReviewImagesTag extends ReviewTag
{
    /**
     * @param int $postId
     * @return array
     */
    public static function getAttachments($postId)
    {
        $attachments = [];
        foreach (glsr_get_review($postId)->images as $imageId) {
            $medium = wp_get_attachment_image_src($imageId, 'medium');
            $large = wp_get_attachment_image_src($imageId, 'large');
            $attachments[] = glsr()->args([
                'ID' => $imageId,
                'caption' => wp_get_attachment_caption($imageId),
                'src' => $medium[0],
                'width' => $medium[1],
                'height' => $medium[2],
                'large_src' => $large[0],
                'large_width' => $large[1],
                'large_height' => $large[2],
            ]);
        }
        return $attachments;
    }

    /**
     * @param int $postId
     * @return array
     */
    public static function getAttachmentsForJs($postId)
    {
        $attachments = [];
        foreach (glsr_get_review($postId)->images as $imageId) {
            $attachment = wp_prepare_attachment_for_js($imageId);
            if (!empty($attachment)) {
                unset($attachment['compat']); // Some plugins/themes add HTML to the "compat" attrbute which breaks the JSON
                $attachments[] = $attachment;
            }
        }
        return $attachments;
    }

    /**
     * @param string $value
     * @param string $wrapWith
     * @return string
     */
    public function wrap($value, $wrapWith = null)
    {
        $rawValue = $value;
        $value = glsr()->filterString($this->for.'/value/'.$this->tag, $value, $this);
        if (Helper::isNotEmpty($value)) {
            $value = glsr()->filterString($this->for.'/wrapped', $value, $rawValue, $this);
            $classes = [sprintf('glsr-%s-%s', $this->for, $this->tag)];
            if ('lightbox' === glsr(Application::class)->imageModal()) {
                $classes[] = 'spotlight-group';
            }
            $value = glsr(Builder::class)->div([
                'class' => implode(' ', $classes),
                'text' => $value,
            ]);
        }
        return glsr()->filterString($this->for.'/wrap/'.$this->tag, $value, $rawValue, $this);
    }

    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if ($this->isHidden()) {
            return;
        }
        glsr()->store('use_images', true); // this allows us to load the lightbox script
        $attachments = static::getAttachments($this->review->ID);
        if (!empty($attachments)) {
            $value = glsr(Application::class)->build('views/images', [
                'attachments' => $attachments,
                'modal' => glsr(Application::class)->imageModal(),
            ]);
            return $this->wrap($value);
        }
    }
}
