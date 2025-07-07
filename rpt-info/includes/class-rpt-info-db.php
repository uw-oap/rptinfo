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
            $result = new Rpt_Info_Cycle($result_row);
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

    public function get_rpt_user_info( $netid ) : Rpt_Info_User
    {
        $user_obj = new Rpt_Info_User($netid);
        $query = $this->rpt_db->prepare("select InterfolioUserID, UWODSPersonKey, UWNetID, FirstName, LastName,
 InterfolioUnitID, UnitName, UnitType from RptUserUnitDetails where IsActive = 'Yes' and UWNetID = %s", $netid);
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query) as $row) {
            $user_obj->update_from_database($row);
        }
        return $user_obj;
    }

    /** ******************* case functions ********************************** */

    public function get_promotion_cases_for_user( Rpt_Info_User $user_obj ) : array
    {
        $result = [];
        $query = "SELECT CaseID, RptCaseID, RptTemplateID, CandidateID, EmployeeID, CaseStatus,
            LegalName, InitiatorID, InitiatorName, CandidateKey, UWODSAppointmentTrackKey, AppointmentType, 
            UWODSUnitKey, UnitName, CurrentRankKey, CurrentRankName, TargetRankKey, TargetRankName, DueDate, RankCategory,
            ParentID, ParentUnitName, LevelOneID, LevelOneUnitName, PromotionCategoryID, PromotionCategoryName, TrackTypeName,
            EffectiveDate, HasJoint, HasSecondary, WorkflowStepNumber, WorkflowStepName, TargetTrackTypeName
FROM RptPromotionDetails where InterfolioUnitID in ("
            . implode(',', array_keys($user_obj->Units)) . ") or  ParentID in ("
            . implode(',', array_keys($user_obj->Units)) . ") or LevelOneID in ("
            . implode(',', array_keys($user_obj->Units)) . ") or '28343' in ("
            . implode(',', array_keys($user_obj->Units)) . ")";
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query) as $row) {
            $result[$row->CaseID] = new Rpt_Info_Promotion($row);
        }
        return $result;
    }

    public function promotion_candidate_search( Rpt_Info_User $user_obj, $search_string) : array
    {
        $result = [];
        $search_terms = explode(' ', $search_string);
        $query = "select InterfolioUserID, EmployeeID, LegalName, RankName, UnitName, AppointmentType, 
       UWODSAppointmentTrackKey, CaseStatus, CaseID from CurrentPromotable where (InterfolioUnitID in ("
            . implode(',', array_keys($user_obj->Units)) . ") or ParentID in ("
            . implode(',', array_keys($user_obj->Units))
            . ") or Level1InterfolioUnitID in (". implode(',', array_keys($user_obj->Units))
            . ") or '28343' in (". implode(',', array_keys($user_obj->Units))
            . ")) and (AppointmentType in ('Primary','Joint'))";
        foreach ($search_terms as $term) {
            $query .= " and (SearchText like '%" . $term . "%')";
        }
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query, ARRAY_A) as $row) {
            $result[$row['InterfolioUserID'] . '-' . $row['UWODSAppointmentTrackKey']] = $row;
        }
        return $result;
    }

    public function sabbatical_candidate_search( Rpt_Info_User $user_obj, $search_string) : array
    {
        $result = [];
        $search_terms = explode(' ', $search_string);
        $query = "select InterfolioUserID, EmployeeID, LegalName, RankName, UnitName, AppointmentType, 
       UWODSAppointmentTrackKey, CaseStatus, CaseID from CurrentSabbaticalEligible where (InterfolioUnitID in ("
            . implode(',', array_keys($user_obj->Units)) . ") or ParentID in ("
            . implode(',', array_keys($user_obj->Units))
            . ") or LevelOneID in (". implode(',', array_keys($user_obj->Units))
            . ") or '28343' in (". implode(',', array_keys($user_obj->Units))
            . ")) and (AppointmentType in ('Primary','Joint'))";
        foreach ($search_terms as $term) {
            $query .= " and (SearchText like '%" . $term . "%')";
        }
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query, ARRAY_A) as $row) {
            $result[$row['InterfolioUserID'] . '-' . $row['UWODSAppointmentTrackKey']] = $row;
        }
        return $result;
    }

    public function get_promotion_case_for_candidate(int $track_id)
    {
        $result = NULL;
        $query = "SELECT CaseID, RptCaseID, RptTemplateID, CandidateID, EmployeeID, CaseStatus,
            LegalName, InitiatorID, InitiatorName, CandidateKey, UWODSAppointmentTrackKey, AppointmentType, 
            UWODSUnitKey, UnitName, CurrentRankKey, CurrentRankName, TargetRankKey, TargetRankName, DueDate, 
            ParentID, ParentUnitName, LevelOneID, LevelOneUnitName, PromotionCategoryID, PromotionCategoryName,
            EffectiveDate, HasJoint, HasSecondary FROM RptPromotionDetails where UWODSAppointmentTrackKey = '"
            . $track_id . "'";
        $this->last_query = $query;
        $result_row = $this->rpt_db->get_row($query);
        if ( $result_row ) {
            $result = new Rpt_Info_Promotion($result_row);
        }
        return $result;
    }

    public function get_sabbatical_case_for_candidate(int $track_id)
    {
        $result = NULL;
        $query = "SELECT CaseID, RptCaseID, RptTemplateID, CandidateID, EmployeeID, CaseStatus,
            LegalName, InitiatorID, InitiatorName, CandidateKey, UWODSAppointmentTrackKey, AppointmentType, 
            UWODSUnitKey, UnitName, CurrentRankKey, CurrentRankName, TargetRankKey, TargetRankName, DueDate, 
            ParentID, ParentUnitName, LevelOneID, LevelOneUnitName, PromotionTypeID, PromotionTypeName,
            EffectiveDate, HasJoint, HasSecondary FROM RptPromotionDetails where UWODSAppointmentTrackKey = '"
            . $track_id . "'";
        $this->last_query = $query;
        $result_row = $this->rpt_db->get_row($query);
        if ( $result_row ) {
            $result = new Rpt_Info_Promotion($result_row);
        }
        return $result;
    }

    public function get_promotion_from_track(int $track_id)
    {
        $result = NULL;
        $query = $this->rpt_db->prepare("SELECT '0' CaseID, '0' RptCaseID,  '0' RptTemplateID,  InterfolioUserID CandidateID,
	LegalName, '0' InitiatorID, '' InitiatorName, UWODSPersonKey CandidateKey, UWODSAppointmentTrackKey, EmployeeID,
	AppointmentType, UWODSUnitKey, UnitName, UWODSRankKey CurrentRankKey, RankName CurrentRankName, 'N/A' CaseStatus,
    InterfolioUnitID, Level1InterfolioUnitID LevelOneID, Level1UnitName LevelOneName, PromotionCategoryID, PromotionCategoryName,
	'0' TargetRankKey, '' TargetRankName, NULL DueDate, NULL EffectiveDate, '' HasJoint, '' HasSecondary,
	ServicePeriod, TrackTypeName, RankCategory, ParentID
FROM CurrentPromotable where UWODSAppointmentTrackKey = %s", $track_id);
        $this->last_query = $query;
        $result_row = $this->rpt_db->get_row($query);
        if ( $result_row ) {
            $result = new Rpt_Info_Promotion($result_row);
        }
        return $result;
    }

    public function get_promotion_by_id(int $case_id)
    {
        $result = NULL;
        $query = $this->rpt_db->prepare("SELECT CaseID, RptCaseID, RptTemplateID, CandidateID, EmployeeID,
CaseStatus, InterfolioUnitID, AcademicYear, WorkflowStepNumber, WorkflowStepName, TemplateName, CoverSheetID,
LegalName, InitiatorID, InitiatorName, CandidateKey, UWODSAppointmentTrackKey, AppointmentType, TrackTypeName,
UWODSUnitKey, UnitName, CurrentRankKey, CurrentRankName, TargetRankKey, TargetRankName, DueDate, RankCategory,
ParentID, ParentUnitName, LevelOneID, LevelOneUnitName, PromotionCategoryID, PromotionCategoryName, ServicePeriod,
EffectiveDate, HasJoint, HasSecondary, SubcommitteeMembers, DatasheetID, Postponed, TenureAward, NewTermLength, 
Vote1Eligible, Vote1Affirmative, Vote1Negative, Vote1Absent, Vote1Abstaining, Vote2Eligible, Vote2Affirmative, 
Vote2Negative, Vote2Absent, Vote2Abstaining, DatasheetID FROM RptPromotionDetails where CaseID = %s", $case_id);
        $this->last_query = $query;
        $result_row = $this->rpt_db->get_row($query);
        if ( $result_row ) {
            $result = new Rpt_Info_Promotion($result_row);
        }
        return $result;
    }

    public function get_sabbatical_by_id(int $case_id)
    {
        $result = NULL;
        return $result;
    }

    public function get_other_appointments( Rpt_Info_Case $case_obj )
    {
        $query = $this->rpt_db->prepare("select distinct UWODSAppointmentTrackKey, UWODSAppointmentKey, UWODSUnitKey, 
    UnitName, UWODSRankKey, RankName, AppointmentType from CurrentPromotable 
    where UWODSPersonKey = %s and UWODSAppointmentTrackKey != %s",
            $case_obj->CandidateKey, $case_obj->UWODSAppointmentTrackKey);
        $this->last_query = $query;
//        echo $query; exit;
        foreach ($this->rpt_db->get_results($query) as $row) {
            $case_obj->OtherAppointments[$row->UWODSAppointmentTrackKey] = $row;
        }
    }

    public function insert_case( Rpt_Info_Promotion|Rpt_Info_Sabbatical $case_obj)
    {
        // insert base case record
        $query_result = $this->rpt_db->insert('RptCase', $case_obj->insert_case_array());
        $case_obj->CaseID = $this->rpt_db->insert_id;
        $this->last_query = $this->rpt_db->last_query;
        switch ( $case_obj->RptTemplateTypeID ) {
            case '2' :
                $query_result = $this->rpt_db->insert('RptPromotion', $case_obj->insert_promotion_array());
//                echo $this->rpt_db->last_query; exit;
                break;
            case '5' :
                break;
        }
        if ( $query_result === FALSE ) {
            return 0;
        }
        return $query_result;
    }

    public function update_case( Rpt_Info_Case $case_obj )
    {
        /*        $query = "update promotions.RptCase set " . $case_obj->update_fields() . " where CaseID = '"
                    . $case_obj->CaseID . "'";
                $this->last_query = $query;
                $query_result = $this->rpt_db->query($query); */
        $query_result = $this->rpt_db->update('RptCase', $case_obj->insert_array(), array('ID' => $case_obj->CaseID));
        $this->last_query = $this->rpt_db->last_error;
        if ( $query_result === FALSE ) {
            return 0;
        }
        return $query_result;
    }

    public function get_valid_templates_for_case( Rpt_Info_Case $case_obj)
    {
        $result = [];
        $query = "select RptTemplateID, TemplateName, UnitName from RptTemplateDetails 
where (InterfolioUnitID = '" . $case_obj->InterfolioUnitID . "' or InterfolioUnitID = '" . $case_obj->ParentID
            . "' or InterfolioUnitID = '" . $case_obj->LevelOneID
            . "' or InterfolioUnitID = '29153') and InUse = 'Yes' and RptTemplateTypeID = '"
            . $case_obj->RptTemplateTypeID . "'";
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query, ARRAY_A) as $row) {
            $result[$row['RptTemplateID']] = $row;
        }
        return $result;
    }

    public function get_valid_promotion_target_ranks($source_rank_key) : array
    {
        $result = [];
        $query = $this->rpt_db->prepare("select TargetUWODSRankKey, TargetRankName, ActionType from ValidPromotion 
where TargetActive = 'Yes' and SourceUWODSRankKey = %s", $source_rank_key);
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query, ARRAY_A) as $row) {
            $result[$row['TargetUWODSRankKey']] = $row;
        }
        return $result;
    }

    public function get_promotion_type_list( $rank_category)
    {
        $result = [];
        $query = $this->rpt_db->prepare("select ID, PromotionCategoryName from PromotionCategory 
        where RankCategory = %s", $rank_category);
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query, ARRAY_A) as $row) {
            $result[$row['ID']] = $row['PromotionCategoryName'];
        }
        return $result;
    }

    /** ******************* template functions ********************************** */

    public function get_template_list($template_type_id, $unit_type ) : array
    {
        $result = [];
        switch ( $unit_type ) {
            case 'all':
                $query = $this->rpt_db->prepare("SELECT RptTemplateID, InterfolioUnitID, UnitName, ParentID, ParentName, 
LevelOneID, LevelOneName, TemplateName, Description, IsPublished, InUse, RptTemplateTypeID, UnitType,
TemplateTypeName, TemplateTypeInUse FROM RptTemplateDetails where RptTemplateTypeID = %s
order by TemplateName", $template_type_id);
                break;
            case 'dep' :
                $query = $this->rpt_db->prepare("SELECT RptTemplateID, InterfolioUnitID, UnitName, ParentID, ParentName, 
LevelOneID, LevelOneName, TemplateName, Description, IsPublished, InUse, RptTemplateTypeID, UnitType,
TemplateTypeName, TemplateTypeInUse FROM RptTemplateDetails where RptTemplateTypeID = %s
and UnitType = 'dep' and TemplateName like '%\_dep%' order by TemplateName", $template_type_id);
                break;
            case 'undep' :
                $query = $this->rpt_db->prepare("SELECT RptTemplateID, InterfolioUnitID, UnitName, ParentID, ParentName, 
LevelOneID, LevelOneName, TemplateName, Description, IsPublished, InUse, RptTemplateTypeID, UnitType, 
TemplateTypeName, TemplateTypeInUse FROM RptTemplateDetails where RptTemplateTypeID = %s
and UnitType = 'undep' and TemplateName like '%\_undep%' order by TemplateName", $template_type_id);
                break;
        }
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query) as $row) {
            $result[$row->RptTemplateID] = new Rpt_Info_Template($row);
        }
        return $result;
    }

    public function get_templates_for_user( Rpt_Info_User $user_obj )
    {
        $result = [];
        $query = "SELECT RptTemplateID, InterfolioUnitID, UnitName, ParentID, ParentName, 
LevelOneID, LevelOneName, TemplateName, Description, IsPublished, InUse, RptTemplateTypeID, UnitType,
TemplateTypeName, TemplateTypeInUse FROM RptTemplateDetails where InterfolioUnitID in ("
            . implode(',', array_keys($user_obj->Units)) . ") or  ParentID in ("
            . implode(',', array_keys($user_obj->Units)) . ") or LevelOneID in ("
            . implode(',', array_keys($user_obj->Units)) . ") or '28343' in ("
            . implode(',', array_keys($user_obj->Units)) . ")";
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query) as $row) {
            $result[$row->RptTemplateID] = new Rpt_Info_Template($row);
        }
        return $result;
    }

    public function get_template_by_id($template_id) : Rpt_Info_Template
    {
        $result = null;
        $query = $this->rpt_db->prepare("SELECT RptTemplateID, InterfolioUnitID, UnitName, 
ParentID, ParentName, LevelOneID, LevelOneName, TemplateName, Description, IsPublished, InUse, 
RptTemplateTypeID, UnitType, TemplateTypeName, TemplateTypeInUse 
FROM RptTemplateDetails where RptTemplateID = %s",
            $template_id);
        $this->last_query = $query;
        $result_row = $this->rpt_db->get_row($query);
        if ( $result_row ) {
            $result = new Rpt_Info_Template($result_row);
        }
        return $result;
    }

    public function update_template_in_use( Rpt_Info_Template $template )
    {
        $query_result = $this->rpt_db->update('RptTemplate', $template->update_array(),
            array('RptTemplateID' => $template->RptTemplateID));
        $this->last_query = $this->rpt_db->last_error;
        if ( $query_result === FALSE ) {
            return 0;
        }
        return $query_result;
    }

    /** ******************* report functions ********************************** */

    public function case_count_by_scc( $template_type_id, $academic_year ) : array
    {
        $result = [];
        $query = $this->rpt_db->prepare("select LevelOneUnitName, LevelOneID, CaseTotal
from CasesByTypeYearSCC
where RptTemplateTypeID = %s and AcademicYear = %s",  $template_type_id, $academic_year);
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query, ARRAY_A) as $row) {
            $result[$row['LevelOneUnitName']] = $row;
        }
        return $result;
    }

    public function case_count_by_unit( $template_type_id, $academic_year, $level_1_id ) : array
    {
        $result = [];
        $query = $this->rpt_db->prepare("select UnitName, CaseTotal
from CasesByTypeYearUnit
where RptTemplateTypeID = %s and AcademicYear = %s and LevelOneID = %s",
            $template_type_id, $academic_year, $level_1_id);
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query, ARRAY_A) as $row) {
            $result[$row['UnitName']] = $row;
        }
        return $result;
    }

}