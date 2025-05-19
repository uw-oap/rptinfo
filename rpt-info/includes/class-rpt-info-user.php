<?php

class Rpt_Info_User
{
    public $InterfolioUserID = 0;
    public $UWODSPersonKey = 0;
    public $FirstName = '';
    public $LastName = '';
    public $UWNetID = '';
    public $DisplayName = '';
    public $Units = array();

    public function __construct( $UWNetID )
    {
        $this->UWNetID = $UWNetID;
    }

    public function update_from_database( $row )
    {
        if ( $this->InterfolioUserID == 0 ) {
            $this->InterfolioUserID = $row->InterfolioUserID;
            $this->UWODSPersonKey = $row->UWODSPersonKey;
            $this->FirstName = $row->FirstName;
            $this->LastName = $row->LastName;
            $this->DisplayName = $row->LastName . ', ' . $row->FirstName;
        }
        $this->Units[$row->InterfolioUnitID] = array(
            'UnitName' => $row->UnitName,
            'UnitType' => $row->UnitType
        );
    }

    public function SystemAdmin() : bool
    {
        if ( array_key_exists('28343', $this->Units) ) {
            return true;
        }
        return false;
    }

    public function display_units()
    {
        $result = '';
        foreach ($this->Units as $id => $unit) {
            if ( $result ) {
                $result .= ', ';
            }
            $result .= $unit['UnitName'];
            if ( $id == '28343' ) {
                $result .= ' (System admin)';
            }
            else {
                $result .= ' (' . $unit['UnitType'] . ')';
            }
        }
        return $result;
    }

}