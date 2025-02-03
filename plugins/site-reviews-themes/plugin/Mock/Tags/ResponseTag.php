<?php

namespace GeminiLabs\SiteReviews\Addon\Themes\Mock\Tags;

use GeminiLabs\SiteReviews\Modules\Html\Builder;

class ResponseTag extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        $title = sprintf(__('Response from %s', 'site-reviews'), get_bloginfo('name'));
        $response = glsr(Builder::class)->div([
            'class' => 'glsr-review-response-inner',
            'text' => sprintf('<p><strong>%s</strong></p><p>%s</p>', $title, $value),
        ]);
        return $response;
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function value($value = null)
    {
        return 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.';
    }
}
