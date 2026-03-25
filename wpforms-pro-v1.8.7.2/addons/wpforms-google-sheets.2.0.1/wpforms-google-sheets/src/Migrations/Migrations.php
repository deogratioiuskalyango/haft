<?php

namespace WPFormsGoogleSheets\Migrations;

use WPForms\Migrations\Base;

/**
 * Class Migrations handles addon upgrade routines.
 *
 * @since 2.0.0
 */
class Migrations extends Base {

	/**
	 * WP option name to store the migration versions.
	 *
	 * @since 2.0.0
	 */
	const MIGRATED_OPTION_NAME = 'wpforms_google_sheets_versions';

	/**
	 * Current plugin version.
	 *
	 * @since 2.0.0
	 */
	const CURRENT_VERSION = WPFORMS_GOOGLE_SHEETS_VERSION;

	/**
	 * Name of plugin used in log messages.
	 *
	 * @since 2.0.0
	 */
	const PLUGIN_NAME = 'WPForms Google Sheets';

	/**
	 * Upgrade classes.
	 *
	 * @since 2.0.0
	 */
	const UPGRADE_CLASSES = [
		'Upgrade200',
	];
}
