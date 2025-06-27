<?php

class Rpt_Info_Cycle
{
    public $AcademicYear = null;
    public $Display = null;
    public $MandatoryDueDate = null;
    public $NonMandatoryDueDate = null;
    public $LibrarianDueDate = null;
    public $EffectiveDate = array(
        '9' => '',
        '12' => ''
    );

    public function __construct( $cycle_row )
    {
        $this->AcademicYear = $cycle_row->AcademicYear;
        $this->Display = $cycle_row->Display;
        $this->MandatoryDueDate = $cycle_row->MandatoryDueDate;
        $this->NonMandatoryDueDate = $cycle_row->NonMandatoryDueDate;
        $this->LibrarianDueDate = $cycle_row->LibrarianDueDate;
        $this->EffectiveDate['9'] = $cycle_row->EffectiveDate09Month;
        $this->EffectiveDate['12'] = $cycle_row->EffectiveDate12Month;
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