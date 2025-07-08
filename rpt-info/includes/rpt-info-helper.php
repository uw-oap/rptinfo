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
    $result = '<div class="form-group rpt-select-group" id="' . $field_name . '-group"';
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
    $result = '<div class="form-group rpt-select-group" id="' . $field_name . '-group"';
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
    $result = '<div class="form-group rpt-select-group" id="' . $field_name . '-group"';
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
    $result = '<div class="form-group rpt-select-group" id="' . $field_name . '-group"';
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

function rpt_form_quarter_select($summer_value, $fall_value, $winter_value, $spring_value, $show_summer = FALSE)
{
    $result = '<div class="form-group rpt-select-group" id="rpt-quarter-select-group">';
    $result .= '<table>';
    $result .= '<thead>';
    $result .= '<tr>';
    $result .= '<th>Quarter</th>';
    $result .= '<th>Requested</th>';
    $result .= '</tr>';
    $result .= '</thead>';
    $result .= '<tbody>';
    if ( $show_summer ) {
        $result .= '<tr>';
        $result .= '<td>Summer</td>';
        $result .= '<td>';
        $result .= '<input type="radio" id="SummerQtrYes" name="SummerQtr" class="QtrYes" value="Yes"';
        if ( $summer_value == 'Yes' ) {
            $result .= ' checked="checked"';
        }
        $result .= '>&nbsp;';
        $result .= '<label for="SummerQtrYes">Yes</label>&nbsp;';
        $result .= '<input type="radio" id="SummerQtrNo" name="SummerQtr" value="No"';
        if ( $summer_value == 'No' ) {
            $result .= ' checked="checked"';
        }
        $result .= '>&nbsp;';
        $result .= '<label for="SummerQtrNo">No</label>';
        $result .= '</td>';
        $result .= '</tr>';
    }
    $result .= '<tr>';
    $result .= '<td>Fall</td>';
    $result .= '<td>';
    $result .= '<input type="radio" id="FallQtrYes" name="FallQtr" class="QtrYes" value="Yes"';
    if ( $fall_value == 'Yes' ) {
        $result .= ' checked="checked"';
    }
    $result .= '>&nbsp;';
    $result .= '<label for="FallQtrYes">Yes</label>&nbsp;';
    $result .= '<input type="radio" id="FallQtrNo" name="FallQtr" value="No"';
    if ( $fall_value == 'No' ) {
        $result .= ' checked="checked"';
    }
    $result .= '>&nbsp;';
    $result .= '<label for="FallQtrNo">No</label>';
    $result .= '</td>';
    $result .= '</tr>';
    $result .= '<tr>';
    $result .= '<td>Winter</td>';
    $result .= '<td>';
    $result .= '<input type="radio" id="WinterQtrYes" name="WinterQtr" class="QtrYes" value="Yes"';
    if ( $winter_value == 'Yes' ) {
        $result .= ' checked="checked"';
    }
    $result .= '>&nbsp;';
    $result .= '<label for="WinterQtrYes">Yes</label>&nbsp;';
    $result .= '<input type="radio" id="WinterQtrNo" name="WinterQtr" value="No"';
    if ( $winter_value == 'No' ) {
        $result .= ' checked="checked"';
    }
    $result .= '>&nbsp;';
    $result .= '<label for="WinterQtrNo">No</label>';
    $result .= '</td>';
    $result .= '</tr>';
    $result .= '<tr>';
    $result .= '<td>Spring</td>';
    $result .= '<td>';
    $result .= '<input type="radio" id="SpringQtrYes" name="SpringQtr" class="QtrYes" value="Yes"';
    if ( $spring_value == 'Yes' ) {
        $result .= ' checked="checked"';
    }
    $result .= '>&nbsp;';
    $result .= '<label for="SpringQtrYes">Yes</label>&nbsp;';
    $result .= '<input type="radio" id="SpringQtrNo" name="SpringQtr" value="No"';
    if ( $spring_value == 'No' ) {
        $result .= ' checked="checked"';
    }
    $result .= '>&nbsp;';
    $result .= '<label for="SpringQtrNo">No</label>';
    $result .= '</td>';
    $result .= '</tr>';
    $result .= '</tbody>';
    $result .= '</table>';
    $result .= '</div>'; // <!-- form group -->
    return $result;
}

function rpt_yes_no_radio($field_name, $field_value, $label_text, $hide_group, $init_required ) : string
{
    $result = '<div class="form-group row rpt-select-group" id="' . $field_name . '-group"';
    if ( $hide_group ) {
        $result .= ' style="display:none;"';
    }
    $result .= '>';
    $result .= '<div class="col-md-6">' . $label_text . '</div>';
    $result .= '<div class="col-md-6">';
    $result .= '<input type="radio" id="' . $field_name . 'Yes" name="' . $field_name
        . '" value="Yes"';
    if ( $field_value == 'Yes' ) {
        $result .= ' checked="checked"';
    }
    if ( $init_required ) {
        $result .= ' required="required"';
    }
    $result .= '>&nbsp;';
    $result .= '<label for="' . $field_name . 'Yes">Yes</label>&nbsp;';
    $result .= '<input type="radio" id="' . $field_name . 'No" name="' . $field_name
        . '" value="No"';
    if ( $field_value == 'No' ) {
        $result .= ' checked="checked"';
    }
    if ( $init_required ) {
        $result .= ' required="required"';
    }
    $result .= '>&nbsp;';
    $result .= '<label for="' . $field_name . 'No">No</label>';
    $result .= '</div>'; // col
    $result .= '</div>'; // <!-- form group -->
    return $result;
}

function rpt_form_textarea($field_name, $field_value, $label_text, $width, $height, $hide_group = FALSE,
                       $init_disabled = FALSE, $help_text = array(), $maxlength = 0)
{
    $result = '  <div class="form-group row rpt-select-group" id="' . $field_name . '-group"';
    if ( $hide_group ) {
        $result .= ' style="display:none;"';
    }
    $result .= '>';
    $result .= '<div class="col-md-12"><label class="control-label" for="' . $field_name . '">' . $label_text . '</label>';
    $result .= '      <textarea type="text" name="' . $field_name . '" id="' . $field_name . '" cols="'
        . $width . '" rows="' . $height . '"';
    if ( $init_disabled ) {
        $result .= ' readonly';
    }
    if ( $maxlength ) {
        $result .= ' maxlength="' . $maxlength . '"';
    }
    $result .= '>'
        . stripslashes($field_value) . '</textarea>';
    if ( array_key_exists($field_name, $help_text) ) {
        $result .= '<br><em class="help-block">' . $help_text[$field_name] . '</em>';
    }
    $result .= '  </div>'; // <!-- col -->
    $result .= '</div>'; // <!-- row -->
    return $result;
}


function rpt_report_table($header = [], $data = [], $link_col = '', $link_val = '',
                          $detail_report = '', $template_type = '0', $ay = '' ) : string
{
    global $wp;
    $result = '<table class="table table-striped table-bordered">';
    $result .= '<thead>';
    $result .= '<tr>';
    foreach ( $header as $hkey => $hvalue ) {
        $result .= '<th>' . $hvalue . '</th>';
    }
    $result .= '</tr>';
    $result .= '</thead>';
    $result .= '<tbody>';
    foreach ( $data as $key => $value ) {
        $result .= '<tr>';
        foreach ( $header as $hkey => $hvalue ) {
            if ( $value[$hkey] == $key ) {
                $result .= '<td>';
                if ($hkey == $link_col) {
                    $result .= '<a href="' . esc_url(add_query_arg(array('rpt_page' => 'report',
                            'template_type' => $template_type,
                            'ay' => $ay,
                            'report_type' => $detail_report,
                            'unit_id' => $value[$link_val]
                            ),
                            home_url($wp->request)))
                        . '">' . $key . '</a>';
                } else {
                    $result .= $key;
                }
                $result .= '</td>';
            }
            else {
              $result .= '<td>' . $value[$hkey] . '</td>';
//                $result .= '<td>' . print_r($value, true) . '</td>';
            }
        }
        $result .= '</tr>';
    }
    $result .= '</tbody>';
    $result .= '</table>';
    return $result;
}

/**
 * rpt_format_date
 *      format arbitrary date using m/d/y for display
 *
 * @param $the_date
 * @return string
 */
function rpt_format_date($the_date) : string
{
    $d = DateTime::createFromFormat('Y-m-d', $the_date);
    if ($d === false) {
        return '';
    }
    else {
        return wp_date('m/d/Y', $d->getTimestamp());
    }
}