<?php
/**
 * Class SBY_Parse_Pro
 *
 * @since 5.0
 */
namespace SmashBalloon\YouTubeFeed\Pro;

use SmashBalloon\YouTubeFeed\SBY_Parse;

class SBY_Parse_Pro extends SBY_Parse {

	public static function get_item_avatar( $post, $avatars = array() ) {
		if ( empty ( $avatars ) ) {
			return '';
		} else {
			$username = SBY_Parse_Pro::get_channel_id( $post );
			if ( isset( $avatars[ $username ] ) ) {
				return $avatars[ $username ];
			}
		}

		return '';
	}

	/**
	 * Number of posts made by account
	 *
	 * @param $header_data
	 *
	 * @return int
	 *
	 * @since 5.0
	 */
	public static function get_post_count( $header_data ) {
		if ( isset( $header_data['data']['counts'] ) ) {
			return $header_data['data']['counts']['media'];
		} elseif ( isset( $header_data['counts'] ) ) {
			return $header_data['counts']['media'];
		} elseif ( isset( $header_data['media_count'] ) ) {
			return $header_data['media_count'];
		}

		return 0;
	}

	/**
	 * Number of followers for account
	 *
	 * @param $header_data
	 *
	 * @return int
	 *
	 * @since 5.0
	 */
	public static function get_follower_count( $header_data ) {
		if ( isset( $header_data['data']['counts'] ) ) {
			return $header_data['data']['counts']['followed_by'];
		} elseif ( isset( $header_data['counts'] ) ) {
			return $header_data['counts']['followed_by'];
		} elseif ( isset( $header_data['followers_count'] ) ) {
			return $header_data['followers_count'];
		}

		return 0;
	}

	public static function get_actual_end_timestamp( $post, $misc_data = array() ) {

		if ( ! empty( $post['liveStreamingDetails']['actualEndTime'] ) ) {
			$remove_extra = str_replace( array( 'T', '+00:00', '.000Z', '+' ), ' ', $post['liveStreamingDetails']['actualEndTime'] );
			$timestamp    = strtotime( $remove_extra );

			return $timestamp;
		} elseif ( isset( $misc_data['live_streaming_details'][ SBY_Parse::get_video_id( $post ) ]['sby_actual_end_time'] ) ) {
			return strtotime( $misc_data['live_streaming_details'][ SBY_Parse::get_video_id( $post ) ]['sby_actual_end_time'] );
		} elseif ( isset( $misc_data['sby_actual_end_time'][0] ) ) {
			return strtotime( $misc_data['sby_actual_end_time'][0] );
		} elseif ( isset( $post['sby_actual_end_time'] ) ) {
			return strtotime( $post['sby_actual_end_time'] );
		}

		return 0;
	}

	/**
	 * Get video duration from post
	 * 
	 * YouTube provide video duration in ISO 8601 format so we need to convert it to duration
	 * 
	 * @since 2.1
	 */
	public static function get_video_duration( $post ) {
		if ( !isset( $post['contentDetails']['duration'] ) && !isset( $post['snippet']['videoDuration'] ) ) {
			return;
		}
		$duration = isset( $post['contentDetails']['duration'] ) ? $post['contentDetails']['duration'] : $post['snippet']['videoDuration'];
		$interval = new \DateInterval($duration);
		$totalSeconds = $interval->s + $interval->i * 60 + $interval->h * 3600;
		$hours = floor($totalSeconds / 3600);
		$minutes = floor(($totalSeconds - $hours * 3600) / 60);
		$seconds = str_pad($totalSeconds % 60, 2, '0', STR_PAD_LEFT);
		if ( $hours > 0 ) {
			return "$hours:$minutes:$seconds";
		} else {
			return "$minutes:$seconds";
		}
	}
}