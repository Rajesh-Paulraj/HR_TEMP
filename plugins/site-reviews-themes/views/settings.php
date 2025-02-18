<?php defined('WPINC') || die; ?>

<h2 class="title">{{ title }}</h2>

<div class="components-notice is-warning" style="background-color:#fff;margin-left:0;">
    <p class="components-notice__content">
        <span class="dashicons-before dashicons-warning" style="color:#dba617;">&nbsp;</span>
        This is a beta version of Review Themes, please use the <a href="https://niftyplugins.com/account/support/" target="_blank">Support Form</a> on your Nifty Plugins account to report any bugs.
    </p>
</div>

<table class="form-table">
    <tbody>
        {{ rows }}
    </tbody>
</table>
