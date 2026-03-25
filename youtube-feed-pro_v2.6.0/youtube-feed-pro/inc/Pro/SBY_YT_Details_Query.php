<?php
namespace SmashBalloon\YouTubeFeed\Pro;

class SBY_YT_Details_Query
{
	private $vid_details;

	private $vid_ids;

	private $cache_expiration_time;

	public function __construct( $args, $settings = array() ) {

		if ( isset( $args['video_ids'] ) ) {
			if ( is_array( $args['video_ids'] ) ) {
				$this->vid_ids = $args['video_ids'];
			} else {
				$this->vid_ids = explode( ',', $args['video_ids'] );
			}
		}

		if ( isset( $settings['details_cache_time'] ) ) {
			$this->cache_expiration_time = $settings['details_cache_time'];
		} else {
			$this->cache_expiration_time = 60 * 2;
		}

		$this->vid_details = $this->get_cached_details_for_posts();
	}

	public function get_video_details_to_update() {
		$vids_to_retrieve = array();
		$first_connected = sby_get_first_connected_account();

		if ( ! isset( $first_connected['api_key'] ) ) {
			return array();
		}

		$params = array();
		$parts = array( 'id', 'statistics' );
		foreach ( $this->vid_details as $video ) {
			if ( ! isset( $video['sby_last_details_check_time'] )
			     || strtotime( $video['sby_last_details_check_time'] ) < (time() - $this->cache_expiration_time) ) {
				if ( $video['sby_description'] === null
				     || substr( $video['sby_description'], -3 ) === '...' ) {
					if ( ! in_array( 'snippet', $parts, true ) ) {
						$parts[] = 'snippet';
					}
				}
				$live_broadcast_content = SBY_Parse_Pro::get_live_broadcast_content( $video );
				if ( ! empty( $live_broadcast_content )
				     && $live_broadcast_content === 'upcoming' || $live_broadcast_content === 'live' || $live_broadcast_content === 'completed' ) {
					if ( ! in_array( 'liveStreamingDetails', $parts, true ) ) {
						$parts[] = 'liveStreamingDetails';
					}
				}
				$vids_to_retrieve[] = $video['sby_video_id'];
			}
		}
		$params['part'] = $parts;

		if ( ! empty( $vids_to_retrieve ) ) {
			$params['id'] = implode( ',', $vids_to_retrieve );
			$video_data_connection = new SBY_API_Connect_Pro( $first_connected, 'videos', $params );
			$video_data_connection->connect();

			$data = $video_data_connection->get_data();

			if ( isset( $data['items'] ) ) {
				return $data['items'];
			}
		}

		return array();
	}

	public function get_cached_details_for_posts() {
		global $wpdb;

		$in_clause = "";
		foreach ( $this->vid_ids as $id ) {
			$in_clause .= "'" . esc_sql( $id ) . "',";
		}
		$in_clause = substr( $in_clause, 0, -1 );

		$vid_details = $wpdb->get_results( "
        SELECT Max(CASE 
			WHEN m.meta_key = 'sby_video_id' THEN m.meta_value 
			ELSE NULL 
			END) AS sby_video_id,
			Max(CASE 
			WHEN m.meta_key = 'sby_last_details_check_time' THEN m.meta_value 
			ELSE NULL 
			END) AS sby_last_details_check_time,
			Max(CASE 
			WHEN m.meta_key = 'sby_description' THEN m.meta_value 
			ELSE NULL 
			END) AS sby_description,
			Max(CASE 
			WHEN m.meta_key = 'sby_comment_count' THEN m.meta_value 
			ELSE NULL 
			END) AS sby_comment_count,
			Max(CASE 
			WHEN m.meta_key = 'sby_like_count' THEN m.meta_value 
			ELSE NULL 
			END) AS sby_like_count,
			Max(CASE 
			WHEN m.meta_key = 'sby_view_count' THEN m.meta_value 
			ELSE NULL 
			END) AS sby_view_count,
			Max(CASE 
			WHEN m.meta_key = 'sby_live_broadcast_content' THEN m.meta_value 
			ELSE NULL 
			END) AS sby_live_broadcast_content,
			Max(CASE 
			WHEN m.meta_key = 'sby_actual_start_time' THEN m.meta_value 
			ELSE NULL 
			END) AS sby_actual_start_time,
			Max(CASE 
			WHEN m.meta_key = 'sby_actual_end_time' THEN m.meta_value 
			ELSE NULL 
			END) AS sby_actual_end_time,
			Max(CASE 
			WHEN m.meta_key = 'sby_scheduled_start_time' THEN m.meta_value 
			ELSE NULL 
			END) AS sby_scheduled_start_time,
			m.post_id
        FROM $wpdb->postmeta as m
        WHERE m.post_id IN (SELECT m2.post_id FROM $wpdb->postmeta as m2 WHERE m2.meta_key = 'sby_video_id' AND m2.meta_value IN ( $in_clause ))
        GROUP BY m.post_id", ARRAY_A );

		return $vid_details;
	}
}