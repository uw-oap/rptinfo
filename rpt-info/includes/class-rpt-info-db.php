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

    /** ******************* promotion cycle functions ******************************** */

    public function get_cycle_info( $academic_year = '' )
    {
        $result = NULL;
        if ( $academic_year ) {
            $query = $this->rpt_db->prepare("SELECT AcademicYear, Display, IsCurrent, MandatoryDueDate, NonMandatoryDueDate, 
LibrarianDueDate, EffectiveDate09Month, EffectiveDate12Month FROM PromotionCycle where AcademicYear = %s", $academic_year);
        }
        else {
            $query = "SELECT AcademicYear, Display, IsCurrent, MandatoryDueDate, NonMandatoryDueDate, LibrarianDueDate, 
EffectiveDate09Month, EffectiveDate12Month FROM PromotionCycle where IsCurrent = 'Yes'";
        }
        $this->last_query = $query;
        $result_row = $this->rpt_db->get_row($query);
        if ( $result_row ) {
            $result = new Pt_Info_Cycle($result_row);
        }
        return $result;
    }

    /** ******************* template type functions ********************************** */

    public function get_template_type_list( $active_only = FALSE )
    {
        $result = [];
        $query = "SELECT RptTemplateTypeID, TemplateTypeName, InUse FROM RptTemplateType";
        if ( $active_only ) {
            $query .= " WHERE InUse = 'Yes'";
        }
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query) as $row) {
            $result[$row->RptTemplateTypeID] = $row;
        }
        return $result;
    }

    public function update_template_types( $update_list )
    {
        $result = 0;
        foreach ( $update_list as $template_type_id => $in_use ) {
            $query_result = $this->rpt_db->update('RptTemplateType',
                array('InUse' => $in_use),
                array('RptTemplateTypeID' => $template_type_id));
            $this->last_query = $this->rpt_db->last_error;
            if ( $query_result === FALSE ) {
                return 0;
            }
            $result++;
        }
        return $result;
    }

    /** ******************* user functions ********************************** */

    public function get_rpt_user_info( $netid )
    {
        $result = new Pt_Info_User($netid);
        $query = $this->rpt_db->prepare("select InterfolioUserID, UWODSPersonKey, UWNetID, FirstName, LastName,
 InterfolioUnitID, UnitName from RptUserUnitDetails where IsActive = 'Yes' and UWNetID = %s", $netid);
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query) as $row) {
            $result->InterfolioUserID = $row->InterfolioUserID;
            $result->UWODSPersonKey = $row->UWODSPersonKey;
            $result->DisplayName = $row->LastName . ', ' . $row->FirstName;
            $result->Units[$row->InterfolioUnitID] = $row->UnitName;
        }
        return $result;
    }

    /** ******************* case functions ********************************** */

    public function get_promotion_cases_for_user( Pt_Info_User $user_obj ) : array
    {
        $result = [];
        $query = "SELECT CaseID, InterfolioCaseID, InterfolioTemplateID, CandidateID, EmployeeID, CaseStatus,
            LegalName, InitiatorID, InitiatorName, CandidateKey, UWODSAppointmentTrackKey, AppointmentType, 
            UWODSUnitKey, UnitName, CurrentRankKey, CurrentRankName, TargetRankKey, TargetRankName, DueDate, RankCategory,
            ParentID, ParentUnitName, LevelOneID, LevelOneUnitName, PromotionTypeID, PromotionTypeName, TrackTypeName,
            EffectiveDate, HasJoint, HasSecondary FROM RptPromotionDetails where InterfolioUnitID in ("
            . implode(',', array_keys($user_obj->Units)) . ") or  ParentID in ("
            . implode(',', array_keys($user_obj->Units)) . ") or LevelOneID in ("
            . implode(',', array_keys($user_obj->Units)) . ") or '28343' in ("
            . implode(',', array_keys($user_obj->Units)) . ")";
        ;
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query) as $row) {
            $result[$row->CaseID] = new Pt_Info_Case($row);
        }
        return $result;
    }

    public function promotion_candidate_search($user_units, $search_string) : array
    {
        $result = [];
        $search_terms = explode(' ', $search_string);
        $query = "select InterfolioUserID, EmployeeID, LegalName, RankName, UnitName, AppointmentType, 
       UWODSAppointmentTrackKey, CaseStatus, CaseID from CurrentPromotable where (InterfolioUnitID in ("
            . implode(',', $user_units) . ") or ParentID in (" . implode(',', $user_units)
            . ") or Level1InterfolioUnitID in (". implode(',', $user_units)
            . ") or '28343' in (". implode(',', $user_units)
            . ")) and (AppointmentType in ('Primary','Joint'))";
        foreach ($search_terms as $term) {
            $query .= " and (SearchText like '%" . $term . "%')";
        }
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query, ARRAY_A) as $row) {
            $result[$row['InterfolioUserID']] = $row;
        }
        return $result;
    }


}