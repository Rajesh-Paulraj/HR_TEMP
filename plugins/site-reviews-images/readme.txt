=== Site Reviews: Review Images ===
Contributors: pryley, geminilabs
Donate link: https://ko-fi.com/pryley
Tags: Site Reviews, review images, review photos
Tested up to: 6.1
Stable tag: 3.0.5
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Allow people to add images with captions to their reviews.

== Description ==

Allow people to add images with captions to their reviews.

Simply drag-and-drop images into the field to add them to the review, then add optional captions to the images. Easy peasy!

Images appear underneath the review and clicking on them will open the images in a lightbox modal.

The settings allow you to set the accepted image file types (PNG and/or JPEG), the maximum allowed file size, and the maximum number of images per review.

Once the add-on is installed and activated, the image field will automatically be available in the review form.

= Minimum plugin requirements =

If your server and website does not meet these minimum requirements, the plugin will automatically deactivate and a notice will appear explaining why.

- WordPress 5.8
- PHP 7.2

== Installation ==

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==

= 3.0.5 (2022-12-27) =

- Fixed addon deactivation
- Fixed notice which is displayed when Site Reviews is not activated
- Fixed textarea layout in caption modal

= 3.0.4 (2022-10-11) =

- Added "Text only" option in the media filter (Review Filters add-on required)

= 3.0.3 (2022-09-24) =

- Fixed add-on documentation
- Fixed attachment post_status of uploaded images

= 3.0.2 (2022-09-19) =

- Added the "site-reviews-images/filename" filter hook
- Added the "site-reviews-images/uploaded" action hook
- Fixed Dropzone to clear images after review submission
- Updated the add-on documentation

= 3.0.1 (2022-09-15) =

- Fixed gallery swiper

= 3.0.0 (2022-09-14) =

- 🚨 Requires at least PHP v7.2
- 🚨 Requires at least Site Reviews v6.0.0
- 🚨 Requires at least WordPress v5.8
- Added Review Image Galleries (use the new block, shortcode, or Elementor widget)
- Added support for images in the Site Reviews REST API
- Fixed bug which removed images when bulk-editing reviews
- Fixed compatibility with the Elementor Lightbox
- Fixed various CSS layout issues with the image modal

= 2.5.0 (2022-05-26) =

- Updated dropzone.js to v5.9.3
- Updated "Tested up to" version

= 2.4.0 (2022-04-09) =

- Added ability to sort reviews by image count on the All Reviews admin page
- Fixed compatibility with Review Filters v2.0
- Fixed dropzone console errors
- Fixed lightbox with pagination when review images do not appear on the first page

= 2.3.2 (2022-03-25) =

- Fixed compatibility with the WPForms plugin
- Fixed image captions when using the Lightbox

= 2.3.1 (2022-02-17) =

- Fixed image dropzones when multiple forms are used on the same page

= 2.3.0 (2022-01-26) =

- Fixed Lightbox compatibility with paginated reviews
- Updated the Site Reviews required version to 5.20.0
- Updated the WordPress required version to 5.8

= 2.2.2 (2021-11-15) =

- Fixed add-on updater
- Fixed deactivation notice on WordPress settings pages

= 2.2.0 (2021-09-23) =

- Added "defer" attribute to scripts loaded from CDN
- Added option to display images in a lightbox

= 2.1.6 (2021-07-15) =

- Fixed compatibility with Elementor Pro popups

= 2.1.5 (2021-07-03) =

- Fixed review SQL queries from returning duplicated images created by multilingual plugins (i.e. WPML Media)

= 2.1.3 (2021-06-23) =

- Fixed compatibility with the WPML Media plugin

= 2.1.2 (2021-06-01) =

- Fixed "Add Image" button in Images metabox

= 2.1.1 (2021-03-20) =

- Added setting tooltips 

= 2.1.0 (2021-02-16) =

- Fixed compatibility with Site Reviews v5.7.0
- Fixed image sizing in modal

= 2.0.0 (2021-02-04) =

- Added ability to add/edit/replace/sort images in existing reviews
- Added integration with the Site Reviews modal (replacing the old modal implementation)
- Fixed image values from incorrectly being stored as metadata

= 1.1.2 (2021-01-29) =

- Fixed an issue that could throw a PHP error in the Media Library

= 1.1.1 (2021-01-25) =

- Fixed an issue which could affect auto-updates

= 1.1.0 (2020-12-13) =

- Added support for Site Reviews v5.3
- Requires Site Reviews v5.3

= 1.0.2 (2020-10-27) =

- Fixed loading of the exif-js script which corrects image orientation

= 1.0.1 (2020-10-26) =

- Fixed a SQL bug

= 1.0.0 (2020-10-22) =

- First release
