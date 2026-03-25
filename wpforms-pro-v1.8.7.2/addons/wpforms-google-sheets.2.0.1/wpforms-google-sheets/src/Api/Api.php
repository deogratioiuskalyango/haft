<?php

namespace WPFormsGoogleSheets\Api;

/**
 * Api class.
 *
 * @since 1.0.0
 */
class Api {

	/**
	 * Determine if the account is valid.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $credentials    List of credentials.
	 * @param string $one_time_token One time token.
	 *
	 * @return bool
	 */
	public function verify_auth( $credentials, $one_time_token ) {

		$type = 'pro';
		$args = [
			'network' => false,
			'siteurl' => site_url(),
			'tt'      => $one_time_token,
			'key'     => $credentials['key'],
			'token'   => $credentials['token'],
			'testurl' => Request::get_api_base_url() . 'test/',
			'version' => WPFORMS_GOOGLE_SHEETS_VERSION,
		];

		if ( ! empty( $credentials['project_id'] ) ) {
			$args['projectid'] = $credentials['project_id'];
			$type              = 'custom';
		}

		return (bool) $this->request( 'auth/verify/' . $type . '/', $args, 'POST' );
	}

	/**
	 * Deactivate the authorization.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $credentials    List of credentials.
	 * @param string $one_time_token One time token.
	 *
	 * @return bool
	 */
	public function deactivate( $credentials, $one_time_token ) {

		$type = ! empty( $credentials['project_id'] ) ? 'custom' : 'pro';

		return (bool) $this->request(
			'auth/delete/' . $type . '/',
			[
				'network' => false,
				'tt'      => $one_time_token,
				'key'     => ! empty( $credentials['key'] ) ? $credentials['key'] : '',
				'token'   => ! empty( $credentials['token'] ) ? $credentials['token'] : '',
				'testurl' => Request::get_api_base_url() . 'test/',
			],
			'POST'
		);
	}

	/**
	 * Get access token.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_access_token() {

		$access_token = $this->request( 'endpoints/access-token/', [] );

		return is_string( $access_token ) ? $access_token : '';
	}

	/**
	 * Get list of available spreadsheets.
	 *
	 * @since 1.0.0
	 * @deprecated 2.0.0
	 *
	 * @return array
	 */
	public function get_spreadsheets() {

		_deprecated_function( __METHOD__, '2.0.0 of the WPForms Google Sheets addon' );

		$spreadsheets = $this->request( 'endpoints/spreadsheets/', [] );

		return is_array( $spreadsheets ) ? $spreadsheets : [];
	}

	/**
	 * Get list of spreadsheet sheets.
	 *
	 * @since 1.0.0
	 *
	 * @param string $spreadsheet_id Spreadsheet ID.
	 *
	 * @return array
	 */
	public function get_sheets( $spreadsheet_id ) {

		$sheets = $this->request( 'endpoints/sheets/', [ 'spreadsheet_id' => $spreadsheet_id ] );

		return is_array( $sheets ) ? $sheets : [];
	}

	/**
	 * Create a new spreadsheet.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Spreadsheet name.
	 *
	 * @return string
	 */
	public function create_spreadsheet( $name ) {

		return (string) $this->request( 'endpoints/spreadsheets/', [ 'name' => $name ], 'POST' );
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

		return (int) $this->request(
			'endpoints/sheets/',
			[
				'spreadsheet_id' => $spreadsheet_id,
				'name'           => $name,
				'id'             => $id,
			],
			'POST'
		);
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

		if ( $sheet_id === 'new' ) {
			return [];
		}

		$headings = $this->request(
			'endpoints/headings/filled/',
			[
				'spreadsheet_id' => $spreadsheet_id,
				'sheet_id'       => $sheet_id,
			]
		);

		return is_array( $headings ) ? $headings : [];
	}

	/**
	 * Get all available headings.
	 *
	 * @since 1.0.0
	 *
	 * @param string $spreadsheet_id Spreadsheet ID.
	 * @param int    $sheet_id       Sheet ID.
	 *
	 * @return array
	 */
	public function get_all_headings( $spreadsheet_id, $sheet_id ) {

		if ( $sheet_id === 'new' ) {
			return $this->default_columns();
		}

		$headings = $this->request(
			'endpoints/headings/all/',
			[
				'spreadsheet_id' => $spreadsheet_id,
				'sheet_id'       => $sheet_id,
			]
		);

		if ( ! is_array( $headings ) || empty( $headings ) ) {
			return $this->default_columns();
		}

		foreach ( $headings as $column_name => $column_heading ) {
			$column_label             = sprintf( /* translators: %s is a column name. */ 'Column %s', $column_name );
			$headings[ $column_name ] = wpforms_is_empty_string( $column_heading ) ? $column_label : sprintf( '%s (%s)', $column_heading, $column_label );
		}

		$headings['A'] = esc_html__( 'Entry ID (Column A)', 'wpforms-google-sheets' );

		return $headings;
	}

	/**
	 * Put fields labels to the 1st line of the sheet.
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

		return $this->request(
			'endpoints/headings/',
			[
				'spreadsheet_id' => $spreadsheet_id,
				'sheet_id'       => $sheet_id,
				'headings'       => $headings,
			],
			'POST'
		);
	}

	/**
	 * Prepare default column names for an empty list.
	 *
	 * @since 1.0.0
	 * @since 2.0.0 Method is public.
	 *
	 * @return array
	 */
	public function default_columns() {

		$headings = [];
		$alphabet = range( 'A', 'Z' );

		foreach ( $alphabet as $column_name ) {
			$headings[ $column_name ] = sprintf( /* translators: %s is a column name. */
				esc_html__( 'Column %s', 'wpforms-google-sheets' ),
				$column_name
			);
		}

		$headings['A'] = esc_html__( 'Entry ID (Column A)', 'wpforms-google-sheets' );

		return $headings;
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

		return (bool) $this->request(
			'endpoints/row/',
			[
				'spreadsheet_id' => $spreadsheet_id,
				'sheet_id'       => $sheet_id,
				'values'         => $values,
			],
			'POST'
		);
	}

	/**
	 * Make a request.
	 *
	 * @since 1.0.0
	 *
	 * @param string $route  Endpoint name.
	 * @param array  $args   List of arguments.
	 * @param string $method Request method.
	 *
	 * @return array|bool|null
	 */
	private function request( $route, $args, $method = 'GET' ) {

		try {
			return ( new Request( $route, $args, $method ) )->request();
		} catch ( RequestException $e ) {
			wpforms_log(
				'API request to Google Sheets failed',
				[
					'message' => $e->getMessage(),
					'route'   => $route,
					'method'  => $method,
				],
				[
					'type' => [ 'provider', 'error' ],
				]
			);

			return null;
		}
	}
}
