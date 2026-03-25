<?php

/**
 * SBY_Comments_Pro.
 *
 * @since 2.3.3
 */

namespace SmashBalloon\YouTubeFeed\Pro;

use SmashBalloon\YouTubeFeed\Pro\SBY_Display_Elements_Pro;

class SBY_Comments_Pro
{
	/**
	 * Generates base HTML for displaying comments for list and gallery layout
	 * 
	 * @return false|string
	 * 
	 * @since 2.3.3
	 */

	public static function comment_html($settings) {
		$message_icon = '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.6693 13.73L11.0026 11.0633H1.66927C1.3026 11.0633 0.988715 10.9327 0.727604 10.6716C0.466493 10.4105 0.335938 10.0966 0.335938 9.72997V1.72997C0.335938 1.3633 0.466493 1.04941 0.727604 0.788304C0.988715 0.527193 1.3026 0.396637 1.66927 0.396637H12.3359C12.7026 0.396637 13.0165 0.527193 13.2776 0.788304C13.5387 1.04941 13.6693 1.3633 13.6693 1.72997V13.73ZM1.66927 9.72997H11.5693L12.3359 10.48V1.72997H1.66927V9.72997Z" fill="currentColor"/></svg>';

		ob_start(); ?>
			<div class="sby-comment-container" <?php echo SBY_Display_Elements_Pro::get_comment_data_attributes( $settings ); ?>>
				<button class="sby-comments-trigger">
					<?php echo $message_icon; ?>
					<p><?php echo esc_html( 'Show Comments', 'feeds-for-youtube' ) ?></p>
				</button>
				<div class="sby-comments-wrap"></div>
			</div>
		<?php

		return ob_get_clean();
	}
}
