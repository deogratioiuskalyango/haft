<?php

namespace WPFormsGoogleSheets\Migrations;

use WPFormsGoogleSheets\Api\Cache;
use WPForms\Migrations\UpgradeBase;
use WPFormsGoogleSheets\Provider\Account;

/**
 * Class Google Sheets addon v2.0.0 upgrade.
 *
 * @since 2.0.0
 */
class Upgrade200 extends UpgradeBase {

	/**
	 * Run upgrade.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function run() {

		if ( ! empty( wpforms_google_sheets()->get( 'account' )->get_credentials() ) ) {
			update_option( Account::FORCE_REAUTH_OPTION, true );

			( new Cache() )->delete_all();
		}

		return true;
	}
}
