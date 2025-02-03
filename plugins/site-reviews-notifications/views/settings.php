<?php defined('WPINC') || die; ?>

<h2 class="title">{{ title }}</h2>

<div class="components-notice is-warning" style="background-color:#fff;margin-left:0;">
    <p class="components-notice__content">
        <span class="dashicons-before dashicons-warning" style="color:#dba617;">&nbsp;</span>
        This is a beta version of Review Notifications, please use the <a href="https://niftyplugins.com/account/support/" target="_blank">Support Form</a> on your Nifty Plugins account to report any bugs.
    </p>
</div>

<div class="components-notice is-info" style="margin-left:0;">
    <p class="components-notice__content">
    <?php
        if (glsr_get_option('addons.woocommerce.enabled', false, 'bool')) {
            printf(_x('Schedule review reminders for product orders on the WooCommerce %sEmails%s settings page.' ,'admin-text', 'site-reviews-notifications'),
                sprintf('<a href="%s">', admin_url('admin.php?page=wc-settings&tab=email')),
                '</a>'
            );
        } else {
            printf(_x('If you %senable%s the WooCommerce Reviews integration, you will be able to schedule review reminders for product orders.' ,'admin-text', 'site-reviews-notifications'),
                sprintf('<a href="%s">', glsr_admin_url('settings', 'addons', 'woocommerce')),
                '</a>'
            );
        }
    ?>
    </p>
</div>

<div id="glsrn" class="gl-table">
    <input type="hidden" id="glsrn-data" name="{{ dataKey }}" value=''>
    <div class="gl-thead">
        <div class="gl-col gl-col-primary">
            <?= _x('Notification', 'admin-text', 'site-reviews-notifications'); ?>
        </div>
        <div class="gl-col gl-col-schedule">
            <?= _x('Schedule', 'admin-text', 'site-reviews-notifications'); ?>
        </div>
        <div class="gl-col gl-col-recipient">
            <?= _x('Recipient', 'admin-text', 'site-reviews-notifications'); ?>
        </div>
    </div>
    <div class="gl-tbody"></div>
    <div class="gl-tfoot">
        <button class="button button-primary button-large add-notification" type="button">
            <?= _x('Add Notification', 'admin-text', 'site-reviews-notifications'); ?>
        </button>
    </div>
</div>

<table class="form-table">
    <tbody>
        {{ rows }}
    </tbody>
</table>
