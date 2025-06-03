<?php
/**
 * rpt_form_hidden_field
 *      generate html for a hidden field on a form
 *
 * @param $field_name
 * @param $field_value
 * @param $field_id
 * @return string
 */
function rpt_form_hidden_field($field_name, $field_value, $field_id = '') : string
{
    if ( $field_id == '' ) {
        $field_id = $field_name;
    }
    return '<input type="hidden" name="' . $field_name . '" id="' . $field_id . '" value="' . $field_value . '">';
}

/**
 * rpt_form_target_rank_list
 *      generate a dropdown list for target ranks
 *
 * @param $field_name
 * @param $field_value
 * @param $label_text
 * @param $value_list
 * @param $display_key
 * @param $hide_group
 * @param $control_class
 * @param $zero_text
 * @param $help_text
 * @return string
 */
function rpt_form_target_rank_list($field_name, $field_value, $label_text, $value_list, $display_key = '',
                                  $hide_group = FALSE, $control_class = '', $zero_text = '', $help_text = '',
                                  $required = FALSE) : string
{
    $result = '<div class="form-group" id="' . $field_name . '-group"';
    if ( $hide_group ) {
        $result .= ' style="display:none;"';
    }
    $result .= '>';
    $result .= '<label for="' . $field_name . '">' . $label_text . '</label>';
    $result .= '      <select type="text" name="' . $field_name . '" id="' . $field_name . '"';
    if ( $control_class != '' ) {
        $result .= ' class="' . $control_class . '"';
    }
    if ( $required == TRUE ) {
        $result .= ' required="required"';
    }
    $result .= ' />';
    if ( $zero_text ) {
        $result .= '      <option value="0">' . $zero_text . '</option>';
    }
    foreach ($value_list as $key => $value) {
        $result .= '        <option value="' . $key . '"';
        if ( $field_value == $key ) {
            $result .= ' selected="selected"';
        }
        $result .= ' data-actiontype="' . $value['ActionType'] . '"';
        $result .= '>' . $value['TargetRankName'] . '</option>';
    }
    $result .= '      </select>';
    if ( $help_text != '' ) {
        $result .= '<br><em class="help-block">' . $help_text . '</em>';
    }
    $result .= '<div><span id="actiontype-display"></span></div>';
    $result .= '  </div>'; // <!-- form group -->
    return $result;
}

/**
 * rpt_template_select
 *      generate a dropdown list for selecting a template for a case
 *
 * @param $field_name
 * @param $field_value
 * @param $label_text
 * @param $value_list
 * @param $hide_group
 * @param $control_class
 * @param $zero_text
 * @param $help_text
 * @return string
 */
function rpt_template_select($field_name, $field_value, $label_text, $value_list,
                            $hide_group = FALSE, $control_class = '', $zero_text = '', $help_text = '',
                            $required = FALSE) : string
{
    $result = '<div class="form-group" id="' . $field_name . '-group"';
    if ( $hide_group ) {
        $result .= ' style="display:none;"';
    }
    $result .= '>';
    $result .= '<label for="' . $field_name . '">' . $label_text . '</label>';
    $result .= '      <select type="text" name="' . $field_name . '" id="' . $field_name . '"';
    if ( $control_class != '' ) {
        $result .= ' class="' . $control_class . '"';
    }
    if ( $required == TRUE ) {
        $result .= ' required="required"';
    }
    $result .= ' />';
    if ( $zero_text ) {
        $result .= '      <option value="0">' . $zero_text . '</option>';
    }
    foreach ($value_list as $key => $value) {
        $result .= '        <option value="' . $key . '"';
        if ( $field_value == $key ) {
            $result .= ' selected="selected"';
        }
        $result .= '>' . $value['TemplateName'] . ' (' . $value['UnitName'] . ')</option>';
    }
    $result .= '      </select>'; // <!-- col -->
    $result .= '  </div>'; // <!-- form group -->
    return $result;
}

/**
 * rpt_form_date_select
 *      generate a date select input
 *
 * @param $field_name
 * @param $field_value
 * @param $label_text
 * @param $hide_group
 * @param $control_class
 * @param $init_disabled
 * @param $init_required
 * @param $help_text
 * @return string
 */
function rpt_form_date_select($field_name, $field_value, $label_text, $hide_group = FALSE,
                             $control_class = '', $init_disabled = FALSE, $init_required = FALSE, $help_text = '')
{
    $result = '<div class="form-group" id="' . $field_name . '-group"';
    if ( $hide_group ) {
        $result .= ' style="display:none;"';
    }
    $result .= '>';
    $result .= '<label for="' . $field_name . '">' . $label_text . '</label>';
    $result .= '      <input type="date" name="' . $field_name . '" value="' . $field_value
        . '" id="' . $field_name . '"';
    if ( $control_class != '' ) {
        $result .= ' class="' . $control_class . '"';
    }
    if ( $init_disabled ) {
        $result .= ' readonly';
    }
    if ( $init_required ) {
        $result .= ' required';
    }
    $result .= ' />';
    if ( $help_text != '' ) {
        $result .= '<br><em class="help-block">' . $help_text . '</em>';
    }
    $result .= '  </div>'; // <!-- form group -->
    return $result;
}

/**
 * rpt_form_dropdown_list
 *      generate a generic dropdown list
 *
 * @param $field_name
 * @param $field_value
 * @param $label_text
 * @param $value_list
 * @param $display_key
 * @param $hide_group
 * @param $control_class
 * @param $zero_text
 * @param $help_text
 * @return string
 */
function rpt_form_dropdown_list($field_name, $field_value, $label_text, $value_list, $display_key = '',
                               $hide_group = FALSE, $control_class = '', $zero_text = '', $help_text = '') : string
{
    $result = '<div class="form-group" id="' . $field_name . '-group"';
    if ( $hide_group ) {
        $result .= ' style="display:none;"';
    }
    $result .= '>';
    $result .= '<label for="' . $field_name . '">' . $label_text . '</label>';
    $result .= '      <select type="text" name="' . $field_name . '" id="' . $field_name . '"';
    if ( $control_class != '' ) {
        $result .= ' class="' . $control_class . '"';
    }
    $result .= ' />';
    if ( $zero_text ) {
        $result .= '      <option value="0">' . $zero_text . '</option>';
    }
    foreach ($value_list as $key => $value) {
        $result .= '        <option value="' . $key . '"';
        if ( $field_value == $key ) {
            $result .= ' selected="selected"';
        }
        foreach ($value as $key2 => $value2) {
            $result .= ' data-' . $key2 . '="' . $value2 . '"';
        }
        if ( $display_key != '' ) {
            $result .= '>' . $value[$display_key] . '</option>';
        }
        else {
            $result .= '>' . $value . '</option>';
        }
    }
    $result .= '      </select>'; // <!-- col -->
    $result .= '  </div>'; // <!-- form group -->
    return $result;
}

function report_table( $header = [], $data = [], $total_columns = [] ) : string
{
    $totals = [];
    foreach ($total_columns as $col) {
        $totals[$col] = 0;
    }
    $label_col = '';
    $result = '<table class="table table-striped table-bordered">';
    $result .= '<thead>';
    $result .= '<tr>';
    foreach ( $header as $key => $value ) {
        if ( $label_col == '' ) {
            $label_col = $key;
        }
        $result .= '<th>' . $value . '</th>';
    }
    $result .= '</tr>';
    $result .= '</thead>';
    $result .= '<tbody>';
    foreach ( $data as $key => $value ) {
        $result .= '<tr>';
        $result .= '<td>' . $key . '</td>';
        foreach ($value as $key2 => $value2) {
            if ( $value2 != $key ) {
                $result .= '<td>' . $value2 . '</td>';
                if ( in_array($key2, $total_columns) ) {
                    $totals[$col] += $value2;
                }
            }
        }
        $result .= '</tr>';
    }
    if ( count( $total_columns) > 0 ) {
        $result .= '<tr>';
        $result .= '<td><strong>Total</strong></td>';
        foreach ( $header as $key => $value ) {
            if ( in_array($key, $total_columns) ) {
                $result .= '<td><strong>' . $totals[$key] . '</strong></td>';
            }
            elseif ( $key != $label_col ) {
                $result .= '<td></td>';
            }
        }
    }
    $result .= '</tbody>';
    $result .= '</table>';
    return $result;
}

function template_table( $template_list, $academic_year, $unit_type, $show_actions ) : string
{
    global $wp;
    $result = '<table class="table table-striped table-bordered">';
    $result .= '<thead>';
    $result .= '<tr>';
    $result .= '<th>ID</th>';
    $result .= '<th>Name</th>';
    $result .= '<th>Unit</th>';
    $result .= '<th>Enabled</th>';
    if ( $show_actions) {
        $result .= '<th>Action</th>';
    }
    $result .= '</tr>';
    $result .= '</thead>';
    $result .= '<tbody>';
    foreach ( $template_list as $template ) {
        $result .= '<tr>';
        $result .= '<td>' . $template->RptTemplateID . '</td>';
        $result .= '<td>' . $template->TemplateName . '</td>';
        $result .= '<td>' . $template->UnitName . '</td>';
        $result .= '<td>' . $template->InUse . '</td>';
        if ( $show_actions) {
            $result .= '<td>';
            $update_value = ($template->InUse == 'Yes') ? 'No' : 'Yes';
            $update_action = ($template->InUse == 'Yes') ? 'Disable' : 'Enable';
            $result .= '<a href="' . esc_url(add_query_arg(array('rpt_page' => 'template',
                    'ay' => $academic_year, 'template_id' => $template->RptTemplateID,
                    'template_type' => $template->RptTemplateTypeID,
                    'in_use' => $update_value, 'unit_type' => $unit_type), home_url($wp->request)))
                . '">' . $update_action . '</a>';
            $result .= '</td>';
        }
        $result .= '</tr>';
    }
    $result .= '</tbody>';
    $result .= '</table>';
    return $result;
}