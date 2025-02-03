<?php defined('WPINC') || die; ?>

<script type="text/template" id="glsrf-field">
    <div class="glsrf-field-handle">
        <div class="glsrf-td glsrf-td-type sortable-drag">
            <span class="glsrf-field-type type-<%= type %>"></span>
        </div>
        <div class="glsrf-td glsrf-td-label sortable-drag">
            <span>
                <strong><a class="glsrf-toggle-field toggle-field" href="#" tabindex="0" title="<?= _x('Edit field', 'admin-text', 'site-reviews-forms'); ?>"><%= label ? jQuery('<div/>').html(label).text() : handle %></a></strong>
            </span>
        </div>
        <div class="glsrf-td glsrf-td-tag sortable-drag">
            <%= tag.length ? '<code class="glsrf-template-tag template-tag" data-tippy-content="<?= _x('This is the template tag for the field.', 'admin-text', 'site-reviews-forms'); ?>">{{ ' + tag + ' }}</code>' : '' %>
        </div>
        <div class="glsrf-td glsrf-td-action">
            <button type="button" class="button-link glsrf-remove-field" 
                data-tippy-allowHTML="true"
                data-tippy-content='<?= _x('Remove this field?', 'admin-text', 'site-reviews-forms'); ?> <a href="#" class="button remove-field"><?= _x('Yes', 'admin-text', 'site-reviews-forms'); ?></a>'
                data-tippy-delay="0"
                data-tippy-followCursor="false"
                data-tippy-interactive="true"
                data-tippy-offset="[0,0]"
                data-tippy-placement="top"
                data-tippy-theme="danger"
                data-tippy-trigger="click"
            >
                <span class="dashicons-before dashicons-trash"></span>
            </button>
        </div>
    </div>
    <div class="glsrf-field-settings" style="<%= !expanded && 'display:none;' %>"></div>
</script>

<script type="text/template" id="glsrf-field-error">
    <span class="glsrf-field-error"><%= error %></span>
</script>

<script type="text/template" id="glsrf-field-format">
    <div class="glsr-metabox-field" data-option="format">
        <div class="glsr-label">
            <label for=""><?= _x('Display Value As', 'admin-text', 'site-reviews-forms'); ?></label>
        </div>
        <div class="glsr-input wp-clearfix">
            <% if (_.isEmpty(formats)) { %>
                <input data-field="format" type="text" placeholder="F j, Y" class="glsr-input-value" value="<%= format %>" 
                    data-tippy-allowHTML="true"
                    data-tippy-content='<?= _x('Enter a custom date format', 'admin-text', 'site-reviews-forms'); ?> <a href="https://wordpress.org/support/article/formatting-date-and-time/" target="_blank"><?= _x('documentation on date and time formatting', 'admin-text', 'site-reviews-forms'); ?></a>'
                    data-tippy-interactive="true"
                >
            <% } else { %>
                <select data-field="format" data-tippy-content='<?= _x('How the field value is displayed in the review.', 'admin-text', 'site-reviews-forms'); ?>'>
                    <% _.each(formats, function (label, val) { %>
                        <option value="<%= val %>" <% if (format === val) { print('selected')} %>>
                            <%= label %>
                        </option>
                    <% }) %>
                </select>
            <% } %>
        </div>
    </div>
</script>

<script type="text/template" id="glsrf-field-hidden">
    <div class="glsr-metabox-field" data-option="hidden">
        <div class="glsr-label">
            <label><?= _x('Hidden', 'admin-text', 'site-reviews-forms'); ?></label>
        </div>
        <div class="glsr-input wp-clearfix">
            <div class="glsr-toggle-field">
                <span class="glsr-toggle" data-tippy-content="<?= _x('Do you want this field to be hidden?', 'admin-text', 'site-reviews-forms'); ?>">
                    <input data-field="hidden" type="checkbox" class="glsr-toggle__input" <% if (hidden) print('checked') %> value="1">
                    <span class="glsr-toggle__track"></span>
                    <span class="glsr-toggle__thumb"></span>
                </span>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="glsrf-field-label">
    <div class="glsr-metabox-field" data-option="label">
        <div class="glsr-label">
            <label for=""><?= _x('Field Label', 'admin-text', 'site-reviews-forms'); ?></label>
        </div>
        <div class="glsr-input wp-clearfix">
            <input data-field="label" type="text" class="glsr-input-value" value="<%= _.escape(label) %>" data-tippy-content="<?= _x('The Field Label is displayed in the form above the field. If this is a hidden field type, then you can use the label to describe the field but it will not be shown in the form.', 'admin-text', 'site-reviews-forms'); ?>">
        </div>
    </div>
</script>

<script type="text/template" id="glsrf-field-name">
    <div class="glsr-metabox-field" data-option="name">
        <div class="glsr-label">
            <label for=""><?= _x('Field Name', 'admin-text', 'site-reviews-forms'); ?></label>
        </div>
        <div class="glsr-input wp-clearfix">
            <input data-field="name" type="text" class="glsr-input-value" value="<%= name %>" data-tippy-content="<?= _x('The Field Name is the custom field key that is used to save the value to the database. It should be a single alphabetic (a-z) lowercase word with no spaces. Underscores are allowed.', 'admin-text', 'site-reviews-forms'); ?>">
        </div>
    </div>
</script>

<script type="text/template" id="glsrf-field-options">
    <div class="glsr-metabox-field" data-option="options">
        <div class="glsr-label">
            <label for=""><?= _x('Field Options', 'admin-text', 'site-reviews-forms'); ?></label>
        </div>
        <div class="glsr-input wp-clearfix">
            <textarea data-field="options" rows="1" class="glsr-input-value" placeholder="value : label" data-tippy-allowHTML="true" data-tippy-content='<?= _x('Enter each option on a new line. For more control, you may specify a separate value and label like this:<br><br>red : Red<br>green : Green<br>blue : Blue', 'admin-text', 'site-reviews-forms'); ?>'><%= options %></textarea>
        </div>
    </div>
</script>

<script type="text/template" id="glsrf-field-placeholder">
    <div class="glsr-metabox-field" data-option="placeholder">
        <div class="glsr-label">
            <label for=""><?= _x('Placeholder', 'admin-text', 'site-reviews-forms'); ?></label>
        </div>
        <div class="glsr-input wp-clearfix">
            <input data-field="placeholder" type="text" class="glsr-input-value" value="<%= placeholder %>" data-tippy-content="<?= _x('The placeholder text provides a brief hint to what kind of information is expected in the field.', 'admin-text', 'site-reviews-forms'); ?>">
        </div>
    </div>
</script>

<script type="text/template" id="glsrf-field-posttypes">
    <div class="glsr-metabox-field" data-option="posttypes">
        <div class="glsr-label">
            <label for=""><?= _x('Post Types', 'admin-text', 'site-reviews-forms'); ?></label>
        </div>
        <div class="glsr-input wp-clearfix">
            <div data-field="posttypes" class="glsr-search-multibox">
                <div class="glsr-search-multibox-entries" data-tippy-content="<?= _x("Restrict posts to a specific Post Type. If you would prefer to use 'post_id', 'parent_id', or use specific Post ID's, then hide the field and enter them in the Default Value option.", 'admin-text', 'site-reviews-forms'); ?>">
                    <div class="glsr-selected-entries"></div>
                    <input class="glsr-search-input" type="search" autocomplete="off" placeholder="<?= esc_attr_x('Select post types...', 'admin-text', 'site-reviews-forms'); ?>">
                </div>
                <div class="glsr-search-results"></div>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="glsrf-field-required">
    <div class="glsr-metabox-field" data-option="required">
        <div class="glsr-label">
            <label><?= _x('Required', 'admin-text', 'site-reviews-forms'); ?></label>
        </div>
        <div class="glsr-input wp-clearfix">
            <div class="glsr-toggle-field">
                <span class="glsr-toggle" data-tippy-content="<?= _x('Do you want this field to be required?', 'admin-text', 'site-reviews-forms'); ?>">
                    <input data-field="required" type="checkbox" class="glsr-toggle__input" <% if (required) print('checked') %> value="1">
                    <span class="glsr-toggle__track"></span>
                    <span class="glsr-toggle__thumb"></span>
                </span>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="glsrf-field-roles">
    <div class="glsr-metabox-field" data-option="roles">
        <div class="glsr-label">
            <label for=""><?= _x('User Roles', 'admin-text', 'site-reviews-forms'); ?></label>
        </div>
        <div class="glsr-input wp-clearfix">
            <div data-field="roles" class="glsr-search-multibox">
                <div class="glsr-search-multibox-entries" data-tippy-content="<?= _x('Restrict to users that have specific roles, or leave empty for all users.', 'admin-text', 'site-reviews-forms'); ?>">
                    <div class="glsr-selected-entries"></div>
                    <input class="glsr-search-input" type="search" autocomplete="off" placeholder="<?= esc_attr_x('Select roles...', 'admin-text', 'site-reviews-forms'); ?>">
                </div>
                <div class="glsr-search-results"></div>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="glsrf-field-save">
    <div class="glsr-metabox-field">
        <div class="glsr-label">
        </div>
        <div class="glsr-input wp-clearfix">
            <div>
                <button type="button" class="button glsrf-save-field save-field"><?= _x('Save Field', 'admin-text', 'site-reviews-forms'); ?></button>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="glsrf-field-tag">
    <div class="glsr-metabox-field" data-option="tag">
        <div class="glsr-label">
            <label for=""><?= _x('Template Tag', 'admin-text', 'site-reviews-forms'); ?></label>
        </div>
        <div class="glsr-input wp-clearfix">
            <input data-field="tag" type="text" class="glsr-input-value" value="<%= tag %>" data-tippy-content="<?= _x('The Template Tag is used to display the field value in the review template, it should be a single alphabetic (a-z) lowercase word with no spaces. Underscores are allowed.', 'admin-text', 'site-reviews-forms'); ?>">
        </div>
    </div>
</script>

<script type="text/template" id="glsrf-field-tag_label">
    <div class="glsr-metabox-field" data-option="tag_label">
        <div class="glsr-label">
            <label for=""><?= _x('Template Tag Label', 'admin-text', 'site-reviews-forms'); ?></label>
        </div>
        <div class="glsr-input wp-clearfix">
            <input data-field="tag_label" type="text" class="glsr-input-value" value="<%= _.escape(tag_label) %>" data-tippy-content="<?= _x('The Template Tag Label is displayed with the template tag in the Review Template. It can be used to describe the value being displayed in the review', 'admin-text', 'site-reviews-forms'); ?>">
        </div>
    </div>
</script>

<script type="text/template" id="glsrf-field-terms">
    <div class="glsr-metabox-field" data-option="terms">
        <div class="glsr-label">
            <label for=""><?= _x('Categories', 'admin-text', 'site-reviews-forms'); ?></label>
        </div>
        <div class="glsr-input wp-clearfix">
            <div data-field="terms" class="glsr-search-multibox">
                <div class="glsr-search-multibox-entries" data-tippy-content="<?= _x('Restrict to specific categories, or leave empty for all categories.', 'admin-text', 'site-reviews-forms'); ?>">
                    <div class="glsr-selected-entries"></div>
                    <input class="glsr-search-input" type="search" autocomplete="off" placeholder="<?= esc_attr_x('Select categories...', 'admin-text', 'site-reviews-forms'); ?>">
                </div>
                <div class="glsr-search-results"></div>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="glsrf-field-type">
    <div class="glsr-metabox-field" data-option="type">
        <div class="glsr-label">
            <label for=""><?= _x('Field Type', 'admin-text', 'site-reviews-forms'); ?></label>
        </div>
        <div class="glsr-input wp-clearfix">
            <select data-field="type" data-tippy-content='<?= _x('"Review Fields" can only be used once in the form.', 'admin-text', 'site-reviews-forms'); ?>'>
                <optgroup label="<?= _x('Custom Fields', 'admin-text', 'site-reviews-forms'); ?>">
                    <?php foreach ($customFields as $field): ?>
                    <option value="<?= $field->type; ?>" <% if (type === '<?= $field->type; ?>') { print('selected')} %>>
                        <?= $field->handle ?>
                    </option>
                    <?php endforeach; ?>
                </optgroup>
                <optgroup label="<?= _x('Review Fields', 'admin-text', 'site-reviews-forms'); ?>">
                    <?php foreach ($reviewFields as $field): ?>
                    <option value="<?= $field->type; ?>" <% if (type === '<?= $field->type; ?>') { print('selected')} %>>
                        <?= $field->handle ?>
                    </option>
                    <?php endforeach; ?>
                </optgroup>
            </select>
        </div>
    </div>
</script>

<script type="text/template" id="glsrf-field-users">
    <div class="glsr-metabox-field" data-option="users">
        <div class="glsr-label">
            <label for=""><?= _x('Users', 'admin-text', 'site-reviews-forms'); ?></label>
        </div>
        <div class="glsr-input wp-clearfix">
            <div data-field="users" class="glsr-search-multibox">
                <div class="glsr-search-multibox-entries">
                    <div class="glsr-selected-entries"></div>
                    <input class="glsr-search-input" type="search" autocomplete="off" placeholder="<?= esc_attr_x('Select users...', 'admin-text', 'site-reviews-forms'); ?>">
                </div>
                <div class="glsr-search-results"></div>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="glsrf-field-value">
    <div class="glsr-metabox-field" data-option="value">
        <div class="glsr-label">
            <label for=""><?= _x('Default Value', 'admin-text', 'site-reviews-forms'); ?></label>
        </div>
        <div class="glsr-input wp-clearfix">
            <input data-field="value" type="<%= ~['rating','number'].indexOf(type) || ~['rating'].indexOf(name) ? 'number' : 'text' %>" class="glsr-input-value" value="<%= value %>" data-tippy-content="<?= _x('Leave this blank if you do not want the field to have a default value.', 'admin-text', 'site-reviews-forms'); ?>">
        </div>
    </div>
</script>

<script type="text/template" id="glsrf-multibox-entry">
    <span class="glsr-multibox-entry">
        <button type="button" class="glsr-remove-button">
            <span class="glsr-remove-icon" aria-hidden="true"></span>
            <span class="screen-reader-text"><?= _x('Remove entry', 'admin-text', 'site-reviews-forms'); ?></span>
        </button>
        <span data-slug="<%= slug %>"><%= name %></span>
    </span>
</script>

<script type="text/template" id="glsrf-multibox-result">
    <span class="glsr-search-result" tabindex="0" data-slug="<%= slug %>"><%= name %></span>
</script>
