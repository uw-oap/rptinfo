<?php

class Rpt_Info_Sabbatical extends Rpt_Info_Case
{
    public $SummerQtr = 'No';
    public $FallQtr = 'No';
    public $WinterQtr = 'No';
    public $SpringQtr = 'No';
    public $SalarySupportPct = '100%';
    public string|float $RosterPct = 0.0000;
    public string $MonthlySalary = '0.0';
    public string $IsTenured = 'No';
    public $TenureAmount = '0';
    public $HireDate = null;
    public $TrackStartDate = null;
    public string $AppointmentStartDate = '';
    public string $AppointmentEndDate = '';
    public int $LastSabbaticalAcademicYear = 0;
    public string $ContingentOnExtension = 'No';
    public string $MultiYear = 'No';
    public string $EligibilityReport = '';
    public string $EligibilityNote = '';

    public function __construct( $case_row = NULL )
    {
//        echo '<pre>' . number_format($case_row->RosterPct * 100) . '</pre>'; exit;
        parent::__construct($case_row);
        $this->RptTemplateTypeID = '5';
        if ( $case_row ) {
            // populate fields
            $this->SummerQtr = $case_row->SummerQtr;
            $this->FallQtr = $case_row->FallQtr;
            $this->WinterQtr = $case_row->WinterQtr;
            $this->SpringQtr = $case_row->SpringQtr;
            $this->RosterPct = $case_row->RosterPct;
            $this->SalarySupportPct = $case_row->SalarySupportPct;
/*            if ( isset($case_row->RosterPct) ) {
                $roster_pct = $case_row->RosterPct * 100;
                $this->RosterPct = number_format($roster_pct);
            } */
//            $this->RosterPct = number_format($case_row->RosterPct * 100);
            $this->MonthlySalary = $case_row->MonthlySalary;
            $this->IsTenured ??= $case_row->IsTenured;
            $this->TenureAmount = $case_row->TenureAmount;
            $this->HireDate = $case_row->HireDate;
            $this->AppointmentStartDate = $case_row->AppointmentStartDate;
            if ( isset( $case_row->AppointmentEndDate) ) {
                $this->AppointmentEndDate = $case_row->AppointmentEndDate;
            }
            if ( isset( $case_row->ContingentOnExtension ) ) {
                $this->TrackStartDate = $case_row->TrackStartDate;
                $this->LastSabbaticalAcademicYear = $case_row->LastSabbaticalAcademicYear;
                $this->ContingentOnExtension = $case_row->ContingentOnExtension;
                $this->MultiYear = $case_row->MultiYear;
                $this->EligibilityReport = $case_row->EligibilityReport;
                $this->EligibilityNote = $case_row->EligibilityNote;
            }
        }
//        echo '<pre>' . print_r( $this, true ) . '</pre>'; exit;
    }

    public function update_from_post( $posted_values ) : void
    {
        parent::update_from_post( $posted_values );
        $qtr_count = 0;
        if (isset($posted_values['SummerQtr'])) {
            $this->SummerQtr = $posted_values['SummerQtr'];
            if ($posted_values['SummerQtr'] == 'Yes') {
                $qtr_count++;
            }
        }
        $this->FallQtr = $posted_values['FallQtr'];
        if ($posted_values['FallQtr'] == 'Yes') {
            $qtr_count++;
        }
        $this->WinterQtr = $posted_values['WinterQtr'];
        if ($posted_values['WinterQtr'] == 'Yes') {
            $qtr_count++;
        }
        $this->SpringQtr = $posted_values['SpringQtr'];
        if ($posted_values['SpringQtr'] == 'Yes') {
            $qtr_count++;
        }
        switch ($qtr_count) {
            case 1:
                $this->SalarySupportPct = '100%';
                break;
            case 2:
                $this->SalarySupportPct = '75%';
                break;
            case 3:
                $this->SalarySupportPct = '67%';
                break;
            default:
                $this->SalarySupportPct = NULL;
                break;
        }
        $this->RosterPct = $posted_values['RosterPct'];
        $this->MonthlySalary = $posted_values['MonthlySalary'];
        $this->TenureAmount = $posted_values['TenureAmount'];
        $this->MultiYear = $posted_values['MultiYear'];
        $this->EligibilityReport = $posted_values['EligibilityReport'];
        $this->EligibilityNote = $posted_values['EligibilityNote'];
        $this->LastSabbaticalAcademicYear = $posted_values['LastSabbaticalAcademicYear'];
        $this->ContingentOnExtension = $posted_values['ContingentOnExtension'];
        $this->AppointmentStartDate = $posted_values['AppointmentStartDate'];
        // also hire date, track start date
    }

    public function insert_sabbatical_array() : array
    {
        return array(
            'CaseID' => $this->CaseID,
            'SummerQtr' => $this->SummerQtr,
            'FallQtr' => $this->FallQtr,
            'WinterQtr' => $this->WinterQtr,
            'SpringQtr' => $this->SpringQtr,
            'SalarySupportPct' => $this->SalarySupportPct,
            'RosterPct' => $this->RosterPct,
            'MonthlySalary' => $this->MonthlySalary,
            'TenureAmount' => $this->TenureAmount,
            'HireDate' => $this->HireDate,
            'TrackStartDate' => $this->TrackStartDate,
            'AppointmentStartDate' => $this->AppointmentStartDate,
            'LastSabbaticalAcademicYear' => $this->LastSabbaticalAcademicYear,
            'ContingentOnExtension' => $this->ContingentOnExtension,
            'MultiYear' => $this->MultiYear,
            'EligibilityReport' => $this->EligibilityReport,
            'EligibilityNote' => $this->EligibilityNote
        );
    }

    private function quarter_list( $include_academic_year = TRUE ) : string
    {
        $quarter_list = array();
        if ($this->SummerQtr == 'Yes') {
            $quarter_list[] = 'Summer';
        }
        if ($this->FallQtr == 'Yes') {
            $quarter_list[] = 'Fall';
        }
        if ($this->WinterQtr == 'Yes') {
            $quarter_list[] = 'Winter';
        }
        if ($this->SpringQtr == 'Yes') {
            $quarter_list[] = 'Spring';
        }
        if ( $include_academic_year ) {
            return 'AY' . $this->AcademicYear . ' ' . implode(', ', $quarter_list);
        }
        else {
            return implode(', ', $quarter_list);
        }
    }

    private function quarter_count() : int
    {
        $result = 0;
        if ($this->SummerQtr == 'Yes') {
            $result++;
        }
        if ($this->FallQtr == 'Yes') {
            $result++;
        }
        if ($this->WinterQtr == 'Yes') {
            $result++;
        }
        if ($this->SpringQtr == 'Yes') {
            $result++;
        }
        return $result;
    }

    public function listing_table_row( $rpt_case_url ) : string
    {
        global $wp;
        $result = '<tr class="border-bottom border-right">';
        $result = '<tr>';
        $result .= '<td>';
        if ( $this->RptCaseID ) {
            $result .= '<a href="' . $rpt_case_url . '/' . $this->RptCaseID
                . '">' . $this->RptCaseID . '</a>';
        }
        else {
            $result .= 'N/A';
        }
        $result .= '</td>';
        $result .= '<td><strong>' . $this->display_name() . ' (' . $this->EmployeeID . ')</strong><br>';
        $result .= $this->CurrentRankName . ' in ' . $this->UnitName . ' ('
            . $this->AppointmentType . ')</td>';
        $result .= '<td>' . $this->quarter_list() . '</td>';
        $result .= '<td>' . $this->CaseStatus . '<br>' . $this->WorkflowStepName . ' (Step '
            . $this->WorkflowStepNumber . ')';
        $result .= '</td>';
        $result .= '<td>';
        $result .= '<a href="' . esc_url(add_query_arg(array('case_id' => $this->CaseID,
                'template_type' => $this->RptTemplateTypeID,
                'ay' => $this->AcademicYear,
                'rpt_page' => 'case'), home_url($wp->request)))
            . '" class="btn btn-outline-secondary">Details</a>';
        $result .= '</td>';
        $result .= '</tr>';
        return $result;
    }

    public function candidate_info_card( $show_instructions = FALSE ) : string
    {
        $result = '<div class="card">';
        $result .= '<div class="card-body">';
        $result .= '<h4 class="card-title">Candidate information</h4>';
        if ( $show_instructions) {
            $result .= '<p class="card-subtitle mb-2 text-muted">';
            $result .= "Please review the candidate's Workday information below. If any data is incorrect, make the "
                . 'change in Workday. Once updated, return to this page to initiate the case. <em>Do not</em> '
                . 'initiate a case with incorrect information.</p>';
        }
        $result .= '<dl class="rptinfo-list">';
        $result .= '<dt>Employee ID</dt>';
        $result .= '<dd>' . $this->EmployeeID . '</dd>';
        $result .= '<dt>Name</dt>';
        $result .= '<dd>' . $this->display_name() . '</dd>';
        $result .= '<dt>Appointment type</dt>';
        $result .= '<dd>' . $this->AppointmentType . '</dd>';
        $result .= '<dt>S/C/C</dt>';
        $result .= '<dd>' . $this->LevelOneUnitName . '</dd>';
        $result .= '<dt>Appointing unit</dt>';
        $result .= '<dd>' . $this->UnitName . '</dd>';
        $result .= '<dt>Current rank</dt>';
        $result .= '<dd>' . $this->CurrentRankName . '</dd>';
        $result .= '<dt>Track type</dt>';
        $result .= '<dd>' . $this->TrackTypeName . '</dd>';
        $result .= '<dt>Hire date</dt>';
        $result .= '<dd>' . rpt_format_date($this->HireDate);
        $result .= '<dt>Appointment dates</dt>';
        $result .= '<dd>' . rpt_format_date($this->AppointmentStartDate) . ' &mdash; ';
        if ( $this->AppointmentEndDate ) {
            $result .= rpt_format_date($this->AppointmentEndDate);
        }
        $result .= '</dd>';
        if ( $this->IsTenured == 'Yes' ) {
            $result .= '<dt>Tenure amount</dt>';
            $result .= '<dd>' . $this->TenureAmount . '%</dd>';
        }
        $result .= '<dt>Roster %</dt>';
        $result .= '<dd>' . ($this->RosterPct * 100) . '</dd>';
        $result .= '<dt>Monthly salary</dt>';
        $result .= '<dd>$' . $this->MonthlySalary . '</dd>';
        $result .= '<dt>Service period</dt>';
        $result .= '<dd>' . $this->ServicePeriod . '</dd>';
        $result .= '<dt>Other appointments</dt>';
        if (count($this->OtherAppointments)) {
            $result .= '<dd><ul>';
            foreach ($this->OtherAppointments as $appointment) {
                $result .= '<li>' . $appointment->RankName . ' in ' . $appointment->UnitName . ' ('
                    . $appointment->AppointmentType . ')</li>';
            }
            $result .= '</ul></dd>';
        }
        else {
            $result .= '<dd>None</dd>';
        }
        $result .= '<dt>Previous leaves</dt>';
        if (count($this->PreviousLeaves)) {
            $result .= '<dd><ul>';
            foreach ($this->PreviousLeaves as $leave) {
                $result .= '<li>' . $leave->LeaveTypeName . ': '
                    . rpt_format_date($leave->StartDate) . ' - '
                    . rpt_format_date($leave->EndDate) . '</li>';
            }
            $result .= '</ul></dd>';
        }
        else {
            $result .= '<dd>None</dd>';
        }
        $result .= '</dl>';
        $result .= '</div>'; // card body
        $result .= '</div>'; // card
        return $result;
    }

    public function sabbatical_info_card( $rpt_case_url, $is_admin = FALSE ) : string
    {
        global $wp;
        $result = '<div class="card">';
        $result .= '<div class="card-body">';
        $result .= '<h4 class="card-title">Sabbatical information</h4>';
        $result .= '<dl class="rptinfo-list">';
        $result .= '<dt>Academic Year</dt>';
        $result .= '<dd>' . $this->AcademicYear . '</dd>';
        $result .= '<dt>Quarters requested (' . $this->quarter_count() . ')</dt>';
        $result .= '<dd>' . $this->quarter_list(FALSE) . '</dd>';
        $result .= '<dt>Salary support pct</dt>';
        $result .= '<dd>' . $this->SalarySupportPct . '</dd>';
        $result .= '<dt>Eligibility report</dt>';
        $result .= '<dd>' . $this->EligibilityReport . '</dd>';
        $result .= '<dt>Eligibility note</dt>';
        $result .= '<dd>' . $this->EligibilityNote . '</dd>';
        $result .= '<dt>Contingent upon reappointment / promotion?</dt>';
        $result .= '<dd>' . $this->ContingentOnExtension . '</dd>';
        $result .= '<dt>Last sabbatical Academic Year</dt>';
        $result .= '<dd>' . $this->LastSabbaticalAcademicYear . '</dd>';
        $result .= '</dl>';
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
        $result .= '<dt>Interfolio Case ID</dt>';
        $result .= '<dd>' . $this->RptCaseID . '</dd>';
        $result .= '<dt>Workflow step</dt>';
        $result .= '<dd>' . $this->WorkflowStepName . ' (' . $this->WorkflowStepNumber. ')</dd>';
        $result .= '<dt>Cover sheet</dt>';
        $result .= '<dd>' . (($this->CoverSheetID) ? 'Present' : 'Not present') . '</dd>';
        $result .= '</dl>';
        $result .= '</div>'; // card body
        $result .= '</div>'; // card
        return $result;
    }

    public function eligibility_report_values() : array
    {
        return array(
            'Likely Eligible' => 'Likely Eligible',
            'Not Yet Eligible' => 'Not Yet Eligible',
            'Review-Adjustment' => 'Review Required - Leave or FTE Adjustment',
            'Review-Multiyear' => 'Review Required - Previous Multiyear Sabbatical'
        );
    }

    public function salary_support_values() : array
    {
        return array(
            '100%' => '100%',
            '75%' => '75%',
            '67%' => '67%'
        );
    }

    public function case_edit_allowed( $is_admin = FALSE ) : bool
    {
        if ( ( $this->CaseStatusID < '3' ) || ( $is_admin ) ) { // draft, submitted, in progress
            return true;
        }
        return false;
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
        return true;
    }

}