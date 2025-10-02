<?php

class Rpt_Info_Case
{
    public int $CaseID = 0;
    public int $RptCaseID = 0;
    public int $RptTemplateID = 0;
    public int $AcademicYear = 0;
    public string $AcademicYearDisplay = '';
    public int $CandidateID = 0;
    public int $CandidateKey = 0;
    public string $EmployeeID = '';
    public string $LegalName = '';
    public string $FirstName = '';
    public string $LastName = '';
    public string $PreferredName = '';
    public string $UWNetID = '';
    public string $CandidateEmail = '';
    public int $UWODSAppointmentTrackKey = 0;
    public string $AppointmentType = '';
    public int $UWODSUnitKey = 0;
    public int $InterfolioUnitID = 0;
    public string $UnitName = '';
    public int $ParentID = 0;
    public string $ParentUnitName = '';
    public int $LevelOneID = 0;
    public string $LevelOneUnitName = '';
    public int $CurrentRankKey = 0;
    public string $CurrentRankName = '';
    public string $RankCategory = '';
    public string $TrackTypeName = '';
    public string $IsPromotable = '';
    public string $ServicePeriod = '';
    public string $HasJoint = 'No';
    public string $HasSecondary = 'No';
    public string $AppointmentStartDate = '';
    public string $AppointmentEndDate = '';
    public int $InitiatorID = 0;
    public string $InitiatorName = '';
    public int $CaseDataSectionID = 0;
    public int $ConcurrenceLetterSecion = 0;
    public int $ConcurrenceLetterCount = 0;
    public int $CoverSheetID = 0;
    public string $CoverSheetStatus = '';
    public int $DataSheetID = 0;
    public string $DataSheetStatus = '';
    public string $WorkflowStepNumber = '';
    public string $WorkflowStepName = '';
    public int $SubcommitteeReviewStep = 0;
    public string $SubcommitteeMembers = '';
    public string $RptStatus = '';
    public int $CaseStatusID = 0;
    public string $CaseStatus = 'Draft';
    public string $APFInternal = 'No';
    public string $StatusActive = 'No';
    public string $TemplateName = '';
    public int $RptTemplateTypeID = 0;
    public array $OtherAppointments = [];
    public array $PreviousLeaves = [];
    public array $PreviousWaivers = [];

    public function __construct( $case_row = NULL )
    {
        if ( $case_row ) {
            $this->CaseID = $case_row->CaseID;
            $this->RptCaseID = $case_row->RptCaseID;
            $this->RptTemplateID = $case_row->RptTemplateID;
            $this->TemplateName = $case_row->TemplateName;
            $this->RptTemplateTypeID = $case_row->RptTemplateTypeID;
            if ( $case_row->CandidateID ) {
                $this->CandidateID = $case_row->CandidateID;
            }
            else {
                $this->CandidateID = 0;
            }
            $this->LegalName = $case_row->LegalName;
            $this->PreferredName = $case_row->PreferredName;
            $this->EmployeeID = $case_row->EmployeeID;
            if ( isset($case_row->InitiatorID) ) {
                $this->InitiatorID = $case_row->InitiatorID;
            }
            $this->InitiatorName = $case_row->InitiatorName;
            $this->CandidateKey = $case_row->CandidateKey;
            if ( isset($case_row->AcademicYear) ) {
                $this->AcademicYear = $case_row->AcademicYear;
            }
            if ( isset($case_row->WorkflowStepName) ) {
                $this->WorkflowStepNumber = $case_row->WorkflowStepNumber;
                $this->WorkflowStepName = $case_row->WorkflowStepName;
            }
            $this->UWODSAppointmentTrackKey = $case_row->UWODSAppointmentTrackKey;
            $this->AppointmentType = $case_row->AppointmentType;
            $this->UWODSUnitKey = $case_row->UWODSUnitKey;
            if ( $case_row->InterfolioUnitID ) {
                $this->InterfolioUnitID = $case_row->InterfolioUnitID;
                $this->ParentID = $case_row->ParentID;
                $this->ParentUnitName = $case_row->ParentUnitName;
                $this->LevelOneID = $case_row->LevelOneID;
                $this->LevelOneUnitName = $case_row->LevelOneUnitName;
            }
            $this->UnitName = $case_row->UnitName;
            $this->CurrentRankKey = $case_row->CurrentRankKey;
            $this->CurrentRankName = $case_row->CurrentRankName;
            if ( $this->AppointmentType == 'Joint' ) {
                $this->HasJoint = 'Yes';
            }
            elseif ( isset($case_row->HasJoint)) {
                $this->HasJoint = $case_row->HasJoint;
            }
            if ( isset($case_row->HasSecondary) ) {
                $this->HasSecondary = $case_row->HasSecondary;
            }
            if ( isset($case_row->CaseStatusID) ) {
                $this->CaseStatusID = $case_row->CaseStatusID;
            }
            if ( isset($case_row->CaseStatus) ) {
                $this->CaseStatus = $case_row->CaseStatus;
            }
            $this->TrackTypeName = $case_row->TrackTypeName;
            $this->RankCategory = $case_row->RankCategory;
            if ( isset( $case_row->ServicePeriod ) ) {
                if ( $case_row->ServicePeriod == '12' ) {
                    $this->ServicePeriod = '12';
                }
                else {
                    $this->ServicePeriod = '9';
                }
            }
//            $this->ServicePeriod ??= $case_row->ServicePeriod;
            if ( isset($case_row->AppointmentStartDate) ) {
                $this->AppointmentStartDate = $case_row->AppointmentStartDate;
            }
            if ( isset($case_row->AppointmentEndDate) ) {
                $this->AppointmentEndDate = $case_row->AppointmentEndDate;
            }
            if ( isset($case_row->RptStatus) ) {
                $this->RptStatus = $case_row->RptStatus;
            }
            if ( isset($case_row->CoverSheetID) ) {
                $this->CoverSheetID = $case_row->CoverSheetID;
            }
            if ( isset($case_row->CoverSheetStatus) ) {
                $this->CoverSheetStatus = $case_row->CoverSheetStatus;
            }
            if ( isset($case_row->DataSheetID) ) {
                $this->DataSheetID = $case_row->DataSheetID;
            }
            if ( isset($case_row->DataSheetStatus) ) {
                $this->DataSheetStatus = $case_row->DataSheetStatus;
            }
            if ( isset($case_row->SubcommitteeMembers) ) {
                $this->SubcommitteeMembers = $case_row->SubcommitteeMembers;
            }
//            echo '<pre>' . print_r( $this, true ) . '</pre>'; exit;
        }
    }

    public function set_calculated_values() : void
    {
        $this->HasJoint = 'No';
        $this->HasSecondary = 'No';
        if ( count($this->OtherAppointments) ) {
            foreach ($this->OtherAppointments as $otherAppointment) {
                if ( $otherAppointment->AppointmentType == 'Joint' ) {
                    $this->HasJoint = 'Yes';
                }
                if ( $otherAppointment->AppointmentType == 'Secondary' ) {
                    $this->HasSecondary = 'Yes';
                }
            }
        }
    }

    public function update_from_post( $posted_values ) : void
    {
        $rpt_id_changed = FALSE;
        $this->CaseID = intval($posted_values['CaseID']);
        if ( ! isset($posted_values['RptCaseID']) ) {
            $this->RptCaseID = 0;
        }
        else {
            if (intval($posted_values['RptCaseID']) != $this->RptCaseID) {
                $rpt_id_changed = TRUE;
            }
            $this->RptCaseID = intval($posted_values['RptCaseID']);
        }
        $this->RptTemplateID = intval($posted_values['RptTemplateID']);
        if ( $posted_values['CoverSheetID'] > '0' ) {
            if ( $rpt_id_changed ) {
                // zero it out so new one is created
                $this->CoverSheetID = 0;
                $this->CoverSheetStatus = 'None';
                $this->CaseDataSectionID = 0;
                $this->DataSheetID = 0;
                $this->DataSheetStatus = 'None';
            }
            else {
                // cover sheet already exists, keep so it can be deleted
                $this->CoverSheetID = $posted_values['CoverSheetID'];
            }
            // set as submitted to trigger upload
            $this->CoverSheetStatus = 'Submitted';
        }
        $this->CandidateID = intval($posted_values['CandidateID']);
        $this->CandidateKey = intval($posted_values['CandidateKey']);
        $this->InitiatorID = intval($posted_values['InitiatorID']);
        $this->UWODSAppointmentTrackKey = intval($posted_values['UWODSAppointmentTrackKey']);
        $this->UWODSUnitKey = intval($posted_values['UWODSUnitKey']);
        $this->CurrentRankKey = intval($posted_values['CurrentRankKey']);
        if ( array_key_exists('AppointmentType', $posted_values) ) {
            $this->AppointmentType = sanitize_text_field($posted_values['AppointmentType']);
        }
        $this->InterfolioUnitID = intval($posted_values['InterfolioUnitID']);
        if ( ! isset($posted_values['CaseStatusID']) ) {
            $this->CaseStatusID = 0;
        }
        else {
            $this->CaseStatusID = intval($posted_values['CaseStatusID']);
        }
        $this->AcademicYear = intval($posted_values['ay']);
        $this->HasJoint = sanitize_text_field($posted_values['HasJoint']);
        $this->HasSecondary = sanitize_text_field($posted_values['HasSecondary']);
    }

    public function insert_case_array() : array
    {
        return array(
            'RptCaseID' => $this->RptCaseID,
            'RptTemplateID' => $this->RptTemplateID,
            'AcademicYear' => $this->AcademicYear,
            'CandidateID' => $this->CandidateID,
            'InitiatorID' => $this->InitiatorID,
            'CandidateKey' => $this->CandidateKey,
            'UWODSAppointmentTrackKey' => $this->UWODSAppointmentTrackKey,
            'CurrentRankKey' => $this->CurrentRankKey,
            'CaseStatusID' => $this->CaseStatusID,
            'HasJoint' => $this->HasJoint,
            'HasSecondary' => $this->HasSecondary
        );
    }

    /**
     * update_case_array
     *      array to be used in updating an existing case
     *      these are the only fields that should ever change
     *          from user input after creation
     *
     * @return array
     */
    public function update_case_array() : array
    {
        return array(
            'RptCaseID' => $this->RptCaseID,
            'CaseStatusID' => $this->CaseStatusID,
            'CaseDataSectionID' => $this->CaseDataSectionID,
            'CoverSheetID' => $this->CoverSheetID,
            'CoverSheetStatus' => $this->CoverSheetStatus
        );
    }

    public function update_case_data_sheet() : array
    {
        return array(
            'DataSheetStatus' => $this->DataSheetStatus,
        );
    }

    public function display_name() : string
    {
        $result = $this->LegalName;
        if ( ( $this->PreferredName ) && ( $this->PreferredName != $this->LegalName ) ) {
            $result .= ' / ' . $this->PreferredName;
        }
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
        $result .= '</dl>';
        $result .= '</div>'; // card body
        $result .= '</div>'; // card
        return $result;
    }

    public function init_case_help_text( $help_url = '' ) : string
    {
        $result = '<p><strong>Initiating new case</strong></p>';
        $result .= '<p>Please complete this page and <strong>Submit</strong> the information to initiate '
            . 'the creation of an RPT case for a candidate. The information from this page will '
            . 'be added to the case for reference.';
        if ( $help_url ) {
            $result .= ' For an overview of the RPT '
                . 'case review process, see <a href="' . $help_url
                . '" alt="RPT case review instructions">this guide</a>';
        }
        $result .= '</p>';
        return $result;
    }

    public function edit_case_help_text( $help_url = '' ) : string
    {
        return '';
    }

}