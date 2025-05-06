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

    private $rpt_db = NULL;

    private $rpt_user = NULL;

    private $template_types = [];

    private $active_page = 'home';

    private $active_template_type = '';

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
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
            . '" class="btn btn-outline-secondary';
        echo '">RPT Home</a>';
        foreach ($this->template_types as $id => $template_type) {
            echo '<a href="' . esc_url(add_query_arg(array('template_type' => $id,
                    'ay' => $this->current_cycle->AcademicYear,
                    'rpt_page' => 'home'), home_url($wp->request)))
                . '" class="btn btn-outline-secondary';
            echo '">' . $template_type->TemplateTypeName . '</a>';
        }
        echo '</div>'; // button group
        echo '</div>'; // toolbar
        if ( $this->active_template_type != '0' ) {
            echo '<div class="toolbar" role="toolbar" aria-label="Application navigation">';
            echo '<div class="btn-group mr-2" role="group" aria-label="Area sub-pages">';
            echo '<a href="' . esc_url(add_query_arg(array('template_type' => $this->active_template_type,
                    'ay' => $this->current_cycle->AcademicYear,
                    'rpt_page' => 'cases'), home_url($wp->request)))
                . '" class="btn btn-outline-secondary';
            echo '">Cases</a>';
            echo '<a href="' . esc_url(add_query_arg(array('template_type' => $this->active_template_type,
                    'ay' => $this->current_cycle->AcademicYear,
                    'rpt_page' => 'templates'), home_url($wp->request)))
                . '" class="btn btn-outline-secondary';
            echo '">Templates</a>';
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
        $ay = get_query_var('ay', '2025');
        $status_type = get_query_var('status', '');
        $status_message = get_query_var('msg', '');
        $this->active_page = get_query_var('rpt_page', 'home');
        $this->active_template_type = get_query_var('template_type', '0');
        $case_id = get_query_var('case_id', '0');
        if ($status_message) {
            $this->show_status_message($status_type, $status_message);
        }
        $this->rpt_user = $this->rpt_db->get_rpt_user_info($this->wordpress_user->user_login);
        $this->current_cycle = $this->rpt_db->get_cycle_info($ay);
        $this->template_types = $this->rpt_db->get_template_type_list(TRUE);
        echo '<div class="row">';
        echo '<div class="col-12">';
        $this->show_main_menu();
        echo '<p><strong>Selected Academic Year: '
            . $this->current_cycle->Display . '</strong></p>';
        echo '<p><strong>Template Type: '
            . $this->active_template_type . '</strong></p>';
        echo '<p><strong>Page: '
            . $this->active_page . '</strong></p>';
        echo '<p><strong>Case ID: '
            . $case_id . '</strong></p>';
        echo '</div>';
        echo '</div>';
        switch ( $this->active_template_type ) {
            case '0':
                break;
            case '2' : // promotions
            case '5': // sabbaticals
                switch ( $this->active_page ) {
                    case 'cases':
                        $this->case_page();
                        break;
                    case 'templates':
                        $this->template_page();
                        break;
                }
                break;
            default:
                break;
        }
        $this->show_footer();
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
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
        echo '<small>';
        echo '<p>';
        echo 'Wordpress user: ' . $this->wordpress_user->user_login . '<br>';
        echo 'Logged in as: ' . $this->rpt_user->DisplayName . ' (' . $this->rpt_user->UWNetID
            . ' &mdash; ' . $this->rpt_user->InterfolioUserID . ')<br>';
        echo 'Access to: ' . implode(', ', $this->rpt_user->Units) . '<br>';
        echo 'Plugin version: ' . $this->version . '<br>';
        echo '</p>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
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
//            $this->case_edit($case_id, $track_id);
        }
    }

    private function case_list()
    {
        global $wp;
        $this->rpt_case_review_url = get_option('ap_ptinfo_rpt_case_review_url');
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
            . esc_url(add_query_arg(array('rpt_page' => 'cases', 'case_id' => 'new',
                'ay' => $this->current_cycle->AcademicYear,
                'template_type' => $this->active_template_type), home_url($wp->request)))
            . '" class="btn btn-primary">Initiate a new case</a>';
        echo '</div>'; // col 6
        echo '</div>'; // row
        echo '<div class="row">';
        echo '<div class="col-12">';
        if ( count( $case_list ) > 0 ) {
            echo '<table class="table table-border sort-table">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Candidate Name</th>';
            echo '<th>Type</th>';
            echo '<th>Status</th>';
            echo '<th>Action</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            foreach ( $case_list as $case ) {
                echo '<tr class="border-bottom border-right">';
                echo '<td><strong>' . $case->LegalName . ' (' . $case->EmployeeID . ')</strong><br>';
                echo $case->CurrentRankName . ' in ' . $case->UnitName . ' (' . $case->AppointmentType . ')</td>';
                echo '<td>' . $case->PromotionTypeName . '</td>';
                echo '<td>' . $case->CaseStatus;
                if ( $case->InterfolioCaseID ) {
                    echo '<br><a href="https://rpt.interfolio.com/28343/cases/' . $case->InterfolioCaseID . '">Go to case</a>';
                }
                echo '</td>';
                echo '<td>';
                /*                echo '<a href="'
                                    . esc_url(add_query_arg(array('pt_function' => 'case', 'case_id' => $case->CaseID), home_url($wp->request)))
                                    . '" class="btn btn-outline-primary">Edit case</a>'; */
                echo '<a href="'
                    . esc_url(add_query_arg(array('pt_function' => 'case', 'case_id' => $case->CaseID,
                        'ay' => $this->current_cycle->AcademicYear), home_url($wp->request)))
                    . '" class="btn btn-outline-primary">Data Sheet</a>';
                echo '</td>';
                echo '</tr>';
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
            'user_id' => $this->rpt_user->UWNetID,
            'template_type' => $this->active_template_type,
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
            printf('<script type="text/javascript">let my_ajax_obj = %s</script>', json_encode($ajax_object));
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
        $user_units = array_keys($this->rpt_db->get_user_units($user_netid));
        $unit_query = $this->rpt_db->get_last_query();
        $template_type = intval($_POST['template_type']);
        switch ( $template_type ) {
            case '2':
                $search_result = $this->rpt_db->promotion_candidate_search($user_units, $search_string);
                break;
        }

        $response = [
            'status' => 'ok',
            'searchstring' => $search_string,
            'query' => $this->rpt_db->get_last_query(),
            'data' => $search_result
        ];
        wp_send_json($response);
    }



    private function template_page()
    {
        echo '<p>' . $this->template_types[$this->active_template_type]->TemplateTypeName
            . ' Template maintenance page</p>';
        global $wp;
        $template_list = array();
        $template_type = get_query_var('template_type', '');
        $template_id = get_query_var('template_id', '');
        $in_use = get_query_var('in_use', '');
    }

}
