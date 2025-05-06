<?php

class Rpt_Info_Promotion extends Rpt_Info_Case
{
    public $TargetRankKey = 0;
    public $TargetRankName = '';
    public $ActionType = '';
    public $PromotionTypeID = 0;
    public $PromotionTypeName = '';
    public $NewTerm = 0;
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
            if ( isset($case_row->NewTerm) ) {
                $this->NewTerm = $case_row->NewTerm;
            }
            $this->SubcommitteeMembers = $case_row->SubcommitteeMembers;
            if ( isset($case_row->DatasheetID) ) {
                $this->DatasheetID = $case_row->DatasheetID;
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

}