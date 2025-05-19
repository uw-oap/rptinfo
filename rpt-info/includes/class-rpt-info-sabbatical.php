<?php

class Rpt_Info_Sabbatical extends Rpt_Info_Case
{

    public function __construct( $case_row = NULL )
    {
        parent::__construct($case_row);
        $this->RptTemplateTypeID = '5';
        if ( $case_row ) {
            // populate fields
        }
    }

    public function update_from_post( $posted_values )
    {
        parent::update_from_post( $posted_values );
        // any other fields
    }

}