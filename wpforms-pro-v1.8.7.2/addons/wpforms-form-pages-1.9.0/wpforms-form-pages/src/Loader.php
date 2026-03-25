<?php

namespace WPFormsFormPages;

use WPForms_Updater;

/**
 * Form Pages loader class.
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

			$instance->init();
		}

		return $instance;
	}

	/**
	 * All the actual plugin loading is done here.
	 *
	 * @since 1.0.0
	 *
	 * @return Loader
	 */
	public function init() { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		$this->url = plugin_dir_url( __DIR__ );

		if ( wpforms_is_admin_page( 'builder' ) ) {
			new Admin\Builder();
		}

		if ( wpforms_is_admin_page( 'overview' ) ) {
			new Admin\Overview();
		}

		if ( wp_doing_ajax() ) {
			new Admin\Ajax();
		}

		if ( ! is_admin() ) {
			new Frontend();
		}

		( new SmartTags() )->init();

		$this->hooks();

		return $this;
	}

	/**
	 * Add hooks.
	 *
	 * @since 1.7.0
	 */
	private function hooks() {

		// Register the updater of this plugin.
		add_action( 'wpforms_updater', [ $this, 'updater' ] );
	}

	/**
	 * Load the plugin updater.
	 *
	 * @since 1.5.0
	 *
	 * @param string $key License key.
	 */
	public function updater( $key ) {

		new WPForms_Updater(
			[
				'plugin_name' => 'WPForms Form Pages',
				'plugin_slug' => 'wpforms-form-pages',
				'plugin_path' => plugin_basename( WPFORMS_FORM_PAGES_FILE ),
				'plugin_url'  => trailingslashit( $this->url ),
				'remote_url'  => WPFORMS_UPDATER_API,
				'version'     => WPFORMS_FORM_PAGES_VERSION,
				'key'         => $key,
			]
		);
	}
}
