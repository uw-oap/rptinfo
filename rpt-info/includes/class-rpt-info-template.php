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
            $this->TemplateTypeInUse = $template_row->TemplateTypeInUse;
        }
    }

    public function update_array()
    {
        return array(
            'InUse' => $this->InUse
        );
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
        $result .= '<td>[action]</td>';
        $result .= '</tr>';
        return $result;
    }

}