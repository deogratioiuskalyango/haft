<?php
/**
 * Plugin Name:       WPForms Google Sheets
 * Plugin URI:        https://wpforms.com
 * Description:       Google Sheets integration with WPForms.
 * Author:            WPForms
 * Author URI:        https://wpforms.com
 * Version:           2.0.1
 * Requires at least: 5.2
 * Requires PHP:      5.6
 * Text Domain:       wpforms-google-sheets
 * Domain Path:       /languages
 *
 * WPForms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WPForms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WPForms. If not, see <https://www.gnu.org/licenses/>.
 *
 * @since     1.0.0
 * @author    WPForms
 * @package   WPFormsGoogleSheets
 * @license   GPL-2.0+
 * @copyright Copyright (c) 2022, WPForms LLC
 */

use WPFormsGoogleSheets\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WPForms Google Sheets version.
 *
 * @since 1.0.0
 */
const WPFORMS_GOOGLE_SHEETS_VERSION = '2.0.1';

/**
 * WPForms Google Sheets path to main file.
 *
 * @since 1.0.0
 */
const WPFORMS_GOOGLE_SHEETS_FILE = __FILE__;

/**
 * WPForms Google Sheets path to directory.
 *
 * @since 1.0.0
 */
define( 'WPFORMS_GOOGLE_SHEETS_PATH', plugin_dir_path( WPFORMS_GOOGLE_SHEETS_FILE ) );

/**
 * WPForms Google Sheets URL to directory.
 *
 * @since 1.0.0
 */
define( 'WPFORMS_GOOGLE_SHEETS_URL', plugin_dir_url( WPFORMS_GOOGLE_SHEETS_FILE ) );

/**
 * Check addon requirements.
 *
 * @since 1.0.0
 * @since 2.0.0 Uses requirements feature.
 */
function wpforms_google_sheets_load() {

	$requirements = [
		'file'    => WPFORMS_GOOGLE_SHEETS_FILE,
		'wpforms' => '1.8.3',
	];

	if ( ! function_exists( 'wpforms_requirements' ) || ! wpforms_requirements( $requirements ) ) {
		return;
	}

	wpforms_google_sheets();
}

add_action( 'wpforms_loaded', 'wpforms_google_sheets_load' );

/**
 * Get the instance of the addon main class.
 *
 * @since 2.0.0
 *
 * @return Plugin
 */
function wpforms_google_sheets() {

	require_once WPFORMS_GOOGLE_SHEETS_PATH . 'vendor/autoload.php';

	return Plugin::get_instance();
}
