<?php

namespace GeminiLabs\SiteReviews\Addon\Images;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Request;
use WP_Error;

class Uploader
{
    /**
     * @param int $reviewId
     * @return void
     */
    public function attachImages(array $images, $reviewId)
    {
        $this->setUploadDirectory(glsr()->id);
        foreach ($images as $index => $image) {
            $attachmentId = $this->sideload($image, $reviewId);
            if (is_wp_error($attachmentId)) {
                glsr_log()->error('['.Application::ID.'] '.$attachmentId->get_error_message())
                          ->debug(['image' => $image, 'review_post_id' => $reviewId]);
                continue;
            }
            $attachment = get_post($attachmentId);
            $attachment->post_status = 'inherit';
            $attachment->menu_order = $index;
            if (!empty($image->caption)) {
                $attachment->post_excerpt = $image->caption;
            }
            glsr(Application::ID)->action('uploaded', $attachment);
            wp_update_post($attachment);
        }
    }

    /**
     * @return array|WP_Error|void
     */
    public function handle(Request $request)
    {
        $method = Helper::buildMethodName($request->method, 'handle');
        if (!method_exists($this, $method)) {
            glsr_log()->error('['.Application::ID.'] Invalid or missing method value in request')->info($request->toArray());
            return new WP_Error();
        }
        return call_user_func([$this, $method], $request);
    }

    /**
     * @return array|void
     */
    public function handleDelete(Request $request)
    {
        $attachment = get_post($request->id);
        if ('attachment' !== $attachment->post_type || empty($attachment->post_parent)) {
            return;
        }
        $parent = get_post($attachment->post_parent);
        if ($parent->post_type !== glsr()->post_type) {
            return;
        }
        wp_delete_attachment($attachment->ID);
        return [
            'id' => $attachment->ID,
        ];
    }

    /**
     * @return array|void
     */
    public function handlePurge(Request $request)
    {
        if (file_exists($request->file)) {
            wp_delete_file($request->file);
            return [
                'file' => $request->file,
            ];
        }
    }

    /**
     * @return array|WP_Error
     */
    public function handleUpload()
    {
        $this->setUploadDirectory(glsr()->id.'/temp', true);
        $upload = wp_handle_upload($_FILES['file'], [
            'mimes' => $this->getValidMimeTypes(),
            'test_form' => false,
        ]);
        return !empty($upload['error'])
            ? new WP_Error(Application::ID, $upload['error'], $upload)
            : $upload;
    }

    /**
     * @param string $path
     * @param bool $forceFlatDirectory
     * @return array
     */
    public function setUpload(array $upload, $path, $forceFlatDirectory = false)
    {
        if (!$forceFlatDirectory && get_option('uploads_use_yearmonth_folders')) {
            $time = current_time('mysql');
            $y = substr($time, 0, 4);
            $m = substr($time, 5, 2);
            $upload['subdir'] = "/$y/$m";
        }
        $upload['subdir'] = '/'.$path.$upload['subdir'];
        $upload['path'] = $upload['basedir'].$upload['subdir'];
        $upload['url'] = $upload['baseurl'].$upload['subdir'];
        wp_mkdir_p($upload['path']);
        return $upload;
    }

    /**
     * @param string $path
     * @param bool $forceFlatDirectory
     * @return void
     */
    public function setUploadDirectory($path, $forceFlatDirectory = false)
    {
        add_filter('upload_dir', function ($upload) use ($path, $forceFlatDirectory) {
            return $this->setUpload($upload, $path, $forceFlatDirectory);
        });
    }

    /**
     * @param object $image
     * @param int $reviewId
     * @return int|WP_Error
     */
    public function sideload($image, $reviewId)
    {
        $title = sprintf(esc_attr_x('%s Image', 'image title', 'site-reviews-images'), glsr()->name);
        $extension = pathinfo($image->file, PATHINFO_EXTENSION);
        $filename = wp_basename($image->file);
        $filename = glsr(Application::ID)->filterString('filename', $filename, $extension);
        $file = [
            'name' => $filename,
            'tmp_name' => $image->file,
        ];
        $attachmentId = media_handle_sideload($file, $reviewId, $title);
        if (!is_wp_error($attachmentId)) {
            $this->handlePurge(new Request(Arr::consolidate($image)));
        }
        return $attachmentId;
    }

    /**
     * @return string
     */
    protected function getUploadPath($path)
    {
        $dir = trailingslashit(wp_upload_dir()['basedir']).trailingslashit(glsr()->id).$path;
        wp_mkdir_p($dir);
        return trailingslashit($dir);
    }

    /**
     * @return array
     */
    protected function getValidMimeTypes()
    {
        return [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];
    }
}
