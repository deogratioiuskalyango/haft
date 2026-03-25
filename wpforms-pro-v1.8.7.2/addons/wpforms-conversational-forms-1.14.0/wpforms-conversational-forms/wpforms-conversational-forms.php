<?php
/**
 * Plugin Name:       WPForms Conversational Forms
 * Plugin URI:        https://wpforms.com
 * Description:       Create Conversational Forms with WPForms.
 * Requires at least: 5.5
 * Requires PHP:      7.0
 * Author:            WPForms
 * Author URI:        https://wpforms.com
 * Version:           1.14.0
 * Text Domain:       wpforms-conversational-forms
 * Domain Path:       languages
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
 */

use WPFormsConversationalForms\Loader;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin version.
 *
 * @since 1.0.0
 */
const WPFORMS_CONVERSATIONAL_FORMS_VERSION = '1.14.0';

/**
 * Plugin file.
 *
 * @since 1.7.0
 */
const WPFORMS_CONVERSATIONAL_FORMS_FILE = __FILE__;

/**
 * Plugin path.
 *
 * @since 1.12.0
 */
define( 'WPFORMS_CONVERSATIONAL_FORMS_PATH', plugin_dir_path( WPFORMS_CONVERSATIONAL_FORMS_FILE ) );

/**
 * Check addon requirements.
 *
 * @since 1.0.0
 * @since 1.12.0 Uses requirements feature.
 */
function wpforms_conversational_forms_load() {

	$requirements = [
		'file'    => WPFORMS_CONVERSATIONAL_FORMS_FILE,
		'wpforms' => '1.8.7',
	];

	if ( ! function_exists( 'wpforms_requirements' ) || ! wpforms_requirements( $requirements ) ) {
		return null;
	}

	wpforms_conversational_forms();
}

add_action( 'wpforms_loaded', 'wpforms_conversational_forms_load' );

/**
 * Get the instance of the addon main class.
 *
 * @since 1.12.0
 *
 * @return Loader
 */
function wpforms_conversational_forms() {

	require_once WPFORMS_CONVERSATIONAL_FORMS_PATH . 'vendor/autoload.php';

	return Loader::get_instance();
}
