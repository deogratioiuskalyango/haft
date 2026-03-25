<?php

namespace WPFormsGoogleSheets\Provider;

use WPFormsGoogleSheets\Plugin;
use WPFormsGoogleSheets\Api\Request;

/**
 * Class Account.
 *
 * @since 1.0.0
 */
class Account {

	/**
	 * Reconnect option name.
	 *
	 * @since 2.0.0
	 */
	const FORCE_REAUTH_OPTION = 'wpforms_google_sheets_required_reconnect';

	/**
	 * Nonce key for authorization.
	 *
	 * @since 1.0.0
	 */
	const NONCE = 'wpforms_google_sheets_auth';

	/**
	 * Is the account connected?
	 *
	 * @since 1.0.0
	 *
	 * @var bool|null
	 */
	private $is_connected;

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		add_action( 'admin_init', [ $this, 'authenticate_listener' ] );
		add_action( 'admin_init', [ $this, 'reauthenticate_listener' ] );
		add_action( 'wp_ajax_nopriv_wpforms_rauthenticate', [ $this, 'rauthenticate' ] );
	}

	/**
	 * Deactivate current account.
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {

		$credentials = $this->get_credentials();

		wpforms_google_sheets()->get( 'client' )->deactivate( $credentials );

		delete_option( self::FORCE_REAUTH_OPTION );
	}

	/**
	 * One token callback.
	 *
	 * @since 1.0.0
	 */
	public function rauthenticate() {

		$required_args = [ 'key', 'token', 'tt', 'network' ];

		foreach ( $required_args as $arg ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( empty( $_REQUEST[ $arg ] ) ) {
				wp_send_json_error(
					[
						'error'   => 'authenticate_missing_arg',
						'message' => sprintf( /* translators: %s is an argument name. */
							esc_html__( 'The %s authenticate parameter is missing', 'wpforms-google-sheets' ),
							esc_html( $arg )
						),
						'version' => WPFORMS_GOOGLE_SHEETS_VERSION,
						'pro'     => wpforms()->is_pro(),
					]
				);
			}
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		if ( ! wpforms_google_sheets()->get( 'client' )->is_valid_one_time_token( $_REQUEST['tt'] ) ) {
			wp_send_json_error(
				[
					'error'   => 'authenticate_invalid_tt',
					'message' => esc_html__( 'Invalid one time token sent', 'wpforms-google-sheets' ),
					'version' => WPFORMS_GOOGLE_SHEETS_VERSION,
					'pro'     => wpforms()->is_pro(),
				]
			);
		}

		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		wp_send_json_success();
	}

	/**
	 * Authenticate listener.
	 *
	 * @since 1.0.0
	 */
	public function authenticate_listener() { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		if ( ! $this->is_auth_handler() ) {
			return;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended

		if ( empty( $_REQUEST['key'] ) || empty( $_REQUEST['token'] ) || empty( $_REQUEST['user_email'] ) ) {
			$this->remove_query_args();

			return;
		}

		$account = [
			'key'   => sanitize_text_field( wp_unslash( $_REQUEST['key'] ) ),
			'token' => sanitize_text_field( wp_unslash( $_REQUEST['token'] ) ),
			'label' => sanitize_text_field( wp_unslash( $_REQUEST['user_email'] ) ),
			'date'  => time(),
		];

		if ( ! empty( $_REQUEST['projectid'] ) ) {
			$account['project_id'] = sanitize_key( $_REQUEST['projectid'] );
		}

		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		if ( ! wpforms_google_sheets()->get( 'client' )->verify_auth( $account ) ) {
			$this->remove_query_args();

			return;
		}

		$this->save_account( $account );

		add_filter( 'wpforms_builder_panel_sidebar_section_classes', [ $this, 'open_builder_tab' ], 10, 4 );

		$this->remove_query_args();
	}

	/**
	 * Open builder tab.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $classes Sidebar section classes.
	 * @param string $name    Sidebar section name.
	 * @param string $slug    Sidebar section slug.
	 * @param string $icon    Sidebar section icon.
	 *
	 * @return array
	 */
	public function open_builder_tab( $classes, $name, $slug, $icon ) {

		if ( $slug !== Plugin::SLUG ) {
			return $classes;
		}

		$classes[] = 'configured';

		return $classes;
	}

	/**
	 * Is it an auth handler?
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function is_auth_handler() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( empty( $_REQUEST['wpforms-oauth-action'] ) || $_REQUEST['wpforms-oauth-action'] !== 'auth' ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		return ! empty( $_REQUEST['tt'] ) && wpforms_google_sheets()->get( 'client' )->is_valid_one_time_token( $_REQUEST['tt'] );

		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Authenticate listener.
	 *
	 * @since 1.0.0
	 */
	public function reauthenticate_listener() { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		if ( ! $this->is_reauth_handler() ) {
			return;
		}

		wpforms_google_sheets()->get( 'client' )->finish_reconnection();

		add_filter( 'wpforms_builder_panel_sidebar_section_classes', [ $this, 'open_builder_tab' ], 10, 4 );

		$credentials = $this->get_credentials();

		// Customers can reauthorize with another Google account, so we have to keep the label up to date.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_REQUEST['user_email'] ) ) {
			$credentials['label'] = sanitize_text_field( wp_unslash( $_REQUEST['user_email'] ) );

			$this->save_account( $credentials );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		$this->remove_query_args();

		delete_option( self::FORCE_REAUTH_OPTION );
	}

	/**
	 * Is it a re-auth handler?
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function is_reauth_handler() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( empty( $_REQUEST['wpforms-oauth-action'] ) || $_REQUEST['wpforms-oauth-action'] !== 'reauth' ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		if ( empty( $_REQUEST['tt'] ) || ! wpforms_google_sheets()->get( 'client' )->is_valid_one_time_token( $_REQUEST['tt'] ) ) {
			return false;
		}

		$credentials = $this->get_credentials();

		return ! empty( $credentials['key'] ) && ! empty( $credentials['token'] );
	}

	/**
	 * Remove $_GET parameters from the URL.
	 *
	 * @since 1.0.0
	 */
	private function remove_query_args() {

		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return;
		}

		$_SERVER['REQUEST_URI'] = remove_query_arg(
			[
				'key',
				'wpforms-oauth-action',
				'user_email',
				'projectid',
				'success',
				'token',
				'tt',
			],
			esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) )
		);
	}

	/**
	 * Save account data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $account Account data.
	 */
	private function save_account( $account ) {

		$accounts   = wpforms_get_providers_options( Plugin::SLUG );
		$account_id = ! empty( $accounts ) && is_array( $accounts ) ? array_keys( $accounts )[0] : '';

		wpforms_update_providers_options( Plugin::SLUG, $account, $account_id );

		delete_option( self::FORCE_REAUTH_OPTION );
	}

	/**
	 * Is account connected?
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_connected() {

		if ( $this->is_connected !== null ) {
			return $this->is_connected;
		}

		if ( ! empty( get_option( self::FORCE_REAUTH_OPTION ) ) ) {
			return false;
		}

		$account = $this->get_credentials();

		if ( empty( $account['key'] ) || empty( $account['token'] ) ) {
			$providers = wpforms_get_providers_options();

			unset( $providers[ Plugin::SLUG ] );
			update_option( 'wpforms_providers', $providers );

			$this->is_connected = false;

			return $this->is_connected;
		}

		$this->is_connected = wpforms_google_sheets()->get( 'client' )->verify_auth( $account );

		return $this->is_connected;
	}

	/**
	 * Get list of credentials.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_credentials() {

		$providers = wpforms_get_providers_options();
		$accounts  = ! empty( $providers[ Plugin::SLUG ] ) ? $providers[ Plugin::SLUG ] : [];

		if ( empty( $accounts ) ) {
			return [];
		}

		return array_shift( $accounts );
	}

	/**
	 * Get form template for a new account.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_form() {

		return wpforms_render( WPFORMS_GOOGLE_SHEETS_PATH . 'templates/auth/pro-form' );
	}

	/**
	 * Get form template for a new account.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_advanced_form() {

		return wpforms_render(
			WPFORMS_GOOGLE_SHEETS_PATH . 'templates/auth/advanced-form',
			[
				'redirect_uris' => [
					Request::get_api_base_url() . 'auth/new/custom/complete/',
					Request::get_api_base_url() . 'auth/reauth/custom/complete/',
				],
			],
			true
		);
	}
}
