<?php
/**
 * Fields repeater template.
 *
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<label>
	<?php
	printf(
		'%s<i class="fa fa-question-circle-o wpforms-help-tooltip tooltipstered" title="%s"></i>',
		esc_html__( 'Field Mapping', 'wpforms-google-sheets' ),
		esc_html__( 'Map fields to spreadsheet column values.', 'wpforms-google-sheets' )
	)
	?>
</label>
<table class="wpforms-builder-provider-connection-fields-table">
	<thead>
		<tr>
			<th><?php esc_html_e( 'Column Name', 'wpforms-google-sheets' ); ?></th>
			<th colspan="4"><?php esc_html_e( 'Form Field Value', 'wpforms-google-sheets' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<# counter = 0; #>
		<# if ( _.isEmpty( data.custom_fields ) ) { #>
			<# data.custom_fields = { 'A': '=HYPERLINK("{entry_details_url}"; "{entry_id}")' }; #>
		<# } #>
		<# _.each( data.custom_fields, function( value, column ) { #>
			<# isCustomField = ! _.isNumber( value ) && ! _.isEmpty( value ); #>
			<tr class="wpforms-builder-provider-connection-fields-table-row">
				<td class="wpforms-builder-provider-connection-fields-table-column">
					<label for="wpforms-builder-provider-connection-field-name-{{ data.connection_id }}-{{ counter }}">
						<select id="wpforms-builder-provider-connection-field-name-{{ data.connection_id }}-{{ counter }}"
								class="wpforms-builder-provider-connection-field-name"
								name="providers[{{ data.provider }}][{{ data.connection_id }}][custom_fields][{{ counter }}][name]"
								<# if ( counter === 0 ) { #>disabled<# } #>>
							<option value=""><?php esc_html_e( '--- Select a Column ---', 'wpforms-google-sheets' ); ?></option>
							<# _.each( data.columns, function( column_name, column_id ) { #>
								<option value="{{ column_id }}" <# if ( column_id === column ) { #> selected<# } #> <# if ( column_id === 'A' ) { #>disabled<# } #>>
									{{ column_name }}
								</option>
							<# } ); #>
						</select>
					</label>
				</td>
				<td class="wpforms-builder-provider-connection-fields-table-column" <# if ( ! isCustomField ) { #>colspan="2"<# } #>>
					<label for="wpforms-builder-provider-connection-field-id-{{ data.connection_id }}-{{ counter }}">
						<select id="wpforms-builder-provider-connection-field-id-{{ data.connection_id }}-{{ counter }}"
								class="wpforms-builder-provider-connection-field-id js-wpforms-builder-provider-connection-field-id"
								name="providers[{{ data.provider }}][{{ data.connection_id }}][custom_fields][{{ counter }}][field_id]"
								<# if ( counter === 0 ) { #>disabled<# } #>>
							<option value=""><?php esc_html_e( '--- Select Form Field ---', 'wpforms-google-sheets' ); ?></option>
							<# _.each( data.fields, function( field, key ) { #>
								<option value="{{ field.id }}" <# if ( ! isCustomField && value === field.id ) { #> selected<# } #>>
									<# if ( ! _.isUndefined( field.label ) && field.label.toString().trim() !== '' ) { #>
										{{ field.label.toString().trim() }}
									<# } else { #>
										{{ wpforms_builder.field + ' #' + key }}
									<# } #>
								</option>
							<# } ); #>
							<option value="custom" <# if ( isCustomField ) { #> selected<# } #>><?php esc_html_e( 'Custom Value', 'wpforms-google-sheets' ); ?></option>
						</select>
					</label>
				</td>
				<td class="wpforms-builder-provider-connection-fields-table-column wpforms-field-option-row<# if ( ! isCustomField ) { #> wpforms-hidden<# } #>">
					<label for="wpforms-builder-provider-connection-field-value-{{ data.connection_id }}-{{ counter }}">
					<input type="text" value="<# if ( isCustomField ) { #>{{ value }}<# } #>"
						   id="wpforms-builder-provider-connection-field-value-{{ data.connection_id }}-{{ counter }}"
						   class="wpforms-builder-provider-connection-field-value"
						   name="providers[{{ data.provider }}][{{ data.connection_id }}][custom_fields][{{ counter }}][value]"
					<# if ( counter === 0 ) { #>disabled<# } #>>

					<# if ( counter !== 0 ) { #>
						<a href="#"
						   class="toggle-smart-tag-display toggle-unfoldable-cont"
						   data-type="all">
							<em class="fa fa-tags" title="<?php esc_html_e( 'Show Smart Tags', 'wpforms-google-sheets' ); ?>"></em>
						</a>
					<# } #>
					</label>
				</td>
				<td class="add">
					<button class="button-secondary js-wpforms-builder-google-sheets-provider-connection-fields-add"
							title="<?php esc_attr_e( 'Add Another', 'wpforms-google-sheets' ); ?>">
						<span class="fa fa-plus-circle"></span>
					</button>
				</td>
				<td class="delete">
					<button class="button js-wpforms-builder-provider-connection-fields-delete <# if ( counter === 0 ) { #>wpforms-hidden<# } #>"
							title="<?php esc_attr_e( 'Remove', 'wpforms-google-sheets' ); ?>">
						<span class="fa fa-minus-circle"></span>
					</button>
				</td>
			</tr>
			<# counter++; #>
		<# } ); #>
		<tr class="wpforms-builder-provider-connection-fields-table-row">
			<td class="wpforms-builder-provider-connection-fields-table-column">
				<label for="wpforms-builder-provider-connection-field-name-{{ data.connection_id }}-{{ counter }}">
					<select id="wpforms-builder-provider-connection-field-name-{{ data.connection_id }}-{{ counter }}"
							class="wpforms-builder-provider-connection-field-name"
							name="providers[{{ data.provider }}][{{ data.connection_id }}][custom_fields][{{ counter }}][name]">
						<option value=""><?php esc_html_e( '--- Select a Column ---', 'wpforms-google-sheets' ); ?></option>
						<# _.each( data.columns, function( column_name, column_id ) { #>
							<option value="{{ column_id }}"<# if ( column_id === 'A' ) { #> disabled<# } #>>
								{{ column_name }}
							</option>
						<# } ); #>
					</select>
				</label>
			</td>
			<td class="wpforms-builder-provider-connection-fields-table-column" colspan="2">
				<label for="wpforms-builder-provider-connection-field-id-{{ data.connection_id }}-{{ counter }}">
					<select id="wpforms-builder-provider-connection-field-id-{{ data.connection_id }}-{{ counter }}"
							class="wpforms-builder-provider-connection-field-id js-wpforms-builder-provider-connection-field-id"
							name="providers[{{ data.provider }}][{{ data.connection_id }}][custom_fields][{{ counter }}][field_id]">
					<option value=""><?php esc_html_e( '--- Select Form Field ---', 'wpforms-google-sheets' ); ?></option>
					<# _.each( data.fields, function( field, key ) { #>
						<option value="{{ field.id }}">
							<# if ( ! _.isUndefined( field.label ) && field.label.toString().trim() !== '' ) { #>
								{{ field.label.toString().trim() }}
							<# } else { #>
								{{ wpforms_builder.field + ' #' + key }}
							<# } #>
						</option>
					<# } ); #>
					<option value="custom"><?php esc_html_e( 'Custom Value', 'wpforms-google-sheets' ); ?></option>
					</select>
				</label>
			</td>
			<td class="wpforms-builder-provider-connection-fields-table-column wpforms-field-option-row wpforms-hidden">
				<label for="wpforms-builder-provider-connection-field-value-{{ data.connection_id }}-{{ counter }}">
				<input type="text" value=""
					   id="wpforms-builder-provider-connection-field-value-{{ data.connection_id }}-{{ counter }}"
					   class="wpforms-builder-provider-connection-field-value"
					   name="providers[{{ data.provider }}][{{ data.connection_id }}][custom_fields][{{ counter }}][value]">
				<a href="#"
				   class="toggle-smart-tag-display toggle-unfoldable-cont<# if ( counter === 0 ) { #> wpforms-hidden<# } #>"
				   data-type="all">
					<em class="fa fa-tags" title="<?php esc_html_e( 'Show Smart Tags', 'wpforms-google-sheets' ); ?>"></em>
				</a>
				</label>
			</td>
			<td class="add">
				<button class="button-secondary js-wpforms-builder-google-sheets-provider-connection-fields-add"
						title="<?php esc_attr_e( 'Add Another', 'wpforms-google-sheets' ); ?>">
					<span class="fa fa-plus-circle"></span>
				</button>
			</td>
			<td class="delete">
				<button class="button js-wpforms-builder-provider-connection-fields-delete"
						title="<?php esc_attr_e( 'Remove', 'wpforms-google-sheets' ); ?>">
					<span class="fa fa-minus-circle"></span>
				</button>
			</td>
		</tr>
	</tbody>
</table>
