<?php

namespace WPFormsConversationalForms;

use WPFormsConversationalForms\Helpers\Colors;
use WP_Post;

/**
 * Conversational Forms frontend functionality.
 *
 * @since 1.0.0
 */
class Frontend {

	/**
	 * Current form data.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $form_data;

	/**
	 * Color helper instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Colors
	 */
	public $colors;

	/**
	 * Color string.
	 *
	 * @since 1.7.1
	 *
	 * @var string
	 */
	private $color = '';

	/**
	 * Color scheme string.
	 *
	 * @since 1.7.1
	 *
	 * @var string
	 */
	private $color_scheme = '';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->colors = new Colors();

		$this->init();
	}

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		if ( wp_doing_ajax() ) {
			add_action( 'wpforms_ajax_submit_before_processing', [ $this, 'handle_ajax_request' ] );

			return;
		}

		add_action( 'parse_request', [ $this, 'handle_request' ] );
	}

	/**
	 * Handle the request.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP $wp WP instance.
	 */
	public function handle_request( $wp ) {

		if ( ! empty( $wp->query_vars['name'] ) ) {
			$request = $wp->query_vars['name'];
		}

		if ( empty( $request ) && ! empty( $wp->query_vars['pagename'] ) ) {
			$request = $wp->query_vars['pagename'];
		}

		if ( empty( $request ) ) {
			$request = ! empty( $_SERVER['REQUEST_URI'] ) ? \esc_url_raw( \wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
			$request = ! empty( $request ) ? \sanitize_key( \wp_parse_url( $request, PHP_URL_PATH ) ) : '';
		}

		$forms = ! empty( $request ) ? wpforms()->get( 'form' )->get( '', [ 'name' => $request ] ) : [];

		$form = ! empty( $forms[0] ) ? $forms[0] : null;

		if ( ! $this->is_conversational_form( $form ) ) {
			return;
		}

		$form_data = wpforms_decode( $form->post_content );

		/**
		 * This filter allows to overwrite a form data for frontend handle request.
		 *
		 * @since 1.7.0
		 *
		 * @param array $form_data Form data array.
		 */
		$this->form_data = apply_filters( 'wpforms_conversational_forms_frontend_handle_request_form_data', $form_data ); // phpcs:ignore WPForms.PHP.ValidateHooks.InvalidHookName

		// Override page URLs with the same slug.
		if ( ! empty( $wp->query_vars['pagename'] ) ) {
			$wp->query_vars['name'] = $wp->query_vars['pagename'];

			unset( $wp->query_vars['pagename'] );
		}

		if ( empty( $wp->query_vars['name'] ) ) {
			$wp->query_vars['name'] = $request;
		}

		$wp->query_vars['post_type'] = 'wpforms';

		// Unset 'error' query var that may appear if custom permalink structures used.
		unset( $wp->query_vars['error'] );

		/**
		 * Execute after detecting the Conversational Form..
		 *
		 * @since 1.5.9.5
		 *
		 * @param array   $form_data Form data array.
		 * @param WP_Post $form      Form data.
		 */
		do_action( 'wpforms_conversational_form_detected', $this->form_data, $form ); // phpcs:ignore WPForms.PHP.ValidateHooks.InvalidHookName

		// Enabled conversational form detected. Adding the hooks.
		$this->conversational_form_hooks();

		// Force classic render engine for WPForms version starting from 1.8.1.
		if ( function_exists( 'wpforms_get_render_engine' ) ) {
			wpforms()->get( 'frontend' )->init_render_engine( 'classic' );
		}
	}

	/**
	 * Conversational form specific hooks.
	 *
	 * @since 1.0.0
	 */
	public function conversational_form_hooks() {

		add_filter( 'template_include', [ $this, 'get_form_template' ], PHP_INT_MAX );
		add_filter( 'document_title_parts', [ $this, 'change_form_page_title' ] );
		add_filter( 'post_type_link', [ $this, 'modify_permalink' ], 10, 2 );
		add_filter( 'wpforms_get_render_engine', [ $this, 'set_render_engine_classic' ], PHP_INT_MAX );

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wpforms_wp_footer', [ $this, 'dequeue_scripts' ] );

		add_action( 'wpforms_frontend_confirmation', [ $this, 'dequeue_scripts' ] );
		add_action( 'wp_print_styles', [ $this, 'css_compatibility_mode' ] );
		add_action( 'wp_head', [ $this, 'print_form_styles' ] );
		add_filter( 'body_class', [ $this, 'set_body_classes' ] );

		add_filter( 'wpforms_frontend_form_data', [ $this, 'ignore_pagebreaks' ] );
		add_filter( 'wpforms_frontend_form_data', [ $this, 'change_multiple_dropdown_type' ] );
		add_filter( 'wpforms_field_data', [ $this, 'ignore_date_dropdowns' ], 10, 2 );
		add_filter( 'wpforms_field_data', [ $this, 'select_field_data' ], 10, 2 );
		add_filter( 'wpforms_field_properties', [ $this, 'ignore_multi_column_layout' ], 10, 3 );
		add_filter( 'wpforms_field_properties', [ $this, 'add_data_field_type_attr' ], 10, 3 );
		add_filter( 'wpforms_field_properties', [ $this, 'field_properties' ], 10, 3 );
		add_filter( 'wpforms_pro_fields_entry_preview_is_fields_ignored', '__return_true' );

		add_action( 'wpforms_display_field_after', [ $this, 'add_file_upload_html' ], 10, 2 );
		add_filter( 'wpforms_datetime_limits_available', '__return_false', 100 );

		add_action( 'wpforms_conversational_forms_content_before', [ $this, 'form_loader_html' ] );
		add_action( 'wpforms_conversational_forms_content_before', [ $this, 'form_header_html' ] );
		add_action( 'wpforms_conversational_forms_footer', [ $this, 'form_footer_html' ] );

		add_filter( 'tiny_mce_before_init', [ $this, 'add_styles_for_rich_text_field' ], 10, 2 );

		add_action( 'wp', [ $this, 'meta_tags' ] );

		add_action( 'wpforms_frontend_confirmation_message_before', [ $this, 'open_confirmation_wrapper' ], -1000 );
		add_action( 'wpforms_frontend_confirmation_message_after', [ $this, 'close_confirmation_wrapper' ], 1000 );

		add_filter( 'wpforms_smarttags_process_page_title_value', [ $this, 'filter_page_title_smart_tag_value' ], 10, 2 );

		// We need to remove some hooks as well.
		$this->remove_conflicting_hooks();
	}

	/**
	 * Remove not needed or conflicting hooks.
	 *
	 * @since 1.6.0
	 */
	public function remove_conflicting_hooks() {

		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );

		remove_action( 'wp_enqueue_scripts', 'twentyseventeen_scripts' );
		remove_action( 'wp_enqueue_scripts', 'twentytwenty_register_scripts' );
		remove_action( 'wp_enqueue_scripts', 'twentytwenty_register_styles' );
	}

	/**
	 * Set render engine to `classic`.
	 *
	 * @since 1.10.0
	 *
	 * @param string $engine Render engine slug.
	 *
	 * @return string
	 */
	public function set_render_engine_classic( $engine ) {

		return 'classic';
	}

	/**
	 * Handle AJAX request.
	 *
	 * @since 1.7.0
	 *
	 * phpcs:disable WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks
	 */
	public function handle_ajax_request() {

		global $post;

		if ( ! $this->is_conversational_form( $post ) ) {
			return;
		}

		add_filter( 'wpforms_get_render_engine', [ $this, 'set_render_engine_classic' ], PHP_INT_MAX );

		// Force classic render engine for WPForms version starting from 1.8.1.
		if ( function_exists( 'wpforms_get_render_engine' ) ) {
			wpforms()->get( 'frontend' )->init_render_engine( 'classic' );
		}

		add_action( 'wpforms_frontend_confirmation_message_before', [ $this, 'open_confirmation_wrapper' ], -1000 );
		add_action( 'wpforms_frontend_confirmation_message_after', [ $this, 'close_confirmation_wrapper' ], 1000 );
		add_filter( 'wpforms_smarttags_process_page_title_value', [ $this, 'filter_page_title_smart_tag_value' ], 10, 2 );
		// phpcs:enable WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks
	}

	/**
	 * Determine whether it is a conversational form.
	 *
	 * @since 1.7.0
	 *
	 * @param WP_Post $form Form post.
	 *
	 * @return bool
	 */
	private function is_conversational_form( $form ) {

		if ( ! isset( $form->post_type ) || $form->post_type !== 'wpforms' ) {
			return false;
		}

		$form_data = wpforms_decode( $form->post_content );

		return ! empty( $form_data['settings']['conversational_forms_enable'] );
	}

	/**
	 * Conversational form template.
	 *
	 * @since 1.0.0
	 */
	public function get_form_template() {

		return plugin_dir_path( \WPFORMS_CONVERSATIONAL_FORMS_FILE ) . 'templates/single-form.php';
	}

	/**
	 * Change document title to a custom form title.
	 *
	 * @since 1.0.0
	 *
	 * @param array $title Original document title parts.
	 *
	 * @return mixed
	 */
	public function change_form_page_title( $title ) {

		$title['title'] = $this->get_title();

		return $title;
	}

	/**
	 * Modify permalink for a conversational form.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $post_link The post's permalink.
	 * @param WP_Post $post      The post object.
	 *
	 * @return string
	 */
	public function modify_permalink( $post_link, $post ) {

		if ( empty( $this->form_data['id'] ) || absint( $this->form_data['id'] ) !== $post->ID ) {
			return $post_link;
		}

		if ( empty( $this->form_data['settings']['conversational_forms_enable'] ) ) {
			return $post_link;
		}

		return \home_url( $post->post_name );
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		$min = wpforms_get_min_suffix();

		if ( wpforms_has_field_type( 'date-time', $this->form_data ) ) {
			wp_enqueue_script(
				'wpforms-maskedinput',
				WPFORMS_PLUGIN_URL . 'assets/lib/jquery.inputmask.min.js',
				[ 'jquery' ],
				'5.0.7-beta.29',
				true
			);
		}

		wp_enqueue_script(
			'wpforms-conversational-forms-mobile-detect',
			wpforms_conversational_forms()->url . "assets/js/vendor/mobile-detect{$min}.js",
			[],
			'1.4.3',
			true
		);

		wp_enqueue_script(
			'wpforms-conversational-forms',
			wpforms_conversational_forms()->url . "assets/js/conversational-forms.es5{$min}.js",
			[ 'jquery', 'wpforms-conversational-forms-mobile-detect' ],
			WPFORMS_CONVERSATIONAL_FORMS_VERSION,
			true
		);

		wp_enqueue_style(
			'wpforms-conversational-forms',
			wpforms_conversational_forms()->url . "assets/css/conversational-forms{$min}.css",
			[ 'wpforms-font-awesome' ],
			\WPFORMS_CONVERSATIONAL_FORMS_VERSION
		);

		wp_localize_script(
			'wpforms-conversational-forms',
			'wpforms_conversational_forms',
			[
				'html' => $this->get_field_additional_html(),
				'i18n' => [
					'select_placeholder'   => esc_html__( 'Type or select an option', 'wpforms-conversational-forms' ),
					'select_list_empty'    => esc_html__( 'No suggestions found', 'wpforms-conversational-forms' ),
					'select_option_helper' => wp_kses( __( '<strong>Enter</strong> to select option', 'wpforms-conversational-forms' ), [ 'strong' => [] ] ),
				],
			]
		);

		wp_enqueue_style(
			'wpforms-font-awesome',
			WPFORMS_PLUGIN_URL . 'assets/lib/font-awesome/font-awesome.min.css',
			[],
			'4.7.0'
		);
	}

	/**
	 * Dequeue scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function dequeue_scripts() {

		wp_dequeue_script( 'wpforms-jquery-timepicker' );
		wp_dequeue_style( 'wpforms-jquery-timepicker' );

		wp_dequeue_script( 'wpforms-flatpickr' );
		wp_dequeue_style( 'wpforms-flatpickr' );

		wp_dequeue_script( 'popup-maker-site' );

		// Dequeue WPForms Core styles.
		wp_dequeue_style( 'wpforms-full' );
		wp_dequeue_style( 'wpforms-base' );

		wp_dequeue_style( 'wpforms-classic-full' );
		wp_dequeue_style( 'wpforms-classic-base' );
		wp_dequeue_style( 'wpforms-pro-classic-full' );
		wp_dequeue_style( 'wpforms-pro-classic-base' );

		wp_dequeue_style( 'wpforms-modern-full' );
		wp_dequeue_style( 'wpforms-modern-base' );
		wp_dequeue_style( 'wpforms-pro-modern-full' );
		wp_dequeue_style( 'wpforms-pro-modern-base' );
	}

	/**
	 * Unload CSS potentially interfering with Conversational Forms layout.
	 *
	 * @since 1.0.0
	 */
	public function css_compatibility_mode() {

		if ( ! \apply_filters( 'wpforms_conversational_forms_css_compatibility_mode', true ) ) {
			return;
		}

		$styles = \wp_styles();

		if ( empty( $styles->queue ) ) {
			return;
		}

		$theme_uri        = \wp_make_link_relative( \get_stylesheet_directory_uri() );
		$parent_theme_uri = \wp_make_link_relative( \get_template_directory_uri() );

		$upload_uri = \wp_get_upload_dir();
		$upload_uri = isset( $upload_uri['baseurl'] ) ? \wp_make_link_relative( $upload_uri['baseurl'] ) : $theme_uri;

		foreach ( $styles->queue as $handle ) {

			if ( ! isset( $styles->registered[ $handle ]->src ) ) {
				continue;
			}

			$src = \wp_make_link_relative( $styles->registered[ $handle ]->src );

			// Dequeue theme or upload folder CSS.
			foreach ( array( $theme_uri, $parent_theme_uri, $upload_uri ) as $uri ) {
				if ( \strpos( $src, $uri ) !== false ) {
					\wp_dequeue_style( $handle );
					break;
				}
			}
		}

		\do_action( 'wpforms_conversational_forms_enqueue_styles' );
	}

	/**
	 * Print dynamic form styles.
	 *
	 * @since 1.0.0
	 */
	public function print_form_styles() {

		if ( empty( $this->form_data['settings']['conversational_forms_color_scheme'] ) ) {
			return;
		}

		$color = \sanitize_hex_color( $this->form_data['settings']['conversational_forms_color_scheme'] );

		if ( empty( $color ) ) {
			$color = '#448ccb';
		}

		$this->color = $color;
		$min         = wpforms_get_min_suffix();

		switch ( $color ) {
			case '#448ccb':
				$theme = 'color-scheme-blue';
				break;
			case '#1a3c5a':
				$theme = 'color-scheme-dark_blue';
				break;
			case '#4aa891':
				$theme = 'color-scheme-teal';
				break;
			case '#9178b3':
				$theme = 'color-scheme-purple';
				break;
			case '#cccccc':
				$theme = 'color-scheme-light';
				break;
			case '#363636':
				$theme = 'color-scheme-dark';
				break;
			default:
				$theme = '';
		}

		if ( ! $theme ) {
			require \plugin_dir_path( WPFORMS_CONVERSATIONAL_FORMS_FILE ) . 'templates/dynamic-color-scheme-styles.php';
			return;
		}

		wp_enqueue_style(
			"wpforms-conversational-forms-{$theme}",
			wpforms_conversational_forms()->url . "assets/css/color-schemes/{$theme}{$min}.css",
			array( 'wpforms-conversational-forms' ),
			\WPFORMS_CONVERSATIONAL_FORMS_VERSION
		);

		$this->color_scheme = $theme;
	}

	/**
	 * Set body classes to apply different form styling.
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes Body classes.
	 *
	 * @return array
	 */
	public function set_body_classes( $classes ) {

		if ( ! empty( $this->form_data['settings']['conversational_forms_custom_logo'] ) ) {
			$classes[] = 'wpforms-conversational-form-custom-logo';
		}

		return $classes;
	}

	/**
	 * Ignore pagebreak elements on render.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data Form data and settings.
	 *
	 * @return array
	 */
	public function ignore_pagebreaks( $form_data ) {

		foreach ( $form_data['fields'] as $id => $field ) {
			if ( 'pagebreak' !== $field['type'] ) {
				continue;
			}
			unset( $form_data['fields'][ $id ] );
		}

		return $form_data;
	}

	/**
	 * Ignore date dropdown style on render.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field     Field settings.
	 * @param array $form_data Form data and settings.
	 *
	 * @return array
	 */
	public function ignore_date_dropdowns( $field, $form_data ) {

		if ( 'date-time' === $field['type'] && 'dropdown' === $field['date_type'] ) {
			$field['date_type'] = 'datepicker';
		}

		return $field;
	}

	/**
	 * Convert date type from `dropdown` to `datepicker` before processing.
	 *
	 * @since 1.6.0
	 * @deprecated 1.7.0
	 *
	 * @param array $form_data Form data and settings.
	 *
	 * @return array Updated form data.
	 */
	public function prepare_date_dropdowns( $form_data ) {

		_deprecated_function( __METHOD__, '1.7.0 of the WPForms Conversational Forms addon',  '\WPFormsConversationalForms\Process::prepare_date_dropdowns()' );

		return ( new Process() )->prepare_date_dropdowns( $form_data, [] );
	}

	/**
	 * Change modern and multiple styles on render for `Dropdown` and `Dropdown Items` fields.
	 *
	 * @since 1.6.0
	 *
	 * @param array $field     Field settings.
	 * @param array $form_data Form data and settings.
	 *
	 * @return array
	 */
	public function select_field_data( $field, $form_data ) {

		if ( ! in_array( $field['type'], array( 'select', 'payment-select' ), true ) ) {
			return $field;
		}

		// Modern select style should look like a Classic style.
		if (
			! empty( $field['style'] ) &&
			\WPForms_Field_Select::STYLE_MODERN === $field['style']
		) {
			$field['style'] = \WPForms_Field_Select::STYLE_CLASSIC;
		}

		// Multiple select has the same logic as checkboxes.
		if ( ! empty( $field['multiple'] ) ) {
			$field['type'] = 'checkbox';
		}

		return $field;
	}

	/**
	 * Ignore multi-column fields layout.
	 *
	 * @since 1.0.0
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Field settings.
	 * @param array $form_data  Form data and settings.
	 *
	 * @return array
	 */
	public function ignore_multi_column_layout( $properties, $field, $form_data ) {

		if ( empty( $properties['container']['class'] ) ) {
			return $properties;
		}

		foreach ( $properties['container']['class'] as $i => $class ) {
			if ( \in_array(
				$class,
				array(
					'wpforms-first',
					'wpforms-one-half',
					'wpforms-one-third',
					'wpforms-two-thirds',
					'wpforms-one-fourth',
					'wpforms-two-fourths',
					'wpforms-one-fifth',
					'wpforms-two-fifths',
				),
				true
			) ) {
				unset( $properties['container']['class'][ $i ] );
			}
		}

		return $properties;
	}

	/**
	 * Add data-field-type attribute to field elements.
	 *
	 * @since 1.0.0
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Field settings.
	 * @param array $form_data  Form data and settings.
	 *
	 * @return array
	 */
	public function add_data_field_type_attr( $properties, $field, $form_data ) {

		$properties['container']['data']['field-type'] = $field['type'];

		return $properties;
	}

	/**
	 * Various field properties filter.
	 *
	 * @since 1.6.0
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Field settings.
	 * @param array $form_data  Form data and settings.
	 *
	 * @return array
	 */
	public function field_properties( $properties, $field, $form_data ) {

		// Numerate fields with empty labels.
		$properties['label']['value'] = empty( $properties['label']['value'] ) ? '&nbsp;' : $properties['label']['value'];

		// Apply "Hide label" advanced option.
		$properties['label']['value'] = ! empty( $field['label_hide'] ) ? '&nbsp;' : $properties['label']['value'];

		return $properties;
	}

	/**
	 * Add HTML to file upload field.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field     Field settings.
	 * @param array $form_data Form data and settings.
	 */
	public function add_file_upload_html( $field, $form_data ) {

		// Display only for file uploader field.
		if ( empty( $field['type'] ) || 'file-upload' !== $field['type'] ) {
			return;
		}

		// Ignore the modern style.
		if ( ! empty( $field['style'] ) && \WPForms_Field_File_Upload::STYLE_MODERN === $field['style'] ) {
			return;
		}
		?>

		<label class="wpforms-field-file-upload-label wpforms-conversational-btn" for="<?php echo \esc_attr( $field['properties']['inputs']['primary']['id'] ); ?>">
			<?php esc_html_e( 'Choose File', 'wpforms-conversational-forms' ); ?>
		</label>
		<span class="wpforms-field-file-upload-file-name wpforms-conversational-form-btn-desc">
			<?php esc_html_e( 'No file chosen', 'wpforms-conversational-forms' ); ?>
		</span>

		<?php
	}

	/**
	 * Form Loader HTML.
	 *
	 * @since 1.0.0
	 */
	public function form_loader_html() {

		$brand_disable = ! empty( $this->form_data['settings']['conversational_forms_brand_disable'] ) ? $this->form_data['settings']['conversational_forms_brand_disable'] : '';

		?>
		<div id="wpforms-conversational-form-loader-container">
			<div class="wpforms-conversational-form-loader-content">
				<div class="wpforms-conversational-form-loader">
					<?php \esc_html_e( 'Loading...', 'wpforms-conversational-forms' ); ?>
				</div>

				<?php if ( ! $brand_disable ) : ?>
					<div class="wpforms-conversational-form-loader-powered-by">
						<span class="wpforms-conversational-form-loader-powered-by-text">
							<?php \esc_html_e( 'powered by', 'wpforms-conversational-forms' ); ?>
						</span>
						<?php
						readfile( plugin_dir_path( WPFORMS_CONVERSATIONAL_FORMS_FILE ) . 'assets/images/wpforms-text-logo.svg' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_readfile
						?>
					</div>
				<?php endif; ?>

			</div>
		</div>
		<?php
	}

	/**
	 * Form header HTML.
	 *
	 * @since 1.0.0
	 */
	public function form_header_html() {

		if ( $this->is_form_submit_success( $this->form_data['id'] ) ) {
			return;
		}

		?>

		<div class="wpforms-conversational-form-header">

			<?php $this->form_logo_html(); ?>
			<?php $this->form_head_html(); ?>

			<?php if ( ! apply_filters( 'wpforms_conversational_forms_start_button_disabled', false, $this->form_data ) ) : ?>

			<div class="wpforms-conversational-form-btn-container">
				<button class="wpforms-conversational-btn-start wpforms-conversational-btn"><?php esc_html_e( 'Start', 'wpforms-conversational-forms' ); ?></button>
				<div class="wpforms-conversational-form-btn-desc">
					<?php echo wp_kses( __( 'press <strong>Enter</strong>', 'wpforms-conversational-forms' ), [ 'strong' => [] ] ); ?>
				</div>
			</div>

			<?php endif; ?>

		</div>
		<?php
	}

	/**
	 * Form custom logo HTML.
	 *
	 * @since 1.0.0
	 */
	public function form_logo_html() {

		if ( empty( $this->form_data['settings']['conversational_forms_custom_logo'] ) ) {
			return;
		}

		$custom_logo_url = wp_get_attachment_image_src( $this->form_data['settings']['conversational_forms_custom_logo'], 'full' );
		$custom_logo_url = isset( $custom_logo_url[0] ) ? $custom_logo_url[0] : '';

		?>
		<div class="wpforms-conversational-form-logo">
			<img src="<?php echo \esc_url( $custom_logo_url ); ?>" alt="<?php \esc_html_e( 'Form Logo', 'wpforms-conversational-forms' ); ?>">
		</div>
		<?php
	}

	/**
	 * Form head area HTML.
	 *
	 * @since 1.0.0
	 */
	public function form_head_html() {

		$settings = $this->form_data['settings'];

		$title       = ! empty( $settings['conversational_forms_title'] ) ? $settings['conversational_forms_title'] : '';
		$description = ! empty( $settings['conversational_forms_description'] ) ? $settings['conversational_forms_description'] : '';

		if ( empty( $title ) && empty( $description ) ) {
			return;
		}

		$settings['form_title'] = $title;
		$settings['form_desc']  = $description;

		wpforms()->get( 'frontend' )->head( array_merge( $this->form_data, [ 'settings' => $settings ] ), null, true, true, [] );
	}

	/**
	 * Field additional HTML.
	 *
	 * @since 1.0.0
	 */
	public function get_field_additional_html() {

		$html = array();

		\ob_start();
		?>
		<div class="wpforms-conversational-form-btn-container wpforms-conversational-form-next-field-btns">
			<button class="wpforms-conversational-btn-next wpforms-conversational-btn"><?php esc_html_e( 'Done', 'wpforms-conversational-forms' ); ?></button>
			<div class="wpforms-conversational-form-btn-desc">
				<?php echo wp_kses( __( 'press <strong>Enter</strong>', 'wpforms-conversational-forms' ), array( 'strong' => array() ) ); ?>
			</div>
		</div>
		<?php

		$html['general']['action_buttons'] = \ob_get_clean();

		\ob_start();
		?>
		<div class="wpforms-conversational-form-field-info">
			<?php echo wp_kses( __( '<strong>Enter</strong> or <strong>&#x2B07;</strong> to go to the next field', 'wpforms-conversational-forms' ), array( 'strong' => array() ) ); ?>
		</div>
		<?php

		$html['general']['next_field'] = \ob_get_clean();

		\ob_start();
		?>
		<div class="wpforms-conversational-form-field-info">
			<?php echo wp_kses( __( '<strong>Shift+Enter</strong> to make a line break', 'wpforms-conversational-forms' ), array( 'strong' => array() ) ); ?>
		</div>
		<?php

		$html['textarea'] = \ob_get_clean();

		\ob_start();
		?>
		<div class="wpforms-conversational-form-field-info">
			<?php echo wp_kses( __( '<strong>Tab</strong> or <strong>&#x2B07;</strong> to switch the line', 'wpforms-conversational-forms' ), array( 'strong' => array() ) ); ?>
		</div>
		<?php

		$html['likert_scale'] = \ob_get_clean();

		\ob_start();
		?>
		<div class="wpforms-conversational-form-field-info">
			<?php echo wp_kses( __( '<strong>Shift+Enter</strong> to open file', 'wpforms-conversational-forms' ), array( 'strong' => array() ) ); ?>
		</div>
		<?php

		$html['file_upload'] = \ob_get_clean();

		\ob_start();
		?>
		<div class="wpforms-conversational-form-field-info">
			<?php echo wp_kses( __( '<strong>Shift+Enter</strong> to go to the next field', 'wpforms-conversational-forms' ), array( 'strong' => array() ) ); ?>
		</div>
		<?php

		$html['checkbox'] = \ob_get_clean();

		return $html;
	}

	/**
	 * Form footer HTML.
	 *
	 * @since 1.0.0
	 */
	public function form_footer_html() {

		$this->form_footer_progress_block_html();
		$this->form_footer_right_block_html();
	}

	/**
	 * Form footer progress block HTML.
	 *
	 * @since 1.0.0
	 */
	public function form_footer_progress_block_html() {

		$progress_style = ! empty( $this->form_data['settings']['conversational_forms_progress_bar'] ) ? $this->form_data['settings']['conversational_forms_progress_bar'] : '';

		?>
		<div class="wpforms-conversational-form-footer-progress">
			<div class="wpforms-conversational-form-footer-progress-status">
				<?php
				if ( 'proportion' === $progress_style ) {
					$this->form_footer_progress_status_proportion_html();
				} else {
					$this->form_footer_progress_status_percentage_html();
				}
				?>
			</div>
			<div class="wpforms-conversational-form-footer-progress-bar">
				<div class="wpforms-conversational-form-footer-progress-completed"></div>
			</div>
		</div>
		<?php
	}

	/**
	 * Form footer progress status (proportion) HTML.
	 *
	 * @since 1.0.0
	 */
	public function form_footer_progress_status_proportion_html() {

		?>
		<div class="wpforms-conversational-form-footer-progress-status-proportion">
			<?php
			printf(
				/* translators: %1$s - Number of fields completed; %2$s - Number of fields in total. */
				\esc_html__(
					'%1$s of %2$s completed',
					'wpforms-conversational-forms'
				),
				'<span class="completed"></span>',
				'<span class="completed-of"></span>'
			);
			?>
		</div>
		<div class="wpforms-conversational-form-footer-progress-status-proportion-completed" style="display: none">
			<?php \esc_html_e( 'Form completed', 'wpforms-conversational-forms' ); ?>
		</div>
		<?php
	}

	/**
	 * Form footer progress status (percentage) HTML.
	 *
	 * @since 1.0.0
	 */
	public function form_footer_progress_status_percentage_html() {

		?>
		<div class="wpforms-conversational-form-footer-progress-status-percentage">
			<?php
			printf(
				/* translators: %s - Percentage of fields completed. */
				\esc_html__(
					'%s%% completed',
					'wpforms-conversational-forms'
				),
				'<span class="completed">100</span>'
			);
			?>
		</div>
		<?php
	}

	/**
	 * Open wrapper for confirmation elements.
	 *
	 * @since 1.7.0
	 */
	public function open_confirmation_wrapper() {

		echo '<div class="wpforms-confirmation-container-wrapper">';
	}

	/**
	 * Close wrapper for confirmation elements.
	 *
	 * @since 1.7.0
	 */
	public function close_confirmation_wrapper() {

		echo '</div>';
	}

	/**
	 * Form footer right block HTML.
	 *
	 * @since 1.0.0
	 */
	public function form_footer_right_block_html() {

		$brand_disable = ! empty( $this->form_data['settings']['conversational_forms_brand_disable'] ) ? $this->form_data['settings']['conversational_forms_brand_disable'] : '';

		?>
		<div class="wpforms-conversational-form-footer-right-container">

			<?php if ( ! $brand_disable ) : ?>
				<div class="wpforms-conversational-form-footer-powered-by">
						<span>
							<?php esc_html_e( 'powered by', 'wpforms-conversational-forms' ); ?>
						</span>
					<?php
					readfile( plugin_dir_path( WPFORMS_CONVERSATIONAL_FORMS_FILE ) . 'assets/images/wpforms-text-logo.svg' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_readfile
					?>
				</div>
			<?php endif; ?>

			<div class="wpforms-conversational-form-footer-switch-step">
				<div class="wpforms-conversational-form-footer-switch-step-up">
					<i class="fa fa-angle-up" aria-hidden="true"></i>
				</div>
				<div class="wpforms-conversational-form-footer-switch-step-down">
					<i class="fa fa-angle-down" aria-hidden="true"></i>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Check if form was submitted successfully.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id Form id.
	 */
	public function is_form_submit_success( $id ) {

		// TODO: Code needs revision. Copy-paste from class-frontend.php.
		$form = wpforms()->get( 'form' )->get( (int) $id );

		if ( empty( $form ) ) {
			return false;
		}

		$form_id   = absint( $form->ID );
		$form_data = apply_filters( 'wpforms_frontend_form_data', wpforms_decode( $form->post_content ) );
		$errors    = empty( wpforms()->get( 'process' )->errors[ $form_id ] ) ? [] : wpforms()->get( 'process' )->errors[ $form_id ];

		// Check for return hash.
		if (
			! empty( $_GET['wpforms_return'] ) &&
			wpforms()->get( 'process' )->valid_hash &&
			absint( wpforms()->get( 'process' )->form_data['id'] ) === $form_id
		) {
			return true;
		}

		// Check for error-free completed form.
		if (
			empty( $errors ) &&
			! empty( $form_data ) &&
			! empty( $_POST['wpforms']['id'] ) &&
			absint( $_POST['wpforms']['id'] ) === $form_id
		) {
			return true;
		}

		return false;
	}

	/**
	 * Meta robots.
	 *
	 * @since 1.3.2
	 *
	 * @deprecated 1.6.0
	 */
	public function meta_robots() {

		_deprecated_function( __METHOD__, '1.6.0 of the WPForms Conversational Forms addon', __CLASS__ . '::meta_tags()' );

		$seo_plugin_enabled = false;

		if ( class_exists( 'WPSEO_Options' ) ) {
			\add_filter( 'wpseo_robots', array( $this, 'get_meta_robots' ), PHP_INT_MAX );
			$seo_plugin_enabled = true;
		}

		if ( class_exists( 'All_in_One_SEO_Pack' ) ) {
			\add_filter( 'aioseop_robots_meta', array( $this, 'get_meta_robots' ), PHP_INT_MAX );
			$seo_plugin_enabled = true;
		}

		if ( ! $seo_plugin_enabled ) {
			\add_action( 'wp_head', array( $this, 'output_meta_robots_tag' ) );
		}
	}

	/**
	 * Get meta robots value.
	 *
	 * @since 1.3.2
	 *
	 * @return string Meta robots value.
	 */
	public function get_meta_robots() {

		return \apply_filters( 'wpforms_conversational_forms_meta_robots_value', 'noindex,nofollow' );
	}

	/**
	 * Output meta robots tag.
	 *
	 * @since 1.3.2
	 */
	public function output_meta_robots_tag() {

		echo sprintf(
			'<meta name="robots" content="%s"/>%s',
			esc_attr( $this->get_meta_robots() ),
			"\n"
		);
	}

	/**
	 * Rank Math robots filter.
	 *
	 * @since 1.6.0
	 *
	 * @return array Robots data.
	 */
	public function get_rank_math_meta_robots() {

		return explode( ',', $this->get_meta_robots() );
	}

	/**
	 * Meta tags.
	 *
	 * @since 1.6.0
	 */
	public function meta_tags() {

		$seo_plugin_enabled = false;

		if ( class_exists( 'WPSEO_Options' ) ) {
			add_filter( 'wpseo_title', [ $this, 'get_seo_title' ], PHP_INT_MAX );
			add_filter( 'wpseo_opengraph_desc', [ $this, 'get_description' ], PHP_INT_MAX );
			add_filter( 'wpseo_opengraph_url', [ $this, 'get_seo_url' ], PHP_INT_MAX );
			add_filter( 'wpseo_canonical', [ $this, 'get_seo_url' ], PHP_INT_MAX );
			add_filter( 'wpseo_twitter_description', [ $this, 'get_description' ], PHP_INT_MAX );
			add_filter( 'wpseo_robots', [ $this, 'get_meta_robots' ], PHP_INT_MAX );
			$seo_plugin_enabled = true;
		}

		if ( class_exists( 'All_in_One_SEO_Pack' ) ) {
			add_filter( 'aioseop_title', [ $this, 'get_seo_title' ], PHP_INT_MAX );
			add_filter( 'aioseop_description_override', [ $this, 'get_description' ], PHP_INT_MAX );
			add_filter( 'aioseop_robots_meta', [ $this, 'get_meta_robots' ], PHP_INT_MAX );
			$seo_plugin_enabled = true;
		}

		if ( class_exists( 'AIOSEO\Plugin\AIOSEO' ) && function_exists( 'aioseo' ) ) {
			// Disable AIOSEO in Conversational Form page.
			add_filter( 'aioseo_disable', '__return_true' );

			add_action( 'wp_head', [ $this, 'output_aioseo_alternative_meta_tags' ], 3 );
			$seo_plugin_enabled = true;
		}

		if ( defined( 'SEOPRESS_VERSION' ) ) {
			add_filter( 'seopress_titles_desc', [ $this, 'get_description' ], PHP_INT_MAX );
			add_filter( 'seopress_social_twitter_card_title', [ $this, 'get_twitter_meta_title' ], PHP_INT_MAX );
			add_filter( 'seopress_social_og_title', [ $this, 'get_fb_meta_title' ], PHP_INT_MAX );
			$seo_plugin_enabled = true;
		}

		if ( class_exists( 'RankMath' ) ) {
			add_filter( 'rank_math/frontend/title', [ $this, 'get_seo_title' ], PHP_INT_MAX );
			add_filter( 'rank_math/frontend/description', [ $this, 'get_rank_math_description' ], PHP_INT_MAX );
			add_filter( 'rank_math/frontend/robots', [ $this, 'get_rank_math_meta_robots' ], PHP_INT_MAX );
			add_filter( 'rank_math/opengraph/facebook/og_description', [ $this, 'get_rank_math_description' ], PHP_INT_MAX );
			$seo_plugin_enabled = true;
		}

		// Handle 'SEO Plugin by Squirrly'.
		if ( defined( 'SQ_VERSION' ) ) {
			add_filter( 'sq_description', [ $this, 'get_sq_description' ], 90 );

			/*
			 * Priority is set to `90` because 'SEO Plugin by Squirrly' plugin has a filter
			 * that converts the `array` args to string at priority `99`.
			 * See `packOpenGraph()` and `packTwitterCard()`.
			 */
			add_filter( 'sq_open_graph', [ $this, 'filter_social_tags' ], 90 );
			add_filter( 'sq_twitter_card', [ $this, 'filter_social_tags' ], 90 );
			$seo_plugin_enabled = true;
		}

		if ( ! $seo_plugin_enabled ) {
			add_action( 'wp_head', [ $this, 'output_meta_robots_tag' ] );
		}
	}

	/**
	 * Get title value.
	 *
	 * @since 1.6.0
	 *
	 * @return string Title value.
	 */
	public function get_title() {

		$title = ! empty( $this->form_data['settings']['form_title'] ) ? $this->form_data['settings']['form_title'] : '';
		if ( ! empty( $this->form_data['settings']['conversational_forms_title'] ) ) {
			$title = $this->form_data['settings']['conversational_forms_title'];
		}

		return wp_strip_all_tags( $title, true );
	}

	/**
	 * Get SEO plugin title value.
	 *
	 * @since 1.6.0
	 *
	 * @param string $title Original title.
	 *
	 * @return string Title value.
	 */
	public function get_seo_title( $title = '' ) {

		if ( ! empty( $this->form_data['settings']['form_title'] ) ) {
			$title = str_replace( $this->form_data['settings']['form_title'], $this->get_title(), $title );
		}

		return $title;
	}

	/**
	 * Get description value.
	 *
	 * @since 1.6.0
	 *
	 * @return string Description value.
	 */
	public function get_description() {

		return ! empty( $this->form_data['settings']['conversational_forms_description'] ) ?
					wp_strip_all_tags( $this->form_data['settings']['conversational_forms_description'], true ) :
					'';
	}

	/**
	 * Get description value for the Rank Math plugin.
	 *
	 * @since 1.6.0
	 *
	 * @return string Description value.
	 */
	public function get_rank_math_description() {

		$description = $this->get_description();

		return ! empty( $description ) ?
			$description :
			false; // `false` means that Rank Math will not generate a description automatically.
	}

	/**
	 * Force Yoast SEO og/twitter descriptions.
	 *
	 * @since 1.0.0
	 *
	 * @deprecated 1.6.0
	 *
	 * @return string
	 */
	public function yoast_seo_description() {

		_deprecated_function( __METHOD__, '1.6.0 of the WPForms Conversational Forms addon', __CLASS__ . '::get_description()' );

		return ! empty( $this->form_data['settings']['conversational_forms_description'] ) ? wp_strip_all_tags( $this->form_data['settings']['conversational_forms_description'], true ) : '';
	}

	/**
	 * Get SEO url value for Yoast SEO plugin.
	 *
	 * @since 1.7.0
	 *
	 * @return string
	 */
	public function get_seo_url() {

		return get_the_permalink();
	}

	/**
	 * Add extra styles for Rich Text field.
	 *
	 * @since 1.7.0
	 *
	 * @param array  $mce_init  An array with TinyMCE config.
	 * @param string $editor_id Unique editor identifier.
	 *
	 * @return array
	 */
	public function add_styles_for_rich_text_field( $mce_init, $editor_id ) {

		if ( strpos( $editor_id, 'wpforms-' ) !== 0 ) {
			return $mce_init;
		}

		// If we have a color scheme, load the color scheme stylesheet. Otherwise, set custom styles.
		if ( $this->color_scheme !== '' ) {
			$min    = wpforms_get_min_suffix();
			$styles = wpforms_conversational_forms()->url . "assets/css/color-schemes/{$this->color_scheme}{$min}.css";

			if ( ! isset( $mce_init['content_css'] ) ) {
				$mce_init['content_css'] = $styles;
			} else {
				$mce_init['content_css'] .= ',' . $styles;
			}
		} else {
			$styles = '.mce-content-body { background-color: transparent; color: ' . $this->color . ' }';

			if ( ! isset( $mce_init['content_style'] ) ) {
				$mce_init['content_style'] = $styles . ' ';
			} else {
				$mce_init['content_style'] .= ' ' . $styles . ' ';
			}
		}

		return $mce_init;
	}

	/**
	 * Change field type to checkbox for multiple dropdown.
	 *
	 * @since 1.7.0
	 *
	 * @param array $form_data Form data and settings.
	 *
	 * @return array
	 */
	public function change_multiple_dropdown_type( $form_data ) {

		foreach ( $form_data['fields'] as $id => $field ) {

			if ( isset( $field['multiple'], $field['type'] ) && $field['type'] === 'select' ) {
				$form_data['fields'][ $id ]['type'] = 'checkbox';
			}
		}

		return $form_data;
	}

	/**
	 * Output meta tags when using AIOSEO plugin.
	 *
	 * @since 1.7.1
	 */
	public function output_aioseo_alternative_meta_tags() {
		?>
		<meta name="description" content="<?php echo esc_attr( $this->get_description() ); ?>" />
		<meta name="robots" content="max-image-preview:large" />

		<?php
		if ( property_exists( aioseo(), 'helpers' ) && method_exists( aioseo()->helpers, 'canonicalUrl' ) ) {
			?>
			<link rel="canonical" href="<?php echo esc_url( aioseo()->helpers->canonicalUrl() ); ?>" />
			<?php
		}

		$this->output_aioseo_facebook_meta();
		$this->output_aioseo_twitter_meta();
	}

	/**
	 * Output Facebook meta tags when AIOSEO plugin is installed.
	 *
	 * @since 1.7.1
	 */
	private function output_aioseo_facebook_meta() {
		?>
		<meta property="og:title" content="<?php echo esc_attr( $this->get_title() ); ?>" />
		<meta property="og:description" content="<?php echo esc_attr( $this->get_description() ); ?>" />
		<?php
		$this->maybe_output_aioseo_social_meta(
			'getFacebookMeta',
			[
				'og:locale',
				'og:site_name',
				'og:type',
				'og:url',
			],
			'property'
		);
	}

	/**
	 * Output social meta tags using AIOSEO.
	 *
	 * @since 1.7.1
	 *
	 * @param string $get_social_meta_function_name Name of the function in AIOSEO to fetch social meta tags.
	 * @param array  $meta_tags_to_output           Meta tags to output.
	 * @param string $name_or_property              Whether to 'name' or 'property' as the meta tag attribute. Accepts 'name' or 'property'.
	 */
	private function maybe_output_aioseo_social_meta( $get_social_meta_function_name, $meta_tags_to_output, $name_or_property ) {

		if ( ! property_exists( aioseo(), 'social' ) ||
			! property_exists( aioseo()->social, 'output' ) ||
			! method_exists( aioseo()->social->output, $get_social_meta_function_name )
		) {
			return;
		}

		$meta_tags = call_user_func( [ aioseo()->social->output, $get_social_meta_function_name ] );

		if ( empty( $meta_tags ) || empty( $meta_tags_to_output ) ) {
			return;
		}

		$meta_tag_attribute = 'property';

		if ( $name_or_property === 'name' ) {
			$meta_tag_attribute = 'name';
		}

		foreach ( $meta_tags_to_output as $meta_to_output ) {
			if ( empty( $meta_tags[ $meta_to_output ] ) ) {
				continue;
			}

			printf(
				'<meta %1$s="%2$s" content="%3$s" />' . "\n",
				esc_attr( $meta_tag_attribute ),
				esc_attr( $meta_to_output ),
				esc_attr( $meta_tags[ $meta_to_output ] )
			);
		}
	}

	/**
	 * Output Twitter meta tags when AIOSEO plugin is installed.
	 *
	 * @since 1.7.1
	 */
	private function output_aioseo_twitter_meta() {
		?>
		<meta name="twitter:title" content="<?php echo esc_attr( $this->get_title() ); ?>" />
		<meta name="twitter:description" content="<?php echo esc_attr( $this->get_description() ); ?>" />
		<?php
		$this->maybe_output_aioseo_social_meta(
			'getTwitterMeta',
			[
				'twitter:card',
				'twitter:site',
			],
			'name'
		);
	}

	/**
	 * Returns the Twitter meta title.
	 *
	 * @since 1.7.1
	 *
	 * @return string
	 */
	public function get_twitter_meta_title() {

		return '<meta name="twitter:title" content="' . esc_attr( $this->get_title() ) . '" />';
	}

	/**
	 * Returns the Facebook meta title.
	 *
	 * @since 1.7.1
	 *
	 * @return string
	 */
	public function get_fb_meta_title() {

		return '<meta property="og:title" content="' . esc_attr( $this->get_title() ) . '" />';
	}

	/**
	 * This function returns `false` if nothing was passed to retain Squirrly's behavior not to
	 * output anything if "No SEO Configuration" was selected and returns CF's description
	 * if anything truthy was passed.
	 *
	 * @since 1.7.1
	 *
	 * @param mixed $desc Description passed to sq_description filter from Squirrly plugin.
	 *
	 * @return false|string
	 */
	public function get_sq_description( $desc ) {

		if ( empty( $desc ) ) {
			return false;
		}

		return $this->get_description();
	}

	/**
	 * Filter social tags.
	 *
	 * @since 1.7.1
	 *
	 * @param array $meta_tags Social meta tags.
	 *
	 * @return mixed
	 */
	public function filter_social_tags( $meta_tags ) {

		if ( ! is_array( $meta_tags ) ) {
			return $meta_tags;
		}

		$social_tags = [
			[
				'tags_to_replace' => [
					'twitter:description',
					'og:description',
				],
				'new_value'       => $this->get_description(),
			],
			[
				'tags_to_replace' => [
					'twitter:title',
					'og:title',
				],
				'new_value'       => $this->get_title(),
			],
		];

		foreach ( $social_tags as $social_tag ) {
			$meta_tags = $this->replace_array_keys_with_new_value(
				$meta_tags,
				$social_tag['tags_to_replace'],
				$social_tag['new_value']
			);
		}

		return array_filter( $meta_tags );
	}

	/**
	 * Replace the value of keys in an array.
	 *
	 * @since 1.7.1
	 *
	 * @param array $arr             Array with keys to be replaced with new value.
	 * @param array $keys_to_replace Keys to be replaced with new value.
	 * @param mixed $new_value       New value.
	 *
	 * @return array
	 */
	private function replace_array_keys_with_new_value( $arr, $keys_to_replace, $new_value ) {

		if ( ! is_array( $arr ) ) {
			return $arr;
		}

		foreach ( $keys_to_replace as $key ) {
			if ( array_key_exists( $key, $arr ) ) {
				$arr[ $key ] = $new_value;
			}
		}

		return $arr;
	}

	/**
	 * Change the `{page_title}` value to Conversational Forms title.
	 *
	 * @since 1.7.1
	 *
	 * @param string $value     The page title.
	 * @param array  $form_data Form data.
	 *
	 * @return string
	 */
	public function filter_page_title_smart_tag_value( $value, $form_data ) {

		if ( empty( $form_data['settings']['conversational_forms_title'] ) ) {
			return $value;
		}

		return esc_html( wp_strip_all_tags( $form_data['settings']['conversational_forms_title'], true ) );
	}
}
