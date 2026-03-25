<?php

namespace WPFormsGoogleSheets\Provider\Settings;

use WPFormsGoogleSheets\Plugin;
use WPForms\Providers\Provider\Settings\FormBuilder as FormBuilderAbstract;

/**
 * Class FormBuilder handles functionality inside the form builder.
 *
 * @since 1.0.0
 */
class FormBuilder extends FormBuilderAbstract {

	/**
	 * Locked field name.
	 *
	 * @since 1.0.0
	 */
	const LOCK = '__lock__';

	/**
	 * Register all hooks (actions and filters).
	 *
	 * @since 1.0.0
	 */
	protected function init_hooks() {

		parent::init_hooks();
		$this->hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	private function hooks() {

		// AJAX-event names.
		static $ajax_events = [
			'ajax_account_save',
			'ajax_account_template_get',
			'ajax_connections_get',
			'ajax_access_token_data_get',
			'ajax_spreadsheet_data_get',
			'ajax_sheet_data_get',
		];

		// Register callbacks for AJAX events.
		array_walk(
			$ajax_events,
			static function ( $ajax_event, $key, $instance ) {

				$provider_slug = $instance->core->slug;

				add_filter(
					"wpforms_providers_settings_builder_{$ajax_event}_{$provider_slug}",
					[ $instance, $ajax_event ]
				);
			},
			$this
		);

		$provider_slug = $this->core->slug;

		// Register callbacks for hooks.
		add_filter( 'wpforms_save_form_args', [ $this, 'save_form' ], 11, 3 );
		add_filter( 'wpforms_builder_strings', [ $this, 'builder_strings' ], 30, 2 );
		add_filter( 'wpforms_builder_save_form_response_data', [ $this, 'refresh_connections' ], 10, 3 );
		add_filter( "wpforms_providers_provider_settings_formbuilder_display_content_default_screen_{$provider_slug}", [ $this, 'default_screen_content' ] );
	}

	/**
	 * Refresh the builder to update a new spreadsheet or a new sheet.
	 *
	 * @since 1.0.0
	 *
	 * @param array $response_data The data to be sent in the response.
	 * @param int   $form_id       Form ID.
	 * @param array $data          Form data.
	 *
	 * @return array
	 */
	public function refresh_connections( $response_data, $form_id, $data ) {

		if ( empty( $data['providers'][ $this->core->slug ] ) ) {
			return $response_data;
		}

		if ( ! wpforms_google_sheets()->get( 'account' )->is_connected() ) {
			return $response_data;
		}

		$this->form_data                = wpforms()->get( 'form' )->get( $form_id, [ 'content_only' => true ] );
		$response_data['google_sheets'] = $this->get_connections_data();

		foreach ( $response_data['google_sheets']['connections'] as $connection ) {
			if ( ! empty( $connection['spreadsheet_id'] ) && isset( $connection['sheet_id'] ) && ! wpforms_is_empty_string( $connection['sheet_id'] ) ) {
				$filled_headings = wpforms_google_sheets()->get( 'client' )->get_filled_headings( $connection['spreadsheet_id'], $connection['sheet_id'] );
				$headings        = wpforms_google_sheets()->get( 'field_mapper' )->prepare_headings( $connection, $filled_headings, $this->form_data );
				$updated         = wpforms_google_sheets()->get( 'client' )->update_headings( $connection['spreadsheet_id'], $connection['sheet_id'], $headings );

				if ( ! $updated ) {
					$response_data['google_sheets']['non_editable_spreadsheets'][] = $connection['spreadsheet_id'];
				}
			}
		}

		return $response_data;
	}

	/**
	 * Add a new item `Google Sheets` to panel sidebar.
	 *
	 * @since 1.0.0
	 *
	 * @param array $sections  Registered sections.
	 * @param array $form_data Contains array of the form data (post_content).
	 *
	 * @return array
	 */
	public function panel_sidebar( $sections, $form_data ) {

		$sections['google-sheets'] = esc_html__( 'Google Sheets', 'wpforms-google-sheets' );

		return $sections;
	}

	/**
	 * Pre-process provider data before saving it in form_data when editing form.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form Form array, usable with wp_update_post.
	 * @param array $data Data retrieved from $_POST and processed.
	 * @param array $args Arguments.
	 *
	 * @return array
	 */
	public function save_form( $form, $data, $args ) {

		if ( empty( $args['context'] ) || $args['context'] !== 'save_form' ) {
			return $form;
		}

		$form_data      = json_decode( stripslashes( $form['post_content'] ), true );
		$prev_form_data = ! empty( $data['id'] ) ? wpforms()->get( 'form' )->get( $data['id'], [ 'content_only' => true ] ) : [];

		// Provider exists.
		if ( ! empty( $form_data['providers'][ Plugin::SLUG ] ) ) {
			$modified_post_content = $this->modify_form_data( $form_data, $prev_form_data );

			if ( ! empty( $modified_post_content ) ) {
				$form['post_content'] = wpforms_encode( $modified_post_content );

				return $form;
			}
		}

		/*
		 * This part works when modification is locked or current filter was called on NOT Settings panel.
		 * Then we need to restore provider connections from the previous form content.
		 */
		$provider = ! empty( $prev_form_data['providers'][ Plugin::SLUG ] ) ? $prev_form_data['providers'][ Plugin::SLUG ] : [];

		if ( ! isset( $form_data['providers'] ) ) {
			$form_data = array_merge( $form_data, [ 'providers' => [] ] );
		}

		$form_data['providers'] = array_merge( (array) $form_data['providers'], [ Plugin::SLUG => $provider ] );
		$form['post_content']   = wpforms_encode( $form_data );

		return $form;
	}

	/**
	 * Prepare modifications for the form content, if it's not locked.
	 *
	 * @since 1.0.0
	 *
	 * @param array $submitted_form_data Submitted form data and settings.
	 * @param array $prev_form_data      Previous form data and settings.
	 *
	 * @return array
	 */
	protected function modify_form_data( $submitted_form_data, $prev_form_data ) {

		/**
		 * Connection is locked.
		 * Why? User clicked the "Save" button when one of AJAX requests
		 * for retrieving data from API was in progress or failed.
		 * Or user reconnected existed connection account.
		 */
		if (
			isset( $submitted_form_data['providers'][ Plugin::SLUG ][ self::LOCK ] ) &&
			absint( $submitted_form_data['providers'][ Plugin::SLUG ][ self::LOCK ] ) === 1
		) {
			return [];
		}

		// Modify content as we need, done by reference.
		foreach ( $submitted_form_data['providers'][ Plugin::SLUG ] as $connection_id => $connection ) {
			if ( $connection_id === self::LOCK ) {
				unset( $submitted_form_data['providers'][ Plugin::SLUG ][ $connection_id ] );
				continue;
			}

			if ( ! empty( $connection[ self::LOCK ] ) ) {
				$submitted_form_data['providers'][ Plugin::SLUG ][ $connection_id ] = $prev_form_data['providers'][ Plugin::SLUG ][ $connection_id ];

				continue;
			}

			$connection = $this->spreadsheet_actions( $connection );

			unset( $connection['spreadsheet_name'], $connection['sheet_name'], $connection['spreadsheet_type'] );

			$connection['custom_fields'] = ! empty( $connection['custom_fields'] ) ? $connection['custom_fields'] : [];
			$connection['custom_fields'] = $this->modify_custom_fields( $connection['custom_fields'] );

			$submitted_form_data['providers'][ Plugin::SLUG ][ $connection_id ] = $connection;
		}

		return $submitted_form_data;
	}

	/**
	 * Modify custom fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array $raw_custom_fields Raw custom fields data.
	 *
	 * @return array
	 */
	private function modify_custom_fields( $raw_custom_fields ) {

		$custom_fields = [];

		foreach ( $raw_custom_fields as $row ) {
			if ( empty( $row['name'] ) ) {
				continue;
			}

			if ( ! isset( $row['field_id'] ) || wpforms_is_empty_string( $row['field_id'] ) ) {
				continue;
			}

			if (
				$row['field_id'] === 'custom' &&
				( ! isset( $row['value'] ) || wpforms_is_empty_string( trim( $row['value'] ) ) )
			) {
				continue;
			}

			$value = $row['field_id'] === 'custom' ? sanitize_text_field( trim( $row['value'] ) ) : absint( $row['field_id'] );

			$custom_fields[ wpforms_sanitize_key( $row['name'] ) ] = $value;
		}

		// Rewrite the A column and always keep it as an Entry ID.
		$custom_fields['A'] = '=HYPERLINK("{entry_details_url}"; "{entry_id}")';

		uksort(
			$custom_fields,
			static function ( $a, $b ) {

				$length_diff = strlen( $a ) - strlen( $b );

				return $length_diff ? $length_diff : strcmp( $a, $b );
			}
		);

		return $custom_fields;
	}

	/**
	 * Sanitize connection.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 *
	 * @return array
	 */
	private function spreadsheet_actions( $connection_data ) {

		$connection_data = wp_parse_args(
			$connection_data,
			[
				'spreadsheet_type' => '',
				'spreadsheet_id'   => '',
				'sheet_id'         => '',
			]
		);

		$spreadsheet_id = $connection_data['spreadsheet_id'];

		if ( $connection_data['spreadsheet_type'] === 'new' ) {
			$spreadsheet_id = $this->create_new_spreadsheet( $connection_data );
		}

		if ( empty( $spreadsheet_id ) ) {
			return $connection_data;
		}

		if ( ! isset( $connection_data['sheet_id'] ) ) {
			$connection_data['spreadsheet_id'] = $spreadsheet_id;

			return $connection_data;
		}

		$sheet_id = $connection_data['sheet_id'];

		if ( $connection_data['sheet_id'] === 'new' ) {
			$sheet_id = $this->create_new_sheet( $spreadsheet_id, $connection_data );
		}

		$connection_data['spreadsheet_id'] = $spreadsheet_id;
		$connection_data['sheet_id']       = wpforms_is_empty_string( $sheet_id ) ? '' : absint( $sheet_id );

		return $connection_data;
	}

	/**
	 * Create a new spreadsheet.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 */
	private function create_new_spreadsheet( $connection_data ) {

		$spreadsheet_name = ! wpforms_is_empty_string( trim( $connection_data['spreadsheet_name'] ) ) ?
			$connection_data['spreadsheet_name'] :
			esc_html__( 'WPForms Spreadsheet', 'wpforms-google-sheets' );

		return wpforms_google_sheets()->get( 'client' )->create_spreadsheet( $spreadsheet_name );
	}

	/**
	 * Create a new sheet.
	 *
	 * @since 1.0.0
	 *
	 * @param string $spreadsheet_id  Spreadsheet ID.
	 * @param array  $connection_data Connection data.
	 *
	 * @return int
	 */
	private function create_new_sheet( $spreadsheet_id, $connection_data ) {

		$sheet_name         = trim( $connection_data['sheet_name'] );
		$is_new_spreadsheet = $connection_data['spreadsheet_type'] === 'new';

		if ( wpforms_is_empty_string( $sheet_name ) ) {
			$sheets     = wpforms_google_sheets()->get( 'client' )->get_sheets( $spreadsheet_id );
			$number     = $is_new_spreadsheet ? count( $sheets ) : count( $sheets ) + 1;
			$sheet_name = sprintf( /* translators: %d is the sheet order number. */
				esc_html__( 'Sheet %d', 'wpforms-google-sheets' ),
				$number
			);
		}

		return wpforms_google_sheets()->get( 'client' )->create_sheet(
			$spreadsheet_id,
			$sheet_name,
			$is_new_spreadsheet ? 0 : ''
		);
	}

	/**
	 * Validate account and send the authorization redirect URL.
	 *
	 * @since 1.0.0
	 */
	public function ajax_account_save() {

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$mode    = ! empty( $_POST['data']['mode'] ) ? sanitize_text_field( wp_unslash( $_POST['data']['mode'] ) ) : 'pro';
		$form_id = ! empty( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		if ( empty( $form_id ) ) {
			wp_send_json_error( '', 400 );
		}

		if ( $mode !== 'advanced' ) {
			wp_send_json_success(
				wpforms_google_sheets()
					->get( 'client' )
					->get_auth_url(
						[
							'return' => $this->get_redirect_url( $form_id ),
						]
					)
			);
		}

		$this->ajax_advanced_account_save( $form_id );
	}

	/**
	 * Validate custom account and send the authorization redirect URL.
	 *
	 * @since 1.0.0
	 *
	 * @param int $form_id Form ID.
	 */
	private function ajax_advanced_account_save( $form_id ) {

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$client_id     = ! empty( $_POST['data']['client_id'] ) ? sanitize_text_field( wp_unslash( $_POST['data']['client_id'] ) ) : '';
		$client_secret = ! empty( $_POST['data']['client_secret'] ) ? sanitize_text_field( wp_unslash( $_POST['data']['client_secret'] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		if ( empty( $client_id ) || empty( $client_secret ) ) {
			wp_send_json_error( '', 400 );
		}

		$args = [
			'return'        => $this->get_redirect_url( $form_id ),
			'client_id'     => $client_id,
			'client_secret' => $client_secret,
		];

		wp_send_json_success(
			wpforms_google_sheets()
				->get( 'client' )
				->get_auth_url( $args, 'custom' )
		);
	}

	/**
	 * Get redirect URL.
	 *
	 * @since 1.0.0
	 *
	 * @param int $form_id Form ID.
	 *
	 * @return string
	 */
	private function get_redirect_url( $form_id ) {

		return add_query_arg(
			[
				'page'    => 'wpforms-builder',
				'view'    => 'settings',
				'form_id' => $form_id,
			],
			admin_url( 'admin.php' )
		);
	}

	/**
	 * Content for Add New Account modal.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function ajax_account_template_get() {

		return [
			'title'   => esc_html__( 'Heads up!', 'wpforms-google-sheets' ),
			'content' => wpforms_google_sheets()->get( 'account' )->get_form(),
			'type'    => 'blue',
		];
	}

	/**
	 * Get the list of all saved connections.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function ajax_connections_get() {

		$account     = wpforms_google_sheets()->get( 'account' );
		$credentials = $account->get_credentials();

		if ( empty( $credentials ) ) {
			return [];
		}

		if ( ! $account->is_connected() ) {
			return [
				'invalid_account' => wpforms_google_sheets()->get( 'client' )->get_reauth_url(
					[
						'return' => $this->get_redirect_url( $this->form_data['id'] ),
					]
				),
			];
		}

		return $this->get_connections_data();
	}

	/**
	 * Get a connection data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection  Connection data.
	 * @param array $connections List of connections.
	 */
	private function get_connection_data( $connection, &$connections ) {

		// This will either return an empty placeholder or complete set of rules, as a DOM.
		$connections['connections'][ $connection['id'] ]['conditional'] = wpforms_conditional_logic()
			->builder_block(
				[
					'form'       => $this->form_data,
					'type'       => 'panel',
					'parent'     => 'providers',
					'panel'      => Plugin::SLUG,
					'subsection' => $connection['id'],
					'reference'  => esc_html__( 'Marketing provider connection', 'wpforms-google-sheets' ),
				],
				false
			);

		if ( empty( $connection['spreadsheet_id'] ) ) {
			return;
		}

		$connections['sheets'][ $connection['spreadsheet_id'] ] = wpforms_google_sheets()->get( 'client' )->get_sheets( $connection['spreadsheet_id'] );

		if ( ! isset( $connection['sheet_id'] ) || wpforms_is_empty_string( $connection['sheet_id'] ) ) {
			return;
		}

		$connections['columns'][ $connection['spreadsheet_id'] ][ $connection['sheet_id'] ] = wpforms_google_sheets()->get( 'client' )->get_columns( $connection['spreadsheet_id'], $connection['sheet_id'] );
	}

	/**
	 * Retrieve saved provider connections data.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_connections_data() {

		$connections_data = [
			'connections' => isset( $this->form_data['providers'][ Plugin::SLUG ] ) ? array_reverse( $this->form_data['providers'][ Plugin::SLUG ], true ) : [],
			'columns'     => [
				'default' => wpforms_google_sheets()->get( 'client' )->get_default_columns(),
			],
		];

		foreach ( $connections_data['connections'] as $connection_id => $connection ) {
			if ( $connection_id === self::LOCK || empty( $connection['id'] ) ) {
				unset( $connections_data['connections'][ $connection_id ] );
				continue;
			}

			$this->get_connection_data( $connection, $connections_data );
		}

		return $connections_data;
	}

	/**
	 * Retrieve Google Sheets account data.
	 *
	 * @since 1.0.0
	 * @deprecated 2.0.0
	 *
	 * @return array
	 */
	public function ajax_account_data_get() {

		_deprecated_function( __METHOD__, '2.0.0 of the WPForms Google Sheets addon' );

		return [
			'spreadsheets' => [],
		];
	}

	/**
	 * Retrieve Google Sheets access token data.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function ajax_access_token_data_get() {

		// phpcs:ignore WPForms.Formatting.EmptyLineBeforeReturn.RemoveEmptyLineBeforeReturnStatement
		return [
			'access_token' => wpforms_google_sheets()->get( 'client' )->get_access_token(),
		];
	}

	/**
	 * Get spreadsheet data.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function ajax_spreadsheet_data_get() {

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( empty( $_POST['spreadsheet_id'] ) ) {
			return [];
		}

		$spreadsheet_id = sanitize_text_field( wp_unslash( $_POST['spreadsheet_id'] ) );
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		return [
			'sheets' => wpforms_google_sheets()->get( 'client' )->get_sheets( $spreadsheet_id ),
		];
	}

	/**
	 * Get list of data that depend on the sheet field.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function ajax_sheet_data_get() {

		// phpcs:disable WordPress.Security.NonceVerification.Missing

		if ( empty( $_POST['spreadsheet_id'] ) || ! isset( $_POST['sheet_id'] ) ) {
			return [];
		}

		$spreadsheet_id = sanitize_text_field( wp_unslash( $_POST['spreadsheet_id'] ) );
		$sheet_id       = $_POST['sheet_id'] !== 'new' ? absint( $_POST['sheet_id'] ) : 'new';

		// phpcs:enable WordPress.Security.NonceVerification.Missing

		return [
			'columns'       => wpforms_google_sheets()->get( 'client' )->get_columns( $spreadsheet_id, $sheet_id ),
			//phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			'custom_fields' => $this->modify_custom_fields( empty( $_POST['custom_fields'] ) ? [] : $_POST['custom_fields'] ),
		];
	}

	/**
	 * Use this method to register own templates for form builder.
	 * Make sure, that you have `tmpl-` in template name in `<script id="tmpl-*">`.
	 *
	 * @since 1.0.0
	 */
	public function builder_custom_templates() {

		$credentials = wpforms_google_sheets()->get( 'account' )->get_credentials();
		$templates   = [
			'auth-error',
			'connection',
			'connection-error',
			'error',
			'fields',
			'sheet-select',
			'lock',
		];

		foreach ( $templates as $template ) {
			printf(
				'<script type="text/html" id="tmpl-wpforms-%s-builder-content-%s">%s</script>',
				esc_attr( Plugin::SLUG ),
				esc_attr( $template ),
				// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
				wpforms_render(
					WPFORMS_GOOGLE_SHEETS_PATH . "templates/builder/$template",
					[
						'email' => ! empty( $credentials['label'] ) ? $credentials['label'] : '',
					],
					true
				)
				// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
			);
		}

		printf(
			'<script type="text/html" id="tmpl-wpforms-%s-builder-content-new-account-advanced-form">%s</script>',
			esc_attr( Plugin::SLUG ),
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			wpforms_google_sheets()->get( 'account' )->get_advanced_form()
		);
	}

	/**
	 * Enqueue JavaScript and CSS files.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {

		parent::enqueue_assets();

		$min = wpforms_get_min_suffix();

		wp_enqueue_script(
			'wpforms-google-sheets-api',
			'https://apis.google.com/js/api.js',
			[],
			'',
			true
		);

		wp_enqueue_script(
			'wpforms-google-sheets-admin-builder',
			WPFORMS_GOOGLE_SHEETS_URL . "assets/js/builder{$min}.js",
			[ 'wpforms-google-sheets-api', 'wpforms-builder', 'choicesjs' ],
			WPFORMS_GOOGLE_SHEETS_VERSION,
			true
		);

		wp_enqueue_style(
			'wpforms-google-sheets-admin-builder',
			WPFORMS_GOOGLE_SHEETS_URL . "assets/css/builder{$min}.css",
			[ 'wpforms-builder' ],
			WPFORMS_GOOGLE_SHEETS_VERSION
		);
	}

	/**
	 * Add own localized strings to the Builder.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $strings Localized strings.
	 * @param object $form    Current form.
	 *
	 * @return array
	 */
	public function builder_strings( $strings, $form ) {

		$credentials = wpforms_google_sheets()->get( 'account' )->get_credentials();
		$email       = ! empty( $credentials['label'] ) ? esc_html( $credentials['label'] ) : '';

		$strings['google_sheets_not_mapped_field_found']      = sprintf(
			'<p>%1$s</p><p>%2$s</p>',
			esc_html__( 'The current sheet does not have some columns used in custom fields.', 'wpforms-google-sheets' ),
			esc_html__( 'If you select another sheet or save the form, the custom field rows without the selected column will be removed.', 'wpforms-google-sheets' )
		);
		$strings['google_sheets_auth_failed']                 = wp_kses(
			__( 'Your Google account connection is no longer valid. Please visit <strong>Settings</strong> » <strong>Google Sheets</strong> to reconnect your account.', 'wpforms-google-sheets' ),
			[
				'strong' => [],
			]
		);
		$strings['google_sheets_select_form_field']           = esc_html__( '--- Select Form Field ---', 'wpforms-google-sheets' );
		$strings['google_sheets_custom_value']                = esc_html__( 'Custom Value', 'wpforms-google-sheets' );
		$strings['google_sheets_email']                       = $email;
		$strings['google_sheets_non_editable_spreadsheets']   = wp_kses(
			sprintf(
				'<p>%1$s</p><p>%2$s</p>',
				sprintf( /* translators: %1$s is email of active google account .*/
					__( 'The Google account (%1$s) you\'ve connected doesn\'t have permission to edit all of your spreadsheets.', 'wpforms-google-sheets' ),
					esc_html( $email )
				),
				__( 'Please ask the owner(s) to add you as an Editor, or visit <strong>WPForms</strong> » <strong>Settings</strong> » <strong>Integrations</strong> » <strong>Google Sheets</strong> to switch to a different Google account.', 'wpforms-google-sheets' )
			),
			[
				'p'      => [],
				'strong' => [],
			]
		);
		$strings['google_sheets_advanced_form_title']         = esc_html__( 'Advanced Mode', 'wpforms-google-sheets' );
		$strings['google_sheets_advanced_form_add_button']    = esc_html__( 'Save', 'wpforms-google-sheets' );
		$strings['google_sheets_advanced_form_cancel_button'] = esc_html__( 'Go Back', 'wpforms-google-sheets' );
		$strings['google_sheets_pro_form_footer']             = sprintf(
			'%1$s <a href="#" class="js-wpforms-google-sheets-change-mode">%2$s</a>',
			esc_html__( 'Need a custom application?', 'wpforms-google-sheets' ),
			esc_html__( 'Enable Advanced Mode', 'wpforms-google-sheets' )
		);
		$strings['google_sheets_advanced_form_footer']        = sprintf(
			'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
			wpforms_utm_link(
				'https://wpforms.com/docs/google-sheets-addon/',
				'Builder Settings',
				'Google Sheets Documentation - Advanced Mode'
			),
			esc_html__( 'Google Sheets Documentation', 'wpforms-google-sheets' )
		);

		return $strings;
	}

	/**
	 * Change default screen content.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function default_screen_content() {

		$message = wpforms_google_sheets()->get( 'account' )->is_connected() ?
			__( 'Connect to a spreadsheet to start working with Google Sheets.', 'wpforms-google-sheets' ) :
			__( 'Connect your Google account to start working with Google Sheets.', 'wpforms-google-sheets' );

		return sprintf(
			'<p>%1$s</p><p><a href="%2$s" target="_blank" rel="noopener noreferer">%3$s</a></p>',
			esc_html( $message ),
			wpforms_utm_link(
				'https://wpforms.com/docs/google-sheets-addon/',
				'Builder Settings',
				'Google Sheets Documentation'
			),
			esc_html__( 'Learn how to get started with Google Sheets.', 'wpforms-google-sheets' )
		);
	}
}
