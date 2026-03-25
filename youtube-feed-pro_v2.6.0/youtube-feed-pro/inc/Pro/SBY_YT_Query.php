<?php
namespace SmashBalloon\YouTubeFeed\Pro;

class SBY_YT_Query
{
	private $posts;

	private $sby_youtube_posts_obj;

	public function __construct( $args ) {

		$args['post_type'] = SBY_CPT;

		if ( isset( $args['channel_title'] ) ) {
			$args['meta_query'][] = array(
					'value'	=>	$args['channel_title'],
					'key'	=>	'sby_channel_title'
				);
		} elseif ( isset( $args['channel_id'] ) ) {
			$args['meta_query'][] = array(
				'value'	=>	$args['channel_id'],
				'key'	=>	'sby_channel_id'
			);
		} elseif ( isset( $args['video_id'] ) ) {
			$args['meta_query'][] = array(
				'value'	=>	$args['video_id'],
				'key'	=>	'sby_video_id'
			);
		} elseif ( isset( $args['sbys'] ) ) {
			$search_in = isset( $args['sbys_in'] ) ? $args['sbys_in'] : array( 'description' );
			foreach ( $search_in as $meta_key ) {
				if ( $meta_key !== 'title' ) {
					$args['meta_query'][] = array(
						'value'	=>	$args['sbys'],
						'key'	=>	'sby_' . $meta_key,
						'compare'  => 'LIKE'
					);
				}
			}
		}

		$sby_posts = new \WP_Query( $args );

		$this->sby_youtube_posts_obj = $sby_posts;

		if ( $sby_posts->have_posts() ) {
			$this->posts = $sby_posts->posts;
		} else {
			$this->posts = array();
		}
	}

	public function get_posts() {
		return $this->posts;
	}

	public static function get_unique_channel_titles() {
		global $wpdb;

		$unique_channels = $wpdb->get_col( "
        SELECT m.meta_value
        FROM $wpdb->postmeta as m
        WHERE m.meta_key = 'sby_channel_title'
        GROUP BY m.meta_value" );

		return $unique_channels;
	}

	public static function get_unique_channel_ids() {
		global $wpdb;

		$unique_channels = $wpdb->get_col( "
        SELECT m.meta_value
        FROM $wpdb->postmeta as m
        WHERE m.meta_key = 'sby_channel_id'
        GROUP BY m.meta_value" );

		return $unique_channels;
	}
}