<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://ap.washington.edu
 * @since      1.0.0
 *
 * @package    Rpt_Info
 * @subpackage Rpt_Info/admin/partials
 */
?>
<div class="wrap">
    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
    <form action="options.php" method="post">
        <?php
        settings_fields($this->plugin_name);
        do_settings_sections($this->plugin_name);
        submit_button('Save above settings'); ?>
    </form>
<!--    <h2>Template types</h2>
    <p>Settings dealing with template types.</p>
    <form action="<?= esc_url(admin_url('admin-post.php')); ?>"
          method="post" accept-charset="utf-8" class="template-type-form"
          id="template-type-form">
        <input type="hidden" name="action" value="process_template_type_updates" />
        <table class="form-table" role="presentation">
            <tbody>
            <?php // foreach ( $template_types as $id => $template_type ) : ?>
                <tr>
                    <td><?= $template_type->TemplateTypeName; ?></td>
                    <td>
                        <select type="text" name="InUse-<?= $id; ?>" id="InUse-<?= $id; ?>">
                            <option value="Yes" <?php if ($template_type->InUse == 'Yes') echo ' selected'; ?>>Yes</option>
                            <option value="No" <?php if ($template_type->InUse == 'No') echo ' selected'; ?>>No</option>
                        </select>
                    </td>
                </tr>
            <?php // endforeach; ?>
            </tbody>
        </table>
        <input type="submit" name="submit" id="templatetypesubmit" class="button button-primary"
               value="Save template type updates" />
        <p><em>Note: This saves only template type changes, not settings at the top of the page!</em></p>
    </form> -->
</div>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
