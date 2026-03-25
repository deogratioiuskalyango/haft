/* global wpforms_admin_builder_conversational_forms, WPFormsBuilder, wpforms_builder, wpf */

/**
 * WPForms Builder Conversational Forms function.
 *
 * @since 1.0.0
 * @package
 */
const WPFormsBuilderConversationalForms = window.WPFormsBuilderConversationalForms || ( function( document, window, $ ) {
	/**
	 * Elements.
	 *
	 * @since 1.0.0
	 *
	 * @type {Object}
	 */
	const $el = {
		toggleSettingsCheckbox: $( '#wpforms-panel-field-settings-conversational_forms_enable' ),
		previewFormBtn        : $( '#wpforms-conversational-forms-preview-conversational-form' ),
		slug                  : {
			textField: $( '#wpforms-panel-field-settings-conversational_forms_page_slug' ),
			editBtn  : $( '.wpforms-conversational-forms-page-slug-edit' ),
			viewBtn  : $( '.wpforms-conversational-forms-page-slug-view' ),
			cancelBtn: $( '.wpforms-conversational-forms-page-slug-cancel' ),
		},
		logo                  : {
			previewContainer: $( '.wpforms-conversational-forms-custom-logo-container' ),
			textField       : $( '#wpforms-panel-field-settings-conversational_forms_custom_logo' ),
			addBtn          : $( '.wpforms-conversational-forms-custom-logo-upload' ),
			deleleBtn       : $( '.wpforms-conversational-forms-custom-logo-delete' ),
		},
	};

	/**
	 * Public functions and properties.
	 *
	 * @since 1.0.0
	 *
	 * @type {Object}
	 */
	var app = {

		/**
		 * All settings / constants.
		 *
		 * @since 1.3.1
		 */
		settings: {
			minicolorsChangeDelay: 750,
			minicolorsInputEventRunLength: 6,
		},

		/**
		 * Start the engine.
		 *
		 * @since 1.0.0
		 */
		init() {
			$( app.ready );
			$( window ).on( 'load', app.load );
		},

		/**
		 * Document ready.
		 *
		 * @since 1.0.0
		 */
		ready() {
			app.conditionals();
			app.events();
			app.actions();
		},

		/**
		 * Page load.
		 *
		 * @since 1.6.0
		 */
		load() {
			app.toggleFieldsOptions();
		},

		/**
		 * Register and load conditionals.
		 *
		 * @since 1.0.0
		 */
		conditionals() {
			if ( typeof $.fn.conditions === 'undefined' ) {
				return;
			}

			$el.toggleSettingsCheckbox.conditions( {
				conditions: {
					element : '#wpforms-panel-field-settings-conversational_forms_enable',
					type    : 'checked',
					operator: 'is',
				},
				actions   : {
					if  : {
						element: '#wpforms-conversational-forms-content-block,#wpforms-conversational-forms-preview-conversational-form',
						action : 'show',
					},
					else: {
						element: '#wpforms-conversational-forms-content-block,#wpforms-conversational-forms-preview-conversational-form',
						action : 'hide',
					},
				},
				effect    : 'appear',
			} );
		},

		/**
		 * Register JS events.
		 *
		 * @since 1.0.0
		 */
		events() {
			app.generalEvents();
			app.colorPickerEvents();
			app.customLogoEvents();
			app.formSlugEvents();
		},

		/**
		 * Run actions.
		 *
		 * @since 1.0.0
		 */
		actions() {
			app.prefillPageTitle();
		},

		/**
		 * Register general events.
		 *
		 * @since 1.0.0
		 */
		generalEvents() {
			$el.toggleSettingsCheckbox.click( function( e ) {
				app.toggleSettingsPanel( e );
			} );

			$( '#wpforms-builder' ).on( 'wpformsFieldAdd', function() {
				app.toggleFieldsOptions();
			} );
		},

		/**
		 * Register colorpicker related events.
		 *
		 * @since 1.0.0
		 */
		colorPickerEvents() {
			$.minicolors.defaults.changeDelay = app.settings.minicolorsChangeDelay;
			$( '#wpforms-panel-field-settings-conversational_forms_color_scheme-7' ).minicolors( {
				show() {
					// Once enabled, colorpicker checks a radio button it's attached to.
					$( this ).prop( 'checked', true );
				},
				change( value ) {
					if ( value ) {
						$( '#wpforms-panel-field-settings-conversational_forms_color-input' ).val( value );
					}
				},
			} );

			$( '#wpforms-panel-field-settings-conversational_forms_color_scheme-wrap .minicolors-panel' ).append( '<input type="text" id="wpforms-panel-field-settings-conversational_forms_color-input" class="minicolors-input-inner">' );

			if ( wpforms_admin_builder_conversational_forms.settings.custom_color !== undefined ) {
				$( '#wpforms-panel-field-settings-conversational_forms_color-input' ).val( wpforms_admin_builder_conversational_forms.settings.custom_color );
			}

			$( document ).on(
				'input',
				'#wpforms-panel-field-settings-conversational_forms_color-input',
				function( event ) {
					if ( event.target.value.length >= app.settings.minicolorsInputEventRunLength ) {
						$( '#wpforms-panel-field-settings-conversational_forms_color_scheme-7' ).minicolors( 'value', event.target.value );
					}
				}
			);
		},

		/**
		 * Register custom logo related events.
		 *
		 * @since 1.0.0
		 */
		customLogoEvents() {
			$el.logo.addBtn.click( function( e ) {
				e.preventDefault();
				app.openMediaFrame();
			} );

			$el.logo.deleleBtn.click( function( e ) {
				e.preventDefault();
				app.deleteCustomLogo();
			} );
		},

		/**
		 * Register form slug related events.
		 *
		 * @since 1.0.0
		 */
		formSlugEvents() {
			$el.previewFormBtn.click( function( e ) {
				app.previewForm( e );
			} );

			$el.slug.viewBtn.click( function( e ) {
				app.previewForm( e );
			} );

			$( '#wpforms-builder' ).on( 'wpformsSaved', function( e, data ) {
				app.updateFormSlugUI( data );
			} );
		},

		/**
		 * Conditionally prevent showing the settings panel.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} e Click event.
		 */
		toggleSettingsPanel( e ) {
			app.toggleFieldsOptions();

			if ( ! $el.toggleSettingsCheckbox.is( ':checked' ) ) {
				return;
			}

			let alertMessage = '';

			// Whether the Form Pages is enabled.
			if ( $( '#wpforms-panel-field-settings-form_pages_enable' ).is( ':checked' ) ) {
				alertMessage = wpforms_admin_builder_conversational_forms.i18n.enable_prevent_modal;
			}

			// Whether the Layout field is used.
			if ( $( '#wpforms-field-options .wpforms-field-option-layout' ).length ) {
				alertMessage = wpforms_admin_builder_conversational_forms.i18n.layout_field_alert_text;
			}

			if ( alertMessage === '' ) {
				return;
			}

			e.preventDefault();

			$.confirm( {
				title: wpforms_builder.heads_up,
				content: alertMessage,
				icon: 'fa fa-exclamation-circle',
				type: 'orange',
				buttons: {
					confirm: {
						text: wpforms_builder.ok,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
					},
				},
			} );
		},

		/**
		 * Toggle particular options of some field types.
		 *
		 * @since 1.6.0
		 */
		toggleFieldsOptions() {
			const enabled = $el.toggleSettingsCheckbox.is( ':checked' );

			const selectors = [
					'.wpforms-field-option-row-date_limit_days',
					'.wpforms-field-option-row-date_limit_days_options',
					'.wpforms-field-option-row-date_disable_past_dates',
					'.wpforms-field-option-row-time_limit_hours',
					'.wpforms-field-option-row-time_limit_hours_options',
				],
				selector = selectors.join( ', ' );

			if ( enabled ) {
				$( selector ).addClass( 'wpforms-hidden-strict' );
			} else {
				$( selector ).removeClass( 'wpforms-hidden-strict' );
				WPFormsBuilder.toggleAllOptionGroups();
			}
		},

		/**
		 * Preview the form after saving it.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} e Click event.
		 */
		previewForm( e ) {
			e.preventDefault();

			if ( WPFormsBuilder.formIsSaved() ) {
				window.open( e.target.href, '_blank' );
				return;
			}

			const formPage = window.open( '', '_blank' );

			WPFormsBuilder.formSave().done( function() {
				// The location trick is needed to avoid browser popup blocking.
				formPage.location = e.target.href;
			} );
		},

		/**
		 * Init new wp.media frame.
		 *
		 * @since 1.0.0
		 *
		 * @return {wp.media.view.MediaFrame} Media selection frame.
		 */
		initMediaFrame() {
			const mediaFrame = wpf.initMediaLibrary( {
				title: wpforms_admin_builder_conversational_forms.i18n.logo_selection_frame_title,
				extensions: wpforms_builder.upload_image_extensions,
				extensionsError: wpforms_builder.upload_image_extensions_error,
				buttonText: wpforms_admin_builder_conversational_forms.i18n.logo_selection_frame_button_text,
			} );

			mediaFrame.on( 'select', function() {
				app.selectCustomLogo( mediaFrame );
			} ).on( 'close', function() {
				mediaFrame.off( 'library:selection:add' );
			} );

			return mediaFrame;
		},

		/**
		 * Open media selection frame.
		 *
		 * @since 1.0.0
		 */
		openMediaFrame() {
			const mediaFrame = app.initMediaFrame();

			mediaFrame.open();
		},

		/**
		 * Select an item inside a media frame.
		 *
		 * @since 1.0.0
		 *
		 * @param {wp.media.view.MediaFrame} mediaFrame Media selection frame.
		 */
		selectCustomLogo( mediaFrame ) {
			const attachment = mediaFrame.state().get( 'selection' ).first().toJSON();
			let url = attachment.url;

			if ( typeof attachment.sizes.medium !== 'undefined' ) {
				url = attachment.sizes.medium.url;
			}

			$el.logo.deleleBtn.find( 'img' ).remove();
			$el.logo.deleleBtn.append( '<img src="' + url + '" alt="' + wpforms_admin_builder_conversational_forms.i18n.logo_preview_alt + '"/>' );
			$el.logo.previewContainer.show();
			$el.logo.textField.val( attachment.id );
			$el.logo.deleleBtn.show();
		},

		/**
		 * Delete custom form logo.
		 *
		 * @since 1.0.0
		 */
		deleteCustomLogo() {
			$el.logo.previewContainer.find( 'img' ).remove();
			$el.logo.addBtn.show();
			$el.logo.previewContainer.hide();
			$el.logo.textField.val( '' );
		},

		/**
		 * Update form slug field and links.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} data Form save response data.
		 */
		updateFormSlugUI( data ) {
			if ( typeof data.conversational_forms === 'undefined' ) {
				return;
			}

			$el.slug.textField.val( data.conversational_forms.slug );
			$el.slug.viewBtn.prop( 'href', data.conversational_forms.url );
			$el.previewFormBtn.prop( 'href', data.conversational_forms.url );
		},

		/**
		 * Prefill page title before new form is saved.
		 *
		 * @since 1.0.0
		 */
		prefillPageTitle() {
			if ( ! wpf.getQueryString( 'newform' ) ) {
				return;
			}

			const $formTitle = $( '#wpforms-panel-field-settings-conversational_forms_title' );

			if ( ! $formTitle.val() ) {
				$formTitle.val( $( '#wpforms-panel-field-settings-form_title' ).val() );
			}
		},
	};

	// Provide access to public functions/properties.
	return app;
}( document, window, jQuery ) );

// Initialize.
WPFormsBuilderConversationalForms.init();
