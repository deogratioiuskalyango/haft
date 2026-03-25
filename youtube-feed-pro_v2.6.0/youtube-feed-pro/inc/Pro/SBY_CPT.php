<?php

namespace SmashBalloon\YouTubeFeed\Pro;

class SBY_CPT {
	public function __construct() {

		add_filter( 'manage_' . SBY_CPT . '_posts_columns', array( $this, 'set_custom_sby_videos_columns' ) );
		add_filter( 'manage_edit-' . SBY_CPT . '_sortable_columns', array( $this, 'set_custom_sortable_sby_videos_columns' ), 10, 1 );
		add_action( 'manage_' . SBY_CPT . '_posts_custom_column', array( $this, 'custom_sby_videos_column' ), 10, 2 );
		add_action( 'pre_get_posts', array( $this, '' . SBY_CPT . '_custom_order' ), 10, 1 );
		add_action( 'admin_init', array( $this, 'channel_action_listener' ) );

		add_shortcode( 'youtube-feed-single', array( $this, 'youtube_feed_single' ) );
	}

	public static function set_up_submenus() {
		add_submenu_page(
			SBY_SLUG,
			__( 'Manage Single Videos', 'feeds-for-youtube' ),
			__( 'Single Video Settings', 'feeds-for-youtube' ),
			'edit_' . SBY_CPT,
			'sby_single_settings',
			array( __CLASS__, 'single_settings_admin_page' )
		);
	}

	public static function single_settings_admin_page() {
		include_once trailingslashit( SBY_PLUGIN_DIR ) . 'inc/Admin/templates/single-settings.php';
	}

	public static function set_custom_sby_videos_columns( $columns ) {

		$columns['channel_title']        = __( 'Channel', 'feeds-for-youtube' );
		$columns['video_id']             = __( 'Video ID', 'feeds-for-youtube' );
		$columns['youtube_publish_date'] = __( 'Publish Date', 'feeds-for-youtube' );

		unset( $columns['author'] );

		return $columns;
	}

	public static function custom_sby_videos_column( $column, $post_id ) {
		switch ( $column ) {

			case 'channel_title' :
				$channel = get_post_meta( $post_id, 'sby_channel_title', true );
				if ( ! empty( $channel ) ) {
					echo esc_html( $channel );
				}

				break;

			case 'video_id' :
				$video_id = get_post_meta( $post_id, 'sby_video_id', true );
				if ( ! empty( $video_id ) ) {
					echo esc_html( $video_id );
				}

				break;

			case 'youtube_publish_date' :
				$publish_date = get_post_meta( $post_id, 'sby_youtube_publish_date', true );
				$date_format  = get_option( 'date_format' );
				$time_format  = get_option( 'time_format' );
				if ( $date_format && $time_format ) {
					$date_time_format = $date_format . ' ' . $time_format;
				} else {
					$date_time_format = 'F j, Y g:i a';
				}

				if ( ! empty( $publish_date ) ) {
					echo esc_html( date_i18n( $date_time_format, strtotime( $publish_date ) + sby_get_utc_offset() ) );
				}


				break;

		}
	}

	public static function set_custom_sortable_sby_videos_columns( $columns ) {
		$columns['channel_title']        = 'sby_channel_title';
		$columns['youtube_publish_date'] = 'sby_youtube_publish_date';

		return $columns;
	}

	public function sby_videos_custom_order( $query ) {
		if ( ! is_admin() ) {
			return;
		}

		$orderby = $query->get( 'orderby' );

		if ( in_array( $orderby, array( 'sby_channel_title', 'sby_video_id', 'sby_youtube_publish_date' ), true ) ) {
			$query->set( 'meta_key', $orderby );
			$query->set( 'orderby', 'meta_value' );
		}

		if ( isset( $_GET['channel_id'] ) && $query->is_main_query() && $query->query_vars['post_type'] == SBY_CPT ) {
			//Get original meta query
			$meta_query = (array) $query->get( 'meta_query' );

			// Add your criteria
			$meta_query[] = array(
				'key'     => 'sby_channel_id',
				'value'   => sanitize_text_field( $_GET['channel_id'] ),
				'compare' => '=',
			);

			// Set the meta query to the complete, altered query
			$query->set( 'meta_query', $meta_query );
		}
	}

	public static function channel_action_listener() {
		if ( ! isset( $_GET['sby_action'] ) || ! isset( $_GET['channel'] ) ) {
			return;
		}

		$action     = sanitize_text_field( $_GET['sby_action'] );
		$channel_id = sanitize_text_field( $_GET['channel'] );

		if ( $action === 'publish' ) {

			if ( ! empty( $channel_id ) ) {
				$args            = array(
					'channel_id'     => $channel_id,
					'post_status'    => array( 'draft', 'pending' ),
					'posts_per_page' => - 1
				);
				$draft_posts     = new SBY_YT_Query( $args );
				$draft_posts_arr = $draft_posts->get_posts();

				foreach ( $draft_posts_arr as $post ) {
					$update_post = array( 'ID' => $post->ID, 'post_status' => 'publish' );
					wp_update_post( $update_post );
				}
			}


		} elseif ( $action === 'trash' ) {
			if ( ! empty( $channel_id ) ) {
				$args            = array(
					'channel_id'     => $channel_id,
					'post_status'    => 'any',
					'posts_per_page' => - 1
				);
				$draft_posts     = new SBY_YT_Query( $args );
				$draft_posts_arr = $draft_posts->get_posts();

				foreach ( $draft_posts_arr as $post ) {
					wp_trash_post( $post->ID );
				}
			}

		}

		wp_safe_redirect( admin_url( 'admin.php?page=youtube-feed-single-videos' ) );
	}

	public static function youtube_feed_single( $atts = array() ) {
		$atts = ! empty( $atts ) ? $atts : array();
		if ( isset( $atts['postid'] ) ) {
			$args          = array(
				'p'              => $atts['postid'],
				'post_status'    => 'any',
				'posts_per_page' => - 1
			);
			$vid_posts     = new SBY_YT_Query( $args );
			$vid_posts_arr = $vid_posts->get_posts();

			if ( isset ( $vid_posts_arr[0] ) ) {
				$youtube_post = $vid_posts_arr[0];
			}
		} elseif ( isset( $atts['videoid'] ) ) {
			$args          = array(
				'video_id'       => $atts['videoid'],
				'post_status'    => 'any',
				'posts_per_page' => - 1
			);
			$vid_posts     = new SBY_YT_Query( $args );
			$vid_posts_arr = $vid_posts->get_posts();

			if ( isset ( $vid_posts_arr[0] ) ) {
				$youtube_post = $vid_posts_arr[0];
			}
		} else {
			global $post;

			if ( $post->post_type === SBY_CPT ) {
				$youtube_post = $post;
			}
		}
		if ( ! isset( $youtube_post ) ) {
			return 'Need to add Post ID';
		}

		global $sby_settings;

		$youtube_post_meta = get_post_meta( $youtube_post->ID );

		$api_data       = json_decode( $youtube_post_meta['sby_json'][0], true );
		$settings       = $sby_settings;
		$shortcode_atts = wp_json_encode( $atts );

		$options_att_arr['cta'] = array(
			'type' => 'default'
		);
		if ( $settings['cta'] === 'link' ) {
			$options_att_arr['cta']['type'] = 'link';
		}

		$options_att_arr['cta']['defaultLink'] = $settings['linkurl'];
		$options_att_arr['cta']['defaultText'] = $settings['linktext'];
		$options_att_arr['cta']['openType'] = $settings['linkopentype'];
		$button_color = str_replace( '#', '', $settings['linkcolor'] );
		$button_text_color = str_replace( '#', '', $settings['linktextcolor'] );
		$options_att_arr['cta']['color'] = ! empty( $button_color ) ? sby_hextorgb( $button_color ) : '';
		$options_att_arr['cta']['textColor'] = ! empty( $button_text_color ) ? sby_hextorgb( $button_text_color ) : '';

		if ( ! empty( $settings['descriptionlength'] ) ) {
			$options_att_arr['descriptionlength'] = (int)$settings['descriptionlength'];
		}

		$other_atts = ' data-options="'.esc_attr( wp_json_encode( $options_att_arr ) ).'"';
		$icon_type      = $settings['font_method'];

		wp_enqueue_script( 'sby_scripts' );

		include sby_get_feed_template_part( 'shortcode-content', $settings );
	}

	public static function get_sby_cpt_settings() {
		$defaults            = array(
			'include'     => array( 'description', 'stats' ),
			'post_status' => 'draft'
		);
		$sby_videos_settings = get_option( SBY_CPT . '_settings', $defaults );

		return $sby_videos_settings;
	}

	public static function setting_name( $name, $is_array = false ) {

		$return = SBY_CPT . '_settings[' . $name . ']';

		if ( $is_array ) {
			$return .= '[]';
		}

		return $return;

	}

	public static function validate_options( $input, $option_name ) {
		$updated_options = get_option( $option_name, array() );

		foreach ( $input as $key => $val ) {
			if ( is_array( $val ) ) {
				$updated_options[ $key ] = array();
				foreach ( $val as $val2 ) {
					$updated_options[ $key ][] = sanitize_text_field( $val2 );
				}

			} else {
				// include in search set to false
				if ( $val === 'on' ) {
					$val = true;
				}
				if ( $key === 'search_include' ) {
					$updated_options[ $key ] = false;
				} else {
					$updated_options[ $key ] = sanitize_text_field( $val );
				}
			}
		}

		$updated_options = apply_filters( 'sby_single_settings_valid_options', $updated_options, $input );

		return $updated_options;
	}
}
