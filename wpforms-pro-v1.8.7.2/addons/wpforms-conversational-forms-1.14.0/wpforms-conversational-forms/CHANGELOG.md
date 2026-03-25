# Changelog
All notable changes to this project will be documented in this file, formatted via [this recommendation](https://keepachangelog.com/).

## [1.14.0] - 2024-02-20
### Added
- Compatibility with the upcoming WPForms 1.8.7.

### Changed
- The minimum WPForms version supported is 1.8.7.

### Fixed
- The Form Builder settings screen had visual issues when an RTL language was used.

## [1.13.0] - 2024-01-09
### IMPORTANT
- Support for PHP 5.6 has been discontinued. If you are running PHP 5.6, you MUST upgrade PHP before installing WPForms Conversational Forms 1.13.0. Failure to do that will disable WPForms Conversational Forms functionality.
- Support for WordPress 5.4 and below has been discontinued. If you are running any of those outdated versions, you MUST upgrade WordPress before installing WPForms Conversational Forms 1.13.0. Failure to do that will disable WPForms Conversational Forms functionality.

### Changed
- Minimum WPForms version supported is 1.8.6.

### Fixed
- Compatibility with the Popup Maker plugin.
- Compatibility with Link by Stripe.
- Improved Rich Text, File Upload and Number fields compatibility with color themes and dark mode.
- In rare cases Turnstile Captcha was not displayed correctly when it expired and was refreshed.
- Incorrect error text was displayed when uploading a file of an illegal format in the Form Builder.

## [1.12.0] - 2023-08-22
### Changed
- Minimum WPForms version supported is 1.8.3.

### Fixed
- Scrolling to the form error message was not working in some cases.
- Some deprecation notices were generated with PHP 8.2.
- Dropdowns on tablets and mobiles had 2 down-arrows.

## [1.11.0] - 2023-06-28
### Added
- Compatibility with the WPForms Coupons addon.

## [1.10.0] - 2023-03-27
### Added
- Compatibility with the upcoming WPForms v1.8.1 release.

### Changed
- The Content field did not have instructions to proceed to the next step.
- Disable automatic scroll to the first field when the form description is larger than the viewport.

### Fixed
- The Header Logo preview was not displayed in the Form Builder if the form contains any field with Image Choices turned on.
- Fields with Icon or Image choices configured did not apply line breaks to manually formatted choice values.
- Appearance of Likert Scale and Net Promoter Score fields was improved in dark themes.

## [1.9.0] - 2023-01-03
### Added
- Compatibility with Icon Choices feature for Checkboxes, Multiple Choice, Checkbox Items and Multiple Items payment fields.

### Fixed
- The AJAX spinner was not centered relative to the Submit button.
- Improved compatibility with the Content field.
- Next field wasn't focused after selecting Likert Scale field option.
- Checkboxes with image labels were displayed enormously big.
- Incorrect positioning if form header contains long-form content.
- Improved handling of line breaks to avoid text overflowing on small screens.

## [1.8.0] - 2022-10-06
### Changed
- Do not allow enabling the Conversational Form mode when a Layout field was added to the list of form fields.

## [1.7.1] - 2022-08-31
### Fixed
- Color Scheme setting value was missing from the color picker's input after the Form Builder page refresh.
- Color picker caused a broken conversational form page.
- Field after radio button or dropdown was not correctly selected.
- Font size for Single Item and Total fields now matches other fields.
- Certain buttons sometimes overlapped the Conversational Forms footer.
- Color schemes compatibility with the Rich Text field was improved.
- `{page_title}` smart tag was getting the incorrect title.
- Incorrect information was displayed in conversational form social previews.

## [1.7.0] - 2022-06-28
### IMPORTANT
- Support for PHP 5.5 has been discontinued. If you are running PHP 5.5, you MUST upgrade PHP before installing the new WPForms Conversational Forms. Failure to do that will disable the WPForms Conversational Forms plugin.

### Added
- New filter `wpforms_conversational_forms_frontend_handle_request_form_data` that can be used to improve multi-language support.

### Changed
- Minimum WPForms version supported is 1.7.5.
- Reorganized locations of 3rd party libraries.
- Date field can be filled in when using the Date Picker with custom date formats.

### Fixed
- Incorrect canonical and og:url page meta values produced by the Yoast SEO plugin.
- Users with editor permissions were unable to save Conversational Forms slugs.
- Improved an error message color for the modern file upload field.
- Missing styles for links added to the Conversational Message.
- Conditional logic was processed incorrectly for the Multiple Dropdown field.
- Correctly display a placeholder for the Modern Dropdown field in the Firefox browser.
- Single Dropdown field didn't work on mobile devices.
- Date/Time field didn't support flatpickr's `range` and `multiple` modes.
- Date/Time field with 24h format for the timepicker wasn't working properly on mobile devices.
- Form couldn't be submitted when a dropdown date option is selected for the required Date/Time field and Conditional Logic applied to the field.
- Opening a mobile device's keyboard for text fields removed focus from the field which was closing keyboard.
- Improved compatibility with Entry Preview and Rich Text fields.
- Signature, Likert Scale, Net Promoter Score fields now take up the full width of the container.

## [1.6.0] - 2021-03-31
### Changed
- Visual difference between radio and checkbox elements of Likert Scale field.
- "Next/Previous" footer buttons are bigger for small screens.
- Radio inputs and select elements look more like traditional HTML elements on mobile.
- Improved styling for Authorize.Net and legacy Stripe CC fields on desktop and mobile.
- Disable autogenerated the `og:description` meta tag in the Rank Math plugin.
- The LikertScale field with a single response per row scrolls to the next row/field on change.
- Radio/Checkbox field items scroll into view while selecting with arrow keys.
- Form Locker UI enhancement when used in conversational mode.

### Fixed
- Compatibility issue with Google v2 reCAPTCHA on certain mobile devices.
- The `nav_menu_item` post type is included in the pool when checking the Conversational Form page slug for uniqueness.
- Textarea and page footer appearance in IE11.
- `blockquote`, `ul`, `ol` elements styling in a form description and a confirmation message.
- Page footer logo appearance in portrait and landscape mobile layouts.
- For the fields without a label, the number indicator is not shown.
- "Hide label" option is not processed for the fields.
- Horizontal line before the Submit button.
- If the checkbox has a label and no options, a long horizontal box appears.
- Number Slider field appearance.
- Smart Phone field does not display a list of countries when clicking the flag.
- TwentySeventeen and TwentyTwenty themes introduce style conflicts with Conversational Form pages.
- Image Choices field scrolling position is set incorrectly in a rare combination of image/screen size and field order.
- Date dropdown field processing issue.
- Focusing and positioning for Stripe CC field.
- Dropdown focusing issue on iPhone X and iPhone SE.
- A conditionally hidden field doesn't get focus if triggered to show by a Dropdown field.
- Field sub-Labels do not hide when enabling the "Hide Sub Label" option in Advanced Field Settings.

## [1.5.0] - 2020-08-05
### Added
- Show a notice if permalinks are not configured.
- Easy access to Conversational Forms classes (e.g. 'wpforms_conversational_forms()->frontend').
- New `wpforms_conversational_form_detected` hook right before Conversational Form hooks are registered.

### Changed
- Page Title tag and meta tags always use Conversational Page Title if set.
- oEmbed links are now removed from Conversational Page HTML.

### Fixed
- Occasionally the form scrolls past or does not activate the conditionally appearing field.

## [1.4.0] - 2020-01-09
### Added
- Meta tag 'robots' with filterable 'noindex,nofollow' value for all Conversational Forms pages.

### Fixed
- Popup Maker popup content displays in Conversational Forms.
- Mobile: Virtual keyboard appearing inconsistently while interacting with the fields which have the sub-fields.

## [1.3.1] - 2019-11-07
### Added
- Basic compatibility with WPForms modern file uploader.

### Fixed
- "error404" body class may appear if the custom permalink structure is used.
- Form preview buttons open two tabs in Edge browser.
- "Cannot read property 'addEventListener' of null" JS error on form creation.

## [1.3.0] - 2019-10-14
### Added
- Hexcode option to color picker.

## [1.2.0] - 2019-07-24
### Added
- "Enter or down arrow to go to the next field" message for HTML, Section Divider, Payment Single and Payment Total blocks.

### Changed
- Dropdown appearance altered to better mimic a traditional <select> element.
- Sublabel placed closer to the input area that it relates to for better visual perception.
- Dropdown chevron icon click and "down arrow" key open a list with all options visible.
- Form scrolls to selected subfields inside multi-input fields for both mobile and desktop.
- Active field is considered completed in footer progress bar calculation.

### Fixed
- Image choices in a Conversational Form shows two-column layout no matter what the Choice Layout selection is.
- Conditional logic not working properly on dropdown fields.
- Conversational forms doesn't accept correct credit card expiration date.
- Inconsistent percentage progress bar behavior (negative values) with conditionally hidden form fields.
- Conversational forms won't submit if the field is required and is hidden by conditional logic.
- Multiline form description is displayed as a single line on a frontend.
- Some themes override Conversational Form's templates.

## [1.1.0] - 2019-02-28
### Added
- Left/Right arrow navigation support for Checkboxes, Radios, Rating, NetPromoter fields.
- Esc for unhighlighting an option previously highlighted by arrow keys in Checkboxes, Radios, Rating, NetPromoter fields.
- Space for selecting (same as Enter) options in Checkboxes, Radios, Rating, NetPromoter fields.
- Shift+Tab to go to a previous option/subfield (same as Up Arrow).

### Changed
- Shift+Enter to go to the next field for Checkboxes (Enter just selects/unselects checkboxes now).
- Dropdowns (desktop version) are not auto opening on focus now.
- More consistent arrow logic for Checkbox and Radio based fields.
- Mobile-native dropdowns are used for mobile devices now.
- Layout is optimized to use screen space more effectively on smaller screens.
- Tweaked virtual keyboard interaction on mobile devices for better mobile UX.
- Mobile Textarea doesn't have "new line" capability now due to mobile UI restrictions.
- Changed tooltip messages in admin area to be more explanatory.
- Changed how mobile/desktop browsers are detected (mobile-detect.js).
- Footer "Up/Down" buttons iterate through subfields on multi-field inputs now instead of instantly skipping to the next field.

### Fixed
- Form's last field (conditionally hidden) was getting focus when trying to go up from "Submit" block.
- Rating had no multi-digit keys support (e.g. impossible to select 10).
- "Active" key navigation star was the same color as the selected one in Rating field.
- Header was overlapping form content on Firefox and Edge browsers.
- Mobile field focusing issues.

## [1.0.0] - 2019-02-21
### Added
- Initial release.
