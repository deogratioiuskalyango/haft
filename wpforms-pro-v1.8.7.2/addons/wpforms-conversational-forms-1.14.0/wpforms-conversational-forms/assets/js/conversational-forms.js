/* globals MobileDetect */

import WPFormsConversationalFormsModules from './modules/_modules';

/**
 * WPForms Conversational Forms function.
 *
 * @since 1.0.0
 */

const WPFormsConversationalForms = window.WPFormsConversationalForms || ( function( document, window, $ ) {
	let helpers,
		scrollControl,
		eventMapControl,
		mainClasses,
		childClasses,
		globalEvents,
		globalEventsMobile,
		app;

	/**
	 * Conversational forms page container.
	 *
	 * @since 1.13.0
	 *
	 * @type {jQuery}
	 */
	const $page = $( '#wpforms-conversational-form-page' );

	/**
	 * Element aliases.
	 *
	 * @since 1.0.0
	 */
	const elements = {

		page              : $page,
		form              : $page.find( '.wpforms-form' ),
		header            : $( '.wpforms-conversational-form-header' ),
		phpErrorContainer : $page.find( '.wpforms-error-container' ),
		fields            : $page.find( '.wpforms-field-container .wpforms-field' ),
		fieldsErrors      : $page.find( '.wpforms-field-container .wpforms-field .wpforms-error' ),
		recaptchaContainer: $page.find( '.wpforms-recaptcha-container' ),
		footer            : $page.find( '.wpforms-submit-container' ),
		progress          : {
			bar       : $( '.wpforms-conversational-form-footer-progress-completed' ),
			completed : $( '.wpforms-conversational-form-footer-progress-status .completed' ),
			totalCount: $( '.wpforms-conversational-form-footer-progress-status .completed-of' ),
		},
	};

	/**
	 * Public functions and properties.
	 *
	 * @since 1.0.0
	 */
	app = {

		/**
		 * Main point of entry for Fields manipulations.
		 *
		 * @since 1.0.0
		 *
		 * @type {mainClasses.FieldsSet}
		 */
		fields: null,

		/**
		 * Controls both app and user page scrolling.
		 *
		 * @since 1.0.0
		 *
		 * @type {scrollControl}
		 */
		scroll: null,

		/**
		 * Global events.
		 *
		 * @since 1.1.0
		 *
		 * @type {globalEvents|globalEventsMobile}
		 */
		globalEvents: null,

		/**
		 * Mobile detection library instance.
		 *
		 * @since 1.1.0
		 *
		 * @type {MobileDetect}
		 */
		mobileDetect: null,

		/**
		 * Start the engine.
		 *
		 * @since 1.0.0
		 */
		init() {
			app.initObjects();

			if ( typeof MobileDetect !== 'undefined' ) {
				app.mobileDetect = new MobileDetect( window.navigator.userAgent );
			}

			this.globalEvents = app.isMobileDevice() ? globalEventsMobile : globalEvents;

			$( app.ready );
		},

		/**
		 * Init objects.
		 *
		 * @since 1.12.0
		 */
		initObjects() {
			const modules = WPFormsConversationalFormsModules;

			mainClasses = {};
			childClasses = {};
			helpers = modules.helpers();
			scrollControl = modules.scrollControl( $, helpers, app, elements, mainClasses );
			eventMapControl = modules.eventMapControl( $, helpers, app );

			app.initObject( mainClasses, 'mainClasses', '' );
			app.initObject( childClasses, 'childClasses', '' );

			globalEvents = modules.globalEvents( $, helpers, app, elements, mainClasses );
			globalEventsMobile = modules.globalEventsMobile( $, helpers, app, elements, mainClasses, globalEvents );

			app.scroll = scrollControl;
			this.fields = new mainClasses.FieldsSet();

			app.extendClasses();
		},

		/**
		 * Initialize object from imported module.
		 * This function modifies given `obj` implementing the structure
		 * from imported object `WPFormsConversationalFormsModules`.
		 *
		 * @since 1.12.0
		 *
		 * @param {Object} obj        Object that should be initialized.
		 * @param {string} objName    Object name.
		 * @param {string} subObjName Sub-object name.
		 *
		 */
		initObject( obj, objName, subObjName ) {
			obj = obj || {};
			subObjName = subObjName || '';

			const modules = WPFormsConversationalFormsModules,
				classes = subObjName.length === 0 ? modules[ objName ] : modules[ objName ][ subObjName ];

			Object.keys( classes ).forEach( function( cls, i ) {
				if ( $.isFunction( classes[ cls ] ) ) {
					// Run imported function which returns object.
					obj[ cls ] = classes[ cls ]( $, helpers, app, elements, mainClasses, childClasses, eventMapControl );
				} else if ( $.isPlainObject( classes[ cls ] )/* typeof classes[ cls ] === 'object'*/ ) {
					// Recursive call in order to init sub-object.
					obj[ cls ] = obj[ cls ] || {};
					app.initObject( obj[ cls ], objName, cls );
				}
			} );
		},

		/**
		 * Document ready.
		 *
		 * @since 1.0.0
		 */
		ready() {
			app.hidePreloader();

			if ( ! app.readyToStart() ) {
				app.runNotReadyActions();
				return;
			}

			app.setup();
			app.events();
		},

		/**
		 * Check if client device is mobile.
		 *
		 * @since 1.1.0
		 *
		 * @return {boolean} Client device is mobile.
		 */
		isMobileDevice() {
			if ( ! app.mobileDetect ) {
				return false;
			}

			return !! app.mobileDetect.mobile();
		},

		/**
		 * Determine if description size is bigger than viewport.
		 *
		 * @since 1.10.0
		 *
		 * @return {boolean} Description is bigger than viewport.
		 */
		isLongDescription() {
			const $description = elements.header.find( '.wpforms-description' );

			if ( ! $description ) {
				return false;
			}

			return $( window ).height() <= $description.height();
		},

		/**
		 * Extend classes.
		 *
		 * @since 1.0.0
		 */
		extendClasses() {
			$.each( childClasses, function( typeName, type ) {
				$.each( type, function( className, subClass ) {
					helpers.class.extend(
						subClass,
						mainClasses[ helpers.string.toCapitalizedCamelCase( typeName ) ]
					);
				} );
			} );
		},

		/**
		 * Hide form preloader.
		 *
		 * @since 1.0.0
		 */
		hidePreloader() {
			$( 'html' ).removeClass( 'wpforms-conversational-form-loading' );
		},

		/**
		 * Check if form is ready to start.
		 *
		 * @since 1.0.0
		 *
		 * @return {boolean} Form is ready to start.
		 */
		readyToStart() {
			return ! $( '.wpforms-confirmation-container' ).length && ! $( '.wpforms-confirmation-container-full' ).length;
		},

		/**
		 * Run actions if form is not ready to start.
		 *
		 * @since 1.0.0
		 */
		runNotReadyActions() {
			$( '.wpforms-conversational-form-footer-progress-status-proportion' ).hide();
			$( '.wpforms-conversational-form-footer-progress-status-proportion-completed' ).show();

			$( '.wpforms-conversational-form-footer-switch-step' ).hide();
		},

		/**
		 * App setup.
		 *
		 * @since 1.0.0
		 */
		setup() {
			app.addRecaptchaToRegisteredFields();

			app.fields.updateRegistered();

			app.loadValidation();
			app.mapAllGlobalEvents();

			app.runInitialActions();

			app.fields.updateActive();
		},

		/**
		 * App events.
		 *
		 * @since 1.0.0
		 */
		events() {
			$( window ).on( 'scroll', app.scroll.passive );

			$( '.wpforms-conversational-btn-start' ).on( 'click', app.scroll.next );

			$( '.wpforms-conversational-form-footer-switch-step-up' ).on( 'click', app.footerStepUpBtnAction );

			$( '.wpforms-conversational-form-footer-switch-step-down' ).on( 'click', app.footerStepDownBtnAction );

			$( document )
				.on( 'wpformsProcessConditionalsField', app.updateProgressBar )
				.on( 'wpformsAjaxSubmitFailed', app.scrollToFailedField );
		},

		/**
		 * Add Google reCAPTCHA (if enabled) to the form elements.
		 *
		 * @since 1.0.0
		 */
		addRecaptchaToRegisteredFields() {
			if ( ! elements.recaptchaContainer.length ) {
				return;
			}

			const $recaptchaEl = elements.recaptchaContainer.find( '.g-recaptcha' );

			if ( ! $recaptchaEl.length ) {
				return;
			}

			if ( 'invisible' === $recaptchaEl.data( 'size' ) ) {
				return;
			}

			elements.recaptchaContainer
				.attr( 'data-field-type', 'recaptcha' )
				.attr( 'data-field-id', 'g' );

			elements.fields = elements.fields.add( elements.recaptchaContainer );
		},

		/**
		 * Load jQuery Validate custom settings.
		 *
		 * @since 1.0.0
		 */
		loadValidation() {
			if ( typeof $.fn.validate === 'undefined' ) {
				return;
			}

			setTimeout( function() {
				const validator = elements.form.data( 'validator' );

				if ( ! validator ) {
					return;
				}

				$.validator.addMethod( 'wpforms-conversational-forms-date', function( value, element ) {
					return this.optional( element ) || /^\d{1,2}\/\d{2}\/\d{4}$/.test( value );
				}, $.validator.messages.date );

				validator.settings.focusInvalid = false;

				// TODO: Dropdown object needs a method getInput() instead of '.wpforms-conversational-form-dropdown-input input'.
				validator.settings.ignore = ':hidden, .wpforms-conversational-form-dropdown-input input';
				validator.settings.invalidHandler = function( event, validator ) {
					const errors = validator.numberOfInvalids();
					if ( ! errors || ! validator.errorList.length ) {
						return;
					}

					const id = $( validator.errorList[ 0 ].element ).closest( '.wpforms-field' ).data( 'field-id' );
					const type = $( validator.errorList[ 0 ].element ).closest( '.wpforms-field' ).data( 'field-type' );

					// TODO: mainClasses.FieldsSet needs getFieldIdFromElement( $el ) method.
					if ( ( id + '-' + type ) in app.fields.registered ) {
						app.scroll.to( app.fields.registered[ id + '-' + type ] );
					}
				};

				elements.form.on( 'invalid-form.validate', validator.settings.invalidHandler );
			}, 0 );
		},

		/**
		 * Map all (both general and keyboard) global events from globalEvents.
		 *
		 * @since 1.0.0
		 */
		mapAllGlobalEvents() {
			$.each( app.globalEvents.events, function( key ) {
				app.globalEvents.events[ key ].$el
					.on(
						app.globalEvents.events[ key ].handler,
						app.globalEvents.events[ key ].fn
					);
			} );

			$.each( app.globalEvents.keyboard, function( key ) {
				app.globalEvents.keyboard[ key ].$el
					.on(
						app.globalEvents.keyboard[ key ].handler,
						app.globalEvents.keyboard[ key ].fn
					);
			} );
		},

		/**
		 * Unmap all (both general and keyboard) global events from globalEvents.
		 *
		 * @since 1.0.0
		 */
		unmapAllGlobalEvents() {
			$.each( app.globalEvents.events, function( key ) {
				app.globalEvents.events[ key ].$el
					.off(
						app.globalEvents.events[ key ].handler,
						app.globalEvents.events[ key ].fn
					);
			} );

			$.each( app.globalEvents.keyboard, function( key ) {
				app.globalEvents.keyboard[ key ].$el
					.off(
						app.globalEvents.keyboard[ key ].handler,
						app.globalEvents.keyboard[ key ].fn
					);
			} );
		},

		/**
		 * Run initial actions after form setup.
		 *
		 * @since 1.0.0
		 */
		runInitialActions() { // eslint-disable-line complexity
			if ( app.isLongDescription() ) {
				elements.page.addClass( 'wpforms-conversational-form-start' );
			}

			if (
				app.scroll.isTop() &&
				! elements.phpErrorContainer.length &&
				$( '.wpforms-conversational-btn-start' ).length > 0
			) {
				elements.page.addClass( 'wpforms-conversational-form-start' );
			}

			if ( elements.phpErrorContainer.length ) {
				app.scroll.to( elements.phpErrorContainer );
			} else if ( elements.fieldsErrors.length ) {
				app.scroll.to( elements.fieldsErrors );
			}
		},

		/**
		 * Update footer progress bar.
		 *
		 * Detects a type of the bar.
		 *
		 * @since 1.0.0
		 */
		updateProgressBar() {
			if ( elements.progress.totalCount.length ) {
				app.updateProportionProgressBar();
			} else {
				app.updatePercentageProgressBar();
			}
		},

		/**
		 * Scroll to the failed field.
		 *
		 * @since 1.12.0
		 *
		 * @param {Object} event Form submit event.
		 * @param {Object} json  Ajax response data.
		 */
		scrollToFailedField( event, json ) {
			const data = json.data;
			const errors = data && ( 'errors' in data ) ? data.errors : null;

			if ( ! errors || ! errors.field || ! Object.keys( errors.field ).length ) {
				return;
			}

			const $form = $( event.target );

			Object.keys( json.data.errors.field ).forEach( function( field ) {
				const $field = $form.find( '[name="' + field + '"]' );
				const $parent = $field.closest( '.wpforms-field' );

				const fieldId = $parent.data( 'field-id' );
				const fieldType = $parent.data( 'field-type' );

				app.scroll.to( app.fields.registered[ fieldId + '-' + fieldType ] );
			} );
		},

		/**
		 * Update footer progress bar (proportion).
		 *
		 * @since 1.0.0
		 */
		updateProportionProgressBar() {
			const visibleFields = app.fields.getVisible(),
				completedOf = Object.keys( visibleFields ).length,
				completedCount = app.fields.isAtBaseline( elements.footer ) ? completedOf : app.fields.getCompletedCount( visibleFields ),
				progress = app.fields.getCompletedPercent( visibleFields, completedCount );

			elements.progress.bar.width( progress + '%' );
			elements.progress.completed.text( completedCount );
			elements.progress.totalCount.text( completedOf );
		},

		/**
		 * Update footer progress bar (percentage).
		 *
		 * @since 1.0.0
		 */
		updatePercentageProgressBar() {
			const progress = app.fields.getCompletedPercent();

			elements.progress.bar.width( progress + '%' );
			elements.progress.completed.text( progress );
		},

		/**
		 * Callback for footer "Step Up" button.
		 *
		 * @since 1.1.0
		 */
		footerStepUpBtnAction() {
			const elementType = app.fields.callOnActive( 'identifyItemType' );

			// Footer "Up" button skips to previous
			if ( [ 'checkbox', 'radio' ].indexOf( elementType ) !== -1 ) {
				app.scroll.prev();
				return;
			}

			try {
				app.fields.active.items.highlightPrev().fail( app.scroll.prev );
			} catch ( e ) {
				app.scroll.prev();
			}
		},

		/**
		 * Callback for footer "Step Down" button.
		 *
		 * @since 1.1.0
		 */
		footerStepDownBtnAction() {
			const elementType = app.fields.callOnActive( 'identifyItemType' );

			if ( [ 'checkbox', 'radio' ].indexOf( elementType ) !== -1 ) {
				app.scroll.next();
				return;
			}

			try {
				app.fields.active.items.highlightNext().fail( app.scroll.next );
			} catch ( e ) {
				app.scroll.next();
			}
		},
	};

	// Provide access to public functions/properties.
	return app;
}( document, window, jQuery ) );

// Initialize.
WPFormsConversationalForms.init();
