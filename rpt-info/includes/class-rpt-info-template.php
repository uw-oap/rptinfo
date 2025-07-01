<?php

class Rpt_Info_Template
{
    public $RptTemplateID = 0;
    public $TemplateName = '';
    public $RptTemplateTypeID = 0;
    public $TemplateTypeName = '';
    public $InterfolioUnitID = 0;
    public $UnitName = '';
    public $ParentID = 0;
    public $ParentName = '';
    public $LevelOneID = 0;
    public $LevelOneName = '';
    public $UnitType = '';
    public $Description = '';
    public $InUse = 'No';
    public $IsPublished = 'No';
    public $TemplateTypeInUse = 'No';

    public function __construct( $template_row = NULL )
    {
        if ( is_object( $template_row) ) {
            $this->RptTemplateID = $template_row->RptTemplateID;
            $this->TemplateName = $template_row->TemplateName;
            $this->RptTemplateTypeID = $template_row->RptTemplateTypeID;
            $this->TemplateTypeName = $template_row->TemplateTypeName;
            $this->InterfolioUnitID = $template_row->InterfolioUnitID;
            $this->UnitName = $template_row->UnitName;
            $this->ParentID = $template_row->ParentID;
            $this->ParentName = $template_row->ParentName;
            $this->LevelOneID = $template_row->LevelOneID;
            $this->LevelOneName = $template_row->LevelOneName;
            $this->UnitType = $template_row->UnitType;
            $this->Description = $template_row->Description;
            $this->InUse = $template_row->InUse;
            $this->IsPublished = $template_row->IsPublished;
            $this->TemplateTypeInUse = $template_row->TemplateTypeInUse;
        }
    }

    public function update_array()
    {
        return array(
            'InUse' => $this->InUse
        );
    }

    public function template_info_card( $rpt_template_url ) : string
    {
        $result = '<div class="card">';
        $result .= '<div class="card-body">';
        $result .= '<h4 class="card-title">Template information</h4>';
        $result .= '<dl class="rptinfo-list">';
        $result .= '<dt>ID</dt>';
        $result .= '<dd>' . $this->RptTemplateID . '</dd>';
        $result .= '<dt>Name</dt>';
        $result .= '<dd>' . $this->TemplateName . '</dd>';
        $result .= '<dt>Type</dt>';
        $result .= '<dd>' . $this->TemplateTypeName . '</dd>';
        $result .= '<dt>S/C/C</dt>';
        $result .= '<dd>' . $this->LevelOneName . ' (' . $this->UnitType . ')</dd>';
        if ( $this->UnitType == 'dep' ) {
            $result .= '<dt>Unit</dt>';
            $result .= '<dd>' . $this->UnitName . '</dd>';
        }
        $result .= '<dt>Description</dt>';
        $result .= '<dd>' . $this->Description . '</dd>';
        $result .= '<dt>Published</dt>';
        $result .= '<dd>' . $this->IsPublished . '</dd>';
        $result .= '<dt>Enabled</dt>';
        $result .= '<dd>' . $this->InUse . '</dd>';
        $result .= '</dl>';
        $result .= '<p><a href="' . $rpt_template_url . '/' . $this->RptTemplateID
            . '">Go to template</a></p>';
        $result .= '</div>'; // card body
        $result .= '</div>'; // card
        return $result;
    }

    public function listing_table_row( $rpt_template_url )
    {
        global $wp;
        $result = '<tr>';
        $result .= '<td><a href="' . $rpt_template_url . '/' . $this->RptTemplateID . '">';
        $result .= $this->RptTemplateID . '</a></td>';
        $result .= '<td>' . $this->TemplateName . '</td>';
        $result .= '<td>';
        $result .= $this->UnitName;
        if ( ( $this->UnitType == 'dep' ) && ( $this->UnitName != $this->LevelOneName ) ) {
            $result .= '<br>' . $this->LevelOneName;
        }
        $result .= '</td>';
        $result .= '<td>' . $this->InUse . '</td>';
        $result .= '<td>';
        $result .= '<a href="' . esc_url(add_query_arg(array('template_id' => $this->RptTemplateID,
                'template_type' => $this->RptTemplateTypeID,
                'ay' => $this->AcademicYear,
                'rpt_page' => 'template'), home_url($wp->request)))
            . '" class="btn btn-outline-secondary">Details</a>';
        $result .= '</td>';
        $result .= '</tr>';
        return $result;
    }

}