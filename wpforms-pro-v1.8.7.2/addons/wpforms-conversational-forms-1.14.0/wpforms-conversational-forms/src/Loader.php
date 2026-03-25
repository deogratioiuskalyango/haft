<?php

namespace WPFormsConversationalForms;

use WPForms_Updater;

/**
 * Conversational Forms loader class.
 *
 * @since 1.0.0
 */
final class Loader {

	/**
	 * URL to a plugin directory. Used for assets.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	public $url = '';

	/**
	 * Admin\Builder Instance if instantiated.
	 *
	 * @var Admin\Builder
	 *
	 * @since 1.6.0
	 */
	public $admin_builder;

	/**
	 * Admin\Overview Instance if instantiated.
	 *
	 * @var Admin\Overview
	 *
	 * @since 1.6.0
	 */
	public $admin_overview;

	/**
	 * Admin\Ajax Instance if instantiated.
	 *
	 * @var Admin\Ajax
	 *
	 * @since 1.6.0
	 */
	public $admin_ajax;

	/**
	 * Frontend Instance if instantiated.
	 *
	 * @var Frontend
	 *
	 * @since 1.6.0
	 */
	public $frontend;

	/**
	 * Initiate main plugin instance.
	 *
	 * @since 1.0.0
	 *
	 * @return Loader
	 */
	public static function get_instance() {

		static $instance;

		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Loader constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		$this->url = plugin_dir_url( __DIR__ );

		add_action( 'wpforms_loaded', [ $this, 'init' ], 15 );
	}

	/**
	 * All the actual plugin loading is done here.
	 *
	 * @since 1.0.0
	 */
	public function init() { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		if ( wpforms_is_admin_page( 'builder' ) ) {
			$this->admin_builder = new Admin\Builder();
		}

		if ( wpforms_is_admin_page( 'overview' ) ) {
			$this->admin_overview = new Admin\Overview();
		}

		if ( wp_doing_ajax() ) {
			$this->admin_ajax = new Admin\Ajax();
		}

		if ( ! is_admin() || wp_doing_ajax() ) {
			$this->frontend = new Frontend();
		}

		( new Process() )->init();

		$this->hooks();
	}

	/**
	 * Add hooks.
	 *
	 * @since 1.12.0
	 */
	private function hooks() {

		// Register the updater of this plugin.
		add_action( 'wpforms_updater', [ $this, 'updater' ] );
	}

	/**
	 * Load the plugin updater.
	 *
	 * @since 1.0.0
	 *
	 * @param {string} $key License key.
	 */
	public function updater( $key ) {

		new WPForms_Updater(
			[
				'plugin_name' => 'WPForms Conversational Forms',
				'plugin_slug' => 'wpforms-conversational-forms',
				'plugin_path' => plugin_basename( WPFORMS_CONVERSATIONAL_FORMS_FILE ),
				'plugin_url'  => trailingslashit( $this->url ),
				'remote_url'  => WPFORMS_UPDATER_API,
				'version'     => WPFORMS_CONVERSATIONAL_FORMS_VERSION,
				'key'         => $key,
			]
		);
	}
}
