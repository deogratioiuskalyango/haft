<?php
/**
 * Deprecated functions.
 * This file is used to keep backward compatibility with older versions of the plugin.
 * The functions and classes listed below will be removed in December 2023.
 *
 * @since 1.5.0
 */

/**
 * Load the plugin updater.
 *
 * @since 1.0.0
 * @deprecated 1.5.0
 *
 * @param string $key License key.
 */
function wpforms_post_submissions_updater( $key ) {

	_deprecated_function( __FUNCTION__, '1.5.0 of the WPForms Post Submissions addon' );
}

/***
 * Legacy `WPForms_Post_Submissions` class was moved to the new `WPFormsPostSubmissions\Plugin` class.
 *
 * @since 1.5.0
 */
class_alias( 'WPFormsPostSubmissions\Plugin', 'WPForms_Post_Submissions' );

/***
 * Legacy `WPForms_Template_Post_Submission` class was moved to the new `WPFormsPostSubmissions\Template` class.
 *
 * @since 1.5.0
 */
class_alias( 'WPFormsPostSubmissions\Template', 'WPForms_Template_Post_Submission' );
