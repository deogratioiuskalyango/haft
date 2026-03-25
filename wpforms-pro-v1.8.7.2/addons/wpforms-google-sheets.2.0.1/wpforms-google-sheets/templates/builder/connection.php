<?php
/**
 * Connection template.
 *
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wpforms-builder-provider-connection" data-connection_id="{{ data.connection.id }}">
	<input type="hidden"
			class="wpforms-builder-provider-connection-id"
			name="providers[{{ data.provider }}][{{ data.connection.id }}][id]"
			value="{{ data.connection.id }}">

	<div class="wpforms-builder-provider-connection-title">
		{{ data.connection.name }}
		<button class="wpforms-builder-provider-connection-delete js-wpforms-builder-provider-connection-delete" type="button">
			<span class="fa fa-trash-o"></span>
		</button>
		<input type="hidden"
				name="providers[{{ data.provider }}][{{ data.connection.id }}][name]"
				value="{{ data.connection.name }}">
	</div>

	<div class="wpforms-builder-provider-connection-block wpforms-builder-google-sheets-provider-spreadsheet-id">
		<label for="wpforms-builder-google-sheets-spreadsheet-field-{{ data.connection.id }}">
			<?php esc_html_e( 'Spreadsheet', 'wpforms-google-sheets' ); ?><span class="required">*</span>
		</label>

		<div class="wpforms-builder-provider-connection-block-field-wrapper wpforms-builder-google-sheets-provider-spreadsheet-type">
			<div class="wpforms-builder-provider-connection-block-field">
				<label>
					<input type="radio" value="existing" name="providers[{{ data.provider }}][{{ data.connection.id }}][spreadsheet_type]" class="js-wpforms-builder-google-sheets-provider-connection-spreadsheet-type wpforms-builder-google-sheets-spreadsheet-type" checked>
					<?php esc_html_e( 'Select Existing', 'wpforms-google-sheets' ); ?>
				</label>
				<label>
					<input type="radio" value="new" name="providers[{{ data.provider }}][{{ data.connection.id }}][spreadsheet_type]" class="js-wpforms-builder-google-sheets-provider-connection-spreadsheet-type wpforms-builder-google-sheets-spreadsheet-type">
					<?php esc_html_e( 'Create New', 'wpforms-google-sheets' ); ?>
				</label>
			</div>
		</div>
		<div class="wpforms-builder-provider-connection-block-field-wrapper wpforms-builder-google-sheets-provider-spreadsheet-existing">
			<input type="hidden"
					name="providers[{{ data.provider }}][{{ data.connection.id }}][spreadsheet_id]"
					value="{{ data.connection.spreadsheet_id }}"
					class="js-wpforms-builder-google-sheets-provider-connection-spreadsheet-id"
			>
			<div class="wpforms-builder-provider-connection-block-field wpforms-builder-provider-connection-block-field-existing wpforms-builder-provider-connection-block-field-exiting-empty<# if ( ! _.isEmpty( data.connection.spreadsheet_id ) ) { #> wpforms-hidden<# } #>">
				<button type="button"
						class="js-wpforms-builder-google-sheets-provider-connection-spreadsheet-id-choose wpforms-btn wpforms-btn-sm wpforms-btn-light-grey-blue-borders">
						<?php esc_html_e( 'Select Spreadsheet', 'wpforms-google-sheets' ); ?>
				</button>
			</div>
			<div class="wpforms-builder-provider-connection-block-field wpforms-builder-provider-connection-block-field-existing wpforms-builder-provider-connection-block-field-exiting-not-empty<# if ( _.isEmpty( data.connection.spreadsheet_id ) ) { #> wpforms-hidden<# } #>">
				<button type="button"
						class="js-wpforms-builder-google-sheets-provider-connection-spreadsheet-id-remove wpforms-btn wpforms-btn-sm wpforms-btn-remove-spreadsheet-connection">
					<?php esc_html_e( 'Remove Spreadsheet Connection', 'wpforms-google-sheets' ); ?>
				</button>
				<a href="#"
					target="_blank"
					rel="noopener noreferrer"
					title="<?php esc_html_e( 'Link to the Google spreadsheet', 'wpforms-google-sheets' ); ?>">
					<i class="fa fa-external-link" aria-hidden="true"></i>
				</a>
			</div>
		</div>
	</div>

	<div class="wpforms-builder-provider-connection-block wpforms-builder-google-sheets-provider-spreadsheet-name wpforms-hidden">
		<label for="wpforms-builder-google-sheets-spreadsheet-name-field-{{ data.connection.id }}">
			<?php esc_html_e( 'Spreadsheet Name', 'wpforms-google-sheets' ); ?>
		</label>

		<input id="wpforms-builder-google-sheets-spreadsheet-name-field-{{ data.connection.id }}"
				type="text"
				class="wpforms-disabled"
				name="providers[{{ data.provider }}][{{ data.connection.id }}][spreadsheet_name]"
				placeholder="<?php esc_html_e( 'WPForms Spreadsheet', 'wpforms-google-sheets' ); ?>"
				value="">
	</div>

	<div class="wpforms-builder-provider-connection-block wpforms-builder-google-sheets-provider-sheet-id<# if ( _.isEmpty( data.connection.spreadsheet_id ) ) { #> wpforms-hidden<# } #>">
		<label for="wpforms-builder-google-sheets-sheet-field-{{ data.connection.id }}">
			<?php esc_html_e( 'Sheet', 'wpforms-google-sheets' ); ?><span class="required">*</span>
		</label>
		<div class="wpforms-builder-provider-connection-block-field"></div>
	</div>

	<div class="wpforms-builder-provider-connection-block wpforms-builder-google-sheets-provider-sheet-name wpforms-hidden">
		<label for="wpforms-builder-google-sheets-sheet-name-field-{{ data.connection.id }}">
			<?php esc_html_e( 'Sheet Name', 'wpforms-google-sheets' ); ?>
		</label>

		<input id="wpforms-builder-google-sheets-sheet-name-field-{{ data.connection.id }}"
				type="text"
				class="wpforms-disabled"
				name="providers[{{ data.provider }}][{{ data.connection.id }}][sheet_name]"
				placeholder="<?php esc_html_e( 'Sheet 1', 'wpforms-google-sheets' ); ?>"
				value="">
	</div>

	<div class="wpforms-builder-provider-connection-block wpforms-builder-google-sheets-provider-connection-fields wpforms-hidden"></div>

	{{{ data.conditional }}}
</div>
