<?php

use SmashBalloon\YouTubeFeed\Pro\SBY_CPT;
use SmashBalloon\YouTubeFeed\Pro\SBY_Display_Elements_Pro;
use SmashBalloon\YouTubeFeed\Pro\SBY_Parse_Pro;
use SmashBalloon\YouTubeFeed\SBY_Display_Elements;
use SmashBalloon\YouTubeFeed\SBY_Parse;

$sby_videos_settings = SBY_CPT::get_sby_cpt_settings();
/* Feed */
$feed_styles = SBY_Display_Elements::get_feed_style( $settings );
$cols_setting = SBY_Display_Elements::get_cols( $settings );
$mobile_cols_setting = SBY_Display_Elements::get_cols_mobile( $settings );
$items_wrap_classes = $settings['infoposition'] === 'side' ? ' sby_info_side' : '';
$items_wrap_style_attr = SBY_Display_Elements::get_style_att( 'items_wrap', $settings );
$num_setting = $settings['num'];
$nummobile_setting = $settings['nummobile'];

/* Player */
$post_id = SBY_Parse::get_post_id( $api_data );
$timestamp = SBY_Parse::get_timestamp( $api_data );
$video_id = SBY_Parse::get_video_id( $api_data );
$protocol = is_ssl() ? 'https' : 'http';
$media_url               = SBY_Display_Elements::get_optimum_media_url( $api_data, $settings );
$media_full_res          = SBY_Parse::get_media_url( $api_data );
$media_all_sizes_json    = SBY_Parse::get_media_src_set( $api_data );
$permalink = SBY_Parse::get_permalink( $api_data );
$img_alt                 = SBY_Parse::get_caption( $api_data, __( 'Image for post', 'feeds-for-youtube' ) . ' ' . $post_id );
$player_outer_wrap_style_attr = SBY_Display_Elements::get_style_att( 'player_outer_wrap', $settings );

// Pro Elements
$caption             = SBY_Parse_Pro::get_caption( $api_data, '' );
//$avatar              = SBY_Parse_Pro::get_item_avatar( $post, $settings['feed_avatars'] );
$avatar              = SBY_Parse_Pro::get_item_avatar( $api_data );
$title = SBY_Parse::get_video_title( $api_data );

$username            = SBY_Parse_Pro::get_channel_title( $api_data, $youtube_post_meta );
$likes_count         = SBY_Display_Elements_Pro::escaped_formatted_count_string( SBY_Parse_Pro::get_like_count( $api_data, $youtube_post_meta ), 'likes' );
$comments_count      = SBY_Display_Elements_Pro::escaped_formatted_count_string( SBY_Parse_Pro::get_comment_count( $api_data, $youtube_post_meta ), 'comments' );
$views_count_string      = SBY_Display_Elements_Pro::escaped_formatted_count_string( SBY_Parse_Pro::get_view_count( $api_data, $youtube_post_meta ), 'views' );
$flag_missing_counts = (SBY_Parse_Pro::get_view_count( $api_data, $youtube_post_meta ) === '') ? ',singleCheckPosts' : '';

$live_broadcast_type = SBY_Parse_Pro::get_live_broadcast_content( $api_data ); // 'none', 'upcoming', 'live', 'completed'
$live_streaming_timestamp = SBY_Parse_Pro::get_live_streaming_timestamp( $api_data, $youtube_post_meta );
$live_streaming_time_string = SBY_Display_Elements_Pro::escaped_live_streaming_time_string( $api_data, $youtube_post_meta );
$formatted_date_string      = $live_broadcast_type === 'none' ? SBY_Display_Elements_Pro::format_date( $timestamp, $settings ) : SBY_Display_Elements_Pro::format_date( $live_streaming_timestamp, $settings, true );

// Pro Styles
$link_styles                = SBY_Display_Elements_Pro::get_sby_link_styles( $settings ); // style="background: rgba(30,115,190,0.85)" already escaped
$hover_styles               = SBY_Display_Elements_Pro::get_hover_styles( $settings ); // style="color: rgba(153,231,255,1)" already escaped
$sby_info_styles            = SBY_Display_Elements_Pro::get_sby_info_styles( $settings ); // style="font-size: 13px;" already escaped
$sby_meta_color_styles      = SBY_Display_Elements_Pro::get_sby_meta_color_styles( $settings ); // style="font-size: 13px;" already escaped
$sby_meta_size_color_styles = SBY_Display_Elements_Pro::get_sby_meta_size_color_styles( $settings ); // style="font-size: 13px;color: rgba(153,231,255,1)" already escaped
?>
<div id="sb_youtube_<?php echo esc_attr( preg_replace( "/[^A-Za-z0-9 ]/", '', $post_id ) ); ?>" class="sb_youtube sby_layout_gallery sby_col_1 sby_mob_col_1 sby_youtube_feed_single" data-shortcode-atts="<?php echo esc_attr( $shortcode_atts ); ?>" data-cols="1" data-colsmobile="1" data-num="1" data-sby-flags="resizeDisable<?php echo esc_attr( $flag_missing_counts ); ?>"<?php echo $other_atts; ?>>
	<div id="sby_player_<?php echo esc_attr( $post_id ); ?>" class="sby_player_outer_wrap sby_player_item">
		<div class="sby_video_thumbnail_wrap">
			<a class="sby_video_thumbnail sby_player_video_thumbnail" href="<?php echo esc_url( $permalink ); ?>" target="_blank" rel="noopener" data-title="<?php echo sby_esc_html_with_br( $caption ); ?>" data-full-res="<?php echo esc_url( $media_full_res ); ?>" data-img-src-set="<?php echo esc_attr( wp_json_encode( $media_all_sizes_json ) ); ?>" data-video-id="<?php echo esc_attr( $video_id ); ?>">
				<span class="sby-screenreader"><?php echo sprintf( __( 'YouTube Video %s', 'feeds-for-youtube' ), $post_id ); ?></span>
				<img src="<?php echo esc_url( $media_url ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>">
				<span class="sby_loader sby_hidden" style="background-color: rgb(255, 255, 255);"></span>
			</a>
			<div class="sby_player_wrap">
				<div class="sby_player"></div>
			</div>
			<?php include sby_get_feed_template_part( 'cta', $settings ); ?>
		</div>

		<?php
		$context = 'single';
		include sby_get_feed_template_part( 'info', $settings ); ?>
    </div>
</div>