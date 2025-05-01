<?php


class RpT_Info_DB
{

    private $rpt_db = NULL;
    private $last_query = '';

    public function __construct()
    {
        $this->rpt_db = new wpdb(DB_USER,DB_PASSWORD,PROMOTION_DB,DB_HOST);
    }

    public function get_last_query()
    {
        return $this->last_query;
    }

    public function get_last_error()
    {
        return $this->rpt_db->last_error;
    }

}