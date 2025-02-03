<?php

namespace GeminiLabs\SiteReviews\Addon\Notifications;

use GeminiLabs\Pelago\Emogrifier\CssInliner;
use GeminiLabs\Pelago\Emogrifier\HtmlProcessor\CssToAttributeConverter;
use GeminiLabs\Pelago\Emogrifier\HtmlProcessor\HtmlPruner;
use GeminiLabs\SiteReviews\Addon\Notifications\Defaults\EmailDefaults;
use GeminiLabs\SiteReviews\Addon\Notifications\Defaults\SettingsDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Color;

class Email extends \GeminiLabs\SiteReviews\Modules\Email
{
    /**
     * @var \GeminiLabs\SiteReviews\Arguments
     */
    public $settings;

    /**
     * @var string
     */
    public $css;

    public function __construct()
    {
        $this->settings = glsr(Application::ID)->options(SettingsDefaults::class);
    }

    /**
     * {@inheritdoc}
     */
    public function app()
    {
        return glsr(Application::class);
    }

    /**
     * @return Email
     */
    public function compose(array $email, array $data = [])
    {
        $this->data = $data;
        $this->normalize($email);
        $this->attachments = $this->email['attachments'];
        $this->css = $this->inlineStyles();
        $this->headers = $this->buildHeaders();
        $this->message = $this->buildHtmlMessage();
        $this->subject = $this->email['subject'];
        $this->to = $this->email['to'];
        add_action('phpmailer_init', [$this, 'buildPlainTextMessage']);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return glsr(EmailDefaults::class);
    }

    /**
     * @param \WP_Error $error
     * @return void
     */
    public function logMailError($error)
    {
        throw new \Exception('[wp_mail] Email was not sent: '.$error->get_error_message());
    }

    /**
     * @return void|bool
     */
    public function send()
    {
        $required = [
            'message' => $this->message,
            'recipient' => $this->to,
            'subject' => $this->subject,
        ];
        $missing = array_keys(array_diff($required, array_filter($required)));
        if (!empty($missing)) {
            $error = sprintf('The email is missing the following fields: %s', implode(', ', $missing));
            throw new \Exception($error);
        }
        return parent::send();
    }

    /**
     * {@inheritdoc}
     */
    public function template()
    {
        return glsr(Template::class);
    }

    /**
     * @return string
     */
    protected function buildHtmlMessage()
    {
        $html = $this->template()->build('templates/emails/'.$this->email['template'], [
            'context' => $this->email['template-tags'],
        ]);
        try {
            $cssInliner = CssInliner::fromHtml($html)->inlineCss($this->css);
            HtmlPruner::fromDomDocument($cssInliner->getDomDocument())
                ->removeElementsWithDisplayNone()
                ->removeRedundantClassesAfterCssInlined($cssInliner);
            CssToAttributeConverter::fromDomDocument($cssInliner->getDomDocument())
                ->convertCssToVisualAttributes();
            $message = $cssInliner->render();
        } catch (\Exception $e) {
            glsr_log()->error('Emogrifer: '.$e->getMessage());
            $style = sprintf('<style type="text/css">%s</style>', $this->css);
            $message = str_replace('</head>', $style.'</head>', $html);
        }
        return $this->app()->filterString('email/message', stripslashes($message), 'html', $this);
    }

    /**
     * @return string
     */
    protected function buildMessage()
    {
        return '';
    }

    /**
     * @return array
     */
    protected function colors()
    {
        $colors = [
            'background_color' => $this->settings->background_color,
            'body_background_color' => $this->settings->body_background_color,
            'body_link_color' => $this->settings->body_link_color,
            'body_text_color' => $this->settings->body_text_color,
            'brand_color' => $this->settings->brand_color,
            'footer_text_color' => '',
            'header_text_color' => '',
        ];
        $color = Color::new($colors['background_color']);
        if (!is_wp_error($color)) {
            if ($color->isLight()) {
                $colors['footer_text_color'] = (string) $color->mix('#000', .25)->toHex();
            } else {
                $colors['footer_text_color'] = (string) $color->mix('#fff', .75)->toHex();
            }
        }
        $color = Color::new($colors['brand_color']);
        if (!is_wp_error($color)) {
            if ($color->isLight()) {
                $colors['header_text_color'] = (string) $color->mix('#000', .85)->toHex();
            } else {
                $colors['header_text_color'] = (string) $color->mix('#fff', .85)->toHex();
            }
        }
        return array_map('esc_attr', $colors);
    }

    /**
     * @return string
     */
    protected function inlineStyles()
    {
        return $this->template()->build('templates/styles/'.$this->email['style'], [
            'context' => $this->colors(),
        ]);
    }
}
