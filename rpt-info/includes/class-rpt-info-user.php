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

    }

}