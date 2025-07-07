<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://ap.washington.edu
 * @since      1.0.0
 *
 * @package    Rpt_Info
 * @subpackage Rpt_Info/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Rpt_Info
 * @subpackage Rpt_Info/public
 * @author     Jon Davis <jld36@uw.edu>
 */
class Rpt_Info_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    private Rpt_Info_DB $rpt_db;

    private $wordpress_user;
    private Rpt_Info_User $rpt_user;

    private $template_types = [];

    private $active_page = 'home';

    private $active_template_type = '';
    private Rpt_Info_Cycle $current_cycle;

    private $rpt_case_review_url = '';

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $db_name = get_option('rpt_info_database_name');
        if ( $db_name) {
            $this->rpt_db = new Rpt_Info_DB($db_name);
        }
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Rpt_Info_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Rpt_Info_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/rpt-info-public.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Rpt_Info_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Rpt_Info_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/rpt-info-public.js', array('jquery'), $this->version, false);

    }

    /**
     * Registers all shortcodes at once
     *
     * @return [type] [description]
     */
    public function register_shortcodes()
    {
        add_shortcode('rptinfo_home', array($this, 'rptinfo_home'));
    }

    /**
     * add_query_vars
     *      set up URL query items
     *
     * @param $vars
     * @return mixed
     */
    public function add_query_vars($vars)
    {
        $vars[] = 'ay'; // academic year
        $vars[] = 'template_type'; // sub-page within main
        $vars[] = 'rpt_page'; // which plugin page to display
        $vars[] = 'status'; // status to pass to next page
        $vars[] = 'msg'; // message to pass to next page
        $vars[] = 'case_id'; // case/packet id
        $vars[] = 'candidate_id'; // candidate id
        $vars[] = 'track_id'; // appointment track id
        $vars[] = 'template_id'; // template id
        $vars[] = 'unit_type'; // dep/undep
        $vars[] = 'unit_id'; // unit id
        $vars[] = 'template_id'; // template id
        $vars[] = 'report_type'; // report name/slug
        return $vars;
    }

    /**
     * force_login
     *      check to see if there is a logged in user
     *      if not, redirect to login
     *
     * @return void
     */
    private function force_login()
    {
        if ( ! is_user_logged_in() ) {
            auth_redirect();
        }
        $this->wordpress_user = wp_get_current_user();
    }

    /**
     * show_status_message
     *      display a status message (alert) for result of previous action
     *
     * @param $status_type
     * @param $status_message
     * @return void
     */
    private function show_status_message( $status_type, $status_message )
    {
        echo '<div class="alert alert-' . $status_type . '" role="alert">';
        echo $status_message;
        echo '</div>';
    }

    /**
     * show_menu
     *      display the plugin's system menu
     *
     * @param $active_page
     * @return void
     */
    private function show_main_menu()
    {
        global $wp;
        echo '<div class="row pt-2 pb-2">';
        echo '<div class="col-12">';
        echo '<div class="toolbar" role="toolbar" aria-label="Application navigation">';
        echo '<div class="btn-group mr-2" role="group" aria-label="Main pages">';
        echo '<a href="' . esc_url(add_query_arg(array('rpt_page' => 'home',
                'ay' => $this->current_cycle->AcademicYear,
                'template_type' => '0'), home_url($wp->request)))
            . '" class="btn ';
        if ( $this->active_page == 'home' ) {
            echo ' active btn-primary';
        }
        else {
            echo ' btn-outline-secondary';
        }
        echo '">RPT Home</a>';
        foreach ($this->template_types as $id => $template_type) {
            echo '<a href="' . esc_url(add_query_arg(array('template_type' => $id,
                    'ay' => $this->current_cycle->AcademicYear,
                    'rpt_page' => 'home'), home_url($wp->request)))
                . '" class="btn ';
            if ( $id == $this->active_template_type ) {
                echo ' active btn-primary';
            }
            else {
                echo ' btn-outline-secondary';
            }
            echo '">' . $template_type->TemplateTypeName . '</a>';
        }
        echo '</div>'; // button group
        echo '</div>'; // toolbar
        if ( $this->active_template_type != '0' ) {
            echo '<div class="toolbar" role="toolbar" aria-label="Application navigation">';
            echo '<div class="btn-group mr-2" role="group" aria-label="Area sub-pages">';
            echo '<a href="' . esc_url(add_query_arg(array('template_type' => $this->active_template_type,
                    'ay' => $this->current_cycle->AcademicYear,
                    'rpt_page' => 'case'), home_url($wp->request)))
                . '" class="btn btn-outline-secondary';
            echo '">Cases</a>';
            echo '<a href="' . esc_url(add_query_arg(array('template_type' => $this->active_template_type,
                    'ay' => $this->current_cycle->AcademicYear,
                    'rpt_page' => 'template'), home_url($wp->request)))
                . '" class="btn btn-outline-secondary';
            echo '">Templates</a>';
            echo '<a href="' . esc_url(add_query_arg(array('template_type' => $this->active_template_type,
                    'ay' => $this->current_cycle->AcademicYear,
                    'rpt_page' => 'report'), home_url($wp->request)))
                . '" class="btn btn-outline-secondary';
            echo '">Reports</a>';
            echo '<a href="' . esc_url(add_query_arg(array('template_type' => $this->active_template_type,
                    'ay' => $this->current_cycle->AcademicYear,
                    'rpt_page' => 'admin'), home_url($wp->request)))
                . '" class="btn btn-outline-secondary';
            echo '">Admin</a>';
            echo '</div>'; // button group
            echo '</div>'; // toolbar
        }
        echo '</div>'; // col 12
        echo '</div>'; // row
    }

    /**
     * rptinfo_home
     *      display plugin home page
     *      from shortcode rptinfo_home
     *
     * @return void
     */
    public function rptinfo_home()
    {
        $this->force_login();
        ob_start();
//        echo 'wtf?';
        $ay = get_query_var('ay', '2026');
        $status_type = get_query_var('status', '');
        $status_message = get_query_var('msg', '');
        $this->active_page = get_query_var('rpt_page', 'home');
        $this->active_template_type = get_query_var('template_type', '0');
        $case_id = get_query_var('case_id', '0');
        if ($status_message) {
            $this->show_status_message($status_type, $status_message);
        }
        if ( isset($this->rpt_db) ) {
            $this->rpt_user = $this->rpt_db->get_rpt_user_info($this->wordpress_user->user_login);
            $this->current_cycle = $this->rpt_db->get_cycle_info($ay);
            $this->template_types = $this->rpt_db->get_template_type_list(TRUE);
            echo '<div class="row">';
            echo '<div class="col-12">';
            $this->show_main_menu();
            echo '</div>'; // col 12
            echo '</div>'; // row
            switch ($this->active_template_type) {
                case '2' : // promotions
                case '5': // sabbaticals
                    switch ($this->active_page) {
                        case 'case':
                            $this->case_page();
                            break;
                        case 'template':
                            $this->template_page();
                            break;
                        case 'report':
                            $this->report_page();
                            break;
                        case 'admin':
                            $this->admin_page();
                            break;
                        default:
                            $this->home_page();
                            break;
                    }
                    break;
                case '0':
                default:
                    $this->home_page();
                    break;
            }
        }
        else {
            echo '<p>Database not defined</p>';
        }
        $this->show_footer();
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    private function home_page()
    {
        echo '<div class="row">';
        echo '<div class="col-12">';
        switch ( $this->active_template_type) {
            case '0' :
                echo '<p>This system provides an interface to certain functions of the Interfolio RPT system.</p>';
                echo "<p>Select the type of function you need from the menu above, and from there you can
                choose among the available actions and reports.</p>";
                break;
            case '2' :
                echo "<p>Functions dealing with Promotions and Tenure.</p>";
                echo "<p>Select the type of function you need from the menu above, and from there you can
                choose among the available actions and reports.</p>";
                break;
            case '5' :
                echo "<p>Functions dealing with Sabbaticals.</p>";
                echo "<p>Select the type of function you need from the menu above, and from there you can
                choose among the available actions and reports.</p>";
                break;
        }
        echo '</div>'; // col 12
        echo '</div>'; // row
    }

    /**
     * show_footer
     *      info block at bottom of page
     *
     * @return void
     */
    private function show_footer()
    {
        echo '<div class="row">';
        echo '<div class="col-8">';
        echo '<div class="card">';
        echo '<div class="card-body">';
//        echo '<pre>' . print_r($this->rpt_user, TRUE) . '</pre>';
        echo '<small>';
        echo '<p>';
        echo 'Wordpress user: ' . $this->wordpress_user->user_login . '<br>';
        echo 'Logged in as: ' . $this->rpt_user->DisplayName . ' (' . $this->rpt_user->UWNetID
            . ' &mdash; ' . $this->rpt_user->InterfolioUserID . ')<br>';
        echo 'Access to: ' . $this->rpt_user->display_units() . '<br>';
        echo 'Plugin version: ' . $this->version . '<br>';
        echo 'Selected Academic Year: ' . $this->current_cycle->Display . '<br>';
        echo 'Template Type: ' . $this->active_template_type . '<br>';
        echo 'Page: ' . $this->active_page . '<br>';
//        echo 'Case ID: ' . $case_id . '<br>';
        echo '</p>';
//        echo '<pre>' . print_r($this->rpt_user, TRUE) . '</pre>';
        echo '</div>'; // card body
        echo '</div>'; // card
        echo '</div>'; // col 8
        echo '</div>'; // row
    }

    /* ********************** functions dealing with cases ********************** */

    private function case_page()
    {
        echo '<p>' . $this->template_types[$this->active_template_type]->TemplateTypeName
            . ' Case maintenance page</p>';
        $case_id = get_query_var('case_id', '0');
        $candidate_id = get_query_var('candidate_id', '0');
        $track_id = get_query_var('track_id', '0');
        if ( ( $case_id == '0' ) && ( $track_id == '0' ) ) {
            $this->case_list();
        }
        elseif ( $case_id == 'new' ) {
            $this->search_form();
        }
        elseif ( $track_id > '0' ) {
            $this->case_edit($case_id, $track_id);
        }
        elseif ( $case_id != 'new' ) {
            $this->case_display($case_id);
        }
    }

    private function case_home()
    {
        echo '<div class="row">';
        echo '<div class="col-12">';
        echo '<p>This system provides an interface to certain functions of the Interfolio RPT system.</p>';
        echo "<p>Select the type of function you need from the menu above, and from there you can
                choose among the available actions and reports.</p>";
        echo '</div>'; // col 12
        echo '</div>'; // row
    }

    private function case_list()
    {
        global $wp;
        echo '<p>Case list page</p>';
        $this->rpt_case_review_url = '';
        $rpt_case_url = get_option('rpt_info_rpt_site_url') . '/'
            . get_option('rpt_info_tenant_id') . '/cases';
//        $this->rpt_case_review_url = get_option('ap_rptinfo_rpt_case_review_url');
        $case_list = [];
        switch ( $this->active_template_type) {
            case '2': // promotion
                $case_list = $this->rpt_db->get_promotion_cases_for_user($this->rpt_user);
                break;
            case '5': // sabbatical
                // $case_list = $this->rpt_db->get_cycle_info($this->rpt_user);
                break;
        }
        echo '<div class="row">';
        echo '<div class="col-12">';
        echo '<p>This page is where RPT ' . $this->template_types[$this->active_template_type]->TemplateTypeName
            . ' cases can be initiated and tracked, and where data sheets '
            . 'can be created. To initiate a case, click on <strong>Initiate a new case</strong> and follow the '
            . "instructions on the next page. To create a data sheet, locate the candidate's name "
            . 'below and click on the <strong>Data Sheet</strong> button. For an overview of the RPT '
            . 'case review step instructions, see <a href="' . $this->rpt_case_review_url
            . '" alt="RPT case review instructions">2025-26 Promotion and Tenure Cycle</a>.</p>';
        echo '</div>'; // col 12
        echo '</div>'; // row
        echo '<div class="row">';
        echo '<div class="col-6">';
        echo '<p>Current cases: (' . count($case_list) . ' found)</p>';
        echo '</div>'; // col 6
        echo '<div class="col-6 text-right">';
        echo '<a href="'
            . esc_url(add_query_arg(array('rpt_page' => 'case', 'case_id' => 'new',
                'ay' => $this->current_cycle->AcademicYear,
                'template_type' => $this->active_template_type), home_url($wp->request)))
            . '" class="btn btn-primary">Initiate a new case</a>';
        echo '</div>'; // col 6
        echo '</div>'; // row
        echo '<div class="row">';
        echo '<div class="col-12">';
        if ( count( $case_list ) > 0 ) {
//            echo '<pre>' . print_r( $case_list, true ) . '</pre>';
            echo '<table class="table table-border sort-table">';
            echo '<thead>';
            echo $this->case_list_header_row();
            echo '</thead>';
            echo '<tbody>';
            foreach ( $case_list as $case ) {
                echo $case->listing_table_row($rpt_case_url);
            }
            echo '</tbody>';
            echo '</table>';
        }
        else {
            echo '<p><em>None found.</em></p>';
        }
        echo '</div>'; // col 12
        echo '</div>'; // row
    }

    private function case_list_header_row() : string
    {
        $result = '<tr>';
        switch ( $this->active_template_type) {
            case '2':
                $result .= '<th>Candidate Name</th>';
                $result .= '<th>Type</th>';
                $result .= '<th>Status</th>';
                $result .= '<th>Action</th>';
                break;
            case '5':
                $result .= '<th>Candidate Name</th>';
                $result .= '<th>Quarters</th>';
                $result .= '<th>Status</th>';
                $result .= '<th>Action</th>';
                break;
        }
        $result .= '</tr>';
        return $result;
    }


    /**
     * search_form
     *      generate search form and set up ajax
     *
     * @return string
     */
    private function search_form()
    {
        global $wp;
        $search_nonce = wp_create_nonce( 'rpt_info_search' );
        $ajax_object = array('ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => $search_nonce,
            'template_type' => $this->active_template_type,
            'user_id' => $this->rpt_user->UWNetID,
            'init_url' => esc_url(add_query_arg(array('rpt_page' => 'case',
                'ay' => $this->current_cycle->AcademicYear,
                'template_type' => $this->active_template_type,
                'track_id' => ''), home_url($wp->request))),
            'case_url' => esc_url(add_query_arg(array('rpt_page' => 'case',
                'ay' => $this->current_cycle->AcademicYear,
                'template_type' => $this->active_template_type,
                'case_id' => ''), home_url($wp->request)))
        );
//        echo print_r($ajax_object);
        add_action('wp_footer', function() use ($ajax_object){
            printf('<script type="text/javascript">let my_ajax_obj = %s</script>',
                json_encode($ajax_object));
        });
        echo '<div class="row">';
        echo '<div class="col-sm-12">';
        echo '<p>Before initiating a case, run R0722 (Academic Promotion/Tenure Data Issues) to identify any data'
            . ' issues for academic personnel in promotion-eligible ranks that need to be addressed prior to'
            . ' initiating a case.</p>';
        echo '<p>When you are ready to initiate a case, find the candidate below by searching their name or '
            . 'Employee ID. The search is constrained to candidates in promotable ranks in units you have access '
            . 'to. If a case is already initiated, you will see a link to the data sheet page for '
            . 'the candidate.</p>';
        echo '</div>';
        echo '</div>';
        echo '<div class="form-group">';
        echo '<label for="rptinfo_search">Search by name or Employee ID</label>';
        echo '<input id="rptinfo_search" name="rptinfo_search" type="text" class="form-control col-lg-4" 
                placeholder="Enter at least 3 characters to search">';
        echo '</div>';
        echo '<div id="rptinfo_search_results"></div>';
    }

    /**
     * rpt_info_candidate_search
     *      ajax  function for candidate search
     *
     * @return void
     */
    public function rpt_info_candidate_search()
    {
        global $wp;
        check_ajax_referer( 'rpt_info_search' );
        $search_string = sanitize_text_field($_POST['searchstring']);
        $user_netid = sanitize_text_field($_POST['user_id']);
        $user_obj = $this->rpt_db->get_rpt_user_info($user_netid);
        $unit_query = $this->rpt_db->get_last_query();
        $template_type = intval($_POST['template_type']);
        $search_result = [];
        switch ( $template_type ) {
            case '2':
                $search_result = $this->rpt_db->promotion_candidate_search($user_obj, $search_string);
                break;
            case '5':
                $search_result = $this->rpt_db->sabbatical_candidate_search($user_obj, $search_string);
                break;
        }
        $sql = '';
        $sql = $this->rpt_db->get_last_query();
        $response = [
            'status' => 'ok',
            'searchstring' => $search_string,
            'query' => $sql,
            'data' => $search_result
        ];
        wp_send_json($response);
    }

    private function case_display( $case_id )
    {
        global $wp;
        $case_obj = NULL;
        $rpt_case_url = get_option('rpt_info_rpt_site_url') . '/'
            . get_option('rpt_info_tenant_id') . '/cases';
        switch ( $this->active_template_type) {
            case '2': // promotion
                $case_obj = $this->rpt_db->get_promotion_by_id($case_id);
                break;
            case '5': // sabbatical
                //
                break;
        }
        $this->rpt_db->get_other_appointments($case_obj);
        $case_obj->set_calculated_values();
//        echo '<pre>' . print_r( $case_obj, true ) . '</pre>';
        echo '<div class="row">';
        echo '<div class="col-6">';
        echo $case_obj->candidate_info_card(FALSE);
        echo '</div>'; // col 6
        // template type specific fields in another card
        echo '<div class="col-6">';
        switch ( $case_obj->RptTemplateTypeID ) {
            case '2':
                echo $case_obj->promotion_info_card($rpt_case_url);
                break;
            case '5':
                break;
        }
        echo '</div>'; // col 6
        echo '</div>'; // row
        echo '<div class="row">';
        echo '<div class="col-6">';
        switch ( $case_obj->RptTemplateTypeID ) {
            case '2':
                echo $case_obj->data_sheet_card();
                break;
            case '5':
                break;
        }
        echo '</div>'; // col 6
        echo '<div class="col-6">';
        echo $case_obj->rpt_info_card();
        echo '</div>'; // col 6
        echo '</div>'; // row
    }

    /**
     * case_edit
     *      page/form for creating and editing case records
     *
     * @param $case_id
     * @param $candidate_id
     * @return void
     */
    private function case_edit( int $case_id = 0, int $track_id = 0)
    {
        global $wp;
        $case_obj = NULL;
        if ( ( $track_id ) && ( ! $case_id ) ) { // candidate but no case - see if there is one
            switch ( $this->active_template_type) {
                case '2': // promotion
                    $case_obj = $this->rpt_db->get_promotion_case_for_candidate($track_id);
                    break;
                case '5': // sabbatical
                    //
                    break;
            }
            if ( $case_obj ) { // case already exists
                $case_id = $case_obj->CaseID;
            }
            else { // initialize object with known info for candidate
                switch ( $this->active_template_type) {
                    case '2': // promotion
                        $case_obj = $this->rpt_db->get_promotion_from_track($track_id);
                        break;
                    case '5': // sabbatical
                        //
                        break;
                }
                $case_obj->InitiatorID = $this->rpt_user->InterfolioUserID;
            }
        }
        elseif ( $case_id ) { // incoming case id - just get it
            switch ( $this->active_template_type) {
                case '2': // promotion
                    $case_obj = $this->rpt_db->get_promotion_by_id($case_id);
                    break;
                case '5': // sabbatical
                    //
                    break;
            }
        }
//        echo '<pre>' . print_r( $case_obj, true ) . '</pre>';
        $this->rpt_db->get_other_appointments($case_obj);
        $case_obj->set_calculated_values();
        $this->case_form($case_obj);
    }

    private function case_form( Rpt_Info_Case $case_obj )
    {
        global $wp;
        echo'<div class="row">';
        echo '<div class="col-12">';
        if ( $case_obj ) {
            if ( $case_obj->InterfolioCaseID == '0')
            {
                echo '<p><strong>Initiating new case</strong></p>';
                echo '<p>Please complete this page and <strong>Submit</strong> the information to initiate '
                    . 'the creation of an RPT case for a candidate. The information from this page will '
                    . 'be added to the case for reference. For an overview of the RPT '
                    . 'case review process, see <a href="' . $this->rpt_case_review_url
                    . '" alt="RPT case review instructions">this guide</a></p>';
            }
            // display main case fields in card
            echo '<div class="row">';
            echo '<div class="col-6">';
            echo $case_obj->candidate_info_card(TRUE);
            echo '</div>'; // col 6
            // template type specific fields in another card
            echo '<div class="col-6">';
            switch ( $this->active_template_type ) {
                case '2':
                    $this->promotion_form( $case_obj );
                    break;
                case '5':
                    break;
            }
            echo '</div>'; // col 6
            echo '</div>'; // row
        }
        else {
            echo '<p><em>Error loading case info</em></p>';
        }
        echo '</div>'; // col 12
        echo '</div>'; // row
    }

    private function promotion_form( Rpt_Info_Case $case_obj )
    {
        global $wp;
        $target_rank_list = $this->rpt_db->get_valid_promotion_target_ranks($case_obj->CurrentRankKey);
        $promotion_type_list = $this->rpt_db->get_promotion_type_list($case_obj->RankCategory);
        $template_list = $this->rpt_db->get_valid_templates_for_case($case_obj);
        echo '<div class="card">';
        echo '<div class="card-body">';
        echo '<h4 class="card-title">Promotion information</h4>';
        echo '<p class="card-subtitle mb-2 text-muted">Please review these '
            . 'selections and update as needed.</p>';
        echo '<form id="rptinfo_promotion_form" name="rptinfo_promotion_form" action="'
            . esc_url(admin_url('admin-post.php'))
            . '" role="form" method="post" accept-charset="utf-8" class="rptinfo-form ">';
        echo rpt_form_hidden_field('action', 'process_rptinfo_case_edit');
        echo rpt_form_hidden_field('RptCaseID', $case_obj->RptCaseID);
        echo rpt_form_hidden_field('CaseID', $case_obj->CaseID);
        echo rpt_form_hidden_field('CandidateID', $case_obj->CandidateID);
        echo rpt_form_hidden_field('CandidateKey', $case_obj->CandidateKey);
        echo rpt_form_hidden_field('InitiatorID', $case_obj->InitiatorID);
        echo rpt_form_hidden_field('UWODSAppointmentTrackKey', $case_obj->UWODSAppointmentTrackKey);
        echo rpt_form_hidden_field('UWODSUnitKey', $case_obj->UWODSUnitKey);
        echo rpt_form_hidden_field('InterfolioUnitID', $case_obj->InterfolioUnitID);
        echo rpt_form_hidden_field('CurrentRankKey', $case_obj->CurrentRankKey);
        echo rpt_form_hidden_field('CaseStatus', $case_obj->CaseStatus);
        echo rpt_form_hidden_field('RptTemplateTypeID', $case_obj->RptTemplateTypeID);
        echo rpt_form_hidden_field('ay', $this->current_cycle->AcademicYear);
        echo rpt_form_target_rank_list('TargetRankKey', $case_obj->TargetRankKey,
            'Proposed rank', $target_rank_list, '', FALSE,
            'form-control', '','', TRUE);
        echo rpt_form_date_select('EffectiveDate',
            $case_obj->propose_effective_date($this->current_cycle),
            'Effective date', FALSE,
            'form-control', FALSE, FALSE);
        echo rpt_form_dropdown_list('PromotionCategoryID', $case_obj->PromotionCategoryID, 'Promotion type',
            $promotion_type_list, '', FALSE, 'form-control', '', '');
        echo rpt_template_select('RptTemplateID', $case_obj->RptTemplateID, 'RPT Template',
            $template_list, FALSE, 'form-control', ( count($template_list) > 1) ? 'Select...' : '',
            'Choose which template to use', TRUE);
        echo '<div class="form-goup row">';
        echo '<div class="col-12">';
        if ( count($template_list) > 0 ) {
            echo '<button type="submit" class="btn btn-primary" name="submit" value="submit">Submit</button>';
        }
        else {
            echo '<p><strong>No valid template found for candidate unit. Please make sure there is '
                . 'a template available before initiating a case.</strong></p>';
        }
        echo '<a href="' . esc_url(add_query_arg(array('rpt_page' => 'case'), home_url($wp->request)))
            . '" class="btn btn-outline-secondary">Cancel</a>';
        echo '</div>'; // form group row
        echo '</div>'; // col 12
        echo '</form>';
        echo '</div>'; // card body
        echo '</div>'; // card
    }

    private function sabbatical_form( Rpt_Info_Case $case_obj )
    {
        global $wp;
        $template_list = $this->rpt_db->get_valid_templates_for_case($case_obj);
        echo '<div class="card">';
        echo '<div class="card-body">';
        echo '<h4 class="card-title">Sabbatical information</h4>';
        echo '<p class="card-subtitle mb-2 text-muted">Please review these '
            . 'selections and update as needed.</p>';
        echo '<form id="rptinfo_promotion_form" name="rptinfo_promotion_form" action="'
            . esc_url(admin_url('admin-post.php'))
            . '" role="form" method="post" accept-charset="utf-8" class="rptinfo-form ">';
        echo rpt_form_hidden_field('action', 'process_rptinfo_case_edit');
        echo rpt_form_hidden_field('InterfolioCaseID', $case_obj->InterfolioCaseID);
        echo rpt_form_hidden_field('CaseID', $case_obj->CaseID);
        echo rpt_form_hidden_field('CandidateID', $case_obj->CandidateID);
        echo rpt_form_hidden_field('CandidateKey', $case_obj->CandidateKey);
        echo rpt_form_hidden_field('InitiatorID', $case_obj->InitiatorID);
        echo rpt_form_hidden_field('UWODSAppointmentTrackKey', $case_obj->UWODSAppointmentTrackKey);
        echo rpt_form_hidden_field('UWODSUnitKey', $case_obj->UWODSUnitKey);
        echo rpt_form_hidden_field('InterfolioUnitID', $case_obj->InterfolioUnitID);
        echo rpt_form_hidden_field('CurrentRankKey', $case_obj->CurrentRankKey);
        echo rpt_form_hidden_field('RptTemplateTypeID', $case_obj->RptTemplateTypeID);
        echo rpt_form_hidden_field('CaseStatus', $case_obj->CaseStatus);
        echo rpt_form_hidden_field('ay', $this->current_cycle->AcademicYear);
        echo rpt_template_select('InterfolioTemplateID', $case_obj->InterfolioTemplateID, 'RPT Template',
            $template_list, FALSE, 'form-control', ( count($template_list) > 1) ? 'Select...' : '',
            'Choose which template to use', TRUE);
        echo '<div class="form-goup row">';
        echo '<div class="col-12">';
        if ( count($template_list) > 0 ) {
            echo '<button type="submit" class="btn btn-primary" name="submit" value="submit">Submit</button>';
        }
        else {
            echo '<p><strong>No valid template found for candidate unit. Please make sure there is '
                . 'a template available before initiating a case.</strong></p>';
        }
        echo '<a href="' . esc_url(add_query_arg(array('rpt_page' => 'case'), home_url($wp->request)))
            . '" class="btn btn-outline-secondary">Cancel</a>';
        echo '</div>'; // form group row
        echo '</div>'; // col 12
        echo '</form>';
        echo '</div>'; // card body
        echo '</div>'; // card
    }

    /**
     * process_ptinfo_case_edit
     *      callback for submit button on case edit form
     *
     * @return void
     */
    public function process_rptinfo_case_edit()
    {
        global $wp;
        $update_values = [];
        $result_status = 'info';
        $result_message = 'No data submitted';
        $save_action = 'none';
        $update_result = 0;
        $redirect_url = '';
        if ( ! empty($_POST) ) {
            $template_type_id = intval($_POST['RptTemplateTypeID']);
            $case_id = intval($_POST['RptCaseID']);
            $candidate_id = intval($_POST['CandidateID']);
            $redirect_url = sanitize_text_field($_POST['RedirectURL']);
            $ay = intval($_POST['ay']);
            switch ( $template_type_id ) {
                case '2': // promotion
                    $case_obj = new Rpt_Info_Promotion();
                    break;
                case '5': // sabbatical
                    $case_obj = new Rpt_Info_Sabbatical();
                    break;
            }
            $case_obj->update_from_post($_POST);
//            echo '<pre>' . print_r($case_obj, TRUE) . '</pre>'; exit;
            $save_action = 'submit';
            $case_obj->CaseStatus = 'Submitted';
            // anything else?
            switch ( $save_action ) {
                case 'save_draft':
                    if ( $case_obj->CaseID == 0 ) {
                        $update_result = $this->rpt_db->insert_case($case_obj);
                    }
                    else {
                        $update_result = $this->rpt_db->update_case($case_obj);
                    }
                    $result_message = 'Saved draft';
                    break;
                case 'submit' :
                    $update_result = $this->rpt_db->insert_case($case_obj);
                    $result_message = 'Case submitted to RPT queue. When it is available in RPT, a <strong>Go '
                        .'to case</strong> link will appear.';
                    break;
                default :
                    break;
            }
            if ( $update_result == 1 ) {
                $result_status = 'success';
            }
            else {
                $result_status = 'danger';
                $result_message = 'Error: ' . $this->rpt_db->get_last_error() . '|' . $this->rpt_db->get_last_query();
            }
        }
        wp_redirect(add_query_arg(array('rpt_page' => 'case', 'msg' => $result_message, 'status' => $result_status,
            'ay' => $ay), home_url('rptinfo')));
        exit;
    }

    /* ********************** functions dealing with templates ********************** */

    /**
     * template_page
     *      main page to control template functions
     *
     * @return void
     */
    private function template_page()
    {
        global $wp;
        echo '<p>' . $this->template_types[$this->active_template_type]->TemplateTypeName
            . ' Template maintenance page</p>';
        $template_list = [];
        $allow_update = FALSE;
        $rpt_template_id = get_query_var('template_id', '0');
        $unit_type = get_query_var('unit_type', 'all');
        if ( $this->rpt_user->SystemAdmin() ) {
            $allow_update = TRUE;
            $in_use = get_query_var('in_use', '');
            if ( ( $rpt_template_id > '0' ) && ( $in_use != '' ) ) {
                $this->update_template_in_use($rpt_template_id, $in_use);
            }
            echo '<p>Enable and disable templates found in RPT.</p>';
            echo '<p><a href="' . esc_url(add_query_arg(array('rpt_page' => 'template',
                    'ay' => $this->current_cycle->AcademicYear,
                    'template_type' => $this->active_template_type, 'unit_type' => 'dep'), home_url($wp->request)))
                . '">DEP</a> | ';
            echo '<a href="' . esc_url(add_query_arg(array('rpt_page' => 'template',
                    'ay' => $this->current_cycle->AcademicYear,
                    'template_type' => $this->active_template_type, 'unit_type' => 'undep'), home_url($wp->request)))
                . '">UNDEP</a> | ';
            echo '<a href="' . esc_url(add_query_arg(array('rpt_page' => 'template',
                    'ay' => $this->current_cycle->AcademicYear,
                    'template_type' => $this->active_template_type, 'unit_type' => 'all'), home_url($wp->request)))
                . '">All</a></p>';
        }
        if ( ( $rpt_template_id > '0' ) && ( $in_use == '' ) ) {
            $this->template_display($rpt_template_id, $allow_update);
        }
        else {
            $rpt_template_url = get_option('rpt_info_rpt_site_url') . '/'
                . get_option('rpt_info_tenant_id') . '/templates';
            if ( $allow_update ) {
                $template_list = $this->rpt_db->get_template_list($this->active_template_type, $unit_type);
            }
            else {
                $template_list = $this->rpt_db->get_templates_for_user( $this->rpt_user );
            }
//        echo '<pre>' . print_r($template_list, TRUE) . '</pre>';
            if (count($template_list) > 0) {
                echo '<table class="table table-bordered table-striped">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>ID</th>';
                echo '<th>Name</th>';
                echo '<th>Unit</th>';
                echo '<th>Enabled</th>';
                echo '<th>Action</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                foreach ($template_list as $template) {
                    $template->AcademicYear = $this->current_cycle->AcademicYear;
                    echo $template->listing_table_row($rpt_template_url, $allow_update);
                }
                echo '</tbody>';
                echo '</table>';
            }
        }
    }

    private function template_display( $rpt_template_id, $allow_update = FALSE )
    {
        global $wp;
        $rpt_template_url = get_option('rpt_info_rpt_site_url') . '/'
            . get_option('rpt_info_tenant_id') . '/templates';
        $template_obj = $this->rpt_db->get_template_by_id($rpt_template_id);
        $template_obj->AcademicYear = $this->current_cycle->AcademicYear;
//        echo '<pre>' . print_r($template_obj, true) . '</pre>';
        echo $template_obj->template_info_card($rpt_template_url, $allow_update);
    }

    private function update_template_in_use( $rpt_template_id, $in_use )
    {
        $template_obj = $this->rpt_db->get_template_by_id($rpt_template_id);
        if ( $template_obj->InUse != $in_use ) {
            $template_obj->InUse = $in_use;
            $update_result = $this->rpt_db->update_template_in_use($template_obj);
            if ( $update_result == 1 ) {
                $this->show_status_message('success', 'Template In-Use updated successfully.');
            }
            else {
                $this->show_status_message('danger', 'Template In-Use update failed. Please try again. '
                    . $this->rpt_db->get_last_error());
            }
        }
    }
    /* ********************** functions dealing with reports ********************** */

    /**
     * report_page
     *      main page to control report functions
     *
     * @return void
     */
    private function report_page()
    {
        global $wp;
        $report_data = [];
        $report_header = [];
        echo '<p>' . $this->template_types[$this->active_template_type]->TemplateTypeName
            . ' RPT reporting</p>';
        echo '<p>';
        echo '<a href="' . esc_url(add_query_arg(array('rpt_page' => 'report',
                'ay' => $this->current_cycle->AcademicYear,
                'template_type' => $this->active_template_type,
                'report_type' => 'cases-by-scc'),
                home_url($wp->request)))
            . '">Cases by SCC</a>';
        echo '</p>';
        $report_type = get_query_var('report_type', '');
        switch ( $report_type) {
            case 'cases-by-scc':
                $report_data = $this->rpt_db->case_count_by_scc($this->active_template_type,
                    $this->current_cycle->AcademicYear);
                $report_header = array('LevelOneUnitName' => 'S/C/C',
                    'CaseTotal' => 'Total');
                $detail_report = 'cases-by-unit';
                break;
            case 'cases-by-unit':
                $unit_id = get_query_var('unit_id', '');
                $report_data = $this->rpt_db->case_count_by_unit($this->active_template_type,
                    $this->current_cycle->AcademicYear, $unit_id);
                $report_header = array('UnitName' => 'Unit',
                    'CaseTotal' => 'Total');
                $detail_report = '';
                break;
        }
//        echo '<pre>' . $this->rpt_db->get_last_query() . '<br>' . print_r($report_data, TRUE) . '</pre>';
        if ( count($report_data) > 0 ) {
            echo rpt_report_table($report_header, $report_data, 'LevelOneUnitName', 'LevelOneID',
                $detail_report, $this->active_template_type, $this->current_cycle->AcademicYear);
        }
    }

    /* ********************** functions dealing with admin ********************** */

    /**
     * admin_page
     *      home for administrative functions
     *
     * @return void
     */
    private function admin_page()
    {
        echo '<p>' . $this->template_types[$this->active_template_type]->TemplateTypeName
            . ' RPT administrative functions</p>';
        echo '<p>';
   }

}