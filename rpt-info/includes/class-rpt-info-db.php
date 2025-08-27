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

    /** ******************* cycle & academic year functions ******************************** */

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

    public function get_academic_year_list( $add_zero = TRUE ) : array
    {
        if ( $add_zero ) {
            $result = array(
                '0' => 'N/A'
            );
        }
        else {
            $result = [];
        }
        $query = $this->rpt_db->prepare("SELECT ID, Display 
FROM AcademicYear where YearStatus = 'Past'");
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query) as $row) {
            $result[$row->ID] = $row->Display;
        }
        return $result;
    }

    public function get_rpt_cycle_list() : array
    {
        $result = [];
        $query = "SELECT AcademicYear, Display, YearStatus, EffectiveDate09Month, EffectiveDate12Month, 
MandatoryDueDate, NonMandatoryDueDate, LibrarianDueDate, PromotionSubmissionStartDate, 
PromotionSubmissionEndDate, SabbaticalCompLimit, SabbaticalSubmissionStartDate, 
SabbaticalSubmissionEndDate,PromotionSubbmissionAllowed, SabbaticalSubmissionAllowed FROM RptCycleDetails;";
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query) as $row) {
            $result[$row->AcademicYear] = new Rpt_Info_Cycle($row);
        }
        return $result;
    }

    public function get_cycle_allowances() : array
    {
        $result = [];
        $query = "select sa.AcademicYear, ay.Display, sa.UWODSUnitKey, sa.QuartersAllowed 
from SabbaticalAllowances sa join AcademicYear ay on ay.ID = sa.AcademicYear order by sa.AcademicYear";
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query) as $row) {
            if ( array_key_exists($row->AcademicYear, $result) ) {
                $result[$row->AcademicYear][$row->UWODSUnitKey] = $row->QuartersAllowed;
            }
            else {
                $result[$row->AcademicYear] = array(
                    'Display' => $row->Display,
                    $row->UWODSUnitKey => $row->QuartersAllowed
                );
            }
        }
        return $result;
    }

    public function get_active_cycle( int $template_type ) : ?Rpt_Info_Cycle
    {
        $result = NULL;
        switch ($template_type) {
            case 2 :
                $query = "";
        }
        return $result;
    }

    public function update_cycle_settings(int $AcademicYear, array $update_values) : int
    {
        $query_result = $this->rpt_db->update('RptCycle', $update_values,
            array('AcademicYear' => $AcademicYear));
        $this->last_query = $this->rpt_db->last_error;
//        echo $this->last_query; exit;
        if ( $query_result === FALSE ) {
            return 0;
        }
        return $query_result;
    }

    public function update_sabbatical_allowances(int $AcademicYear, array $update_values) : int
    {
        $result = 0;
        foreach ($update_values as $unit => $allowance) {
            $query_result = $this->rpt_db->update('SabbaticalAllowances',
                array('QuartersAllowed' => $allowance),
                array('AcademicYear' => $AcademicYear, 'UWODSUnitKey' => $unit));
            if ( $query_result === FALSE ) {
                return 0;
            }
            else {
                $result += $query_result;
            }
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
        $query = "SELECT CaseID, RptCaseID, RptTemplateID, CandidateID, EmployeeID, RptTemplateTypeID, CaseStatus, 
InterfolioUnitID, AcademicYear, WorkflowStepNumber, WorkflowStepName, TemplateName, CoverSheetID, 
LegalName, InitiatorID, InitiatorName, CandidateKey, UWODSAppointmentTrackKey, AppointmentType, 
TrackTypeName, UWODSUnitKey, UnitName, CurrentRankKey, CurrentRankName, TargetRankKey, TargetRankName, 
DueDate, RankCategory, ParentID, ParentUnitName, LevelOneID, LevelOneUnitName, PromotionCategoryID, 
PromotionCategoryName, ServicePeriod, EffectiveDate, HasJoint, HasSecondary, SubcommitteeMembers, 
DatasheetID, Postponed, TenureAward, NewTermLength, Vote1Eligible, Vote1Affirmative, Vote1Negative, 
Vote1Absent, Vote1Abstaining, Vote2Eligible, Vote2Affirmative, Vote2Negative, Vote2Absent, 
Vote2Abstaining, DatasheetID, DataSheetStatus, TargetTrackTypeName, TargetRankDefaultTerm, TargetRankTenured, 
Postponed, CaseStatusID, CoverSheetStatus
FROM RptPromotionDetails where InterfolioUnitID in ("
            . implode(',', array_keys($user_obj->Units)) . ") or  ParentID in ("
            . implode(',', array_keys($user_obj->Units)) . ") or LevelOneID in ("
            . implode(',', array_keys($user_obj->Units)) . ") or '28343' in ("
            . implode(',', array_keys($user_obj->Units)) . ")";
        $this->last_query = $query;
//        echo $this->last_query;
        foreach ($this->rpt_db->get_results($query) as $row) {
//            echo '<pre>' . print_r($row, true) . '</pre>'; exit;
            $result[$row->CaseID] = new Rpt_Info_Promotion($row);
        }
        return $result;
    }

    public function get_sabbatical_cases_for_user( Rpt_Info_User $user_obj ) : array
    {
        $result = [];
        $query = "SELECT CaseID, RptCaseID, RptTemplateID, AcademicYear, TemplateName, 
RptTemplateTypeID, TemplateTypeName, CandidateID, CaseDataSectionID, ConcurrenceLetterSection, 
ConcurrenceLetterCount, SubcommitteeReviewStep, SubcommitteeMembers, LegalName, FirstName, LastName, 
UWNetID, CandidateEmail, EmployeeID, InitiatorID, InitiatorName, CandidateKey, UWODSAppointmentTrackKey, 
AppointmentType, UWODSUnitKey, InterfolioUnitID, UnitName, ParentID, ParentUnitName, LevelOneID, 
LevelOneUnitName, CurrentRankKey, CurrentRankName, RankCategory, TrackTypeName, ServicePeriod, 
DueDate, AppointmentStartDate, AppointmentEndDate, HasJoint, HasSecondary, CaseStatus, RptStatus, 
WorkflowStepNumber, WorkflowStepName, CoverSheetStatus, CoverSheetID, DataSheetID, DataSheetStatus, 
SummerQtr, FallQtr, WinterQtr, SpringQtr, SalarySupportPct, RosterPct, MonthlySalary, TenureAmount, 
HireDate, TrackStartDate, AppointmentStartDate, LastSabbaticalAcademicYear, ContingentOnExtension, 
MultiYear, EligibilityReport, EligibilityNote, HireDate
FROM RptSabbaticalDetails where InterfolioUnitID in ("
            . implode(',', array_keys($user_obj->Units)) . ") or  ParentID in ("
            . implode(',', array_keys($user_obj->Units)) . ") or LevelOneID in ("
            . implode(',', array_keys($user_obj->Units)) . ") or '28343' in ("
            . implode(',', array_keys($user_obj->Units)) . ")";
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query) as $row) {
            $result[$row->CaseID] = new Rpt_Info_Sabbatical($row);
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

    public function get_promotion_case_for_candidate(int $track_id) : ?Rpt_Info_Promotion
    {
        $result = NULL;
        $query = $this->rpt_db->prepare("SELECT CaseID, RptCaseID, RptTemplateID, CandidateID, EmployeeID,
CaseStatus, InterfolioUnitID, AcademicYear, WorkflowStepNumber, WorkflowStepName, TemplateName, CoverSheetID,
LegalName, InitiatorID, InitiatorName, CandidateKey, UWODSAppointmentTrackKey, AppointmentType, TrackTypeName,
UWODSUnitKey, UnitName, CurrentRankKey, CurrentRankName, TargetRankKey, TargetRankName, DueDate, RankCategory,
ParentID, ParentUnitName, LevelOneID, LevelOneUnitName, PromotionCategoryID, PromotionCategoryName, ServicePeriod,
EffectiveDate, HasJoint, HasSecondary, SubcommitteeMembers, DataSheetID, Postponed, TenureAward, NewTermLength, 
Vote1Eligible, Vote1Affirmative, Vote1Negative, Vote1Absent, Vote1Abstaining, Vote2Eligible, Vote2Affirmative, 
Vote2Negative, Vote2Absent, Vote2Abstaining, DataSheetID, TargetTrackTypeName, TargetRankDefaultTerm,
TargetRankTenured, Postponed, RptTemplateTypeID, Leaves, Waivers, CoverSheetStatus, DataSheetStatus, CandidateKey
FROM RptPromotionDetails where UWODSAppointmentTrackKey = %", $track_id);
        $this->last_query = $query;
        $result_row = $this->rpt_db->get_row($query);
        if ( $result_row ) {
            $result = new Rpt_Info_Promotion($result_row);
        }
        return $result;
    }

    public function get_sabbatical_case_for_candidate(int $track_id) : ?Rpt_Info_Sabbatical
    {
        $result = NULL;
        $query = $this->rpt_db->prepare("SELECT CaseID, RptCaseID, RptTemplateID, AcademicYear, TemplateName, 
RptTemplateTypeID, TemplateTypeName, CandidateID, CaseDataSectionID, ConcurrenceLetterSection, 
ConcurrenceLetterCount, SubcommitteeReviewStep, SubcommitteeMembers, LegalName, FirstName, LastName, 
UWNetID, CandidateEmail, EmployeeID, InitiatorID, InitiatorName, CandidateKey, UWODSAppointmentTrackKey, 
AppointmentType, UWODSUnitKey, InterfolioUnitID, UnitName, ParentID, ParentUnitName, LevelOneID, 
LevelOneUnitName, CurrentRankKey, CurrentRankName, RankCategory, TrackTypeName, ServicePeriod, 
DueDate, AppointmentStartDate, AppointmentEndDate, HasJoint, HasSecondary, CaseStatus, RptStatus, 
WorkflowStepNumber, WorkflowStepName, CoverSheetStatus, CoverSheetID, DataSheetID, DataSheetStatus, 
SummerQtr, FallQtr, WinterQtr, SpringQtr, SalarySupportPct, RosterPct, MonthlySalary, TenureAmount, 
HireDate, TrackStartDate, AppointmentStartDate, LastSabbaticalAcademicYear, ContingentOnExtension, 
MultiYear, EligibilityReport, EligibilityNote, HireDate, CandidateKey
FROM RptSabbaticalDetails where UWODSAppointmentTrackKey = %",
            $track_id);
        $this->last_query = $query;
        $result_row = $this->rpt_db->get_row($query);
        if ( $result_row ) {
            $result = new Rpt_Info_Sabbatical($result_row);
        }
        return $result;
    }

    public function get_promotion_from_track(int $track_id) : ?Rpt_Info_Promotion
    {
        $result = NULL;
        $query = $this->rpt_db->prepare("SELECT '0' CaseID, '0' RptCaseID, '0' RptTemplateID, 
'0' CandidateID, EmployeeID, '0' CaseStatusID, CaseStatus, InterfolioUnitID, '0' AcademicYear, 
'0' WorkflowStepNumber, '' WorkflowStepName, '' TemplateName, '0' CoverSheetID, LegalName, 
'0' InitiatorID, '' InitiatorName, UWODSPersonKey CandidateKey, UWODSAppointmentTrackKey, 
AppointmentType, TrackTypeName, UWODSUnitKey, UnitName, UWODSRankKey CurrentRankKey, 
RankName CurrentRankName, '0' TargetRankKey, '' TargetRankName, RankCategory, ParentID, 
'' ParentUnitName, Level1InterfolioUnitID LevelOneID, Level1UnitName LevelOneUnitName, 
PromotionCategoryID, PromotionCategoryName, ServicePeriod, NULL EffectiveDate, 'No' HasJoint, 
'No' HasSecondary, '' SubcommitteeMembers, '0' DatasheetID, '2' RptTemplateTypeID
FROM CurrentPromotable where UWODSAppointmentTrackKey = %s", $track_id);
        $this->last_query = $query;
        $result_row = $this->rpt_db->get_row($query);
        if ( $result_row ) {
            $result = new Rpt_Info_Promotion($result_row);
        }
        return $result;
    }

    public function get_sabbatical_from_track(int $track_id) : ?Rpt_Info_Sabbatical
    {
        $result = NULL;
        $query = $this->rpt_db->prepare("SELECT '0' CaseID, '0' RptCaseID, '0' RptTemplateID, 
'0' AcademicYear, '' TemplateName, AppointmentStartDate, AppointmentEndDate,
'5' RptTemplateTypeID, 'Sabbatical' TemplateTypeName, InterfolioUserID CandidateID, 
'0' CaseDataSectionID, LegalName, UWNetID, EmployeeID, '0' InitiatorID, '' InitiatorName,
UWODSPersonKey CandidateKey,UWODSAppointmentTrackKey, AppointmentType, UWODSUnitKey, TenureAmount,
InterfolioUnitID, UnitName, ParentID, ParentUnitName, LevelOneID, LevelOneUnitName, IsTenured,
UWODSRankKey CurrentRankKey, RankName CurrentRankName, RankCategory, TrackTypeName, ServicePeriod, 
'N/A' CaseStatus, 'No' SummerQtr, 'No' FallQtr, 'No' WinterQtr, 'No' SpringQtr, '' SalarySupportPct,
RosterPct, '' MonthlySalary, '' EligibilityReport, '' EligibilityNote, 'No' MultiYear,
AppointmentStartDate, AppointmentEndDate, TotalBasePayAmt MonthlySalary, HireDate
from CurrentSabbaticalEligible
where UWODSAppointmentTrackKey = %s", $track_id);
        $this->last_query = $query;
//        echo $this->last_query; exit;
        $result_row = $this->rpt_db->get_row($query);
//        echo '<pre>' . print_r( $result_row, true ) . '</pre>';
        if ( $result_row ) {
            $result = new Rpt_Info_Sabbatical($result_row);
        }
        return $result;
    }

    public function get_promotion_by_id(int $case_id) : ?Rpt_Info_Promotion
    {
        $result = NULL;
        $query = $this->rpt_db->prepare("SELECT CaseID, RptCaseID, RptTemplateID, CandidateID, EmployeeID,
CaseStatus, InterfolioUnitID, AcademicYear, WorkflowStepNumber, WorkflowStepName, TemplateName, CoverSheetID,
LegalName, InitiatorID, InitiatorName, CandidateKey, UWODSAppointmentTrackKey, AppointmentType, TrackTypeName,
UWODSUnitKey, UnitName, CurrentRankKey, CurrentRankName, TargetRankKey, TargetRankName, DueDate, RankCategory,
ParentID, ParentUnitName, LevelOneID, LevelOneUnitName, PromotionCategoryID, PromotionCategoryName, ServicePeriod,
EffectiveDate, HasJoint, HasSecondary, SubcommitteeMembers, DataSheetID, Postponed, TenureAward, NewTermLength, 
Vote1Eligible, Vote1Affirmative, Vote1Negative, Vote1Absent, Vote1Abstaining, Vote2Eligible, Vote2Affirmative, 
Vote2Negative, Vote2Absent, Vote2Abstaining, DataSheetID, TargetTrackTypeName, TargetRankDefaultTerm, CaseStatusID,
TargetRankTenured, Postponed, RptTemplateTypeID, Leaves, Waivers, CoverSheetStatus, DataSheetStatus, CandidateKey
FROM RptPromotionDetails where CaseID = %s", $case_id);
        $this->last_query = $query;
        $result_row = $this->rpt_db->get_row($query);
//        echo '<pre>' . print_r( $result_row, true ) . '</pre>';
        if ( $result_row ) {
            $result = new Rpt_Info_Promotion($result_row);
        }
        return $result;
    }

    public function get_sabbatical_by_id(int $case_id) : ?Rpt_Info_Sabbatical
    {
        $result = NULL;
        $query = $this->rpt_db->prepare("SELECT CaseID, RptCaseID, RptTemplateID, AcademicYear, TemplateName, 
RptTemplateTypeID, TemplateTypeName, CandidateID, CaseDataSectionID, ConcurrenceLetterSection, 
ConcurrenceLetterCount, SubcommitteeReviewStep, SubcommitteeMembers, LegalName, FirstName, LastName, 
UWNetID, CandidateEmail, EmployeeID, InitiatorID, InitiatorName, CandidateKey, UWODSAppointmentTrackKey, 
AppointmentType, UWODSUnitKey, InterfolioUnitID, UnitName, ParentID, ParentUnitName, LevelOneID, 
LevelOneUnitName, CurrentRankKey, CurrentRankName, RankCategory, TrackTypeName, ServicePeriod, 
DueDate, AppointmentStartDate, AppointmentEndDate, HasJoint, HasSecondary, CaseStatus, RptStatus, 
WorkflowStepNumber, WorkflowStepName, CoverSheetStatus, CoverSheetID, DataSheetID, DataSheetStatus, 
SummerQtr, FallQtr, WinterQtr, SpringQtr, SalarySupportPct, RosterPct, MonthlySalary, TenureAmount, 
HireDate, TrackStartDate, AppointmentStartDate, LastSabbaticalAcademicYear, ContingentOnExtension, 
MultiYear, EligibilityReport, EligibilityNote, HireDate, CandidateKey
FROM RptSabbaticalDetails where CaseID = %s", $case_id);
        $this->last_query = $query;
//        echo $this->last_query; exit;
        $result_row = $this->rpt_db->get_row($query);
        if ( $result_row ) {
            $result = new Rpt_Info_Sabbatical($result_row);
        }
        return $result;
    }

    public function get_other_appointments( Rpt_Info_Case $case_obj ) : void
    {
        $query = $this->rpt_db->prepare("select distinct UWODSAppointmentTrackKey, UWODSAppointmentKey, UWODSUnitKey, 
    UnitName, UWODSRankKey, RankName, AppointmentType from PersonAppointmentDetails 
    where UWODSPersonKey = %s and UWODSAppointmentTrackKey != %s and IsActive = 'Yes'
    and AppointmentType in ('Primary','Joint','Secondary')",
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
        if ( $case_obj->CaseID ) {
            // now insert the promotion or sabbatical record
            switch ($case_obj->RptTemplateTypeID) {
                case '2' :
                    $query_result = $this->rpt_db->insert('RptPromotion', $case_obj->insert_promotion_array());
//                echo $this->rpt_db->last_query; exit;
                    break;
                case '5' :
                    $query_result = $this->rpt_db->insert('RptSabbatical', $case_obj->insert_sabbatical_array());
                    break;
            }
        }
        else {
            return 0;
        }
        if ( $query_result === FALSE ) {
            return 0;
        }
        return $query_result;
    }

    public function update_case( Rpt_Info_Promotion|Rpt_Info_Sabbatical $case_obj, $update_type = 'case' )
    {
        if ( $update_type == 'case' ) {
            // first update the base case record
            $query_result = $this->rpt_db->update('RptCase', $case_obj->update_case_array(),
                array('ID' => $case_obj->CaseID));
            // then update to promotion or sabbatical record
            switch ($case_obj->RptTemplateTypeID) {
                case '2' :
                    $query_result = $this->rpt_db->update('RptPromotion', $case_obj->update_promotion_array(),
                        array('CaseID' => $case_obj->CaseID));
                    break;
                case '5' :
                    // TODO: add sabbatical update function
                    break;
            }
        }
        elseif ( $update_type == 'datasheet' ) {
            $query_result = $this->rpt_db->update('RptCase', $case_obj->update_case_data_sheet(),
                array('ID' => $case_obj->CaseID));
            switch ($case_obj->RptTemplateTypeID) {
                case '2' :
                    $query_result = $this->rpt_db->update('RptPromotion', $case_obj->update_data_sheet_array(),
                        array('CaseID' => $case_obj->CaseID));
                    break;
                case '5' :
                    // TODO: add sabbatical update function
                    break;
            }
        }
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

    /** ******************* template functions ********************************** */

    public function get_template_list($template_type_id, $unit_type ) : array
    {
        $result = [];
        switch ( $unit_type ) {
            case 'all':
                $query = $this->rpt_db->prepare("SELECT RptTemplateID, InterfolioUnitID, UnitName, ParentID, ParentName, 
LevelOneID, LevelOneName, TemplateName, Description, IsPublished, InUse, RptTemplateTypeID, UnitType,
TemplateTypeName, TemplateTypeInUse FROM RptTemplateDetails where IsPublished = 'Yes' and RptTemplateTypeID = %s
order by TemplateName", $template_type_id);
                break;
            case 'dep' :
                $query = $this->rpt_db->prepare("SELECT RptTemplateID, InterfolioUnitID, UnitName, ParentID, ParentName, 
LevelOneID, LevelOneName, TemplateName, Description, IsPublished, InUse, RptTemplateTypeID, UnitType,
TemplateTypeName, TemplateTypeInUse FROM RptTemplateDetails where IsPublished = 'Yes' and RptTemplateTypeID = %s
and UnitType = 'dep' and TemplateName like '%\_dep%' order by TemplateName", $template_type_id);
                break;
            case 'undep' :
                $query = $this->rpt_db->prepare("SELECT RptTemplateID, InterfolioUnitID, UnitName, ParentID, ParentName, 
LevelOneID, LevelOneName, TemplateName, Description, IsPublished, InUse, RptTemplateTypeID, UnitType, 
TemplateTypeName, TemplateTypeInUse FROM RptTemplateDetails where IsPublished = 'Yes' and RptTemplateTypeID = %s
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

    /** ******************* person (candidate) functions ********************************** */

    public function get_candidate_leaves( Rpt_Info_Case $case_obj ) : void
    {
        $case_obj->PreviousLeaves = [];
        $display = [];
        $query = $this->rpt_db->prepare("select StartDate, EndDate, LeaveTypeName from PersonLeaveDetails
where UWODSPersonKey = %s", $case_obj->CandidateKey);
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query) as $row) {
            $case_obj->PreviousLeaves[] = $row;
            $display[] = rpt_format_date($row->StartDate) . '&mdash;' . rpt_format_date($row->EndDate)
                . ': ' . $row->LeaveTypeName;
        }
        if ( count($case_obj->PreviousLeaves) > 0 ) {
            $case_obj->Leaves = implode("\n", $display);
        }
        else {
            $case_obj->Leaves = '';
        }
    }

    public function get_candidate_waivers( Rpt_Info_Case $case_obj ) : void
    {
        $case_obj->PreviousWaivers = [];
        $display = [];
        $query = $this->rpt_db->prepare("select AcademicYear, WaiverReason from PersonWaiverDetails
where UWODSPersonKey = %s", $case_obj->CandidateKey);
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query) as $row) {
            $case_obj->PreviousWaivers[] = $row;
            $display[] = $row->AcademicYear . ': ' . $row->WaiverReason;
        }
        if ( count($case_obj->PreviousWaivers) > 0 ) {
            $case_obj->Waivers = implode("\n", $display);
        }
        else {
            $case_obj->Waivers = '';
        }
    }

    public function check_for_postponement( Rpt_Info_Promotion $case_obj ) : void
    {
        $query = $this->rpt_db->prepare("select UWODSRankKey, PromotionOutcomeID 
from PreviousPromotionDetails
where UWODSAppointmentTrackKey = %s", $case_obj->UWODSAppointmentTrackKey);
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query, ARRAY_A) as $row) {
            if ( ( $row['PromotionOutcomeID'] == '1' )
                    && ( $row['UWODSRankKey'] == $case_obj->CurrentRankKey ) ) {
                $case_obj->Postponed = 'Yes';
            }
        }
        $case_obj->Postponed = 'No';
    }

    /** ******************* miscellaneous functions ********************************** */

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

    public function get_level_1_unit_list()
    {
        $result = [];
        $query = "select UWODSUnitKey, UnitName from InterfolioUnit  where UnitLevel = 1 order by UnitName";
        $this->last_query = $query;
        foreach ($this->rpt_db->get_results($query) as $row) {
            $result[$row->UWODSUnitKey] = $row->UnitName;
        }
        return $result;
    }

}