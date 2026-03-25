<?php
/**
 * Auth notice template.
 *
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wpforms-alert wpforms-alert-danger">
	<div class="wpforms-alert-message">
		<p>
			<?php
			echo wp_kses(
				sprintf( /* translators: %1$s is email of active google account; %2$s is a spreadsheet URL with no access. */
					__( 'The Google account (%1$s) you connected doesn\'t have permission to edit <a href="%2$s" target="_blank" rel="noopener noreferrer">this spreadsheet</a>. Please ask the owner to add you as an Editor, or visit <strong>WPForms</strong> » <strong>Settings</strong> » <strong>Integrations</strong> » <strong>Google Sheets</strong> to switch to a different Google account.', 'wpforms-google-sheets' ),
					'{{ data.email }}',
					'{{ data.spreadsheet_url }}'
				),
				[
					'a'      => [
						'href'   => [],
						'rel'    => [],
						'target' => [],
					],
					'strong' => [],
				]
			);
			?>
		</p>
	</div>
</div>
