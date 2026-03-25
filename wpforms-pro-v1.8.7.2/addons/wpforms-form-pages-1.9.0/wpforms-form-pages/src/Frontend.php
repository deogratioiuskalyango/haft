<?php

// phpcs:ignore Generic.Commenting.DocComment.MissingShort
/** @noinspection PhpUndefinedFunctionInspection */

namespace WPFormsFormPages;

use WP;
use WP_Post;
use WPFormsFormPages\Helpers\Colors;

/**
 * Form Pages frontend functionality.
 *
 * @since 1.0.0
 */
class Frontend {

	/**
	 * Current form data.
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	protected $form_data;

	/**
	 * Color helper instance.
	 *
	 * @var Colors
	 *
	 * @since 1.0.0
	 */
	public $colors;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->colors = new Helpers\Colors();

		$this->init();
	}

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		add_action( 'parse_request', [ $this, 'handle_request' ] );
	}

	/**
	 * Handle the request.
	 *
	 * @since 1.0.0
	 *
	 * @param WP $wp WP instance.
	 */
	public function handle_request( $wp ) {

		if ( ! empty( $wp->query_vars['name'] ) ) {
			$request = $wp->query_vars['name'];
		}

		if ( empty( $request ) && ! empty( $wp->query_vars['pagename'] ) ) {
			$request = $wp->query_vars['pagename'];
		}

		if ( empty( $request ) ) {
			$request = ! empty( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
			$request = ! empty( $request ) ? sanitize_key( wp_parse_url( $request, PHP_URL_PATH ) ) : '';
		}

		$form_obj = wpforms()->get( 'form' );
		$forms    = ! empty( $request ) && $form_obj ? $form_obj->get( '', [ 'name' => $request ] ) : [];

		$form = ! empty( $forms[0] ) ? $forms[0] : null;

		if ( ! isset( $form->post_type ) || $form->post_type !== 'wpforms' ) {
			return;
		}

		$form_data = wpforms_decode( $form->post_content );

		if ( empty( $form_data['settings']['form_pages_enable'] ) ) {
			return;
		}

		/**
		 * This filter allows to overwrite a form data for frontend handle request.
		 *
		 * @since 1.5.0
		 *
		 * @param array $form_data Form data array.
		 */
		// Set form data to be used by other methods of the class.
		$this->form_data = apply_filters( 'wpforms_form_pages_frontend_handle_request_form_data', $form_data );

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

		// Enabled form page detected. Adding the hooks.
		$this->form_page_hooks();
	}

	/**
	 * Form Page specific hooks.
	 *
	 * @since 1.0.0
	 */
	public function form_page_hooks() {

		add_filter( 'template_include', [ $this, 'get_form_template' ], PHP_INT_MAX );
		add_filter( 'document_title_parts', [ $this, 'change_form_page_title' ] );
		add_filter( 'post_type_link', [ $this, 'modify_permalink' ], 10, 2 );

		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
		remove_action( 'wp_head','wp_oembed_add_discovery_links' );
		remove_action( 'wp_head','wp_oembed_add_host_js' );

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wpforms_wp_footer', [ $this, 'dequeue_scripts' ] );
		add_action( 'wp_print_styles', [ $this, 'css_compatibility_mode' ] );
		add_action( 'wp_head', [ $this, 'print_form_styles' ] );
		add_filter( 'body_class', [ $this, 'set_body_classes' ] );

		add_action( 'wpforms_form_pages_content_before', [ $this, 'form_logo_html' ] );
		add_action( 'wpforms_frontend_output', [ $this, 'form_head_html' ], 5, 4 );
		add_action( 'wpforms_form_pages_footer', [ $this, 'form_footer_html' ] );

		add_action( 'wp', [ $this, 'meta_tags' ] );
	}

	/**
	 * Form Page template.
	 *
	 * @since 1.0.0
	 */
	public function get_form_template() {

		return plugin_dir_path( WPFORMS_FORM_PAGES_FILE ) . 'templates/single-form.php';
	}

	/**
	 * Change document title to a custom form title.
	 *
	 * @since 1.0.0
	 *
	 * @param array $title Original document title parts.
	 *
	 * @return array
	 */
	public function change_form_page_title( $title ) {

		$title['title'] = $this->get_title();

		return $title;
	}

	/**
	 * Modify permalink for a form page.
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

		if ( empty( $this->form_data['settings']['form_pages_enable'] ) ) {
			return $post_link;
		}

		return home_url( $post->post_name );
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		$min = wpforms_get_min_suffix();

		wp_enqueue_style(
			'wpforms-form-pages',
			wpforms_form_pages()->url . "assets/css/form-pages{$min}.css",
			[],
			WPFORMS_FORM_PAGES_VERSION
		);
	}

	/**
	 * Dequeue scripts and styles.
	 *
	 * @since 1.2.2
	 */
	public function dequeue_scripts() {

		wp_dequeue_script( 'popup-maker-site' );
	}

	/**
	 * Unload CSS potentially interfering with Form Pages layout.
	 *
	 * @since 1.0.0
	 */
	public function css_compatibility_mode() {

		if ( ! apply_filters( 'wpforms_form_pages_css_compatibility_mode', true ) ) {
			return;
		}

		$styles = wp_styles();

		if ( empty( $styles->queue ) ) {
			return;
		}

		$theme_uri        = wp_make_link_relative( get_stylesheet_directory_uri() );
		$parent_theme_uri = wp_make_link_relative( get_template_directory_uri() );

		$upload_uri = wp_get_upload_dir();
		$upload_uri = isset( $upload_uri['baseurl'] ) ? wp_make_link_relative( $upload_uri['baseurl'] ) : $theme_uri;

		foreach ( $styles->queue as $handle ) {

			if ( ! isset( $styles->registered[ $handle ]->src ) ) {
				continue;
			}

			$src = wp_make_link_relative( $styles->registered[ $handle ]->src );

			// Dequeue theme or upload folder CSS.
			foreach ( [ $theme_uri, $parent_theme_uri, $upload_uri ] as $uri ) {
				if ( strpos( $src, $uri ) !== false ) {
					wp_dequeue_style( $handle );
					break;
				}
			}
		}

		do_action( 'wpforms_form_pages_enqueue_styles' );
	}

	/**
	 * Print dynamic form styles.
	 *
	 * @since 1.0.0
	 */
	public function print_form_styles() {

		if ( empty( $this->form_data['settings']['form_pages_color_scheme'] ) ) {
			return;
		}

		$color = sanitize_hex_color( $this->form_data['settings']['form_pages_color_scheme'] );

		if ( empty( $color ) ) {
			$color = '#ffffff';
		}

		$render_engine = wpforms_get_render_engine();

		?>

		<style>
			.wpforms-form-page-modern #wpforms-form-page-page {
				border-top-color: <?php echo esc_attr( $color ); ?>;
				background-color: <?php echo esc_attr( $this->colors->hex_opacity( $color, 0.92 ) ); ?>;
			}

			.wpforms-form-page-classic #wpforms-form-page-page {
				border-top-color: <?php echo esc_attr( $color ); ?>;
				background-color: <?php echo esc_attr( $this->colors->hex_opacity( $color, 0.15 ) ); ?>;
			}

			.wpforms-form-page-modern #wpforms-form-page-page .wpforms-form-page-wrap {
				box-shadow: 0 30px 40px 0 rgba(0, 0, 0, 0.25), inset 0 4px 0 0<?php echo esc_attr( $this->colors->hex_opacity( $color, 0.5 ) ); ?>;
			}

			.wpforms-form-page-classic #wpforms-form-page-page .wpforms-form-page-wrap {
				box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.25), inset 0 8px 0 0<?php echo esc_attr( $this->colors->hex_opacity( $color, 0.5 ) ); ?>;
			}

			<?php if ( $render_engine === 'modern' ) : ?>
				#wpforms-form-page-page .wpforms-form-page-main .wpforms-submit {
					background-color: <?php echo esc_attr( $color ); ?>;
					color: <?php echo esc_attr( wpforms_light_or_dark( $color, '#444', '#fff' ) ); ?>;
					border: 1px solid<?php echo esc_attr( $this->colors->hex_opacity( $color, -0.75 ) ); ?>;
				}

				#wpforms-form-page-page .wpforms-form-page-main .wpforms-submit:hover,
				#wpforms-form-page-page .wpforms-form-page-main .wpforms-submit:active{
					background-color: <?php echo esc_attr( $this->colors->hex_opacity( $color, -0.85 ) ); ?>;
				}

				#wpforms-form-page-page .wpforms-field-net_promoter_score input[type=radio]:checked+label {
					background-color: <?php echo esc_attr( wpforms_light_or_dark( $color, $this->colors->hex_opacity( $color, -0.9 ), $color ) ); ?>;
					color: <?php echo esc_attr( wpforms_light_or_dark( $color, '#444', '#fff' ) ); ?>;
				}

				#wpforms-form-page-page .wpforms-field-likert_scale input[type=radio]:checked+label:after,
				#wpforms-form-page-page .wpforms-field-likert_scale input[type=checkbox]:checked+label:after {
					background-color: <?php echo esc_attr( wpforms_light_or_dark( $color, $this->colors->hex_opacity( $color, -0.9 ), $color ) ); ?>;
				}
			<?php endif; ?>

			.wpforms-form-page-modern .wpforms-form-page-footer {
				color: <?php echo esc_attr( wpforms_light_or_dark( $color, $this->colors->hex_opacity( $color, -0.8 ), '#fff' ) ); ?>;
			}

			.wpforms-form-page-classic .wpforms-form-page-footer {
				color: <?php echo esc_attr( wpforms_light_or_dark( $color, $this->colors->hex_opacity( $color, -0.9 ), $this->colors->hex_opacity( $color, 0.45 ) ) ); ?>;
			}

			.wpforms-form-page-modern .cls-1 {
				fill: <?php echo esc_attr( wpforms_light_or_dark( $color, $this->colors->hex_opacity( $color, -0.8 ), '#fff' ) ); ?>;
			}

			.wpforms-form-page-classic .cls-1 {
				fill: <?php echo esc_attr( wpforms_light_or_dark( $color, $this->colors->hex_opacity( $color, -0.9 ), $this->colors->hex_opacity( $color, 0.45 ) ) ); ?>;
			}
		</style>
		<?php
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

		if ( empty( $this->form_data['settings']['form_pages_style'] ) ) {
			return $classes;
		}

		$form_style = $this->form_data['settings']['form_pages_style'];

		if ( $form_style === 'modern' ) {
			$classes[] = 'wpforms-form-page-modern';
		}

		if ( $form_style === 'classic' ) {
			$classes[] = 'wpforms-form-page-classic';
		}

		if ( ! empty( $this->form_data['settings']['form_pages_custom_logo'] ) ) {
			$classes[] = 'wpforms-form-page-custom-logo';
		}

		return $classes;
	}

	/**
	 * Form custom logo HTML.
	 *
	 * @since 1.0.0
	 */
	public function form_logo_html() {

		if ( empty( $this->form_data['settings']['form_pages_custom_logo'] ) ) {
			return;
		}

		$custom_logo_url = wp_get_attachment_image_src( $this->form_data['settings']['form_pages_custom_logo'], 'full' );
		$custom_logo_url = isset( $custom_logo_url[0] ) ? $custom_logo_url[0] : '';

		?>
		<div class="wpforms-custom-logo">
			<img src="<?php echo esc_url( $custom_logo_url ); ?>" alt="<?php esc_html_e( 'Form Logo', 'wpforms-form-pages' ); ?>">
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

		$title       = ! empty( $settings['form_pages_title'] ) ? $settings['form_pages_title'] : '';
		$description = ! empty( $settings['form_pages_description'] ) ? $settings['form_pages_description'] : '';

		if ( empty( $title ) && empty( $description ) ) {
			return;
		}

		// Save our original form title in a settings var, so we can use it correctly in smart tags.
		$settings['form_name'] = $settings['form_title'];

		$settings['form_title'] = $title;
		$settings['form_desc']  = $description;

		$frontend = wpforms()->get( 'frontend' );

		if ( $frontend ) {
			$frontend->head( array_merge( $this->form_data, [ 'settings' => $settings ] ), null, true, true, [] );
		}
	}

	/**
	 * Form footer area.
	 *
	 * @since 1.0.0
	 */
	public function form_footer_html() {

		if ( ! empty( $this->form_data['settings']['form_pages_footer'] ) ) {
			printf(
				'<p>%s</p>',
				wp_kses(
					$this->form_data['settings']['form_pages_footer'],
					[
						'em'     => [],
						'strong' => [],
						'a'      => [
							'href'   => [],
							'target' => [],
						],
					]
				)
			);
		}

		if ( empty( $this->form_data['settings']['form_pages_brand_disable'] ) ) {

			?>
			<div class="wpforms-form-page-created-with">
				<a href="https://wpforms.com/?utm_source=poweredby&utm_medium=link&utm_campaign=formpages" rel="nofollow">
				<span><?php esc_html_e( 'created with', 'wpforms-form-pages' ); ?></span>
				<?php
					readfile( plugin_dir_path( WPFORMS_FORM_PAGES_FILE ) . 'assets/images/wpforms-text-logo.svg' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_readfile
				?>
				</a>
			</div>
			<?php
		}
	}

	/**
	 * Meta robots.
	 *
	 * @since 1.2.2
	 * @deprecated 1.4.0
	 *
	 * @noinspection PhpUnused
	 */
	public function meta_robots() {

		_deprecated_function( __METHOD__, '1.4.0 of the WPForms Form Pages addon', __CLASS__ . '::meta_tags()' );

		$seo_plugin_enabled = false;

		if ( class_exists( 'WPSEO_Options' ) ) {
			add_filter( 'wpseo_robots', [ $this, 'get_meta_robots' ], PHP_INT_MAX );
			$seo_plugin_enabled = true;
		}

		if ( class_exists( 'All_in_One_SEO_Pack' ) ) {
			add_filter( 'aioseop_robots_meta', [ $this, 'get_meta_robots' ], PHP_INT_MAX );
			$seo_plugin_enabled = true;
		}

		if ( ! $seo_plugin_enabled ) {
			add_action( 'wp_head', [ $this, 'output_meta_robots_tag' ] );
		}
	}

	/**
	 * Get meta robots value.
	 *
	 * @since 1.2.2
	 *
	 * @return string Meta robots value.
	 */
	public function get_meta_robots() {

		return apply_filters( 'wpforms_form_pages_meta_robots_value', 'noindex,nofollow' );
	}

	/**
	 * Output meta robots tag.
	 *
	 * @since 1.2.2
	 */
	public function output_meta_robots_tag() {

		printf(
			'<meta name="robots" content="%s"/>%s',
			esc_attr( $this->get_meta_robots() ),
			"\n"
		);
	}

	/**
	 * Rank Math robots filter.
	 *
	 * @since 1.4.0
	 *
	 * @return array Robots data.
	 */
	public function get_rank_math_meta_robots() {

		return explode( ',', $this->get_meta_robots() );
	}

	/**
	 * Meta tags.
	 *
	 * @since 1.4.0
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

		if ( function_exists( 'aioseo' ) ) {
			add_filter( 'aioseo_twitter_tags', [ $this, 'filter_social_tags' ], PHP_INT_MAX );
			add_filter( 'aioseo_facebook_tags', [ $this, 'filter_social_tags' ], PHP_INT_MAX );
			$seo_plugin_enabled = true;
		}

		if ( class_exists( 'All_in_One_SEO_Pack' ) ) {
			add_filter( 'aioseop_title', [ $this, 'get_seo_title' ], PHP_INT_MAX );
			add_filter( 'aioseop_description', [ $this, 'get_description' ], PHP_INT_MAX );
			add_filter( 'aioseop_robots_meta', [ $this, 'get_meta_robots' ], PHP_INT_MAX );
			$seo_plugin_enabled = true;
		}

		if ( class_exists( 'AIOSEO\Plugin\AIOSEO' ) && function_exists( 'aioseo' ) ) {
			// Disable AIOSEO in Conversational Form page.
			add_filter( 'aioseo_disable', '__return_true' );

			add_action( 'wp_head', [ $this, 'output_aioseo_alternative_meta_tags' ], 3 );
			$seo_plugin_enabled = true;
		}

		if ( class_exists( 'RankMath' ) ) {
			add_filter( 'rank_math/frontend/title', [ $this, 'get_seo_title' ], PHP_INT_MAX );
			add_filter( 'rank_math/frontend/description', [ $this, 'get_description' ], PHP_INT_MAX );
			add_filter( 'rank_math/frontend/robots', [ $this, 'get_rank_math_meta_robots' ], PHP_INT_MAX );
			add_filter( 'rank_math/opengraph/facebook/og_description', [ $this, 'get_description' ], PHP_INT_MAX );
			add_filter( 'rank_math/opengraph/twitter/twitter_description', [ $this, 'get_description' ], PHP_INT_MAX );
			$seo_plugin_enabled = true;
		}

		if ( defined( 'SEOPRESS_VERSION' ) ) {
			add_filter( 'seopress_titles_desc', [ $this, 'get_description' ], PHP_INT_MAX );
			add_filter( 'seopress_social_og_desc', [ $this, 'filter_og_description_tag' ], PHP_INT_MAX );
			add_filter( 'seopress_social_twitter_card_desc', [ $this, 'filter_twitter_description_tag' ], PHP_INT_MAX );
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
	 * @since 1.4.0
	 *
	 * @return string Title value.
	 */
	public function get_title() {

		$title = ! empty( $this->form_data['settings']['form_title'] ) ? $this->form_data['settings']['form_title'] : '';

		if ( ! empty( $this->form_data['settings']['form_pages_title'] ) ) {
			$title = $this->form_data['settings']['form_pages_title'];
		}

		return wp_strip_all_tags( $title, true );
	}

	/**
	 * Get SEO plugin title value.
	 *
	 * @since 1.4.0
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
	 * @since 1.4.0
	 *
	 * @return string Description value.
	 */
	public function get_description() {

		return ! empty( $this->form_data['settings']['form_pages_description'] ) ?
			wp_strip_all_tags( $this->form_data['settings']['form_pages_description'], true ) :
			'';
	}

	/**
	 * Force Yoast SEO og/twitter descriptions.
	 *
	 * @since 1.0.0
	 * @deprecated 1.4.0
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function yoast_seo_description() {

		_deprecated_function( __METHOD__, '1.4.0 of the WPForms Form Pages addon', __CLASS__ . '::get_description()' );

		return ! empty( $this->form_data['settings']['form_pages_description'] ) ? wp_strip_all_tags( $this->form_data['settings']['form_pages_description'], true ) : '';
	}

	/**
	 * Get SEO url value for Yoast SEO plugin.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_seo_url() {

		return get_the_permalink();
	}

	/**
	 * This function returns `false` if nothing was passed to retain Squirrly's behavior not to
	 * output anything if "No SEO Configuration" was selected and returns FP's description
	 * if anything truthy was passed.
	 *
	 * @since 1.5.1
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
	 * @since 1.5.1
	 *
	 * @param array $meta_tags AIOSEO social meta tags.
	 *
	 * @return array
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
	 * @since 1.5.1
	 *
	 * @param array $arr             Array with keys to be replaced with new value.
	 * @param array $keys_to_replace Keys to be replaced with new value.
	 * @param mixed $new_value       New value.
	 *
	 * @return mixed
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
	 * Replace the 'twitter:description' meta tag.
	 *
	 * @since 1.5.1
	 *
	 * @return string
	 */
	public function filter_twitter_description_tag() {

		$desc = $this->get_description();

		return empty( $desc ) ? '' : '<meta name="twitter:description" content="' . esc_attr( $desc ) . '" />';
	}

	/**
	 * Replace the 'og:description' meta tag.
	 *
	 * @since 1.5.1
	 *
	 * @return string
	 */
	public function filter_og_description_tag() {

		$desc = $this->get_description();

		return empty( $desc ) ? '' : '<meta property="og:description" content="' . esc_attr( $desc ) . '" />';
	}

	/**
	 * Output meta tags when using AIOSEO plugin.
	 *
	 * @since 1.5.1
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
	 * @since 1.5.1
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
	 * @since 1.5.1
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
	 * @since 1.5.1
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
}
