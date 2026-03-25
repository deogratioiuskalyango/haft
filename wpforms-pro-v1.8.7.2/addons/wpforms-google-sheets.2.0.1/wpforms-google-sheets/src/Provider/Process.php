<?php

namespace WPFormsGoogleSheets\Provider;

use WPFormsGoogleSheets\Plugin;
use WPFormsGoogleSheets\Tasks\ProcessTask;

/**
 * Class Process handles entries processing using the provider settings and configuration.
 *
 * @since 1.0.0
 */
class Process extends \WPForms\Providers\Provider\Process {

	/**
	 * Receive all wpforms_process_complete params and do the actual processing.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields    Array of form fields.
	 * @param array $entry     Submitted form content.
	 * @param array $form_data Form data and settings.
	 * @param int   $entry_id  ID of a saved entry.
	 */
	public function process( $fields, $entry, $form_data, $entry_id ) {

		if ( empty( $form_data['providers'][ Plugin::SLUG ] ) ) {
			return;
		}

		foreach ( $form_data['providers'][ Plugin::SLUG ] as $connection_data ) {
			$this->process_each_connection( $connection_data, $fields, $form_data, $entry_id );
		}
	}

	/**
	 * Iteration loop for connections - call action for each connection.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 * @param array $fields          Array of form fields.
	 * @param array $form_data       Form data and settings.
	 * @param int   $entry_id        ID of a saved entry.
	 */
	protected function process_each_connection( $connection_data, $fields, $form_data, $entry_id ) {

		// Check for conditional logic.
		if ( ! $this->is_conditionals_passed( $connection_data, $fields, $form_data, $entry_id ) ) {
			return;
		}

		wpforms()
			->get( 'tasks' )
			->create( ProcessTask::ACTION )
			->async()
			->params( $connection_data, $fields, $form_data, $entry_id )
			->register();
	}

	/**
	 * Process Conditional Logic for the provided connection.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 * @param array $fields          Array of form fields.
	 * @param array $form_data       Form data and settings.
	 * @param int   $entry_id        ID of a saved entry.
	 *
	 * @return bool
	 */
	private function is_conditionals_passed( $connection_data, $fields, $form_data, $entry_id ) {

		$pass = $this->process_conditionals( $fields, $form_data, $connection_data );

		if ( ! $pass ) {
			wpforms_log(
				'Sending entry to Google Sheets was stopped by conditional logic.',
				$fields,
				[
					'type'    => [ 'provider', 'conditional_logic' ],
					'parent'  => $entry_id,
					'form_id' => $form_data['id'],
				]
			);
		}

		return $pass;
	}
}
