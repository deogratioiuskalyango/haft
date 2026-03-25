<?php

namespace WPFormsGoogleSheets;

use WPForms_Updater;
use WPForms\Tasks\Meta;
use WPFormsGoogleSheets\Api\Api;
use WPFormsGoogleSheets\Api\Cache;
use WPFormsGoogleSheets\Api\SiteId;
use WPFormsGoogleSheets\Api\Client;
use WPFormsGoogleSheets\Provider\Core;
use WPFormsGoogleSheets\Provider\Account;
use WPFormsGoogleSheets\Api\OneTimeToken;
use WPFormsGoogleSheets\Tasks\ProcessTask;
use WPFormsGoogleSheets\Provider\FieldMapper;
use WPFormsGoogleSheets\Migrations\Migrations;
use WPForms\Providers\Loader as ProvidersLoader;

/**
 * Google Sheets plugin class.
 *
 * @since 1.0.0
 */
final class Plugin {

	/**
	 * Integration slug.
	 *
	 * @since 1.0.0
	 */
	const SLUG = 'google-sheets';

	/**
	 * Account instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Account
	 */
	private $account;

	/**
	 * Field mapper.
	 *
	 * @since 1.0.0
	 *
	 * @var FieldMapper
	 */
	private $field_mapper;

	/**
	 * Provider core.
	 *
	 * @since 1.0.0
	 *
	 * @var Core
	 */
	private $provider;

	/**
	 * Process task.
	 *
	 * @since 1.0.0
	 *
	 * @var ProcessTask
	 */
	private $process_task;

	/**
	 * Client.
	 *
	 * @since 1.0.0
	 *
	 * @var Client
	 */
	private $client;

	/**
	 * Constructor method.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 *
	 * @return Plugin
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
	 */
	public function init() {

		$this->account      = new Account();
		$this->client       = new Client( new Api(), new Cache(), new OneTimeToken(), new SiteId() );
		$this->field_mapper = new FieldMapper();
		$this->process_task = ( new ProcessTask( new Meta(), $this->field_mapper ) );
		$this->provider     = new Core();

		$this->hooks();

		( new Migrations() )->init();

		return $this;
	}

	/**
	 * Hooks.
	 *
	 * @since 1.0.0
	 */
	private function hooks() {

		add_action( 'wpforms_updater', [ $this, 'updater' ] );
		add_filter( 'wpforms_helpers_templates_include_html_located', [ $this, 'templates' ], 10, 4 );

		$this->account->hooks();
		$this->process_task->hooks();

		ProvidersLoader::get_instance()->register( $this->provider );

		$form_builder = $this->provider->get_form_builder();

		// Move Google Sheets provider to the Settings tab.
		remove_action( 'wpforms_providers_panel_sidebar', [ $form_builder, 'display_sidebar' ], Core::PRIORITY );
		remove_action( 'wpforms_providers_panel_content', [ $form_builder, 'display_content' ], Core::PRIORITY );
		add_filter( 'wpforms_builder_settings_sections', [ $form_builder, 'panel_sidebar' ], Core::PRIORITY, 2 );
		add_action( 'wpforms_form_settings_panel_content', [ $form_builder, 'display_content' ], Core::PRIORITY );
	}

	/**
	 * Get private property.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Private property name.
	 *
	 * @return mixed
	 */
	public function get( $slug ) {

		return isset( $this->{$slug} ) ? $this->{$slug} : null;
	}

	/**
	 * Load the addon updater.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key License key.
	 */
	public function updater( $key ) {

		new WPForms_Updater(
			[
				'plugin_name' => 'WPForms Google Sheets',
				'plugin_slug' => 'wpforms-google-sheets',
				'plugin_path' => plugin_basename( WPFORMS_GOOGLE_SHEETS_FILE ),
				'plugin_url'  => trailingslashit( WPFORMS_GOOGLE_SHEETS_URL ),
				'remote_url'  => WPFORMS_UPDATER_API,
				'version'     => WPFORMS_GOOGLE_SHEETS_VERSION,
				'key'         => $key,
			]
		);
	}

	/**
	 * Change a template location.
	 *
	 * @since 1.0.0
	 *
	 * @param string $located  Template location.
	 * @param string $template Template.
	 * @param array  $args     Arguments.
	 * @param bool   $extract  Extract arguments.
	 *
	 * @return string
	 */
	public function templates( $located, $template, $args, $extract ) {

		// Checking if `$template` is an absolute path and passed from this plugin.
		if ( strpos( $template, WPFORMS_GOOGLE_SHEETS_PATH ) === 0 && is_readable( $template ) ) {
			return $template;
		}

		return $located;
	}
}
