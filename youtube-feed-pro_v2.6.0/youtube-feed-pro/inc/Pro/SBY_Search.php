<?php
namespace SmashBalloon\YouTubeFeed\Pro;

class SBY_Search
{
	public function __construct() {
		add_shortcode( SBY_SEARCH_SLUG, array( $this, 'youtube_video_search' ) );
	}

	public static function youtube_video_search( $atts ) {
		global $sby_settings;
		$atts = is_array( $atts ) ? $atts : array();
		$input_name = SBY_SEARCH_NAME;
		$search_term = '';
		$results = array();
		$results_title = array();
		$results_description = array();

		if ( isset( $_GET[ $input_name ] ) ) {
			$search_term = sanitize_text_field( $_GET[ $input_name ] );

			$args = array(
				's' => $search_term,
			);
			$title_query = new SBY_YT_Query( $args );
			$results_title = $title_query->get_posts();

			if ( empty( $results_title ) ) {
				$args = array(
					'sbys' => $search_term,
					'sbys_in' => array( 'description' )
				);
				$description_query = new SBY_YT_Query( $args );

				$results_description = $description_query->get_posts();
			}

			wp_reset_postdata();

			$results_raw = array_merge( $results_title, $results_description );

			$results = SBY_Search::remove_duplicates( $results_raw );
		}

		ob_start();
		include sby_get_feed_template_part( 'form', $sby_settings );
		$html = ob_get_contents();
		ob_get_clean();

		return $html;

	}

	public static function remove_duplicates( $posts ) {
		$return_posts = array();

		$ids_included = array();
		foreach ( $posts as $post ) {
			if ( ! in_array( $post->ID, $ids_included, true ) ) {
				$ids_included[] = $post->ID;
				$return_posts[] = $post;
			}
		}

		return $return_posts;
	}

	public static function results_loop( $posts ) {
		global $sby_settings;
		foreach ( $posts as $youtube_post ) {
			$youtube_post_meta = get_post_meta( $youtube_post->ID );

			$api_data = json_decode( $youtube_post_meta['sby_json'][0], true );
			$settings = $sby_settings;

			include sby_get_feed_template_part( 'result', $sby_settings );

		}
	}

}