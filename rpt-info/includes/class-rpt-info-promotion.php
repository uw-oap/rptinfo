<?php

class Rpt_Info_Promotion extends Rpt_Info_Case
{
    public $TargetRankKey = 0;
    public $TargetRankName = '';
    public $ActionType = '';
    public $PromotionTypeID = 0;
    public $PromotionTypeName = '';

    // fields for datasheet
    public $Postponed = 'No';
    public $TenureAward = '';
    public $NewTermLength = '';
    public $SubcommitteeMembers = '';
    public $Vote1Eligible = '0';
    public $Vote1Affirmative = '0';
    public $Vote1Negative = '0';
    public $Vote1Absent = '0';
    public $Vote1Abstaining = '0';
    public $Vote2Eligible = '0';
    public $Vote2Affirmative = '0';
    public $Vote2Negative = '0';
    public $Vote2Absent = '0';
    public $Vote2Abstaining = '0';
    // datasheet fields
    public $DataSheetID = '0';

    public function __construct( $case_row = NULL )
    {
        parent::__construct( $case_row );
        $this->RptTemplateTypeID = '2';
        if ( $case_row ) {
            $this->TargetRankKey = $case_row->TargetRankKey;
            $this->TargetRankName = $case_row->TargetRankName;
            $this->PromotionTypeID = $case_row->PromotionTypeID;
            $this->PromotionTypeName = $case_row->PromotionTypeName;
            $this->SubcommitteeMembers = $case_row->SubcommitteeMembers;
            if ( isset($case_row->DatasheetID) ) {
                $this->DataSheetID = $case_row->DataSheetID;
                $this->TenureAward = $case_row->TenureAward;
                $this->NewTermLength = $case_row->NewTermLength;
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
            }
        }
    }

    public function update_from_post( $posted_values )
    {
        parent::update_from_post( $posted_values );
        $this->TargetRankKey = intval($posted_values['TargetRankKey']);
        $this->EffectiveDate = sanitize_text_field($posted_values['EffectiveDate']);
        $this->InterfolioTemplateID = intval($posted_values['InterfolioTemplateID']);
        $this->PromotionTypeID = intval($posted_values['PromotionTypeID']);
    }

    public function insert_promotion_array() : array
    {
        return array(
            'CaseID' => $this->CaseID,
            'PromotionTypeID' => $this->PromotionTypeID,
            'CurrentRankKey' => $this->CurrentRankKey,
            'TargetRankKey' => $this->TargetRankKey,
            'NewTermLength' => $this->NewTermLength,
            'EffectiveDate' => $this->EffectiveDate,
            'HasJoint'
        );
    }

    public function listing_table_row( $rpt_case_url ) : string
    {
        global $wp;
        $result = '<tr class="border-bottom border-right">';
        $result .= '<td><strong>' . $this->LegalName . ' (' . $this->EmployeeID . ')</strong><br>';
        $result .= $this->CurrentRankName . ' in ' . $this->UnitName . ' ('
            . $this->AppointmentType . ')</td>';
        $result .= '<td>' . $this->PromotionTypeName . '</td>';
        $result .= '<td>' . $this->CaseStatus;
        if ( $this->InterfolioCaseID ) {
            $result .= '<br><a href="' . $rpt_case_url . '/' . $this->InterfolioCaseID . '">Go to case</a>';
        }
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

    public function promotion_info_card( $rpt_case_url ) : string
    {
        $result = '';
        $result .= '<div class="card">';
        $result .= '<div class="card-body">';
        $result .= '<h4 class="card-title">Promotion information</h4>';
        $result .= '<dl class="rptinfo-list">';
        $result .= '<dt>Proposed rank</dt>';
        $result .= '<dd>' . $this->TargetRankName . '</dd>';
        $result .= '<dt>Effective date</dt>';
        $result .= '<dd>' . rpt_format_date($this->EffectiveDate) . '</dd>';
        $result .= '<dt>Promotion type</dt>';
        $result .= '<dd>' . $this->PromotionTypeName . '</dd>';
        $result .= '<dt>RPT template</dt>';
        $result .= '<dd>' . $this->TemplateName . '</dd>';
        $result .= '<dt>Current status</dt>';
        $result .= '<dd>' . $this->CaseStatus . '</dd>';
        $result .= '<dt>Workflow step</dt>';
        $result .= '<dd>' . $this->WorkflowStepName . ' (' . $this->WorkflowStepNumber. ')</dd>';
        $result .= '</dl>';
        $result .= '<p><a href="' . $rpt_case_url . '/' . $this->InterfolioCaseID . '">Go to case</a></p>';
        $result .= '</div>'; // card body
        $result .= '</div>'; // card
        return $result;
    }

    public function data_sheet_card( $rpt_case_url ) : string
    {
        $result = '';
        $result .= '<div class="card">';
        $result .= '<div class="card-body">';
        $result .= '<h4 class="card-title">Data sheet</h4>';
        $result .= '<dl class="rptinfo-list">';
        $result .= '<dt>Proposed rank</dt>';
        $result .= '<dd>' . $this->TargetRankName . '</dd>';
        $result .= '</dl>';
        $result .= '</div>'; // card body
        $result .= '</div>'; // card
        return $result;
    }

}