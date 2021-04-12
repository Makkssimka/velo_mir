<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since             1.0.0
 * @package           Importer
 *
 * @wordpress-plugin
 * Plugin Name:       importer
 * Plugin URI:        /
 * Description:       Плагин для экспорта данных из 1С
 * Version:           1.0.0
 * Author:            Makkssimka
 * Author URI:        /
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       importer
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Update it as you release new versions.
 */
define( 'IMPORTER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-importer-activator.php
 */
function activate_importer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-importer-activator.php';
	Importer_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-importer-deactivator.php
 */
function deactivate_importer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-importer-deactivator.php';
	Importer_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_importer' );
register_deactivation_hook( __FILE__, 'deactivate_importer' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-importer.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_importer() {

	$plugin = new Importer();
	$plugin->run();

}
run_importer();
