<?php defined('WPINC') || die; ?>

<script type="text/template" id="tmpl-glsrn-criteria">
    <div class="glsr-criteria-option">
        <select id="<%= key %>-<%= cid %>">
            <% _.each(conditions, function (name, key) { %>
                <option value="<%= key %>" <% if (key === selected) { print('selected') } %>><%= name %></option>
            <% }); %>
        </select>
        <button type="button" class="button dashicons-before dashicons-plus-alt2 glsr-add-condition" aria-label="<?= _x('Add a new condition', 'admin-text', 'site-reviews-notifications'); ?>"></button>
    </div>
    <div class="glsr-criteria-conditions"></div>
</script>

<script type="text/template" id="tmpl-glsrn-criteria-condition">
    <select data-condition="field">
        <% _.each(fields, function (name, key) { %>
            <option value="<%= key %>" <% if (key === field) { print('selected') } %>><%= name %></option>
        <% }); %>
    </select>
    <% if (!_.isEmpty(operators)) { %>
        <select data-condition="operator">
            <% _.each(operators, function (name, key) { %>
                <option value="<%= key %>" <% if (key === operator) { print('selected') } %>><%= name %></option>
            <% }); %>
        </select>
    <% } %>
    <% if (!_.isEmpty(values)) { %>
        <select data-condition="value">
            <% _.each(values, function (name, key) { %>
                <option value="<%= key %>" <% if (key === value) { print('selected') } %>><%= name %></option>
            <% }); %>
        </select>
    <% } %>
    <% if (!_.isEmpty(operators) && _.isEmpty(values)) { %>
        <input data-condition="value" type="text" class="glsr-input-value" value="<%= value %>">
    <% } %>
    <button type="button" class="button button-link-delete dashicons-before dashicons-minus glsr-remove-condition" aria-label="<?= _x('Remove this condition', 'admin-text', 'site-reviews-notifications'); ?>"></button>
</script>

<script type="text/template" id="tmpl-glsrn-field-actions">
    <div class="glsr-metabox-field">
        <div class="glsr-label"></div>
        <div class="glsr-input wp-clearfix">
            <div style="display:flex;justify-content:space-between">
                <div>
                    <button type="button" class="components-button is-secondary save"><?= _x('Save', 'admin-text', 'site-reviews-notifications'); ?></button>
                    <button type="button" class="components-button is-destructive delete"><?= _x('Delete', 'admin-text', 'site-reviews-notifications'); ?></button>
                </div>
                <div>
                    <button style="display:none" type="button" class="components-button is-secondary test"><?= _x('Send Test Email', 'admin-text', 'site-reviews-notifications'); ?></button>
                </div>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="tmpl-glsrn-field-conditions">
    <div class="glsr-metabox-field" data-option="conditions">
        <div class="glsr-label">
            <label for="conditions-<%= cid %>">
                <?= _x('Send Conditions', 'admin-text', 'site-reviews-notifications'); ?>
            </label>
        </div>
        <div class="glsr-input">
            <div data-field="conditions" class="glsr-criteria"></div>
        </div>
    </div>
</script>

<script type="text/template" id="tmpl-glsrn-field-enabled">
    <div class="glsr-metabox-field" data-option="enabled">
        <div class="glsr-label">
            <label for="enabled-<%= cid %>"><?= _x('Enabled', 'admin-text', 'site-reviews-notifications'); ?></label>
        </div>
        <div class="glsr-input wp-clearfix">
            <div class="glsr-toggle-field">
                <span class="glsr-toggle">
                    <input data-field="enabled" id="enabled-<%= cid %>" type="checkbox" class="glsr-toggle__input" <% if (enabled) print('checked') %> value="1">
                    <span class="glsr-toggle__track"></span>
                    <span class="glsr-toggle__thumb"></span>
                </span>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="tmpl-glsrn-field-error">
    <span class="glsrn-field-error"><%= error %></span>
</script>

<script type="text/template" id="tmpl-glsrn-field-heading">
    <div class="glsr-metabox-field" data-option="heading">
        <div class="glsr-label">
            <label for="heading-<%= cid %>">
                <i class="dashicons-before dashicons-editor-help" data-tippy-allowhtml="1" data-tippy-content="<?= sprintf(_x('The available placeholder tags are: %s', 'admin-text', 'site-reviews-notifications'), $tags); ?>" data-tippy-delay="[200,null]" data-tippy-interactive="1"  data-tippy-placement="top-start"></i>
                <?= _x('Email Heading', 'admin-text', 'site-reviews-notifications'); ?>
            </label>
        </div>
        <div class="glsr-input wp-clearfix">
            <input data-field="heading" type="text" id="heading-<%= cid %>" class="glsr-input-value" value="<%- heading %>">
        </div>
    </div>
</script>

<script type="text/template" id="tmpl-glsrn-field-message">
    <div class="glsr-metabox-field" data-option="message">
        <div class="glsr-label">
            <label for="message-<%= cid %>">
                <i class="dashicons-before dashicons-editor-help" data-tippy-allowhtml="1" data-tippy-content="<?= sprintf(_x('The available placeholder tags are: %s', 'admin-text', 'site-reviews-notifications'), $tags); ?>" data-tippy-delay="[200,null]" data-tippy-interactive="1"  data-tippy-placement="top-start"></i>
                <?= _x('Email Message', 'admin-text', 'site-reviews-notifications'); ?>
            </label>
        </div>
        <div class="glsr-input wp-clearfix">
            <textarea data-field="message" id="message-<%= cid %>" rows="6" class="wp-editor-area glsr-input-value"><%- message %></textarea>
        </div>
    </div>
</script>

<script type="text/template" id="tmpl-glsrn-field-recipients">
    <div class="glsr-metabox-field" data-option="recipients">
        <div class="glsr-label">
            <label for="recipients-<%= cid %>"><?= _x('Recipients', 'admin-text', 'site-reviews-notifications'); ?></label>
        </div>
        <div class="glsr-input wp-clearfix">
            <div data-field="recipients" class="glsr-search-multibox"></div>
        </div>
    </div>
</script>

<script type="text/template" id="tmpl-glsrn-field-schedule">
    <div class="glsr-metabox-field" data-option="schedule">
        <div class="glsr-label">
            <label for="schedule-<%= cid %>"><?= _x('Send Schedule', 'admin-text', 'site-reviews-notifications'); ?></label>
        </div>
        <div class="glsr-input wp-clearfix">
            <select data-field="schedule" id="schedule-<%= cid %>">
                <% _.each(addon.schedule, function (name, num) { %>
                    <option value="<%= num %>" <% if (+num === +schedule) { print('selected')} %>><%= name %></option>
                <% }) %>
            </select>
        </div>
    </div>
</script>

<script type="text/template" id="tmpl-glsrn-field-subject">
    <div class="glsr-metabox-field" data-option="subject">
        <div class="glsr-label">
            <label for="subject-<%= cid %>">
                <i class="dashicons-before dashicons-editor-help" data-tippy-allowhtml="1" data-tippy-content="<?= sprintf(_x('The available placeholder tags are: %s', 'admin-text', 'site-reviews-notifications'), $tags); ?>" data-tippy-delay="[200,null]" data-tippy-interactive="1"  data-tippy-placement="top-start"></i>
                <?= _x('Email Subject', 'admin-text', 'site-reviews-notifications'); ?>
            </label>
        </div>
        <div class="glsr-input wp-clearfix">
            <input data-field="subject" type="text" id="subject-<%= cid %>" class="glsr-input-value" value="<%- subject %>">
        </div>
    </div>
</script>

<script type="text/template" id="tmpl-glsrn-multibox">
    <div class="glsr-search-multibox-entries">
        <div class="glsr-selected-entries"></div>
        <input id="<%= key %>-<%= cid %>" class="glsr-search-input" type="search" autocomplete="off" placeholder="<?= esc_attr_x('Select a recipient or type an email...', 'admin-text', 'site-reviews-notifications'); ?>">
    </div>
    <div class="glsr-search-results">
        <% _.each(options, function (option) { %>
            <span class="glsr-search-result" data-slug="<%= option.slug %>"><%= option.name %></span>
        <% }) %>
    </div>
</script>

<script type="text/template" id="tmpl-glsrn-multibox-entry">
    <span class="glsr-multibox-entry">
        <button type="button" data-slug="<%= slug %>" class="glsr-remove-button glsr-remove-icon">
            <span class="screen-reader-text"><?= _x('Remove entry', 'admin-text', 'site-reviews-notifications'); ?></span>
        </button>
        <span data-slug="<%= slug %>"><%= name %></span>
    </span>
</script>

<script type="text/template" id="tmpl-glsrn-notification">
    <div class="glsrn-notification <%= enabled ? 'is-active' : '' %>">
        <div class="gl-header">
            <div class="gl-col gl-col-primary">
                <i class="dashicons-before dashicons-yes-alt"></i>
                <span>
                    <a class="toggle-field" href="#" tabindex="0" title="<?= _x('Edit notification', 'admin-text', 'site-reviews-notifications'); ?>">
                        <%= subject || '<?= _x('No Subject', 'admin-text', 'site-reviews-notifications'); ?>' %>
                    </a>
                </span>
            </div>
            <div class="gl-col gl-col-schedule">
                <span><%= selected_schedule %></span>
            </div>
            <div class="gl-col gl-col-recipient">
                <span><%= selected_recipients %></span>
            </div>
        </div>
        <div class="gl-settings"></div>
    </div>
</script>

<script type="text/template" id="tmpl-glsrn-notification-empty">
    <div class="glsrn-notification glsrn-placeholder">
        <div class="gl-header-instruct">
            <div class="gl-col gl-col-primary">
                <span><?= _x('Click the <strong>Add Notification</strong> button to add a new notification.', 'admin-text', 'site-reviews-notifications'); ?></span>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="tmpl-glsrn-preview">
    <style>
        <?= $style; ?>
    </style>
    <div class="preview-email">
        <?= $preview; ?>
    </div>
</script>
