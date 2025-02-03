<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Tags;

use GeminiLabs\SiteReviews\Addon\Themes\ThemeSettings;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Avatar;
use GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewAvatarTag;

class AvatarTag extends ReviewAvatarTag
{
    /**
     * @var int
     */
    public $avatarSize;

    /**
     * {@inheritdoc}
     */
    public function regenerateAvatar($avatarUrl)
    {
        if ($this->canRegenerateAvatar()) {
            return glsr(Avatar::class)->generate($this->review, $this->avatarSize);
        }
        return $avatarUrl;
    }

    /**
     * // We ignore the avatar settings here
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        $this->setAvatarSize();
        $this->review->set('avatar', $this->regenerateAvatar($value));
        return $this->wrap(
            glsr(Avatar::class)->img($this->review, $this->avatarSize)
        );
    }

    /**
     * @return void
     */
    protected function setAvatarSize()
    {
        $this->avatarSize = glsr(ThemeSettings::class)
            ->themeId($this->args->theme)
            ->get('design.avatar.avatar_size', 0);
    }
}
