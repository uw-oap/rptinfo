<?php

class Rpt_Info_Case
{
    public $CaseID = 0;
    public $RptCaseID = 0;
    public $RptTemplateID = 0;
    public $TemplateName = 0;
    public $RptTemplateTypeID = 0;
    public $RptTemplateTypeName = '';
    public $CandidateID = 0;
    public $LegalName = '';
    public $EmployeeID = '';
    public $InitiatorID = 0;
    public $InitiatorName = '';
    public $CandidateKey = 0;
    public $UWODSAppointmentTrackKey = 0;
    public $AppointmentType = '';
    public $UWODSUnitKey = 0;
    public $UnitName = '';
    public $InterfolioUnitID = 0;
    public $ParentID = 0;
    public $ParentUnitName = '';
    public $LevelOneID = 0;
    public $LevelOneName = '';
    public $CurrentRankKey = 0;
    public $CurrentRankName = '';
    public $DueDate = NULL;
    public $EffectiveDate = NULL;
    public $HasJoint = 'No';
    public $HasSecondary = 'No';
    public $CaseStatus = 'Draft';
    public $ServicePeriod = 0;
    public $StartDate = '';
    public $EndDate = '';
    public $TrackTypeName = '';
    public $RankCategory = '';
    public $OtherAppointments = [];
    public $CoverSheetID = '0';
    public $AcademicYear = '';
    public $WorkflowStepNumber = '';
    public $WorkflowStepName = '';

    public function __construct( $case_row = NULL )
    {
        if ( $case_row ) {
            $this->CaseID = $case_row->CaseID;
            $this->InterfolioCaseID = $case_row->InterfolioCaseID;
            $this->RptTemplateTypeID = $case_row->RptTemplateTypeID;
            if ( $case_row->CandidateID ) {
                $this->CandidateID = $case_row->CandidateID;
            }
            else {
                $this->CandidateID = 0;
            }
            $this->LegalName = $case_row->LegalName;
            $this->EmployeeID = $case_row->EmployeeID;
            $this->InitiatorID = $case_row->InitiatorID;
            $this->InitiatorName = $case_row->InitiatorName;
            $this->CandidateKey = $case_row->CandidateKey;
            $this->AcademicYear = $case_row->AcademicYear;
            $this->WorkflowStepNumber = $case_row->WorkflowStepNumber;
            $this->WorkflowStepName = $case_row->WorkflowStepName;
            $this->UWODSAppointmentTrackKey = $case_row->UWODSAppointmentTrackKey;
            $this->AppointmentType = $case_row->AppointmentType;
            $this->UWODSUnitKey = $case_row->UWODSUnitKey;
            $this->InterfolioUnitID = $case_row->InterfolioUnitID;
            $this->ParentID = $case_row->ParentID;
            $this->ParentUnitName = $case_row->ParentUnitName;
            $this->LevelOneID = $case_row->LevelOneID;
            $this->LevelOneName = $case_row->LevelOneName;
            $this->UnitName = $case_row->UnitName;
            $this->CurrentRankKey = $case_row->CurrentRankKey;
            $this->CurrentRankName = $case_row->CurrentRankName;
//            $this->DueDate = $case_row->DueDate;
            $this->EffectiveDate = $case_row->EffectiveDate;
            if ( $this->AppointmentType == 'Joint' ) {
                $this->HasJoint = 'Yes';
            }
            else {
                $this->HasJoint = $case_row->HasJoint;
            }
            $this->HasSecondary = $case_row->HasSecondary;
            $this->CaseStatus = $case_row->CaseStatus;
            $this->TrackTypeName = $case_row->TrackTypeName;
            $this->RankCategory = $case_row->RankCategory;
            if ( isset($case_row->ServicePeriod) ) {
                $this->ServicePeriod = $case_row->ServicePeriod;
            }
            if ( isset($case_row->StartDate) ) {
                $this->StartDate = $case_row->StartDate;
            }
            if ( isset($case_row->EndDate) ) {
                $this->EndDate = $case_row->EndDate;
            }
            $this->CoverSheetID = $case_row->CoverSheetID;
        }
    }

    public function set_calculated_values()
    {
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

    public function propose_effective_date( Rpt_Info_Cycle $cycle_obj )
    {
        if ( $this->EffectiveDate ) {
            return $this->EffectiveDate;
        }
        else {
            return $cycle_obj->EffectiveDate[$this->ServicePeriod];
        }
    }

    public function update_from_post( $posted_values )
    {
        $this->CaseID = intval($posted_values['CaseID']);
        $this->InterfolioCaseID = intval($posted_values['InterfolioCaseID']);
        $this->CandidateID = intval($posted_values['CandidateID']);
        $this->CandidateKey = intval($posted_values['CandidateKey']);
        $this->InitiatorID = intval($posted_values['InitiatorID']);
        $this->UWODSAppointmentTrackKey = intval($posted_values['UWODSAppointmentTrackKey']);
        $this->UWODSUnitKey = intval($posted_values['UWODSUnitKey']);
        $this->CurrentRankKey = intval($posted_values['CurrentRankKey']);
        $this->AppointmentType = sanitize_text_field($posted_values['AppointmentType']);
        $this->InterfolioUnitID = intval($posted_values['InterfolioUnitID']);
        $this->CaseStatus = sanitize_text_field($posted_values['CaseStatus']);
    }

    public function insert_case_array()
    {
        return array(
            'InterfolioCaseID' => $this->InterfolioCaseID,
            'RptTemplateID' => $this->RptTemplateID,
            'CandidateID' => $this->CandidateID,
            'InitiatorID' => $this->InitiatorID,
            'CandidateKey' => $this->CandidateKey,
            'UWODSAppointmentTrackKey' => $this->UWODSAppointmentTrackKey,
            'CaseStatus' => $this->CaseStatus,
        );
    }

}