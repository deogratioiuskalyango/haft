<?php

use SmashBalloon\YouTubeFeed\Pro\SBY_Display_Elements_Pro;
use SmashBalloon\YouTubeFeed\Pro\SBY_Parse_Pro;
use SmashBalloon\YouTubeFeed\SBY_Display_Elements;
use SmashBalloon\YouTubeFeed\SBY_Parse;

$post_id                      = SBY_Parse::get_post_id( $placeholder_post );
$timestamp                    = SBY_Parse::get_timestamp( $placeholder_post );
$video_id                     = SBY_Parse::get_video_id( $placeholder_post );
$protocol                     = is_ssl() ? 'https' : 'http';
$media_url                    = SBY_Display_Elements::get_optimum_media_url( $placeholder_post, $settings );
$media_full_res               = SBY_Parse::get_media_url( $placeholder_post );
$media_all_sizes_json         = SBY_Parse::get_media_src_set( $placeholder_post );
$permalink                    = SBY_Parse::get_permalink( $placeholder_post );
$img_alt                      = SBY_Parse::get_caption( $placeholder_post, __( 'Image for post', 'feeds-for-youtube' ) . ' ' . $post_id );
$player_outer_wrap_style_attr = SBY_Display_Elements::get_style_att( 'player_outer_wrap', $settings );
$title                        = SBY_Parse::get_video_title( $placeholder_post );
$media_url                    = SBY_Display_Elements::get_media_url( $settings, $media_url, $media_full_res );

// Pro Elements
$caption             = SBY_Parse::get_pro_caption( $placeholder_post, '', $misc_data );
$avatar              = SBY_Parse::get_item_avatar( $placeholder_post, $settings['feed_avatars'] );

$username            = SBY_Parse::get_channel_title( $placeholder_post, $misc_data );
$likes_count         = SBY_Display_Elements::escaped_formatted_count_string( SBY_Parse::get_like_count( $placeholder_post, $misc_data ), 'likes' );
$comments_count      = SBY_Display_Elements::escaped_formatted_count_string( SBY_Parse::get_comment_count( $placeholder_post, $misc_data ), 'comments' );
$views_count_string      = SBY_Display_Elements::escaped_formatted_count_string( SBY_Parse::get_view_count( $placeholder_post, $misc_data ), 'views' );

$live_broadcast_type = SBY_Parse::get_live_broadcast_content( $placeholder_post ); // 'none', 'upcoming', 'live', 'completed'
$live_streaming_timestamp = SBY_Parse::get_live_streaming_timestamp( $placeholder_post, $misc_data );
$live_streaming_time_string = SBY_Display_Elements::escaped_live_streaming_time_string( $placeholder_post, $misc_data );
$formatted_date_string      = $live_broadcast_type === 'none' ? SBY_Display_Elements::format_date( $timestamp, $settings ) : SBY_Display_Elements::format_date( $live_streaming_timestamp, $settings, true );

$sby_link_classes    = SBY_Display_Elements::get_sby_link_classes( $settings ); // // ' sby_disable_lightbox'

$player_attributes = SBY_Display_Elements::get_player_attributes( $settings );

// Pro Styles
$link_styles                = SBY_Display_Elements::get_sby_link_styles( $settings ); // style="background: rgba(30,115,190,0.85)" already escaped
$hover_styles               = SBY_Display_Elements::get_hover_styles( $settings ); // style="color: rgba(153,231,255,1)" already escaped
$sby_info_styles            = SBY_Display_Elements::get_sby_info_styles( $settings ); // style="font-size: 13px;" already escaped
$sby_meta_color_styles      = SBY_Display_Elements::get_sby_meta_color_styles( $settings ); // style="font-size: 13px;" already escaped
$sby_meta_size_color_styles = SBY_Display_Elements::get_sby_meta_size_color_styles( $settings ); // style="font-size: 13px;color: rgba(153,231,255,1)" already escaped?>
<div id="sby_player_<?php echo esc_attr( $post_id ); ?>" class="sby_player_outer_wrap sby_player_item" <?php echo $player_outer_wrap_style_attr; echo $player_attributes; ?> >
    <div class="sby_video_thumbnail_wrap">
        <a class="sby_video_thumbnail sby_player_video_thumbnail" href="<?php echo esc_url( $permalink ); ?>" target="_blank" rel="noopener" data-full-res="<?php echo esc_url( $media_full_res ); ?>" data-img-src-set="<?php echo esc_attr( wp_json_encode( $media_all_sizes_json ) ); ?>" data-video-id="<?php echo esc_attr( $video_id ); ?>">
            <span class="sby-screenreader"><?php echo sprintf( __( 'YouTube Video %s', 'feeds-for-youtube' ), $post_id ); ?></span>
            <img src="<?php echo esc_url( $media_url ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>">
            <span class="sby_loader sby_hidden" style="background-color: rgb(255, 255, 255);"></span>
        </a>
        <?php if ( SBY_Display_Elements::should_show_element( 'icon', 'player', $settings ) ) : ?>
            <div class="sby_play_btn">
            <?php echo SBY_Display_Elements::get_icon( 'play', 'svg' ); ?>
                <span class="sby_play_btn_bg"></span>
            </div>
        <?php endif; ?>
        <div class="sby_player_wrap">
            <div class="sby_player"></div>
        </div>
	    <?php include sby_get_feed_template_part( 'cta', $settings ); ?>
    </div>

    <?php
	    $context = 'player';
	    include sby_get_feed_template_part( 'player-info', $settings ); 
    ?>
</div>
