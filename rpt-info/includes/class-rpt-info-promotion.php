<?php

class Rpt_Info_Promotion extends Rpt_Info_Case
{
    public int $TargetRankKey = 0;
    public string $TargetRankName = '';
    public string $TargetTrackTypeName = '';
    public int $TargetRankDefaultTerm = 0;
    public string $TargetRankTenured = 'No';
    public string $ActionType = '';
    public int $PromotionCategoryID = 0;
    public string $PromotionCategoryName = '';
    public string $PromotionShowOutcomes = 'No';
    public string $PromotionOutcomeName = '';

    // fields for datasheet
    public string $Postponed = 'No';
    public int $TenureAward = 0;
    public int $NewTermLength = 0;
    public int $Vote1Eligible = 0;
    public int $Vote1Affirmative = 0;
    public int $Vote1Negative = 0;
    public int $Vote1Absent = 0;
    public int $Vote1Abstaining = 0;
    public int $Vote2Eligible = 0;
    public int $Vote2Affirmative = 0;
    public int $Vote2Negative = 0;
    public int $Vote2Absent = 0;
    public int $Vote2Abstaining = 0;
    public string $Leaves = ''; // string representation to submit
    public string $Waivers = '';
    public string $DataSheetValidation = '';

    public function __construct( $case_row = NULL )
    {
        parent::__construct( $case_row );
        $this->RptTemplateTypeID = '2';
        if ( $case_row ) {
//            echo '<pre>' . print_r( $case_row, true ) . '</pre>'; exit;
            $this->TargetRankKey = $case_row->TargetRankKey;
            $this->TargetRankName = $case_row->TargetRankName;
            if ( isset($case_row->TargetTrackTypeName) ) {
                $this->TargetTrackTypeName = $case_row->TargetTrackTypeName;
                $this->TargetRankDefaultTerm = $case_row->TargetRankDefaultTerm;
                $this->TargetRankTenured = $case_row->TargetRankTenured;
            }
            $this->PromotionCategoryID = $case_row->PromotionCategoryID;
            $this->PromotionCategoryName = $case_row->PromotionCategoryName;
            $this->EffectiveDate = $case_row->EffectiveDate;
//            echo '<pre>' . print_r( $this, true ) . '</pre>'; exit;
            if ( isset($case_row->Vote1Eligible) ) {
                $this->TenureAward = $case_row->TenureAward;
                $this->NewTermLength = $case_row->NewTermLength;
                $this->EffectiveDate = $case_row->EffectiveDate;
                $this->Vote1Eligible = $case_row->Vote1Eligible;
                $this->Vote1Affirmative = $case_row->Vote1Affirmative;
                $this->Vote1Negative = $case_row->Vote1Negative;
                $this->Vote1Absent = $case_row->Vote1Absent;
                $this->Vote1Abstaining = $case_row->Vote1Abstaining;
                $this->Vote2Eligible = $case_row->Vote2Eligible;
                $this->Vote2Affirmative = $case_row->Vote2Affirmative;
                $this->Vote2Negative = $case_row->Vote2Negative;
                $this->Vote2Absent = $case_row->Vote2Absent;
                $this->Vote2Abstaining = $case_row->Vote2Abstaining;
                $this->Postponed = $case_row->Postponed;
            }
            if ( isset($case_row->Leaves) ) {
                $this->Leaves = $case_row->Leaves;
            }
            if ( isset($case_row->Waivers) ) {
                $this->Waivers = $case_row->Waivers;
            }
            if ( isset($case_row->PromotionShowOutcomes) ) {
                $this->PromotionShowOutcomes = $case_row->PromotionShowOutcomes;
            }
            if ( isset($case_row->PromotionOutcomeName) ) {
                $this->PromotionOutcomeName = $case_row->PromotionOutcomeName;
            }
//            echo '<pre>' . print_r( $this, true ) . '</pre>'; exit;
        }
    }

    public function update_from_post( $posted_values ) : void
    {
        parent::update_from_post( $posted_values );
        $this->TargetRankKey = intval($posted_values['TargetRankKey']);
        $this->EffectiveDate = sanitize_text_field($posted_values['EffectiveDate']);
        $this->PromotionCategoryID = intval($posted_values['PromotionCategoryID']);
    }

    public function update_from_data_sheet_post( $posted_values ) : void
    {
        if ( isset($posted_values['TenureAward']) ) {
            $this->TenureAward = intval($posted_values['TenureAward']);
        }
        if ( isset($posted_values['NewTermLength']) ) {
            $this->NewTermLength = intval($posted_values['NewTermLength']);
        }
        $this->Postponed = sanitize_text_field($posted_values['Postponed']);
        $this->Vote1Eligible = intval($posted_values['Vote1Eligible']);
        $this->Vote1Affirmative = intval($posted_values['Vote1Affirmative']);
        $this->Vote1Negative = intval($posted_values['Vote1Negative']);
        $this->Vote1Absent = intval($posted_values['Vote1Absent']);
        $this->Vote1Abstaining = intval($posted_values['Vote1Abstaining']);
        $this->Vote2Eligible = intval($posted_values['Vote2Eligible']);
        $this->Vote2Affirmative = intval($posted_values['Vote2Affirmative']);
        $this->Vote2Negative = intval($posted_values['Vote2Negative']);
        $this->Vote2Absent = intval($posted_values['Vote2Absent']);
        $this->Vote2Abstaining = intval($posted_values['Vote2Abstaining']);
        $this->Leaves = sanitize_text_field($posted_values['Leaves']);
        $this->Waivers = sanitize_text_field($posted_values['Waivers']);
    }

    public function propose_effective_date( Rpt_Info_Cycle $cycle_obj )
    {
        if ( $this->EffectiveDate ) {
            return $this->EffectiveDate;
        }
        else {
            return $cycle_obj->EffectiveDate[$this->ServicePeriod];
        }
    }

    public function insert_promotion_array() : array
    {
        return array(
            'CaseID' => $this->CaseID,
            'PromotionCategoryID' => $this->PromotionCategoryID,
            'TargetRankKey' => $this->TargetRankKey,
            'NewTermLength' => $this->NewTermLength,
            'EffectiveDate' => $this->EffectiveDate,
            'AppointmentType' => $this->AppointmentType,
            'Postponed' => $this->Postponed,
            'TenureAward' => $this->TenureAward,
            'Vote1Eligible' => $this->Vote1Eligible,
            'Vote1Affirmative' => $this->Vote1Affirmative,
            'Vote1Negative' => $this->Vote1Negative,
            'Vote1Absent' => $this->Vote1Absent,
            'Vote1Abstaining' => $this->Vote1Abstaining,
            'Vote2Eligible' => $this->Vote2Eligible,
            'Vote2Affirmative' => $this->Vote2Affirmative,
            'Vote2Negative' => $this->Vote2Negative,
            'Vote2Absent' => $this->Vote2Absent,
            'Vote2Abstaining' => $this->Vote2Abstaining,
            'Leaves' => $this->Leaves,
            'Waivers' => $this->Waivers,
            'OtherAppointments' => ''
        );
    }

    /**
     * update_promotion_array
     *      array to be used to update a promotion record
     *      these are the only fields that should change due to user
     *          input after creation
     *
     * @return array
     */
    public function update_promotion_array() : array
    {
        return array(
            'PromotionCategoryID' => $this->PromotionCategoryID,
            'TargetRankKey' => $this->TargetRankKey,
            'EffectiveDate' => $this->EffectiveDate,
        );
    }

    public function update_data_sheet_array() : array
    {
        return array(
            'TenureAward' => $this->TenureAward,
            'NewTermLength' => $this->NewTermLength,
            'Postponed' => $this->Postponed,
            'Vote1Eligible' => $this->Vote1Eligible,
            'Vote1Affirmative' => $this->Vote1Affirmative,
            'Vote1Negative' => $this->Vote1Negative,
            'Vote1Absent' => $this->Vote1Absent,
            'Vote1Abstaining' => $this->Vote1Abstaining,
            'Vote2Eligible' => $this->Vote2Eligible,
            'Vote2Affirmative' => $this->Vote2Affirmative,
            'Vote2Negative' => $this->Vote2Negative,
            'Vote2Absent' => $this->Vote2Absent,
            'Vote2Abstaining' => $this->Vote2Abstaining,
            'Leaves' => $this->Leaves,
            'Waivers' => $this->Waivers
        );
    }

    public function listing_table_row( $rpt_case_url, $outcome_col = FALSE ) : string
    {
        global $wp;
        $result = '<tr class="border-bottom border-right">';
        $result .= '<td>';
        if ( $this->RptCaseID > '0' ) {
            $result .= '<a href="' . $rpt_case_url . '/' . $this->RptCaseID . '">'
                . $this->RptCaseID . '</a>';
        }
        else {
            $result .= 'N/A';
        }
        $result .= '</td>';
        $result .= '<td>' . $this->AcademicYear . '</td>';
        $result .= '<td><strong>' . $this->display_name() . ' (' . $this->EmployeeID . ')</strong><br>';
        $result .= $this->CurrentRankName . ' in ' . $this->UnitName . ' ('
            . $this->AppointmentType . ')</td>';
        $result .= '<td>' . $this->PromotionCategoryName . '</td>';
        $result .= '<td>' . $this->CaseStatus . '<br>'  . $this->RptStatus . '</td>';
        $result .= '<td>' . $this->WorkflowStepName . '<br>(Step '
            . $this->WorkflowStepNumber . ')';
        $result .= '</td>';
        if ( $outcome_col) {
            $result .= '<td>';
            if ( $this->PromotionShowOutcomes == 'Yes' ) {
                $result .= $this->PromotionOutcomeName;
            }
            else {
                $result .= '&nbsp;';
            }
            $result .= '</td>';
        }
        $result .= '<td>';
        $result .= '<a href="' . esc_url(add_query_arg(array('case_id' => $this->CaseID,
                    'template_type' => $this->RptTemplateTypeID,
                    'ay' => $this->AcademicYear,
                    'rpt_page' => 'case'), home_url($wp->request)))
                . '" class="btn btn-outline-secondary">Details</a>';
/*        if ( $this->RptCaseID > '0' ) {
            $result .= '<a href="' . esc_url(add_query_arg(array('case_id' => $this->CaseID,
                    'template_type' => $this->RptTemplateTypeID,
                    'ay' => $this->AcademicYear,
                    'rpt_page' => 'datasheet'), home_url($wp->request)))
                . '" class="btn btn-outline-secondary">Data sheet</a>';
        } */
        $result .= '</td>';
        $result .= '</tr>';
        return $result;
    }

    public function promotion_info_card( $rpt_case_url, $is_admin = FALSE ) : string
    {
        global $wp;
        $result = '<div class="card">';
        $result .= '<div class="card-body">';
        $result .= '<h4 class="card-title">Promotion information</h4>';
        $result .= '<dl class="rptinfo-list">';
        $result .= '<dt>Proposed rank</dt>';
        $result .= '<dd>' . $this->TargetRankName . '</dd>';
        $result .= '<dt>Effective date</dt>';
        $result .= '<dd>' . rpt_format_date($this->EffectiveDate) . '</dd>';
        $result .= '<dt>Promotion type</dt>';
        $result .= '<dd>' . $this->PromotionCategoryName . '</dd>';
        $result .= '<dt>RPT template</dt>';
        $result .= '<dd>' . $this->TemplateName . '</dd>';
        $result .= '<dt>Current status</dt>';
        $result .= '<dd>' . $this->CaseStatus . '</dd>';
        $result .= '</dl>';
        $result .= '<p>';
        if ( $this->RptCaseID ) {
            $result .= '<a href="' . $rpt_case_url . '/' . $this->RptCaseID
                . '" class="btn btn-outline-secondary">Go to case in RPT</a>';
        }
        if ( $this->case_edit_allowed($is_admin) ) {
            $result .= '<a href="' . esc_url(add_query_arg(array('case_id' => $this->CaseID,
                    'template_type' => $this->RptTemplateTypeID,
                    'ay' => $this->AcademicYear,
                    'rpt_page' => 'edit'), home_url($wp->request)))
                . '" class="btn btn-outline-secondary">Edit</a>';
        }
        $result .= '</p>';
        $result .= '</div>'; // card body
        $result .= '</div>'; // card
        return $result;
    }

    public function data_sheet_card() : string
    {
        global $wp;
        $result = '<div class="card">';
        $result .= '<div class="card-body">';
        $result .= '<h4 class="card-title">Data sheet</h4>';
        $result .= '<dl class="rptinfo-list">';
        $result .= '<dt>Proposed rank</dt>';
        $result .= '<dd>' . $this->TargetRankName . '</dd>';
        $result .= '<dt>Proposed track</dt>';
        $result .= '<dd>' . $this->TargetTrackTypeName . '</dd>';
        $result .= '<dt>Start date</dt>';
        $result .= '<dd>' . rpt_format_date($this->EffectiveDate) . '</dd>';
        $result .= '<dt>Tenure award</dt>';
        if ( $this->TargetRankTenured == 'Yes' ) {
            $result .= '<dd>' . $this->TenureAward . '%</dd>';
        }
        else {
            $result .= '<dd>N/A</dd>';
        }
        $result .= '<dt>New term length</dt>';
        if ( $this->TargetRankDefaultTerm > '0' ) {
            $result .= '<dd>' . $this->NewTermLength . '</dd>';
        }
        else {
            $result .= '<dd>N/A</dd>';
        }
        $result .= '<dt>Previously postponed?</dt>';
        $result .= '<dd>' . $this->Postponed . '</dd>';
        $result .= '<dt>Vote #1</dt>';
        $result .= '<dd>Eligible: ' . $this->Vote1Eligible . '<br>Affirmative: '
            . $this->Vote1Affirmative . '<br>Negative: '
            . $this->Vote1Negative . '<br>Abstaining: '
            . $this->Vote1Abstaining . '<br>Absent: '
            . $this->Vote1Absent. '</dd>';
        $result .= '<dt>Vote #2</dt>';
        $result .= '<dd>Eligible: ' . $this->Vote2Eligible . '<br>Affirmative: '
            . $this->Vote2Affirmative . '<br>Negative: '
            . $this->Vote2Negative . '<br>Abstaining: '
            . $this->Vote2Abstaining . '<br>Absent: '
            . $this->Vote2Absent. '</dd>';
        $result .= '<dt>Subcommittee members</dt>';
        $result .= '<dd>' . $this->SubcommitteeMembers . '</dd>';
        $result .= '<dt>Previous leaves</dt>';
        $result .= '<dd>' . $this->Leaves . '</dd>';
        $result .= '<dt>Clock waivers</dt>';
        $result .= '<dd>' . $this->Waivers . '</dd>';
        $result .= '<dt>Status</dt>';
        $result .= '<dd>' . $this->DataSheetStatus . '</dd>';
        $result .= '</dl>';
        if ( $this->data_sheet_edit_allowed() ) {
            if ( $this->data_sheet_ok() ) {
                $button_text = 'Edit / Submit';
            }
            else {
                $button_text = 'Edit';
            }
            $result .= '<a href="' . esc_url(add_query_arg(array('case_id' => $this->CaseID,
                    'template_type' => $this->RptTemplateTypeID,
                    'ay' => $this->AcademicYear,
                    'rpt_page' => 'datasheet'), home_url($wp->request)))
                . '" class="btn btn-outline-secondary">' . $button_text . '</a>';
            if ( $this->DataSheetValidation) {
                $result .= '<div class="form-check">';
                $result .= '<p><strong>Issues preventing submission:</strong></p>';
                $result .= $this->DataSheetValidation;
                $result .= '</div>';
            }
        }
        $result .= '</div>'; // card body
        $result .= '</div>'; // card
        return $result;
    }

    public function rpt_info_card() : string
    {
        $result = '<div class="card">';
        $result .= '<div class="card-body">';
        $result .= '<h4 class="card-title">RPT details</h4>';
        $result .= '<dl class="rptinfo-list">';
        $result .= '<dt>Interfolio case ID</dt>';
        $result .= '<dd>' . $this->RptCaseID . '</dd>';
        $result .= '<dt>Status in RPT</dt>';
        $result .= '<dd>';
        $result .= ($this->RptStatus) ? $this->RptStatus : '(Not set)';
        $result .= '</dd>';
        $result .= '<dt>Workflow step</dt>';
        $result .= '<dd>' . $this->WorkflowStepName . ' (' . $this->WorkflowStepNumber. ')</dd>';
        $result .= '<dt>Cover sheet</dt>';
        $result .= '<dd>' . $this->CoverSheetStatus . ' (' . $this->CoverSheetID . ')</dd>';
        $result .= '<dt>Data sheet</dt>';
        $result .= '<dd>' . $this->DataSheetStatus . ' (' . $this->DataSheetID . ')</dd>';
        $result .= '<dt>Concurrence letters</dt>';
        $result .= '<dd>' . $this->ConcurrenceLetterCount . '</dd>';
        $result .= '</dl>';
        $result .= '</div>'; // card body
        $result .= '</div>'; // card
        return $result;
    }

    public function propose_new_term() : string
    {
        if ( $this->NewTermLength ) {
            return $this->NewTermLength;
        }
        else {
            return $this->TargetRankDefaultTerm;
        }
    }

    public function case_edit_allowed( $is_admin = FALSE ) : bool
    {
        if ( ( $this->CaseStatusID < '3' ) || ( $is_admin ) ) { // draft, submitted, in progress
            return true;
        }
        return $is_admin;
    }

    public function data_sheet_edit_allowed( $is_admin = FALSE ) : bool
    {
        // what conditions should be here?
        if ( $this->RptCaseID == 0 ) {
            return false;
        }
        return true;
    }

    /**
     * ok_to_submit
     *      check to see if it's ok to submit
     *
     * @return bool
     */
    public function ok_to_submit() : bool
    {
        // not already in RPT
        if ( $this->RptCaseID > 0 ) {
            return false;
        }
        if ( ( $this->CaseStatusID !== 0 ) && ( $this->CaseStatusID !== 1 ) ){
            return FALSE;
        }
        if ( ! $this->TargetRankKey ) {
            return FALSE;
        }
        if ( ! $this->EffectiveDate ) {
            return FALSE;
        }
        if ( ! $this->RptTemplateID ) {
            return FALSE;
        }
        return TRUE;
    }

    public function data_sheet_ok() : bool
    {
        $errors = [];
        // not if data sheet already submitted
        if ( $this->DataSheetID > 0 ) {
//            $errors[] = 'Data sheet already submitted';
        }
        // not if main vote counts not present
        if ( $this->Vote1Eligible == '0' ) {
            $errors[] = 'No counts for Vote #1';
        }
        else {
            // not if main vote counts don't add up
            if ( ( $this->Vote1Affirmative
                    + $this->Vote1Negative
                    + $this->Vote1Absent
                    + $this->Vote1Abstaining) != $this->Vote1Eligible ) {
                $errors[] = 'Vote #1 counts do not add up';
            }
        }
        // if 2ndary vote present, must add up
        if ( $this->Vote2Eligible > '0' ) {
            if ( ( $this->Vote2Affirmative
                    + $this->Vote2Negative
                    + $this->Vote2Absent
                    + $this->Vote2Abstaining) != $this->Vote2Eligible ) {
                $errors[] = 'Vote #2 counts do not add up';
            }
        }
        // subcommittee members must be set
        if ( $this->SubcommitteeMembers == '' ) {
//            $errors[] = 'Missing Subcommittee Members (must be set in Interfolio)';
        }
        // missing tenure on tenured rank
        if ( ( $this->TargetRankTenured == 'Yes' && $this->TenureAward == 0 ) &&
            !($this->AppointmentType == 'Joint')) {
            $errors[] = 'Missing tenure amount on tenured rank';
        }
        // missing term length on termed rank
        if ( ( $this->TargetDefaultTerm ) && ( ! $this->NewTermLength ) ) {
            $errors[] = 'Missing term length on termed rank';
        }
        if ( count( $errors ) ) {
            $this->DataSheetValidation = implode( '<br />', $errors );
            return FALSE;
        }
        return TRUE;
    }

}