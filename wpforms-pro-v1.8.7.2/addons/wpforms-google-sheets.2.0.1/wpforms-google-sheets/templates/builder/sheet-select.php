<?php
/**
 * Sheet select template.
 *
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<select id="wpforms-builder-google-sheets-sheet-field-{{ data.connection_id }}"
		class="js-wpforms-builder-google-sheets-provider-connection-sheet-id wpforms-required"
		name="providers[{{ data.provider }}][{{ data.connection_id }}][sheet_id]">
	<option value="" selected disabled><?php esc_html_e( '--- Select a Sheet ---', 'wpforms-google-sheets' ); ?></option>
	<option value="new"><?php esc_html_e( 'Create a New Sheet', 'wpforms-google-sheets' ); ?></option>
	<# _.each( data.sheets, function( sheet ) { #>
		<# sheet_id = parseInt( sheet.id ); #>
		<option value="{{ sheet_id }}">{{ sheet.name }}</option>
	<# } ); #>
</select>
