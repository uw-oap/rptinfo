<?php


class Rpt_Info_DB
{

    private $rpt_db = NULL;
    private $last_query = '';

    public function __construct($db_name)
    {
        $this->rpt_db = new wpdb(DB_USER, DB_PASSWORD, $db_name, DB_HOST);
    }

    public function get_last_query()
    {
        return $this->last_query;
    }

    public function get_last_error()
    {
        return $this->rpt_db->last_error;
    }

    public function get_template_type_list()
    {
        $result = [];
        $query = "SELECT RptTemplateTypeID, TemplateTypeName, InUse FROM RptTemplateType;";
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query) as $row) {
            $result[$row->RptTemplateTypeID] = $row;
        }
        return $result;
    }

}