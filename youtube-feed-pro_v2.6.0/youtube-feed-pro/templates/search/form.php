<?php
$search_videos_text = __( 'Search Videos', 'feeds-for-youtube' );
$search_placeholder_text = __( 'Search...', 'feeds-for-youtube' );
$submit_text = __( 'Submit', 'feeds-for-youtube' );
?>

<div id="sby_search" class="sby_search">
	<div class="sby_sf_label">
		<label for="sby_sf_input"><?php echo esc_html( $search_videos_text ); ?></label>
	</div>
	<div class="sby_sf_input">
        <form role="search" action="" method="get">
            <input id="sby_sf_input" type="search" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $search_term ); ?>" placeholder="<?php echo esc_attr( $search_placeholder_text ); ?>">
            <button type="submit"><?php echo esc_html( $submit_text ); ?></button>
        </form>
	</div>
	<?php
	include sby_get_feed_template_part( 'results', $sby_settings );
	?>
</div>
