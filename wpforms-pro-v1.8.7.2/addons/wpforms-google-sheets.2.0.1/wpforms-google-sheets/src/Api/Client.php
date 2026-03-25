<?php

namespace WPFormsGoogleSheets\Api;

/**
 * Client for optimized work with API.
 *
 * @since 1.0.0
 */
class Client {

	/**
	 * Api.
	 *
	 * @since 1.0.0
	 *
	 * @var Api
	 */
	private $api;

	/**
	 * Cache.
	 *
	 * @since 1.0.0
	 *
	 * @var Cache
	 */
	private $cache;

	/**
	 * One-time token instance.
	 *
	 * @since 1.0.0
	 *
	 * @var OneTimeToken
	 */
	private $one_time_token;

	/**
	 * Site ID generator.
	 *
	 * @since 1.0.0
	 *
	 * @var SiteId
	 */
	private $site_id;

	/**
	 * Constructor method.
	 *
	 * @since 1.0.0
	 *
	 * @param Api          $api            Api.
	 * @param Cache        $cache          Cache.
	 * @param OneTimeToken $one_time_token One-time token instance.
	 * @param SiteId       $site_id        Site ID generator.
	 */
	public function __construct( Api $api, Cache $cache, OneTimeToken $one_time_token, SiteId $site_id ) {

		$this->api            = $api;
		$this->cache          = $cache;
		$this->one_time_token = $one_time_token;
		$this->site_id        = $site_id;
	}

	/**
	 * Get access token.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_access_token() {

		// phpcs:ignore WPForms.Formatting.EmptyLineBeforeReturn.RemoveEmptyLineBeforeReturnStatement
		return $this->api->get_access_token();
	}

	/**
	 * Get list of spreadsheets.
	 *
	 * @since 1.0.0
	 * @deprecated 2.0.0
	 *
	 * @return array
	 */
	public function get_spreadsheets() {

		_deprecated_function( __METHOD__, '2.0.0 of the WPForms Google Sheets addon' );

		$spreadsheets = $this->cache->get( 'spreadsheets' );

		if ( $spreadsheets ) {
			return $spreadsheets;
		}

		$spreadsheets = $this->api->get_spreadsheets();

		$this->cache->set( $spreadsheets, 10, 'spreadsheets' );

		return $spreadsheets;
	}

	/**
	 * Get sheets list.
	 *
	 * @since 1.0.0
	 *
	 * @param string $spreadsheet_id Spreadsheet ID.
	 *
	 * @return array
	 */
	public function get_sheets( $spreadsheet_id ) {

		$sheets = $this->cache->get( $spreadsheet_id );

		if ( $sheets ) {
			return $sheets;
		}

		$sheets = $this->api->get_sheets( $spreadsheet_id );

		$this->cache->set( $sheets, 15, $spreadsheet_id );

		return $sheets;
	}

	/**
	 * Get columns list.
	 *
	 * @since 1.0.0
	 *
	 * @param string $spreadsheet_id Spreadsheet ID.
	 * @param int    $sheet_id       Sheet ID.
	 *
	 * @return array
	 */
	public function get_columns( $spreadsheet_id, $sheet_id ) {

		$columns = $this->cache->get( $spreadsheet_id, $sheet_id );

		if ( $columns ) {
			return $columns;
		}

		$columns = $this->api->get_all_headings( $spreadsheet_id, $sheet_id );

		$this->cache->set( $columns, 15, $spreadsheet_id, $sheet_id );

		return $columns;
	}

	/**
	 * Get list of default columns.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_default_columns() {

		return $this->api->default_columns();
	}

	/**
	 * Create a new spreadsheet.
	 *
	 * @since 1.0.0
	 *
	 * @param string $spreadsheet_name Spreadsheet name.
	 *
	 * @return string
	 */
	public function create_spreadsheet( $spreadsheet_name ) {

		$spreadsheet_id = $this->api->create_spreadsheet( $spreadsheet_name );

		$this->cache->delete( 'spreadsheets' );

		return $spreadsheet_id;
	}

	/**
	 * Create a new sheet.
	 *
	 * @since 1.0.0
	 *
	 * @param string     $spreadsheet_id Spreadsheet ID.
	 * @param string     $name           Sheet name.
	 * @param string|int $id             Sheet ID.
	 *
	 * @return int
	 */
	public function create_sheet( $spreadsheet_id, $name, $id = '' ) {

		$sheet_id = $this->api->create_sheet( $spreadsheet_id, $name, $id );

		$this->cache->delete( $spreadsheet_id );

		return $sheet_id;
	}

	/**
	 * Put fields labels to the 1st line of the spreadsheet list.
	 *
	 * @since 1.0.0
	 *
	 * @param string $spreadsheet_id Spreadsheet ID.
	 * @param int    $sheet_id       Sheet ID.
	 * @param array  $headings       List of headings for update.
	 *
	 * @return bool
	 */
	public function update_headings( $spreadsheet_id, $sheet_id, $headings ) {

		$this->cache->delete( $spreadsheet_id, $sheet_id );

		return $this->api->update_headings( $spreadsheet_id, $sheet_id, $headings );
	}

	/**
	 * Deactivate account on the relay.
	 *
	 * @since 1.0.0
	 *
	 * @param array $credentials Account credentials.
	 */
	public function deactivate( $credentials ) {

		$this->api->deactivate( $credentials, $this->one_time_token->get() );
		$this->one_time_token->refresh();
		$this->cache->delete_all();
	}

	/**
	 * Determine if the account is valid.
	 *
	 * @since 1.0.0
	 *
	 * @param array $credentials List of credentials.
	 *
	 * @return bool
	 */
	public function verify_auth( $credentials ) {

		$is_valid = $this->api->verify_auth( $credentials, $this->one_time_token->get() );

		if ( $is_valid ) {
			$this->one_time_token->refresh();
		}

		return $is_valid;
	}

	/**
	 * Add a new account URL.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args List of arguments.
	 * @param string $type Authorization type.
	 *
	 * @return string
	 */
	public function get_auth_url( $args, $type = 'pro' ) {

		$type     = $type === 'custom' ? 'custom' : 'pro';
		$base_url = trailingslashit( Request::get_api_base_url() . 'auth/new/' . $type );

		return add_query_arg( $this->prepare_auth_args( $args ), $base_url );
	}

	/**
	 * Reconnect account URL.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args List of arguments.
	 * @param string $type Authorization type.
	 *
	 * @return string
	 */
	public function get_reauth_url( $args, $type = 'pro' ) {

		$credentials        = wpforms_google_sheets()->get( 'account' )->get_credentials();
		$type               = $type === 'custom' ? 'custom' : 'pro';
		$args['key']        = isset( $credentials['key'] ) ? $credentials['key'] : '';
		$args['token']      = isset( $credentials['token'] ) ? $credentials['token'] : '';
		$args['login_hint'] = isset( $credentials['label'] ) ? $credentials['label'] : '';

		if ( ! empty( $credentials['project_id'] ) ) {
			$args['projectid'] = $credentials['project_id'];
			$type              = 'custom';
		}

		$base_url = trailingslashit( Request::get_api_base_url() . 'auth/reauth/' . $type );

		return add_query_arg( $this->prepare_auth_args( $args ), $base_url );
	}

	/**
	 * Prepare arguments for authorization and reauthorization URL.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Passed arguments for authorization.
	 *
	 * @return array
	 */
	private function prepare_auth_args( $args ) {

		$args = wp_parse_args(
			$args,
			[
				'tt'      => $this->one_time_token->get(),
				'siteid'  => $this->site_id->get(),
				'version' => WPFORMS_GOOGLE_SHEETS_VERSION,
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'network' => 'site',
				'siteurl' => site_url(),
				'testurl' => Request::get_api_base_url() . 'test/',
				'license' => wpforms_get_license_key(),
				'amc'     => defined( 'WPFORMS_GOOGLE_SHEETS_AMC' ) ? WPFORMS_GOOGLE_SHEETS_AMC : '',
			]
		);

		if ( ! empty( $args['return'] ) ) {
			$args['return'] = rawurlencode( $args['return'] );
		}

		return $args;
	}

	/**
	 * Verify the one time token value.
	 *
	 * @since 1.0.0
	 *
	 * @param string $passed_one_time_token A passed one time token value.
	 *
	 * @return bool
	 */
	public function is_valid_one_time_token( $passed_one_time_token ) {

		return $this->one_time_token->validate( $passed_one_time_token );
	}

	/**
	 * Complete the reconnection process.
	 *
	 * @since 1.0.0
	 */
	public function finish_reconnection() {

		$this->one_time_token->refresh();
	}

	/**
	 * Get filled headings.
	 *
	 * @since 1.0.0
	 *
	 * @param string $spreadsheet_id Spreadsheet ID.
	 * @param int    $sheet_id       Sheet ID.
	 *
	 * @return array
	 */
	public function get_filled_headings( $spreadsheet_id, $sheet_id ) {

		return $this->api->get_filled_headings( $spreadsheet_id, $sheet_id );
	}

	/**
	 * Append row to the spreadsheet.
	 *
	 * @since 1.0.0
	 *
	 * @param string $spreadsheet_id Spreadsheet ID.
	 * @param int    $sheet_id       Sheet ID.
	 * @param array  $values         List of values for the row.
	 *
	 * @return bool
	 */
	public function append( $spreadsheet_id, $sheet_id, $values ) {

		return $this->api->append( $spreadsheet_id, $sheet_id, $values );
	}
}
