<?php

namespace WPFormsFormPages\Admin;

use WPForms_Builder_Panel_Settings;

/**
 * Form Pages builder functionality.
 *
 * @since 1.0.0
 */
class Builder {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->init();
	}

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public function init() { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		add_action( 'wpforms_builder_enqueues_before', [ $this, 'enqueue_scripts' ] );
		add_filter( 'wpforms_builder_settings_sections', [ $this, 'register_settings' ], 30, 2 );
		add_action( 'wpforms_form_settings_panel_content', [ $this, 'settings_content' ], 30, 2 );
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		$min = wpforms_get_min_suffix();

		wp_enqueue_media();

		wp_enqueue_script(
			'wpforms-admin-builder-form-pages',
			wpforms_form_pages()->url . "assets/js/admin-builder-form-pages{$min}.js",
			[ 'jquery', 'wpforms-builder', 'wpforms-utils' ],
			WPFORMS_FORM_PAGES_VERSION,
			true
		);

		wp_localize_script(
			'wpforms-admin-builder-form-pages',
			'wpforms_admin_builder_form_pages',
			[
				'i18n'     => [
					'enable_prevent_modal'             => esc_html__( 'Form Pages cannot be enabled if Conversational Forms is enabled at the same time.', 'wpforms-form-pages' ),
					'enable_prevent_modal_ok'          => esc_html__( 'OK', 'wpforms-form-pages' ),
					'logo_preview_alt'                 => esc_html__( 'Form Logo', 'wpforms-form-pages' ),
					'logo_selection_frame_title'       => esc_html__( 'Select or Upload Form Custom Logo', 'wpforms-form-pages' ),
					'logo_selection_frame_button_text' => esc_html__( 'Use this image', 'wpforms-form-pages' ),
				],
				'settings' => [
					'custom_color' => $this->get_custom_color(),
				],
			]
		);

		wp_enqueue_style(
			'wpforms-form-pages-admin-builder',
			wpforms_form_pages()->url . "assets/css/admin-builder-form-pages{$min}.css",
			[],
			WPFORMS_FORM_PAGES_VERSION
		);
	}

	/**
	 * Get color scheme custom color.
	 *
	 * @since 1.5.0
	 *
	 * @return string Custom color.
	 */
	private function get_custom_color() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$form_id   = isset( $_GET['form_id'] ) ? absint( $_GET['form_id'] ) : 0;
		$form      = wpforms()->get( 'form' );
		$form_data = $form_id && $form ? $form->get( $form_id, [ 'content_only' => true ] ) : [];

		return ! empty( $form_data['settings']['form_pages_color_scheme'] ) ? sanitize_hex_color( $form_data['settings']['form_pages_color_scheme'] ) : '';
	}

	/**
	 * Register settings area.
	 *
	 * @since 1.0.0
	 *
	 * @param array $sections Settings area sections.
	 *
	 * @return array
	 */
	public function register_settings( $sections ) {

		$sections['form_pages'] = esc_html__( 'Form Pages', 'wpforms-form-pages' );

		return $sections;
	}

	/**
	 * Settings content.
	 *
	 * @since 1.0.0
	 *
	 * @param WPForms_Builder_Panel_Settings $instance Settings panel instance.
	 */
	public function settings_content( $instance ) {

		$permalink_enabled = ! empty( get_option( 'permalink_structure' ) );

		echo '<div class="wpforms-panel-content-section wpforms-panel-content-section-form_pages">';

		echo '<div class="wpforms-panel-content-section-title">';

		esc_html_e( 'Form Pages', 'wpforms-form-pages' );

		if ( $permalink_enabled ) {
			echo '<a href="' . esc_url( home_url( $instance->form->post_name ) ) . '" id="wpforms-form-pages-preview-form-page" class="button" rel="noopener noreferrer" target="_blank">' . esc_html__( 'Preview Form Page', 'wpforms-form-pages' ) . '</a>';
		}

		echo '</div><!-- .wpforms-panel-content-section-title -->';

		// Display 'configure permalink' notice.
		if ( ! $permalink_enabled ) {

			echo '<p class="wpforms-alert wpforms-alert-info">' .
		        sprintf(
		            wp_kses(
						/* translators: %s - Permalink Settings page URL. */
						__( 'Heads up! To use Form Pages, please configure your site\'s permalinks on the WordPress <a href="%s">Permalink Settings</a> page.', 'wpforms-form-pages' ),
						[
							'a' => [
								'href' => [],
							],
						]
					),
					esc_url( admin_url( 'options-permalink.php' ) )
				) .
				'</p>' .
			'</div><!-- .wpforms-panel-content-section-conversational_forms -->';

			return;
		}

		wpforms_panel_field(
			'toggle',
			'settings',
			'form_pages_enable',
			$instance->form_data,
			esc_html__( 'Enable Form Page Mode', 'wpforms-form-pages' )
		);

		echo '<div id="wpforms-form-pages-content-block">';

		wpforms_panel_field(
			'text',
			'settings',
			'form_pages_title',
			$instance->form_data,
			esc_html__( 'Form Page Title', 'wpforms-form-pages' )
		);

		wpforms_panel_field(
			'tinymce',
			'settings',
			'form_pages_description',
			$instance->form_data,
			esc_html__( 'Message', 'wpforms-form-pages' ),
			[
				'tinymce' => [
					'editor_height' => 175,
				],
				'tooltip' => esc_html__( 'This content will be displayed below the Form Page Title, above the form.', 'wpforms-form-pages' ),
			]
		);

		wpforms_panel_field(
			'text',
			'settings',
			'form_pages_page_slug',
			$instance->form_data,
			esc_html__( 'Permalink', 'wpforms-form-pages' ),
			[
				'value'       => isset( $instance->form_data['settings']['form_pages_page_slug'] ) ? esc_html( urldecode( $instance->form_data['settings']['form_pages_page_slug'] ) ) : '',
				'after_label' => '<div class="wpforms-form-pages-page-slug-container">
                                 <span class="form-pages-page-slug-pre-url wpforms-one-third">' . trailingslashit( home_url() ) . '</span>',
				'after'       => $this->get_page_slug_buttons_html( $instance ) . '</div><!-- .wpforms-form-pages-page-slug-container -->',
				'tooltip'     => esc_html__( 'This is the URL for your form page.', 'wpforms-form-pages' ),
			]
		);

		wpforms_panel_field(
			'text',
			'settings',
			'form_pages_custom_logo',
			$instance->form_data,
			esc_html__( 'Header Logo', 'wpforms-form-pages' ),
			[
				'readonly'    => true,
				'after_label' => $this->get_custom_logo_preview_html( $instance->form_data ),
				'after'       => $this->get_custom_logo_buttons_html(),
				'tooltip'     => esc_html__( 'This is a custom logo.', 'wpforms-form-pages' ),
			]
		);

		wpforms_panel_field(
			'text',
			'settings',
			'form_pages_footer',
			$instance->form_data,
			esc_html__( 'Footer Text', 'wpforms-form-pages' ),
			[
				'default' => esc_html__( 'This content is neither created nor endorsed by WPForms.', 'wpforms-form-pages' ),
			]
		);

		wpforms_panel_field(
			'toggle',
			'settings',
			'form_pages_brand_disable',
			$instance->form_data,
			esc_html__( 'Hide WPForms Branding', 'wpforms-form-pages' )
		);

		$color_options = $this->get_color_options( $instance );

		wpforms_panel_field(
			'radio',
			'settings',
			'form_pages_color_scheme',
			$instance->form_data,
			esc_html__( 'Color Scheme', 'wpforms-form-pages' ),
			[
				'default' => isset( $color_options[0]['value'] ) ? $color_options[0]['value'] : '#ffffff',
				'options' => $color_options,
				'tooltip' => esc_html__( 'This is color of the submit button and page background.', 'wpforms-form-pages' ),
			]
		);

		wpforms_panel_field(
			'radio',
			'settings',
			'form_pages_style',
			$instance->form_data,
			esc_html__( 'Style', 'wpforms-form-pages' ),
			[
				'default' => 'modern',
				'options' => [
					'modern'  => [
						'pre_label' => '<img src="' . wpforms_form_pages()->url . 'assets/images/forms-style-modern.png">',
						'label'     => esc_html__( 'Modern Design', 'wpforms-form-pages' ),
					],
					'classic' => [
						'pre_label' => '<img src="' . wpforms_form_pages()->url . 'assets/images/forms-style-classic.png">',
						'label'     => esc_html__( 'Classic Design', 'wpforms-form-pages' ),
					],
				],
				'tooltip' => esc_html__( 'Modern: Wider form, rounded corners, darker background', 'wpforms-form-pages' ) . '<br>' . esc_html__( 'Classic: Narrower form, square corners, lighter background', 'wpforms-form-pages' ),
			]
		);

		echo '</div><!-- #wpforms-form-pages-content-block -->';

		echo '</div><!-- .wpforms-panel-content-section-form_pages -->';
	}

	/**
	 * Get available color options for the settings.
	 *
	 * @since 1.0.0
	 *
	 * @param WPForms_Builder_Panel_Settings $instance Settings panel instance.
	 *
	 * @return array
	 */
	public function get_color_options( $instance ) {

		$color_options = [
			[
				'label' => '<span class="form-pages-color-scheme-color blue"></span>',
				'value' => '#448ccb',
			],
			[
				'label' => '<span class="form-pages-color-scheme-color cyan"></span>',
				'value' => '#1aa59f',
			],
			[
				'label' => '<span class="form-pages-color-scheme-color green"></span>',
				'value' => '#5ab552',
			],
			[
				'label' => '<span class="form-pages-color-scheme-color red"></span>',
				'value' => '#d34342',
			],
			[
				'label' => '<span class="form-pages-color-scheme-color purple"></span>',
				'value' => '#9376b5',
			],
			[
				'label' => '<span class="form-pages-color-scheme-color grey"></span>',
				'value' => '#999999',
			],
		];

		$custom_color = ! empty( $instance->form_data['settings']['form_pages_color_scheme'] ) ? sanitize_hex_color( $instance->form_data['settings']['form_pages_color_scheme'] ) : '';

		if ( empty( $custom_color ) || wp_list_filter( $color_options, [ 'value' => $custom_color ] ) ) {
			$custom_color = '#ffffff';
		}

		$color_options[] = [
			'label' => '<span></span>',
			'value' => $custom_color,
		];

		return $color_options;
	}

	/**
	 * Form custom logo preview HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data Form data.
	 *
	 * @return false|string
	 */
	public function get_custom_logo_preview_html( $form_data ) {

		$custom_logo_id = ! empty( $form_data['settings']['form_pages_custom_logo'] ) ? $form_data['settings']['form_pages_custom_logo'] : '';

		$custom_logo_url = wp_get_attachment_image_src( $custom_logo_id, 'medium' );
		$custom_logo_url = empty( $custom_logo_url ) ? wp_get_attachment_image_src( $custom_logo_id, 'full' ) : $custom_logo_url;
		$custom_logo_url = isset( $custom_logo_url[0] ) ? $custom_logo_url[0] : '';

		ob_start();

		?>
		<div class="wpforms-form-pages-custom-logo-container" <?php echo $custom_logo_url ? '' : 'style="display: none;"'; ?>>
			<a href="#" class="wpforms-form-pages-custom-logo-delete">
				<?php if ( $custom_logo_url ) : ?>
					<img src="<?php echo esc_url( $custom_logo_url ); ?>" alt="<?php esc_attr_e( 'Form Logo', 'wpforms-form-pages' ); ?>" />
				<?php endif; ?>
			</a>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Form custom logo control buttons HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return false|string
	 */
	public function get_custom_logo_buttons_html() {

		ob_start();

		?>
		<p>
			<a href="#" class="wpforms-form-pages-custom-logo-upload wpforms-btn wpforms-btn-light-grey wpforms-btn-sm">
				<?php esc_html_e( 'Upload Image', 'wpforms-form-pages' ); ?>
			</a>
		</p>
		<?php

		return ob_get_clean();
	}

	/**
	 * Form page slug control buttons HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param WPForms_Builder_Panel_Settings $instance Settings panel instance.
	 *
	 * @return false|string
	 */
	public function get_page_slug_buttons_html( $instance ) {

		ob_start();

		?>
		<a href="<?php echo esc_url( home_url( $instance->form->post_name ) ); ?>" class="wpforms-form-pages-page-slug-view wpforms-btn wpforms-btn-light-grey wpforms-btn-sm" target="_blank">
			<?php esc_html_e( 'View', 'wpforms-form-pages' ); ?>
		</a>
		<?php

		return ob_get_clean();
	}
}
