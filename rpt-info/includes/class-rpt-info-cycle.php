<?php

class Rpt_Info_Cycle
{
    public $AcademicYear = null;
    public $Display = null;
    public $YearStatus = ''; // past, current, future

    // promotion related items
    public $EffectiveDate = array(
        '9' => '',
        '12' => ''
    );
    public $MandatoryDueDate = null;
    public $NonMandatoryDueDate = null;
    public $LibrarianDueDate = null;
    public $PromotionSubmissionStartDate = '';
    public $PromotionSubmissionEndDate = '';
    public $PromotionSubbmissionAllowed = '';
    public $PromotionShowOutcomes = 'No';

    // sabbatical related items
    public $SabbaticalCompLimit = 0.00;
    public $SabbaticalSubmissionStartDate = '';
    public $SabbaticalSubmissionEndDate = '';
    public $SabbaticalSubmissionAllowed = '';
    public $SabbaticalShowOutcomes = 'No';

    public function __construct( $cycle_row )
    {
        $this->AcademicYear = $cycle_row->AcademicYear;
        $this->Display = $cycle_row->Display;
        $this->YearStatus = $cycle_row->YearStatus;
        $this->EffectiveDate['9'] = $cycle_row->EffectiveDate09Month;
        $this->EffectiveDate['12'] = $cycle_row->EffectiveDate12Month;
        $this->MandatoryDueDate = $cycle_row->MandatoryDueDate;
        $this->NonMandatoryDueDate = $cycle_row->NonMandatoryDueDate;
        $this->LibrarianDueDate = $cycle_row->LibrarianDueDate;
        $this->PromotionSubmissionStartDate = $cycle_row->PromotionSubmissionStartDate;
        $this->PromotionSubmissionEndDate = $cycle_row->PromotionSubmissionEndDate;
        $this->PromotionSubbmissionAllowed = $cycle_row->PromotionSubbmissionAllowed;
        $this->PromotionShowOutcomes = $cycle_row->PromotionShowOutcomes;
        $this->SabbaticalCompLimit = $cycle_row->SabbaticalCompLimit;
        $this->SabbaticalSubmissionStartDate = $cycle_row->SabbaticalSubmissionStartDate;
        $this->SabbaticalSubmissionEndDate = $cycle_row->SabbaticalSubmissionEndDate;
        $this->SabbaticalSubmissionAllowed = $cycle_row->SabbaticalSubmissionAllowed;
        $this->SabbaticalShowOutcomes = $cycle_row->SabbaticalShowOutcomes;
    }

    public function template_type_submissions_allowed( int $template_type_id ) : bool
    {
        switch ( $template_type_id ) {
            case 2:
                return ($this->PromotionSubbmissionAllowed == 'Yes');
            case 5:
                return ($this->SabbaticalSubmissionAllowed == 'Yes');
            default:
                return false;
        }
    }

    public function template_type_submission_window( int $template_type_id ) : string
    {
        switch ( $template_type_id ) {
            case 2:
                return rpt_format_date($this->PromotionSubmissionStartDate) . ' &mdash; '
                    . rpt_format_date($this->PromotionSubmissionEndDate);
            case 5:
                return rpt_format_date($this->SabbaticalSubmissionStartDate) . ' &mdash; '
                    . rpt_format_date($this->SabbaticalSubmissionEndDate);
            default:
                return 'Error getting dates';
        }
    }

    public function summer_quarter_start_date() : DateTime
    {
        return new DateTime($this->AcademicYear . '-07-01');
    }

    public function fall_quarter_end_date() : DateTime
    {
        return new DateTime($this->AcademicYear . '-09-16');
    }

    public function winter_quarter_start_date() : DateTime
    {
        return new DateTime($this->AcademicYear . '-12-16');
    }

    public function spring_quarter_start_date() : DateTime
    {
        return new DateTime(($this->AcademicYear + 1) . '-03-16');
    }

}