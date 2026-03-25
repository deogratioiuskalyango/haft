<?php
$post_thumbnail          = SBY_Parse::get_media_url( $api_data, 'high' );
$title = get_the_title( $youtube_post->ID );
$permalink = get_the_permalink( $youtube_post->ID );
$img_alt                 = sby_maybe_shorten_text( SBY_Parse::get_caption( $api_data, __( 'Image for post', 'feeds-for-youtube' ) . ' ' . $youtube_post->ID ), array() );
$caption             = sby_maybe_shorten_text( SBY_Parse_Pro::get_caption( $api_data, '' ), array() );

$timestamp = SBY_Parse::get_timestamp( $api_data );
$live_broadcast_type = SBY_Parse_Pro::get_live_broadcast_content( $api_data ); // 'none', 'upcoming', 'live', 'completed'
$live_streaming_timestamp = SBY_Parse_Pro::get_live_streaming_timestamp( $api_data, $youtube_post_meta );
$live_streaming_time_string = SBY_Display_Elements_Pro::escaped_live_streaming_time_string( $api_data, $youtube_post_meta );
$formatted_date_string      = $live_broadcast_type === 'none' ? SBY_Display_Elements_Pro::format_date( $timestamp, $settings ) : SBY_Display_Elements_Pro::format_date( $live_streaming_timestamp, $settings, true );
?>

<div class="sby_sf_result">
	<div class="sby_sf_thumb_wrap">
		<a href="<?php echo $permalink; ?>">
			<img src="<?php echo esc_url( $post_thumbnail ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>">
		</a>
	</div>
	<div class="sby_sf_info_wrap">
		<a href="<?php echo $permalink; ?>">
			<h3><?php echo esc_html( $title ); ?></h3>
		</a>
		<p class="sby_sf_dates">
			<?php if ( $live_broadcast_type !== 'none' ) : ?>
				<span class="sby_ls_message_wrap"><span class="sby_ls_message"><?php echo $live_streaming_time_string; ?></span></span>
			<?php endif; ?>
			<span class="sby_date sby_live_broadcast_type_<?php echo esc_attr( $live_broadcast_type ); ?>"><?php echo $formatted_date_string; ?></span>
		</p>
		<p>
			<?php echo $caption; ?>
		</p>
	</div>
</div>
