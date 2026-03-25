<?php

namespace WPFormsGoogleSheets\Api;

// phpcs:ignore WPForms.PHP.UseStatement.UnusedUseStatement
use WP_Error;

/**
 * API Request class.
 *
 * @since 1.0.0
 */
final class Request {

	/**
	 * The API base URL.
	 *
	 * @since 1.0.0
	 */
	const BASE = 'https://google.wpforms.com/v2/';

	/**
	 * An endpoint route.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $route;

	/**
	 * List of arguments.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $args;

	/**
	 * Request method.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $method;

	/**
	 * Get API base URL.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 * @noinspection PhpUndefinedConstantInspection
	 */
	public static function get_api_base_url() {

		return defined( 'WPFORMS_GOOGLE_SHEETS_API_URL' ) ? WPFORMS_GOOGLE_SHEETS_API_URL : self::BASE;
	}

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $route  The API route to target.
	 * @param array  $args   Array of API credentials.
	 * @param string $method The API method.
	 */
	public function __construct( $route, $args, $method ) {

		$this->route  = $route;
		$this->args   = $args;
		$this->method = $method;
	}

	/**
	 * Processes the API request.
	 *
	 * @since 1.0.0
	 *
	 * @return array|bool $value The response to the API call.
	 *
	 * @throws RequestException Error message for a failed request.
	 */
	public function request() {

		$credentials = wpforms_google_sheets()->get( 'account' )->get_credentials();

		/**
		 * Allow modifying body arguments.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args List of body args.
		 */
		$body = apply_filters(
			'wpforms_google_sheets_api_request_body_args',
			wp_parse_args(
				$this->args,
				[
					'token'    => empty( $credentials['token'] ) ? '' : $credentials['token'],
					'key'      => empty( $credentials['key'] ) ? '' : $credentials['key'],
					'siteurl'  => site_url(),
					'license'  => wpforms_get_license_key(),
					'plugin'   => 'wpforms',
					'version'  => WPFORMS_GOOGLE_SHEETS_VERSION,
					'timezone' => gmdate( 'e' ),
					'network'  => 'site',
					'ip'       => wpforms_get_ip(),
				]
			)
		);

		/**
		 * Allow modifying request arguments.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args List of body args.
		 */
		$request_args = apply_filters(
			'wpforms_google_sheets_api_request_args',
			[
				'headers'    => [
					'Content-Type'  => 'application/x-www-form-urlencoded',
					'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0',
					'Pragma'        => 'no-cache',
					'Expires'       => 0,
					'MIAPI-Referer' => site_url(),
					'MIAPI-Sender'  => 'WordPress',
				],
				'method'     => $this->method,
				'body'       => $body,
				'timeout'    => 3000,
				'user-agent' => 'WPForms/' . WPFORMS_GOOGLE_SHEETS_VERSION . '; ' . site_url(),
				'sslverify'  => false,
			]
		);

		$response = wp_remote_request( self::get_api_base_url() . $this->route, $request_args );

		return $this->retrieve_response_body( $response );
	}

	/**
	 * Retrieve response body.
	 *
	 * @since 1.0.0
	 *
	 * @param array|WP_Error $response The response to the API call.
	 *
	 * @return array|bool
	 *
	 * @throws RequestException Error message for a failed request.
	 */
	private function retrieve_response_body( $response ) {

		if ( is_wp_error( $response ) ) {
			throw new RequestException( $response->get_error_message() );
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( $response_code === 200 && is_array( $response_body ) ) {
			return isset( $response_body['data'] ) ? $response_body['data'] : empty( $response_body['error'] );
		}

		if ( ! empty( $response_body['message'] ) ) {
			throw new RequestException(
				sprintf(
					'The API returned a <strong>%1$d</strong> response with this message: <strong>%2$s</strong>',
					absint( $response_code ),
					esc_html( $response_body['message'] )
				)
			);
		}

		if ( ! empty( $response_body['error'] ) ) {
			throw new RequestException(
				sprintf(
					'The API returned a <strong>%1$d</strong> response with this message: <strong>%2$s</strong>',
					absint( $response_code ),
					esc_html( $response_body['error'] )
				)
			);
		}

		throw new RequestException( 'The API was unreachable' );
	}
}
