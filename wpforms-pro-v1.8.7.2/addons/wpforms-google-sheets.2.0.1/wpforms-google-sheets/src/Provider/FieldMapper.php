<?php

namespace WPFormsGoogleSheets\Provider;

/**
 * FieldMapper class.
 *
 * @since 1.0.0
 */
class FieldMapper {

	/**
	 * Formulas start characters.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	const FORMULAS_START_CHARS = [ '=', '-', '+', '@', "\t", "\r" ];

	/**
	 * Prepare row for a spreadsheet.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 * @param array $fields          Submitted fields.
	 * @param array $form_data       Form data and settings.
	 * @param int   $entry_id        Entry ID.
	 *
	 * @return array
	 */
	public function prepare_row( $connection_data, $fields, $form_data, $entry_id ) {

		if ( empty( $connection_data['custom_fields'] ) ) {
			return [];
		}

		$keys        = array_keys( $connection_data['custom_fields'] );
		$last_number = $this->convert_column_to_number( end( $keys ) );
		$row         = array_fill( 0, $last_number, '' );

		foreach ( $connection_data['custom_fields'] as $column_name => $column_value ) {
			$value = $this->get_field_value( $column_value, $form_data, $fields, $entry_id );

			$row[ $this->convert_column_to_number( $column_name ) ] = $value;
		}

		return $row;
	}

	/**
	 * Get field value.
	 *
	 * @since 1.0.0
	 *
	 * @param string|int $column_value Field ID or custom value.
	 * @param array      $form_data    Form data and settings.
	 * @param array      $fields       Submitted fields.
	 * @param int        $entry_id     Entry ID.
	 *
	 * @return string
	 */
	private function get_field_value( $column_value, $form_data, $fields, $entry_id ) {

		if ( ! is_string( $column_value ) ) {
			return isset( $fields[ $column_value ]['value'] ) ? $this->escape_formulas( html_entity_decode( $fields[ $column_value ]['value'] ) ) : '';
		}

		// We allow using formulas for custom values if the formula isn't a part of a smart tag.
		if ( in_array( $column_value[0], self::FORMULAS_START_CHARS, true ) ) {
			return wpforms_process_smart_tags( $column_value, $form_data, $fields, $entry_id );
		}

		return $this->escape_formulas( wpforms_process_smart_tags( $column_value, $form_data, $fields, $entry_id ) );
	}

	/**
	 * Escaping formulas for a cell.
	 *
	 * @since 1.0.0
	 *
	 * @param string $text Cell text.
	 *
	 * @return string
	 */
	private function escape_formulas( $text ) {

		if ( ! in_array( substr( (string) $text, 0, 1 ), self::FORMULAS_START_CHARS, true ) ) {
			return $text;
		}

		return "'" . $text;
	}

	/**
	 * Convert column name to number.
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_name Column name e.g. A, B, ..., AAA.
	 *
	 * @return int
	 */
	private function convert_column_to_number( $column_name ) {

		$alphabet        = array_flip( range( 'A', 'Z' ) );
		$alphabet_length = count( $alphabet );

		$letters = str_split( $column_name );
		$number  = 0;
		$i       = 0;

		while ( $letters ) {
			$letter = array_pop( $letters );

			$number += ( $alphabet[ $letter ] + 1 ) * ( $alphabet_length ** $i );

			$i ++;
		}

		$number --;

		return (int) $number;
	}

	/**
	 * Prepare column names.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 * @param array $filled_headings Filled headings in the spreadsheet.
	 * @param array $form_data       Form data and settings.
	 *
	 * @return array
	 */
	public function prepare_headings( $connection_data, $filled_headings, $form_data ) {

		if ( empty( $connection_data['custom_fields'] ) ) {
			return [];
		}

		$columns = [];

		foreach ( $connection_data['custom_fields'] as $column_name => $value ) {
			$number = $this->convert_column_to_number( $column_name );

			if ( ! isset( $filled_headings[ $number ] ) ) {
				$columns[ $column_name ] = $this->get_field_label( $value, $column_name, $form_data );
			}
		}

		return $columns;
	}

	/**
	 * Get field label.
	 *
	 * @since 1.0.0
	 *
	 * @param string|int $column_value Field ID or custom value.
	 * @param string     $column_name  Column name e.g. A, B, ..., AAA.
	 * @param array      $form_data    Form data and settings.
	 *
	 * @return string
	 */
	private function get_field_label( $column_value, $column_name, $form_data ) {

		if ( $column_name === 'A' ) {
			return esc_html__( 'Entry ID', 'wpforms-google-sheets' );
		}

		if ( is_string( $column_value ) ) {
			return sprintf( /* translators: %s is a column name. */
				esc_html__( 'Column %s', 'wpforms-google-sheets' ),
				esc_html( $column_name )
			);
		}

		if ( ! isset( $form_data['fields'][ $column_value ]['label'] ) || wpforms_is_empty_string( $form_data['fields'][ $column_value ]['label'] ) ) {
			return sprintf( /* translators: %d is a field id. */
				esc_html__( 'Field %d', 'wpforms-google-sheets' ),
				esc_html( $column_value )
			);
		}

		return esc_html( $form_data['fields'][ $column_value ]['label'] );
	}
}
