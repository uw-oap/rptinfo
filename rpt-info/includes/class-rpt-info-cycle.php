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

    public function __construct($cycle_row = NULL)
    {
        $this->AcademicYear = $cycle_row->AcademicYear;
        $this->Display = $cycle_row->Display;
        $this->MandatoryDueDate = $cycle_row->MandatoryDueDate;
        $this->NonMandatoryDueDate = $cycle_row->NonMandatoryDueDate;
        $this->LibrarianDueDate = $cycle_row->LibrarianDueDate;
        $this->EffectiveDate['9'] = $cycle_row->EffectiveDate09Month;
        $this->EffectiveDate['12'] = $cycle_row->EffectiveDate12Month;
    }

}