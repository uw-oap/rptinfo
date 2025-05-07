<?php

class Rpt_Info_User
{
    public $InterfolioUserID = 0;
    public $UWODSPersonKey = 0;
    public $UWNetID = '';
    public $DisplayName = '';
    public $Units = array();

    public function __construct( $UWNetID )
    {
        $this->UWNetID = $UWNetID;
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
            if ( $id != '28343' ) {
                $result .= ' (' . $unit['UnitType'] . ')';
            }
        }
        return $result;
    }

}