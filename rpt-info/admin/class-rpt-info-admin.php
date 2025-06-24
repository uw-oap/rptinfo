<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ap.washington.edu
 * @since      1.0.0
 *
 * @package    Rpt_Info
 * @subpackage Rpt_Info/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rpt_Info
 * @subpackage Rpt_Info/admin
 * @author     Jon Davis <jld36@uw.edu>
 */
class Rpt_Info_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rpt-info-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rpt-info-admin.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * Add an options page under the Settings submenu
     *
     * @since  1.0.0
     */
    public function add_options_page()
    {
        $this->plugin_screen_hook_suffix = add_options_page(
            __( 'RPTinfo Settings', 'rptinfo' ),
            __( 'RPTinfo', 'rptinfo' ),
            'manage_options',
            $this->plugin_name,
            array( $this, 'display_options_page' )
        );
    }

    /**
     * Render the options page for plugin
     *
     * @since  1.0.0
     */
    public function display_options_page()
    {
        $this->template_types = $this->rpt_db->get_template_type_list();
        $template_types = $this->template_types;
        include_once 'partials/rpt-info-admin-display.php';
    }

    public function register_settings()
    {
        // General Interfolio settings
        add_settings_section(
            $this->option_name . '_interfolio_setup',
            __( 'Interfolio', 'rptinfo' ),
            array( $this, $this->option_name . '_interfolio_setup_cb' ),
            $this->plugin_name
        );
        add_settings_field(
            $this->option_name . '_tenant_id',
            __( 'Interfolio tenant ID', 'rptinfo' ),
            array( $this, $this->option_name . '_tenant_id_cb' ),
            $this->plugin_name,
            $this->option_name . '_interfolio_setup',
            array( 'label_for' => $this->option_name . '_tenant_id' )
        );
        register_setting( $this->plugin_name, $this->option_name . '_tenant_id' );
        add_settings_field(
            $this->option_name . '_rpt_site_url',
            __( 'RPT home URL', 'rptinfo' ),
            array( $this, $this->option_name . '_rpt_site_url_cb' ),
            $this->plugin_name,
            $this->option_name . '_interfolio_setup',
            array( 'label_for' => $this->option_name . '_rpt_site_url' )
        );
        register_setting( $this->plugin_name, $this->option_name . '_rpt_site_url' );
        add_settings_field(
            $this->option_name . '_admin_unit_id',
            __( 'Admin unit ID', 'rptinfo' ),
            array( $this, $this->option_name . '_admin_unit_id_cb' ),
            $this->plugin_name,
            $this->option_name . '_interfolio_setup',
            array( 'label_for' => $this->option_name . '_admin_unit_id' )
        );
        register_setting( $this->plugin_name, $this->option_name . '_admin_unit_id' );
    }


    /**
     * Render the text for the Interfolio section
     *
     * @since  1.0.0
     */
    public function rpt_info_interfolio_setup_cb() {
        echo '<p>' . __( 'Settings dealing with Interfolio.', 'rptinfo' ) . '</p>';
    }

    /**
     * Setting callback function - RPT site URL
     *
     * @since  1.0.0
     */
    public function rpt_info_tenant_id_cb()
    {
        $tenant_id = get_option( $this->option_name . '_tenant_id' );
        echo '<input type="text" name="' . $this->option_name . '_tenant_id'
            . '" id="' . $this->option_name . '_tenant_id'
            . '" size="100'
            . '" value="' . $tenant_id
            . '">';
        echo '<p><em>The Interfolio tenant ID.</em></p>';
    }

    /**
     * Setting callback function - RPT site URL
     *
     * @since  1.0.0
     */
    public function rpt_info_rpt_site_url_cb()
    {
        $site_url = get_option( $this->option_name . '_rpt_site_url' );
        echo '<input type="text" name="' . $this->option_name . '_rpt_site_url'
            . '" id="' . $this->option_name . '_rpt_site_url'
            . '" size="100'
            . '" value="' . $site_url
            . '">';
        echo '<p><em>Link to the Interfolio RPT home page.</em></p>';
    }

    /**
     * Setting callback function - RPT site URL
     *
     * @since  1.0.0
     */
    public function rpt_info_admin_unit_id_cb()
    {
        $unit_id = get_option( $this->option_name . '_admin_unit_id' );
        echo '<input type="text" name="' . $this->option_name . '_admin_unit_id'
            . '" id="' . $this->option_name . '_admin_unit_id'
            . '" size="100'
            . '" value="' . $unit_id
            . '">';
        echo '<p><em>The ID of the unit where system admins are assigned.</em></p>';
    }

}
