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
    private string $version;

    private Rpt_Info_DB $rpt_db;

    private $wordpress_user;
    private Rpt_Info_User $rpt_user;

    private array $template_types = [];

    private string $active_page = 'home';

    private int $active_template_type = 0;
    private array $cycle_list = [];
    private Rpt_Info_Cycle $current_cycle;

    private string $rpt_case_review_url = '';

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
    public function enqueue_styles() : void
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
    public function enqueue_scripts() : void
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
    public function register_shortcodes() : void
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
    public function add_query_vars($vars) : array
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
        $vars[] = 'rank_id'; // rank id (key)
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
    private function force_login() : void
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
    private function show_status_message( $status_type, $status_message ) : void
    {
        echo '<div class="alert alert-' . $status_type . '" role="alert">';
        echo $status_message;
        echo '</div>';
    }

    private function set_current_cycle( $ay ) : void
    {
        $this->current_cycle = $this->cycle_list[$ay];
    }

    /**
     * show_menu
     *      display the plugin's system menu
     *
     * @param $active_page
     * @return void
     */
    private function show_main_menu() : void
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
        if ( ( $this->active_page == 'home' ) && ( ! $this->active_template_type ) ) {
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
                . '" class="btn ';
            if ( $this->active_page == 'case' ) {
                echo ' active btn-primary';
            }
            else {
                echo ' btn-outline-secondary';
            }
            echo '">Cases</a>';
            echo '<a href="' . esc_url(add_query_arg(array('template_type' => $this->active_template_type,
                    'ay' => $this->current_cycle->AcademicYear,
                    'rpt_page' => 'template'), home_url($wp->request)))
                . '" class="btn ';
            if ( $this->active_page == 'template' ) {
                echo ' active btn-primary';
            }
            else {
                echo ' btn-outline-secondary';
            }
            echo '">Templates</a>';
            echo '<a href="' . esc_url(add_query_arg(array('template_type' => $this->active_template_type,
                    'ay' => $this->current_cycle->AcademicYear,
                    'rpt_page' => 'report'), home_url($wp->request)))
                . '" class="btn ';
            if ( $this->active_page == 'report' ) {
                echo ' active btn-primary';
            }
            else {
                echo ' btn-outline-secondary';
            }
            echo '">Reports</a>';
            if ( $this->rpt_user->SystemAdmin() ) {
                echo '<a href="' . esc_url(add_query_arg(array('template_type' => $this->active_template_type,
                        'ay' => $this->current_cycle->AcademicYear,
                        'rpt_page' => 'admin'), home_url($wp->request)))
                    . '" class="btn ';
                if ( $this->active_page == 'admin' ) {
                    echo ' active btn-primary';
                }
                else {
                    echo ' btn-outline-secondary';
                }
                echo '">Admin</a>';
            }
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
    public function rptinfo_home() : string
    {
        $this->force_login();
        ob_start();
//        echo 'wtf?';
        $ay = get_query_var('ay', '2025');
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
            $this->cycle_list = $this->rpt_db->get_rpt_cycle_list();
//            echo '<pre>' . print_r($this->cycle_list, true) . '</pre>';
            $this->set_current_cycle($ay);
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
                        case 'datasheet':
                            $this->case_page('datasheet');
                            break;
                        case 'edit':
                            $this->case_page('edit');
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

    private function home_page() : void
    {
        echo '<div class="row">';
        echo '<div class="col-12">';
        echo '<p>Currently selected year: ' . $this->current_cycle->Display . '<br>';
        echo 'Promotion initiation open: ' . $this->current_cycle->PromotionSubbmissionAllowed . '<br>'
            . 'Sabbatical submission open: ' . $this->current_cycle->SabbaticalSubmissionAllowed . '</p>';
        switch ( $this->active_template_type) {
            case '0' :
                $page_text = get_option('rpt_info_home_page_text');
                break;
            case '2' :
                $page_text = get_option('rpt_info_promo_home_page_text');
                break;
            case '5' :
                $page_text = get_option('rpt_info_sab_home_page_text');
                break;
        }
        echo '<p>' . $page_text . '</p>';
        echo '</div>'; // col 12
        echo '</div>'; // row
    }

    /**
     * show_footer
     *      info block at bottom of page
     *
     * @return void
     */
    private function show_footer() : void
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

    private function case_page( $selector = 'case' ) : void
    {
        $case_id = get_query_var('case_id', '0');
        $candidate_id = get_query_var('candidate_id', '0');
        $track_id = get_query_var('track_id', '0');
//        echo '<p>Case ID: ' . $case_id . ', Track ID: ' . $track_id . '</p>';
        if ( ( $case_id == '0' ) && ( $track_id == '0' ) ) {
            $this->case_list();
        }
        elseif ( $case_id == 'new' ) {
            $this->search_form();
        }
        elseif ( ( $track_id > '0' ) || ( $selector != 'case' ) ) {
            $this->case_edit($case_id, $track_id, $selector);
        }
        else { // case id is set
            $this->case_display($case_id);
        }
    }

    private function case_list() : void
    {
        global $wp;
        switch ( $this->active_template_type) {
            case '2' :
                $page_text = get_option('rpt_info_promo_case_list_page_text');
                break;
            case '5' :
                $page_text = get_option('rpt_info_sab_case_list_page_text');
                break;
        }
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
                $case_list = $this->rpt_db->get_sabbatical_cases_for_user($this->rpt_user);
                break;
        }
//        echo '<pre>' . print_r($case_list, true) . '</pre>'; exit;
        echo '<div class="row">';
        echo '<div class="col-12">';
        echo '<p>' . $page_text . '</p>';
        if ( $this->current_cycle->template_type_submissions_allowed($this->active_template_type) ) {
//            echo '<p>Case initiation is allowed</p>';
        }
        else {
            echo '<p>Case initiation is not allowed<br>Initiation window: '
                . $this->current_cycle->template_type_submission_window($this->active_template_type) . '</p>';
        }
        // help link here
        echo '</div>'; // col 12
        echo '</div>'; // row
        echo '<div class="row">';
        echo '<div class="col-6">';
        echo '<p>Current cases: (' . count($case_list) . ' found)</p>';
        echo '</div>'; // col 6
        echo '<div class="col-6 text-right">';
        if ( $this->current_cycle->template_type_submissions_allowed($this->active_template_type) ) {
            echo '<a href="'
                . esc_url(add_query_arg(array('rpt_page' => 'case', 'case_id' => 'new',
                    'ay' => $this->current_cycle->AcademicYear,
                    'template_type' => $this->active_template_type), home_url($wp->request)))
                . '" class="btn btn-primary">Initiate a new case</a>';
        }
        echo '</div>'; // col 6
        echo '</div>'; // row
        echo '<div class="row">';
        echo '<div class="col-12">';
        if ( count( $case_list ) > 0 ) {
//            echo '<pre>' . print_r( $case_list, true ) . '</pre>';
            echo '<table class="table table-bordered table-striped sort-table">';
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
            case '2': // promotion
                $result .= '<th>ID</th>';
                $result .= '<th>Candidate Name</th>';
                $result .= '<th>Type</th>';
                $result .= '<th>Status</th>';
                $result .= '<th>Workflow Step</th>';
                $result .= '<th>Action</th>';
                break;
            case '5': // sabbatical
                $result .= '<th>ID</th>';
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
        switch ( $this->active_template_type) {
            case '2' :
                $page_text = get_option('rpt_info_promo_case_page_text');
                break;
            case '5' :
                $page_text = get_option('rpt_info_sab_case_page_text');
                break;
        }
        echo '<p>' . $page_text . '</p>';
        $case_obj = NULL;
//        echo 'case id ' . $case_id . ', type ' . $this->active_template_type;
        $rpt_case_url = get_option('rpt_info_rpt_site_url') . '/'
            . get_option('rpt_info_tenant_id') . '/cases';
        switch ( $this->active_template_type ) {
            case '2': // promotion
                $case_obj = $this->rpt_db->get_promotion_by_id($case_id);
                break;
            case '5': // sabbatical
                $case_obj = $this->rpt_db->get_sabbatical_by_id($case_id);
                break;
        }
//        echo '<pre>' . print_r( $case_obj, true ) . '</pre>';
        $this->rpt_db->get_other_appointments($case_obj);
        $case_obj->set_calculated_values();
        echo '<div class="row">';
        echo '<div class="col-6">';
        echo $case_obj->candidate_info_card(FALSE);
        echo '</div>'; // col 6
        // template type specific fields in another card
        echo '<div class="col-6">';
        switch ( $case_obj->RptTemplateTypeID ) {
            case '2':
                echo $case_obj->promotion_info_card($rpt_case_url, $this->rpt_user->SystemAdmin());
                break;
            case '5':
                echo $case_obj->sabbatical_info_card($rpt_case_url, $this->rpt_user->SystemAdmin());
                break;
        }
        echo '</div>'; // col 6
        echo '</div>'; // row
        echo '<div class="row">';
        echo '<div class="col-6">';
        echo $case_obj->rpt_info_card();
        echo '</div>'; // col 6
        echo '<div class="col-6">';
        switch ( $case_obj->RptTemplateTypeID ) {
            case '2':
                echo $case_obj->data_sheet_card();
                break;
            case '5':
                break;
        }
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
    private function case_edit( int $case_id = 0, int $track_id = 0, $selector = 'case' ) : void
    {
        global $wp;
        switch ($selector) {
            case 'case':
            case 'edit':
                $page_text = 'Edit the basic information for case submission.';
                break;
            case 'datasheet':
                $page_text = 'Edit the basic information for data sheet submission.';
        }
        echo '<p>' . $page_text . '</p>';
//        echo 'track id ' . $track_id . ', case id ' . $case_id;
        $case_obj = NULL;
        if ( ( $track_id ) && ( ! $case_id ) ) { // candidate but no case - see if there is one
            switch ( $this->active_template_type) {
                case '2': // promotion
                    $case_obj = $this->rpt_db->get_promotion_case_for_candidate($track_id);
                    break;
                case '5': // sabbatical
                    $case_obj = $this->rpt_db->get_sabbatical_case_for_candidate($track_id);
                    break;
            }
            if ( $case_obj ) { // case already exists
                $case_id = $case_obj->CaseID;
            }
            else { // initialize object with known info for candidate
                switch ( $this->active_template_type) {
                    case '2': // promotion
                        $case_obj = $this->rpt_db->get_promotion_from_track($track_id);
                        if ( ! $case_obj ) { $case_obj = new Rpt_Info_Promotion(); }
                        break;
                    case '5': // sabbatical
                        $case_obj = $this->rpt_db->get_sabbatical_from_track($track_id);
                        if ( ! $case_obj ) { $case_obj = new Rpt_Info_Sabbatical(); }
                        break;
                }
                $case_obj->InitiatorID = $this->rpt_user->InterfolioUserID;
                $case_obj->AcademicYear = $this->current_cycle->AcademicYear;
                $case_obj->AcademicYearDisplay = $this->current_cycle->Display;
            }
//            echo '<pre>' . print_r( $case_obj, true ) . '</pre>';
        }
        elseif ( $case_id ) { // incoming case id - just get it
            switch ( $this->active_template_type) {
                case '2': // promotion
                    $case_obj = $this->rpt_db->get_promotion_by_id($case_id);
                    break;
                case '5': // sabbatical
                    $case_obj = $this->rpt_db->get_sabbatical_by_id($case_id);
                    break;
            }
        }
//        echo '<pre>' . print_r( $case_obj, true ) . '</pre>';
        if ( $case_obj ) {
            $this->rpt_db->get_other_appointments($case_obj);
            if ( $case_obj->Leaves == '' ) {
                $this->rpt_db->get_candidate_leaves($case_obj);
            }
            if ( $case_obj->Waivers == '' ) {
                $this->rpt_db->get_candidate_waivers($case_obj);
            }
            $case_obj->set_calculated_values();
//            echo '<pre>' . print_r( $case_obj, true ) . '</pre>';
            echo '<div class="row">';
            echo '<div class="col-12">';
            if ($case_obj->RptCaseID == '0') {
                echo $case_obj->init_case_help_text($this->rpt_case_review_url);
            }
            echo '<div class="row">';
            echo '<div class="col-6">';
            echo $case_obj->candidate_info_card( ( $case_obj->RptCaseID == 0 ) );
            echo '</div>'; // col 6
            // template type specific fields in another card
            echo '<div class="col-6">';
            switch ($selector) {
                case 'case':
                case 'edit':
                    $this->case_form($case_obj);
                    break;
                case 'datasheet':
                    $this->datasheet_form($case_obj);
            }
            echo '</div>'; // col 6
            echo '</div>'; // row
            echo '</div>'; // col 12
            echo '</div>'; // row
        }
        else {
            echo '<p><em>Error loading case info</em></p>';
        }
    }

    private function case_form( Rpt_Info_Case $case_obj ) : void
    {
        switch ( $case_obj->RptTemplateTypeID ) {
            case '2':
                $this->promotion_form( $case_obj );
                break;
            case '5':
                $this->sabbatical_form( $case_obj );
                break;
        }
    }

    private function promotion_form( Rpt_Info_Promotion $case_obj ) : void
    {
        global $wp;
        // get lists for dropdowns
        $target_rank_list = $this->rpt_db->get_valid_promotion_target_ranks($case_obj->CurrentRankKey);
        $promotion_type_list = $this->rpt_db->get_promotion_type_list($case_obj->RankCategory);
        $template_list = $this->rpt_db->get_valid_templates_for_case($case_obj);
        // display card
        echo '<div class="card">';
        echo '<div class="card-body">';
        echo '<h4 class="card-title">Promotion information</h4>';
        if ( $case_obj->CoverSheetID == '0' ) {
            echo '<p class="card-subtitle mb-2 text-muted">Please review these '
                . 'selections and update as needed.</p>';
        }
        else {
            echo '<p class="card-subtitle mb-2 text-muted">The cover sheet for this case '
                . 'is already present in RPT. Make sure to delete the old one before '
                . 'making changes. Template cannot be changed once the case is initialized.</p>';
        }
        echo '<form id="rptinfo_promotion_form" name="rptinfo_promotion_form" action="'
            . esc_url(admin_url('admin-post.php'))
            . '" role="form" method="post" accept-charset="utf-8" class="rptinfo-form ">';
        echo rpt_form_hidden_field('action', 'process_rptinfo_case_edit');
        echo rpt_form_hidden_field('CaseID', $case_obj->CaseID);
        echo rpt_form_hidden_field('RptTemplateTypeID', $case_obj->RptTemplateTypeID);
        echo rpt_form_hidden_field('ay', $this->current_cycle->AcademicYear);
        echo rpt_form_hidden_field('RedirectURL', home_url($wp->request));
        echo rpt_form_hidden_field('CandidateID', $case_obj->CandidateID);
        echo rpt_form_hidden_field('CandidateKey', $case_obj->CandidateKey);
        echo rpt_form_hidden_field('InitiatorID', $case_obj->InitiatorID);
        echo rpt_form_hidden_field('UWODSAppointmentTrackKey', $case_obj->UWODSAppointmentTrackKey);
        echo rpt_form_hidden_field('AppointmentType', $case_obj->AppointmentType);
        echo rpt_form_hidden_field('UWODSUnitKey', $case_obj->UWODSUnitKey);
        echo rpt_form_hidden_field('InterfolioUnitID', $case_obj->InterfolioUnitID);
        echo rpt_form_hidden_field('CurrentRankKey', $case_obj->CurrentRankKey);
        echo rpt_form_hidden_field('CoverSheetID', $case_obj->CoverSheetID);
        echo rpt_form_hidden_field('HasJoint', $case_obj->HasJoint);
        echo rpt_form_hidden_field('HasSecondary', $case_obj->HasSecondary);
        if ( $this->rpt_user->SystemAdmin() ) {
            echo rpt_form_number_box('RptCaseID', $case_obj->RptCaseID, 'RPT case ID', FALSE,
            '', FALSE, FALSE, 'If case already exists in RPT, enter ID');
            echo rpt_form_dropdown_list('CaseStatusID', $case_obj->CaseStatusID, 'Case Status',
                $this->rpt_db->get_case_status_list());
        }
        else {
            echo rpt_form_hidden_field('RptCaseID', $case_obj->RptCaseID);
            echo rpt_form_hidden_field('CaseStatusID', $case_obj->CaseStatusID);
        }
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
            'Choose which template to use', TRUE, ($case_obj->RptCaseID > '0'));
        echo '<div class="form-goup row">';
        echo '<div class="col-12">';
        if ( count($template_list) > 0 ) {
            echo '<button type="submit" class="btn btn-primary" name="submit" value="submit">Submit</button>';
        }
        else {
            echo '<p><strong>No valid template found for candidate unit. Please make sure there is '
                . 'a template available before initiating a case.</strong></p>';
        }
        if ( $case_obj->CoverSheetID == '0' ) { // no case, back to listing
            echo '<a href="' . esc_url(add_query_arg(array('rpt_page' => 'case',
                    'ay' => $this->current_cycle->AcademicYear,
                    'template_type' => $this->active_template_type), home_url($wp->request)))
                . '" class="btn btn-outline-secondary">Cancel</a>';
        }
        else { // case exists, return to display
            echo '<a href="' . esc_url(add_query_arg(array('rpt_page' => 'case',
                    'ay' => $this->current_cycle->AcademicYear,
                    'case_id' => $case_obj->CaseID,
                    'template_type' => $this->active_template_type), home_url($wp->request)))
                . '" class="btn btn-outline-secondary">Cancel</a>';
        }
        echo '</div>'; // form group row
        echo '</div>'; // col 12
        echo '</form>';
        echo '</div>'; // card body
        echo '</div>'; // card
    }

    private function sabbatical_form( Rpt_Info_Sabbatical $case_obj )
    {
        global $wp;
        // get lists for dropdowns
        $template_list = $this->rpt_db->get_valid_templates_for_case($case_obj);
        // display card
        echo '<div class="card">';
        echo '<div class="card-body">';
        echo '<h4 class="card-title">Sabbatical information</h4>';
        echo '<p class="card-subtitle mb-2 text-muted">Please review these '
            . 'selections and update as needed.</p>';
        echo '<form id="rptinfo_sabbatical_form" name="rptinfo_sabbatical_form" action="'
            . esc_url(admin_url('admin-post.php'))
            . '" role="form" method="post" accept-charset="utf-8" class="rptinfo-form ">';
        echo rpt_form_hidden_field('action', 'process_rptinfo_case_edit');
        if ( $this->rpt_user->SystemAdmin() ) {
            echo rpt_form_number_box('RptCaseID', $case_obj->RptCaseID, 'RPT case ID', FALSE,
                '', FALSE, FALSE, 'If case already exists in RPT, enter ID');
        }
        else {
            echo rpt_form_hidden_field('RptCaseID', $case_obj->RptCaseID);
        }
        echo rpt_form_hidden_field('CaseID', $case_obj->CaseID);
        echo rpt_form_hidden_field('RptTemplateTypeID', $case_obj->RptTemplateTypeID);
        echo rpt_form_hidden_field('ay', $this->current_cycle->AcademicYear);
        echo rpt_form_hidden_field('RedirectURL', home_url($wp->request));
        echo rpt_form_hidden_field('CandidateID', $case_obj->CandidateID);
        echo rpt_form_hidden_field('CandidateKey', $case_obj->CandidateKey);
        echo rpt_form_hidden_field('InitiatorID', $case_obj->InitiatorID);
        echo rpt_form_hidden_field('UWODSAppointmentTrackKey', $case_obj->UWODSAppointmentTrackKey);
        echo rpt_form_hidden_field('UWODSUnitKey', $case_obj->UWODSUnitKey);
        echo rpt_form_hidden_field('InterfolioUnitID', $case_obj->InterfolioUnitID);
        echo rpt_form_hidden_field('CurrentRankKey', $case_obj->CurrentRankKey);
        echo rpt_form_hidden_field('CaseStatus', $case_obj->CaseStatus);
        echo rpt_form_hidden_field('HasJoint', $case_obj->HasJoint);
        echo rpt_form_hidden_field('HasSecondary', $case_obj->HasSecondary);
        echo rpt_form_hidden_field('RosterPct', $case_obj->RosterPct);
        echo rpt_form_hidden_field('MonthlySalary', $case_obj->MonthlySalary);
        echo rpt_form_hidden_field('TenureAmount', $case_obj->TenureAmount);
        echo rpt_form_hidden_field('AppointmentStartDate', $case_obj->AppointmentStartDate);
        echo '<div class="form-goup row">';
        echo '<div class="col-12">';
        echo '<p>' . $case_obj->AcademicYearDisplay . ' Quarter(s) requested: <span id="QtrCount"></span></p>';
        echo rpt_form_quarter_select($case_obj->SummerQtr, $case_obj->FallQtr, $case_obj->WinterQtr,
                $case_obj->SpringQtr, ($case_obj->ServicePeriod == 12));
        echo rpt_form_dropdown_list('SalarySupportPct', $case_obj->SalarySupportPct,
            'Salary support:', $case_obj->salary_support_values());
        echo '</div>'; // col 12
        echo '</div>'; // form group row
        echo rpt_yes_no_radio('MultiYear', $case_obj->MultiYear,
            'Multi-year distribution?', FALSE, TRUE);
        echo rpt_form_dropdown_list('LastSabbaticalAcademicYear', $case_obj->LastSabbaticalAcademicYear,
            'Last sabbatical academic year', $this->rpt_db->get_academic_year_list());
        echo rpt_form_dropdown_list('EligibilityReport', $case_obj->EligibilityReport,
            'Eligibility report status?', $case_obj->eligibility_report_values());
        if ( $case_obj->AppointmentEndDate ) {
            echo rpt_yes_no_radio('ContingentOnExtension', $case_obj->ContingentOnExtension,
                'Sabbatical contingent upon reappointment/promotion?', FALSE, TRUE);
        }
        else {
            echo rpt_form_hidden_field('ContingentOnExtension', 'No');
        }
        // selector for AY of last sabbatical
        echo rpt_form_textarea('EligibilityNote', $case_obj->EligibilityNote,
        'Eligibility note', 40, 5);
        echo rpt_template_select('RptTemplateID', $case_obj->RptTemplateID, 'RPT Template',
            $template_list, FALSE, 'form-control', ( count($template_list) > 1) ? 'Select...' : '',
            'Choose which template to use', TRUE);
        echo '<div class="form-group row">';
        echo '<div class="col-12">';
        if ( count($template_list) > 0 ) {
            echo '<button type="submit" class="btn btn-primary" name="submit" value="submit">Submit</button>';
        }
        else {
            echo '<p><strong>No valid template found for candidate unit. Please make sure there is '
                . 'a template available before initiating a case.</strong></p>';
        }
        echo '<a href="' . esc_url(add_query_arg(array('rpt_page' => 'case',
                'ay' => $this->current_cycle->AcademicYear,
                'template_type' => $this->active_template_type), home_url($wp->request)))
            . '" class="btn btn-outline-secondary">Cancel</a>';
        echo '</div>'; // col 12
        echo '</div>'; // form group row
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
    public function process_rptinfo_case_edit() : void
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
            $case_id = intval($_POST['CaseID']);
            $rpt_case_id = intval($_POST['RptCaseID']);
            $candidate_id = intval($_POST['CandidateID']);
            $redirect_url = sanitize_text_field($_POST['RedirectURL']);
            $ay = intval($_POST['ay']);
            switch ( $template_type_id ) {
                case '2': // promotion
                    $case_obj = $this->rpt_db->get_promotion_by_id($case_id);
                    if ( ! $case_obj ) { $case_obj = new Rpt_Info_Promotion(); }
                    break;
                case '5': // sabbatical
                    $case_obj = $this->rpt_db->get_sabbatical_by_id($case_id);
                    if ( ! $case_obj ) { $case_obj = new Rpt_Info_Sabbatical(); }
                    break;
            }
            $case_obj->update_from_post($_POST);
            if ( $case_obj->ok_to_submit() && ( $case_obj->CaseStatusID == 0 ) ) {
                $case_obj->CaseStatusID = '1';
            }
//            echo '<pre>' . print_r($case_obj->insert_case_array(), TRUE) . '</pre>'; exit;
            // anything else?
            if ( $case_obj->CaseID == 0 ) {
                $update_result = $this->rpt_db->insert_case($case_obj);
            }
            else {
                $update_result = $this->rpt_db->update_case($case_obj);
            }
            $result_message = 'Your changes have been saved';
            if ( $update_result === FALSE ) {
                $result_status = 'danger';
                $result_message = 'Error: ' . $this->rpt_db->get_last_error() . '|' . $this->rpt_db->get_last_query();
            }
            else {
                $result_status = 'success';
            }
        }
        wp_redirect(add_query_arg(array('rpt_page' => 'case', 'msg' => $result_message,
            'status' => $result_status, 'template_type' => $case_obj->RptTemplateTypeID,
            'ay' => $ay), home_url($redirect_url)));
        exit;
    }

    private function datasheet_form( Rpt_Info_Case $case_obj ) : void
    {
        switch ( $case_obj->RptTemplateTypeID ) {
            case '2':
                $this->promotion_datasheet_form( $case_obj );
                break;
            case '5':
                $this->sabbatical_datasheet_form( $case_obj );
                break;
        }
    }

    private function promotion_datasheet_form( Rpt_Info_Promotion $case_obj ) : void
    {
        global $wp;
        // get lists for dropdowns, etc
        $this->rpt_db->check_for_postponement($case_obj);
        // display card
        echo '<div class="card">';
        echo '<div class="card-body">';
        echo '<h4 class="card-title">Data sheet information</h4>';
        if ( $case_obj->DataSheetID == '0' ) {
            echo '<p class="card-subtitle mb-2 text-muted">Please review these '
                . 'selections and update as needed.</p>';
        }
        else {
            echo '<p class="card-subtitle mb-2 text-muted">The data sheet for this case '
                . 'is already present in RPT. Changes made here will be flagged to create '
                . 'a new data sheet to replace the old one.</p>';
        }
        echo '<form id="rptinfo_datasheet_form" name="rptinfo_datasheet_form" action="'
            . esc_url(admin_url('admin-post.php'))
            . '" role="form" method="post" accept-charset="utf-8" class="rptinfo-form ">';
        echo rpt_form_hidden_field('action', 'process_rptinfo_datasheet_edit');
        echo rpt_form_hidden_field('RptCaseID', $case_obj->RptCaseID);
        echo rpt_form_hidden_field('CaseID', $case_obj->CaseID);
        echo rpt_form_hidden_field('RptTemplateTypeID', $case_obj->RptTemplateTypeID);
        echo rpt_form_hidden_field('ay', $this->current_cycle->AcademicYear);
        echo rpt_form_hidden_field('RedirectURL', home_url($wp->request));
        echo rpt_form_hidden_field('DataSheetStatus', $case_obj->DataSheetStatus);
        echo rpt_form_hidden_field('DataSheetID', $case_obj->DataSheetID);
        echo rpt_form_text_box('TargetRankName', $case_obj->TargetRankName,
            'Proposed rank', FALSE, 'form-control', TRUE, FALSE);
        echo rpt_form_text_box('TargetTrackTypeName', $case_obj->TargetTrackTypeName,
            'Track', FALSE, 'form-control', TRUE, FALSE);
        echo rpt_form_text_box('EffectiveDate', rpt_format_date($case_obj->EffectiveDate),
            'Start date', FALSE, 'form-control', TRUE, FALSE);
        if ( $case_obj->TargetRankTenured == 'Yes' ) {
            echo rpt_form_number_box('TenureAward', $case_obj->TenureAward,
                'Tenure %', FALSE, 'form-control', FALSE, FALSE);
        }
        if ( $case_obj->TargetRankDefaultTerm > '0' ) {
            echo rpt_form_number_box('NewTermLength', $case_obj->propose_new_term(),
                'New term length', FALSE, 'form-control', FALSE, FALSE);
        }
        echo rpt_yes_no_radio('Postponed', $case_obj->Postponed, 'Previously postponed?',
            FALSE, TRUE);
        echo '<p><strong>Vote #1</strong> &mdash; On the question of whether to recommend ' . $case_obj->ActionType . '</p>';
        echo '<table>';
        echo '<tr>';
        echo '<th>Total Eligible to Vote</th>';
        echo '<th>Total In Favor</th>';
        echo '<th>Total Opposed</th>';
        echo '<th>Total Absent</th>';
        echo '<th>Total Abstaining</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<td><input type="text" name="Vote1Eligible" id="Vote1Eligible" size="5" value="'
            . $case_obj->Vote1Eligible . '" /></td>';
        echo '<td><input type="text" name="Vote1Affirmative" id="Vote1Affirmative" size="5" value="'
            . $case_obj->Vote1Affirmative . '" /></td>';
        echo '<td><input type="text" name="Vote1Negative" id="Vote1Negative" size="5" value="'
            . $case_obj->Vote1Negative . '" /></td>';
        echo '<td><input type="text" name="Vote1Absent" id="Vote1Absent" size="5" value="'
            . $case_obj->Vote1Absent . '" /></td>';
        echo '<td><input type="text" name="Vote1Abstaining" id="Vote1Abstaining" size="5" value="'
            . $case_obj->Vote1Abstaining . '" /></td>';
        echo '</tr>';
        echo '</table>';
        echo '<p><em>Vote counts must not include chair.</em></p>';
        echo '<p><em>If this is a mandatory review and Vote #1 resulted in a majority Opposed, enter Vote #2 data.</em></p>';
        echo '<p><strong>Vote #2</strong> &mdash; On the question of whether to recommend postponement of mandatory review</p>';
        echo '<table>';
        echo '<tr>';
        echo '<th>Total Eligible to Vote</th>';
        echo '<th>Total In Favor</th>';
        echo '<th>Total Opposed</th>';
        echo '<th>Total Absent</th>';
        echo '<th>Total Abstaining</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<td><input type="text" name="Vote2Eligible" id="Vote2Eligible" size="5" value="'
            . $case_obj->Vote2Eligible . '" /></td>';
        echo '<td><input type="text" name="Vote2Affirmative" id="Vote2Affirmative" size="5" value="'
            . $case_obj->Vote2Affirmative . '" /></td>';
        echo '<td><input type="text" name="Vote2Negative" id="Vote2Negative" size="5" value="'
            . $case_obj->Vote2Negative . '" /></td>';
        echo '<td><input type="text" name="Vote2Absent" id="Vote2Absent" size="5" value="'
            . $case_obj->Vote2Absent . '" /></td>';
        echo '<td><input type="text" name="Vote2Abstaining" id="Vote2Abstaining" size="5" value="'
            . $case_obj->Vote2Abstaining . '" /></td>';
        echo '</tr>';
        echo '</table>';
        echo rpt_form_textarea('SubcommitteeMembers', strip_tags($case_obj->SubcommitteeMembers),
            'Subcommittee members', 60, 4, FALSE, TRUE);
        echo rpt_form_textarea('Leaves', $case_obj->Leaves,
            'Leave history', 60, 4, FALSE, FALSE);
        echo rpt_form_textarea('Waivers', $case_obj->Waivers,
            'Clock waivers', 60, 4, FALSE, FALSE);
        echo '<button type="submit" class="btn btn-primary" name="save" value="save">Save</button>';
        if ( $case_obj->data_sheet_ok() ) {
            echo '<button type="submit" class="btn btn-primary" name="submit" value="submit">Submit</button>';
        }
        echo '<a href="' . esc_url(add_query_arg(array('rpt_page' => 'case',
                'ay' => $this->current_cycle->AcademicYear,
                'case_id' => $case_obj->CaseID,
                'template_type' => $this->active_template_type), home_url($wp->request)))
            . '" class="btn btn-outline-secondary">Cancel</a>';
        echo '</form>';
        if ( ! $case_obj->data_sheet_ok() ) {
            echo '<p>Click <strong>Save</strong> to save your changes. If the data sheet passes validation,'
                . ' you will be able to submit it to RPT.</p>';
        }
        else {
            echo '<p>Click <strong>Save</strong> to save your changes, or click <strong>Submit</strong> '
                . ' to add the data sheet to the case in RPT.</p>';
        }
        echo '</div>'; // card body
        echo '</div>'; // card
    }

    private function sabbatical_datasheet_form( Rpt_Info_Sabbatical $case_obj ) : void
    {
        // placeholder
    }

    /**
     * process_rptinfo_datasheet_edit
     *      callback for submit button on datasheet edit form
     *
     * @return void
     */
    public function process_rptinfo_datasheet_edit() : void
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
            $case_id = intval($_POST['CaseID']);
            $redirect_url = sanitize_text_field($_POST['RedirectURL']);
            $ay = intval($_POST['ay']);
            switch ( $template_type_id ) {
                case '2': // promotion
                    $case_obj = $this->rpt_db->get_promotion_by_id($case_id);
                    break;
                case '5': // sabbatical
                    $case_obj = $this->rpt_db->get_sabbatical_by_id($case_id);
                    break;
            }
            $case_obj->update_from_data_sheet_post($_POST);
            if ( isset($_POST['save']) ) {
                $case_obj->DataSheetStatus = 'Draft';
            }
            elseif ( isset($_POST['submit']) ) {
                if ( $case_obj->data_sheet_ok() ) {
                    $case_obj->DataSheetStatus = 'Submitted';
                }
                else {
                    $case_obj->DataSheetStatus = 'Draft';
                }
            }
//            echo '<pre>' . print_r($case_obj, TRUE) . '</pre>'; exit;
            $update_result = $this->rpt_db->update_case($case_obj, 'datasheet');
            if ( $update_result === FALSE ) {
                $result_status = 'danger';
                $result_message = 'Error: ' . $this->rpt_db->get_last_error() . '|' . $this->rpt_db->get_last_query();
            }
            else {
                $result_status = 'success';
                $result_message = 'Your changes have been saved';
            }
        }
        wp_redirect(add_query_arg(array('rpt_page' => 'case', 'msg' => $result_message,
            'status' => $result_status, 'template_type' => $case_obj->RptTemplateTypeID,
            'case_id' => $case_obj->CaseID,
            'ay' => $ay), home_url($redirect_url)));
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
                echo '<table class="table table-bordered table-striped sort-table">';
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
        echo '&nbsp;|&nbsp;<a href="' . esc_url(add_query_arg(array('rpt_page' => 'report',
                'ay' => $this->current_cycle->AcademicYear,
                'template_type' => $this->active_template_type,
                'report_type' => 'withdrawn'),
                home_url($wp->request)))
            . '">Withdrawn Cases</a>';
        echo '&nbsp;|&nbsp;<a href="' . esc_url(add_query_arg(array('rpt_page' => 'report',
                'ay' => $this->current_cycle->AcademicYear,
                'template_type' => $this->active_template_type,
                'report_type' => 'missing'),
                home_url($wp->request)))
            . '">Missing Cases</a>';
        if ( $this->active_template_type == 2 ) {
            echo '&nbsp;|&nbsp;<a href="' . esc_url(add_query_arg(array('rpt_page' => 'report',
                    'ay' => $this->current_cycle->AcademicYear,
                    'template_type' => $this->active_template_type,
                    'report_type' => 'voting'),
                    home_url($wp->request)))
                . '">Eligible Voting Faculty</a>';
        }
        if ( $this->rpt_user->SystemAdmin() ) {
            echo '&nbsp;|&nbsp;<a href="' . esc_url(add_query_arg(array('rpt_page' => 'report',
                    'ay' => $this->current_cycle->AcademicYear,
                    'template_type' => $this->active_template_type,
                    'report_type' => 'apf'),
                    home_url($wp->request)))
                . '">Cases with APF</a>';
        }
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
            case 'voting':
                $this->voting_report();
                break;
            case 'withdrawn':
            case 'missing':
            case 'apf':
                $this->cases_by_status($report_type);
                break;
        }
//        echo '<pre>' . $this->rpt_db->get_last_query() . '<br>' . print_r($report_data, TRUE) . '</pre>';
        if ( count($report_data) > 0 ) {
            echo rpt_report_table($report_header, $report_data, 'LevelOneUnitName', 'LevelOneID',
                $detail_report, $this->active_template_type, $this->current_cycle->AcademicYear);
        }
    }

    private function voting_report()
    {
        global $wp;
        // incoming params
        $unit_id = get_query_var('unit_id', '');
        $rank_id = get_query_var('rank_id', '');
        $this->voting_matrix_url = get_option('ap_ptinfo_voting_matrix_url');
        $this->committee_setup_url = get_option('ap_ptinfo_rpt_committee_setup_url');
        // lists for dropdowns
        $unit_list = $this->rpt_db->get_user_subunits(array_keys($this->rpt_user->Units));
        $rank_list = $this->rpt_db->get_target_ranks();
//        echo '<pre>' . print_r($rank_list, true) . '</pre>'; exit;
        // parameter form
        echo '<div class="row">';
        echo '<div class="col-12">';
        echo '<p>Obtain a list of eligible voting faculty for a unit by selecting the proposed rank of a '
            . 'candidate. This list can be used to create the committee in RPT for the Eligible Voting '
            . 'Faculty Review case step.</p>';
        echo '<p>The list is based on Workday current appointment information, so please review the '
            . 'results carefully before creating your committee. The results will not take into account any faculty on leave.</p>';
        echo '<p>Additional voting resources:</p>';
        echo '<ul>';
        echo '<li><a href="' . $this->voting_matrix_url . '" alt="Voting Matrix page">Voting Guidelines</a></li>';
        echo '<li><a href="' . $this->committee_setup_url . '" alt="Shell committee setu page">Shell Committee Setup</a></li>';
        echo '</div>'; // col
        echo '</div>'; // row
        echo '<div class="row">';
        echo '<div class="col-6">';
        echo '<div class="card">';
        echo '<div class="card-body">';
        echo '<h4 class="card-title">Report parameters</h4>';
        echo '<form id="rptinfo_voting_form" name="rptinfo_voting_form" action="'
            . esc_url(add_query_arg(array('rpt_page' => 'report'), home_url($wp->request)))
            . '" role="form" method="get" accept-charset="utf-8" class="rptinfo-form ">';
        echo rpt_form_hidden_field('rpt_page', 'report');
        echo rpt_form_hidden_field('report_type', 'voting');
        echo rpt_form_hidden_field('template_type', '2');
        echo rpt_form_hidden_field('ay', $this->current_cycle->AcademicYear);
        if ( count($unit_list) > 1 ) {
            echo rpt_form_dropdown_list('unit_id', $unit_id, 'Unit', $unit_list);
        }
        else {
            echo rpt_form_hidden_field('unit_id', $unit_id);
            $unit = reset($this->rpt_user->Units);
            echo '<p>' . $unit . '</p>';
        }
        echo rpt_form_dropdown_list('rank_id', $rank_id,
            'Voting on promotions to rank', $rank_list);
        echo '<button type="submit" class="btn btn-primary" name="submit" value="submit">Submit</button>';
        echo '</form>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        // display report if parameters present
        if ( ( $unit_id != 0 ) && ( $rank_id != 0 ) ) {
            $voting_list = $this->rpt_db->get_voting_faculty($unit_id, $rank_id);
            $unit_name = $this->rpt_db->get_unit_name($unit_id);
            $rank_name = $this->rpt_db->get_rank_name($rank_id);
            if ( ! empty($voting_list) ) {
                echo '<div class="row">';
                echo '<div class="col-12">';
                echo '<h3>Faculty members eligible to vote for ' . $rank_name . ' in ' . $unit_name . '</h3>';
                echo '<table class="table table-bordered table-striped">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>NetID</th>';
                echo '<th>Name</th>';
                echo '<th>Rank</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                foreach ( $voting_list as $voting ) {
                    echo '<tr>';
                    echo '<td>' . $voting->UWNetID . '</td>';
                    echo '<td>' . $voting->LegalName . '</td>';
                    echo '<td>' . $voting->RankName . '</td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
            }
            echo '</div>';
            echo '</div>';
        }
    }

    private function cases_by_status( $status )
    {
        $rpt_case_url = get_option('rpt_info_rpt_site_url') . '/'
            . get_option('rpt_info_tenant_id') . '/cases';
//        $this->rpt_case_review_url = get_option('ap_rptinfo_rpt_case_review_url');
        $case_list = [];
        switch ( $this->active_template_type) {
            case '2': // promotion
                $case_list = $this->rpt_db->get_promotion_cases_for_user($this->rpt_user, $status);
                break;
            case '5': // sabbatical
                $case_list = $this->rpt_db->get_sabbatical_cases_for_user($this->rpt_user, $status);
                break;
        }
        echo '<div class="row">';
        echo '<div class="col-12">';
        if ( count( $case_list ) > 0 ) {
//            echo '<pre>' . print_r( $case_list, true ) . '</pre>';
            echo '<table class="table table-bordered table-striped sort-table">';
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
        switch ( $this->active_template_type ) {
            case 2:
                $this->promotion_academic_year_setup();
                break;
            case 5:
                $this->sabbatical_academic_year_setup();
                $this->sabbatical_allowance_setup();
                break;
        }
   }

    private function promotion_academic_year_setup()
    {
        global $wp;
        // get lists for dropdowns
        // display card
        echo '<div class="card">';
        echo '<div class="card-body">';
        echo '<h4 class="card-title">Promotion cycle setup</h4>';
        echo '<p class="card-subtitle mb-2 text-muted">Select an Academic Year to change its settings.</p>';
        echo '<form id="rptinfo_promotion_setup_form" name="rptinfo_promotion_setup_form" action="'
            . esc_url(admin_url('admin-post.php'))
            . '" role="form" method="post" accept-charset="utf-8" class="rptinfo-form ">';
        echo rpt_form_hidden_field('action', 'process_rptinfo_admin_setup');
        echo rpt_form_hidden_field('RedirectURL', home_url($wp->request));
        echo rpt_form_hidden_field('ay', $this->current_cycle->AcademicYear);
        echo rpt_form_hidden_field('RptTemplateTypeID', '2');
        echo rpt_form_dropdown_list('CycleAcademicYear', $this->current_cycle->AcademicYear,
            'Academic year', $this->academic_year_select_list( 'dates' ), 'Display');
        echo rpt_form_date_select('PromotionSubmissionStartDate',
            $this->current_cycle->PromotionSubmissionStartDate,
            'Start date for promotion initializations');
        echo rpt_form_date_select('PromotionSubmissionEndDate',
            $this->current_cycle->PromotionSubmissionEndDate,
            'End date for promotion initializations');
        echo '<button type="submit" class="btn btn-primary" name="submit" value="submit">Submit</button>';
        echo '</div>'; // col 12
        echo '</div>'; // form group row
        echo '</form>';
        echo '</div>'; // card body
        echo '</div>'; // card
    }

    private function sabbatical_academic_year_setup()
   {
       global $wp;
       // get lists for dropdowns
       // display card
       echo '<div class="card">';
       echo '<div class="card-body">';
       echo '<h4 class="card-title">Sabbatical cycle setup</h4>';
       echo '<p class="card-subtitle mb-2 text-muted">Select an Academic Year to change its settings.</p>';
       echo '<form id="rptinfo_sabbatical_setup_form" name="rptinfo_sabbatical_setup_form" action="'
           . esc_url(admin_url('admin-post.php'))
           . '" role="form" method="post" accept-charset="utf-8" class="rptinfo-form ">';
       echo rpt_form_hidden_field('action', 'process_rptinfo_admin_setup');
       echo rpt_form_hidden_field('RedirectURL', home_url($wp->request));
       echo rpt_form_hidden_field('ay', $this->current_cycle->AcademicYear);
       echo rpt_form_hidden_field('RptTemplateTypeID', '5');
       echo rpt_form_dropdown_list('CycleAcademicYear', $this->current_cycle->AcademicYear,
           'Academic year', $this->academic_year_select_list( 'dates' ), 'Display');
       echo rpt_form_number_box('SabbaticalCompLimit', $this->current_cycle->SabbaticalCompLimit,
           'Statutory compensation limit');
       echo rpt_form_date_select('SabbaticalSubmissionStartDate',
           $this->current_cycle->SabbaticalSubmissionStartDate,
           'Start date for sabbatical initializations');
       echo rpt_form_date_select('SabbaticalSubmissionEndDate',
           $this->current_cycle->SabbaticalSubmissionEndDate,
            'End date for sabbatical initializations');
       echo '<button type="submit" class="btn btn-primary" name="submit" value="submit">Submit</button>';
       echo '</div>'; // col 12
       echo '</div>'; // form group row
       echo '</form>';
       echo '</div>'; // card body
       echo '</div>'; // card
   }

   private function sabbatical_allowance_setup()
   {
       global $wp;
       // get lists for dropdowns
       $unit_list = $this->rpt_db->get_level_1_unit_list();
       // display card
       echo '<div class="card">';
       echo '<div class="card-body">';
       echo '<h4 class="card-title">Sabbatical quarter allowances</h4>';
       echo '<p class="card-subtitle mb-2 text-muted">Select an Academic Year to change its allowances.</p>';
       echo '<form id="rptinfo_sabbatical_setup_form" name="rptinfo_sabbatical_setup_form" action="'
           . esc_url(admin_url('admin-post.php'))
           . '" role="form" method="post" accept-charset="utf-8" class="rptinfo-form ">';
       echo rpt_form_hidden_field('action', 'process_rptinfo_admin_setup');
       echo rpt_form_hidden_field('RedirectURL', home_url($wp->request));
       echo rpt_form_hidden_field('ay', $this->current_cycle->AcademicYear);
       echo rpt_form_hidden_field('RptTemplateTypeID', '5');
       echo rpt_form_dropdown_list('SabbaticalAcademicYear', $this->current_cycle->AcademicYear,
           'Academic year', $this->academic_year_select_list( 'allowances' ), 'Display');
       foreach ($unit_list as $unit_key => $unit) {
           echo rpt_form_number_box('Unit-' . $unit_key, '0', $unit);
       }
       echo '<button type="submit" class="btn btn-primary" name="submit" value="submit">Submit</button>';
       echo '</div>'; // col 12
       echo '</div>'; // form group row
       echo '</form>';
       echo '</div>'; // card body
       echo '</div>'; // card
   }

   public function process_rptinfo_admin_setup()
   {
       global $wp;
       $update_values = [];
       $result_status = 'info';
       $result_message = 'No data submitted';
       if ( ! empty($_POST) ) {
           $template_type_id = intval($_POST['RptTemplateTypeID']);
           $ay = intval($_POST['ay']);
           $redirect_url = sanitize_text_field($_POST['RedirectURL']);
           if ( isset($_POST['CycleAcademicYear']) ) {
               if ($template_type_id == 2) {
                   $update_ay = intval($_POST['CycleAcademicYear']);
                   $update_values['PromotionSubmissionStartDate'] = sanitize_text_field($_POST['PromotionSubmissionStartDate']);
                   $update_values['PromotionSubmissionEndDate'] = sanitize_text_field($_POST['PromotionSubmissionEndDate']);
               } elseif ($template_type_id == 5) {
                   $update_ay = intval($_POST['CycleAcademicYear']);
                   $update_values['SabbaticalCompLimit'] = intval($_POST['SabbaticalCompLimit']);
                   $update_values['SabbaticalSubmissionStartDate'] = sanitize_text_field($_POST['SabbaticalSubmissionStartDate']);
                   $update_values['SabbaticalSubmissionEndDate'] = sanitize_text_field($_POST['SabbaticalSubmissionEndDate']);
               }
//           echo '<pre>' . print_r($update_values, true) . '</pre>'; exit;
               $update_result = $this->rpt_db->update_cycle_settings($update_ay, $update_values);
           }
           elseif ( isset($_POST['SabbaticalAcademicYear']) ) {
               $update_ay = intval($_POST['SabbaticalAcademicYear']);
               $update_values['175'] = intval($_POST['Unit-175']);
               $update_values['169'] = intval($_POST['Unit-169']);
               $update_values['181'] = intval($_POST['Unit-181']);
               $update_values['16'] = intval($_POST['Unit-16']);
               $update_values['178'] = intval($_POST['Unit-178']);
               $update_values['153'] = intval($_POST['Unit-153']);
               $update_values['152'] = intval($_POST['Unit-152']);
               $update_values['195'] = intval($_POST['Unit-195']);
               $update_values['86'] = intval($_POST['Unit-86']);
               $update_values['62'] = intval($_POST['Unit-62']);
               $update_values['59'] = intval($_POST['Unit-59']);
               $update_values['126'] = intval($_POST['Unit-126']);
               $update_values['133'] = intval($_POST['Unit-133']);
               $update_values['183'] = intval($_POST['Unit-183']);
               $update_values['58'] = intval($_POST['Unit-58']);
               $update_values['17'] = intval($_POST['Unit-17']);
               $update_values['164'] = intval($_POST['Unit-164']);
               $update_values['118'] = intval($_POST['Unit-118']);
//           echo '<pre>' . print_r($update_values, true) . '</pre>'; exit;
               $update_result = $this->rpt_db->update_sabbatical_allowances($update_ay, $update_values);
           }
           if ( $update_result === 0 ) {
               $result_status = 'error';
               $result_message = 'There was an error updating the settings';
           }
           else {
               $result_status = 'success';
               $result_message = 'The settings have been updated';
           }
       }
       wp_redirect(add_query_arg(array('rpt_page' => 'admin', 'msg' => $result_message,
           'status' => $result_status, 'template_type' => $template_type_id,
           'ay' => $ay), home_url($redirect_url)));
       exit;
   }

   private function academic_year_select_list( $source = 'dates' ) : array
   {
       $result = [];
       if ( $source == 'dates' ) {
           // use the list we've already got
           foreach ($this->cycle_list as $id => $item) {
               $result[$id] = array(
                   'Display' => $item->Display,
                   'PromotionSubmissionStartDate' => $item->PromotionSubmissionStartDate,
                   'PromotionSubmissionEndDate' => $item->PromotionSubmissionEndDate,
                   'SabbaticalCompLimit' => $item->SabbaticalCompLimit,
                   'SabbaticalSubmissionStartDate' => $item->SabbaticalSubmissionStartDate,
                   'SabbaticalSubmissionEndDate' => $item->SabbaticalSubmissionEndDate
               );
           }
       }
       elseif ( $source == 'allowances' ) {
            $result = $this->rpt_db->get_cycle_allowances();
       }
       return $result;
   }
}