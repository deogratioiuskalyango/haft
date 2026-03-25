<?php
/**
 * AdminAjaxServicePro
 * 
 * @since 2.3.3
 */

namespace SmashBalloon\YouTubeFeed\Pro\Services;

use Smashballoon\Stubs\Services\ServiceProvider;
use SmashBalloon\YouTubeFeed\Pro\SBY_API_Connect_Pro;
use SmashBalloon\YouTubeFeed\Pro\SBY_Settings_Pro;

class AdminAjaxServicePro extends ServiceProvider {

	public function register() {
		add_action('wp_ajax_sby_get_comments', [$this, 'sby_get_comments']);
		add_action('wp_ajax_nopriv_sby_get_comments', [$this, 'sby_get_comments']);
    }

	/**
	 * Retrieves comments for a specific video.
	 * @return void
	 * @since 2.3.3
	 */
	public function sby_get_comments()
	{
		$video_id  = !empty($_POST['video_id']) ? $_POST['video_id'] : '';

		if (empty($video_id)) {
			wp_send_json_error('Error: Video ID is invalid');
		}

		$atts = isset($_POST['atts']) ? json_decode(stripslashes($_POST['atts']), true) : null;
		if (is_array($atts)) {
			array_map('sanitize_text_field', $atts);
		} else {
			$atts = array();
		}

		$database_settings = sby_get_database_settings();
		$youtube_feed_settings = new SBY_Settings_Pro($atts, $database_settings);


		if (empty($database_settings['connected_accounts']) && empty($database_settings['api_key'])) {
			wp_send_json_error('Error: No connected account');
		}

		$video_id = sanitize_text_field($_POST['video_id']);

		$settings = $youtube_feed_settings->get_settings();

		$comment_count = isset($settings['numcomments']) ? $settings['numcomments'] : 5;
		$enable_comments = isset($settings['enablecomments']) ? (bool)$settings['enablecomments'] : false;

		if (!empty($video_id) && true === $enable_comments) {

			$get_cache = get_transient('sby_comment_cache');

			$cache = json_decode($get_cache);

			if (!isset($cache->$video_id->etag) ) {

				$params = array(
					'num' => (int) $comment_count,
					'video_id' => $video_id
				);

				$connection = new SBY_API_Connect_Pro(sby_get_first_connected_account(), 'comments', $params);
				$connection->connect();

				$comment_data = array(
					$video_id => $connection->get_data()
				);

				$current_set_cache = $comment_data;

				if ($cache) {
					$cache->$video_id = $comment_data[$video_id];
					$current_set_cache = $cache;
				}

				set_transient('sby_comment_cache', json_encode($current_set_cache));
			}

			$updated_cache = get_transient('sby_comment_cache');

			if (!empty($updated_cache)) {
				$cache = json_decode(get_transient('sby_comment_cache'));
				echo wp_json_encode($cache->$video_id);
			}

			die();
		}
	}
}
