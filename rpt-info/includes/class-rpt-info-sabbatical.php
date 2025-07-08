<?php

class Rpt_Info_Sabbatical extends Rpt_Info_Case
{
    public $SummerQtr = 'No';
    public $FallQtr = 'No';
    public $WinterQtr = 'No';
    public $SpringQtr = 'No';
    public $SalarySupportPct = '0';
    public $RosterPct = '0.0';
    public $MonthlySalary = '0.0';
    public $TenureAmount = '0';
    public $HireDate = null;
    public $TrackStartDate = null;
    public $AppointmentStartDate = null;
    public $LastSabbaticalDate = null;
    public $UpForPromotion = 'No';
    public $MultiYear = 'No';
    public $EligibilityReport = 'Yes';
    public $EligibilityNote = '';

    public function __construct( $case_row = NULL )
    {
        parent::__construct($case_row);
        $this->RptTemplateTypeID = '5';
        if ( $case_row ) {
            // populate fields
            $this->SummerQtr = $case_row->SummerQtr;
            $this->FallQtr = $case_row->FallQtr;
            $this->WinterQtr = $case_row->WinterQtr;
            $this->SpringQtr = $case_row->SpringQtr;
            $this->SalarySupportPct = $case_row->SalarySupportPct;
            $this->RosterPct = $case_row->RosterPct;
            $this->MonthlySalary = $case_row->MonthlySalary;
            $this->TenureAmount = $case_row->TenureAmount;
            $this->HireDate = $case_row->HireDate;
            $this->TrackStartDate = $case_row->TrackStartDate;
            $this->AppointmentStartDate = $case_row->AppointmentStartDate;
            $this->LastSabbaticalDate = $case_row->LastSabbaticalDate;
            $this->UpForPromotion = $case_row->UpForPromotion;
            $this->MultiYear = $case_row->MultiYear;
            $this->EligibilityReport = $case_row->EligibilityReport;
            $this->EligibilityNote = $case_row->EligibilityNote;
        }
    }

    public function update_from_post( $posted_values )
    {
        parent::update_from_post( $posted_values );
        // any other fields
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
            'LastSabbaticalDate' => $this->LastSabbaticalDate,
            'UpForPromotion' => $this->UpForPromotion,
            'MultiYear' => $this->MultiYear,
            'EligibilityReport' => $this->EligibilityReport,
            'EligibilityNote' => $this->EligibilityNote
        );
    }

    public function listing_table_row( $rpt_case_url ) : string
    {
        global $wp;
        $result = '';
        return $result;
    }

    public function sabbatical_info_card($rpt_case_url) : string
    {
        global $wp;
        $result = '<div class="card">';
        $result .= '<div class="card-body">';
        $result .= '<h4 class="card-title">Sabbatical information</h4>';
        $result .= '<dl class="rptinfo-list">';
        $result .= '</dl>';
        if ( $this->RptCaseID ) {
            $result .= '<p><a href="' . $rpt_case_url . '/' . $this->RptCaseID
                . '">Go to case</a></p>';
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

}