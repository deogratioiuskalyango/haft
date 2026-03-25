<?php

namespace WPFormsFormPages;

use WP_Post;

/**
 * Smart tags functionality.
 *
 * @since 1.5.0
 */
class SmartTags {

	/**
	 * Initialize.
	 *
	 * @since 1.5.0
	 */
	public function init() {

		$this->hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.5.0
	 */
	private function hooks() {

		add_filter( 'wpforms_smarttags_process_form_name_value', [ $this, 'form_name_smart_tag_value' ], 10, 2 );
		add_filter( 'wpforms_smarttags_process_page_title_value', [ $this, 'filter_page_title_smart_tag_value' ], 10, 2 );
	}

	/**
	 * Determine whether it is a WPForms Form Page.
	 *
	 * @since 1.5.1
	 *
	 * @param WP_Post $form Form post.
	 *
	 * @return bool
	 */
	private function is_form_page( $form ) {

		if ( ! isset( $form->post_type ) || $form->post_type !== 'wpforms' ) {
			return false;
		}

		$form_data = wpforms_decode( $form->post_content );

		return ! empty( $form_data['settings']['form_pages_enable'] );
	}

	/**
	 * The Form Pages addon rewrites the form_title setting for it's internal needs, so we want to first check if
	 * we have a saved title for the form, and if so, we will use that for the form title smart tag.
	 *
	 * @since 1.5.0
	 *
	 * @param string $value     Default value.
	 * @param array  $form_data Form data.
	 *
	 * @return string
	 */
	public function form_name_smart_tag_value( $value, $form_data ) {

		return isset( $form_data['settings']['form_name'] ) && $form_data['settings']['form_name'] !== ''
			? esc_html( wp_strip_all_tags( $form_data['settings']['form_name'] ) )
			: $value;
	}

	/**
	 * Change the `{page_title}` value to Form Page title.
	 *
	 * @since 1.5.1
	 *
	 * @param string $value     The page title.
	 * @param array  $form_data Form data.
	 *
	 * @return string
	 */
	public function filter_page_title_smart_tag_value( $value, $form_data ) {

		global $post;

		if ( ! $this->is_form_page( $post ) || empty( $form_data['settings']['form_pages_title'] ) ) {
			return $value;
		}

		return esc_html( wp_strip_all_tags( $form_data['settings']['form_pages_title'], true ) );
	}
}
