<?php

namespace WPFormsConversationalForms;

/**
 * Logic related to form processing.
 *
 * @since 1.7.0
 */
class Process {

	/**
	 * Initialize class.
	 *
	 * @since 1.7.0
	 */
	public function init() {

		$this->hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.7.0
	 */
	private function hooks() {

		add_filter( 'wpforms_process_before_form_data', [ $this, 'prepare_date_dropdowns' ], 10, 2 );
	}

	/**
	 * Convert date type from `dropdown` to `datepicker` before processing.
	 *
	 * @since 1.6.0
	 * @since 1.7.0 moved to this class from \WPFormsConversationalForms\Frontend class as this is not related to frontend only.
	 *
	 * @param array $form_data Form data and settings.
	 * @param array $entry     Form submission raw data ($_POST).
	 *
	 * @return array Updated form data.
	 */
	public function prepare_date_dropdowns( $form_data, $entry ) {

		if ( empty( $form_data['fields'] ) ) {
			return $form_data;
		}

		foreach ( $form_data['fields'] as $id => $field ) {

			if ( empty( $field['date_type'] ) ) {
				continue;
			}

			if ( empty( $field['type'] ) ) {
				continue;
			}

			if ( $field['type'] === 'date-time' && $field['date_type'] === 'dropdown' ) {
				$form_data['fields'][ $id ]['date_type'] = 'datepicker';
			}
		}

		return $form_data;
	}
}
