<?php

namespace WPFormsFormPages\Admin;

use WP_Post;

/**
 * Form Pages overview functionality.
 *
 * @since 1.0.0
 */
class Overview {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->init();
	}

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public function init() { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		add_filter( 'wpforms_overview_row_actions', [ $this, 'add_row_view_action' ], 10, 2 );
		add_action( 'wpforms_form_handler_duplicate_form', [ $this, 'add_to_location_meta_duplicate' ], 10, 3 );
	}

	/**
	 * Add form page to location meta on duplicate.
	 *
	 * @since        1.9.0
	 *
	 * @param int|mixed $id            The original Form ID.
	 * @param int|mixed $new_form_id   The new/duplicatedForm ID.
	 * @param array     $new_form_data New form data.
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function add_to_location_meta_duplicate( $id, $new_form_id, $new_form_data ) {

		$form_obj = wpforms()->get( 'form' );

		if ( ! $form_obj ) {
			return;
		}

		// Get duplicated form settings.
		$duplicated_form_data = $form_obj->get( $new_form_id, [ 'content_only' => true ] );

		// Generate a unique slug.
		$unique_slug = $duplicated_form_data['settings']['form_pages_page_slug'] . '-' . absint( $new_form_id );

		// Add the unique slug to the duplicated form settings.
		$duplicated_form_data['settings']['form_pages_page_slug'] = $unique_slug;

		// Update duplicated form settings.
		$form_obj->update( $new_form_id, $duplicated_form_data );

		$locator = wpforms()->get( 'locator' );

		if ( ! $locator ) {
			return;
		}

		// Add the duplicated form to the locations' meta.
		$locator->add_standalone_location_to_locations_meta( $new_form_id, $new_form_data );
	}

	/**
	 * Add view row action if Form Page mode is activated.
	 *
	 * @since 1.0.0
	 *
	 * @param array   $row_actions Table row actions.
	 * @param WP_Post $form        Form object.
	 *
	 * @return array
	 */
	public function add_row_view_action( $row_actions, $form ) {

		$form_data = ! empty( $form->post_content ) ? wpforms_decode( $form->post_content ) : [];

		if ( empty( $form_data['settings']['form_pages_enable'] ) ) {
			return $row_actions;
		}

		$action = [
			'view' => sprintf(
				'<a href="%s" title="%s" target="_blank">%s</a>',
				esc_url( home_url( $form->post_name ) ),
				esc_html__( 'View Form Page', 'wpforms-form-pages' ),
				esc_html__( 'Form Page Preview', 'wpforms-form-pages' )
			),
		];

		return wpforms_array_insert( $row_actions, $action, 'preview_' );
	}
}
