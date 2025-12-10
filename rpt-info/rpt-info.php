<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://ap.washington.edu
 * @since             1.0.0
 * @package           Rpt_Info
 *
 * @wordpress-plugin
 * Plugin Name:       RPTinfo
 * Plugin URI:        https://ap.washington.edu
 * Description:       Connect with Interfolio RPT module
 * Version:           2.0.10
 * Author:            Jon Davis
 * Author URI:        https://ap.washington.edu/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rpt-info
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'RPT_INFO_VERSION', '2.1.7' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-rpt-info-activator.php
 */
function activate_rpt_info() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rpt-info-activator.php';
	Rpt_Info_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-rpt-info-deactivator.php
 */
function deactivate_rpt_info() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rpt-info-deactivator.php';
	Rpt_Info_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_rpt_info' );
register_deactivation_hook( __FILE__, 'deactivate_rpt_info' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-rpt-info.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_rpt_info() {

	$plugin = new Rpt_Info();
	$plugin->run();

}

run_rpt_info();
