# Changelog
All notable changes to this project will be documented in this file, formatted via [this recommendation](https://keepachangelog.com/).

## [1.9.0] - 2024-02-22
### Added
- Compatibility with the WPForms 1.8.7.

### Changed
- The minimum WPForms version supported is 1.8.7.

### Fixed
- The Form Builder settings screen had visual issues when RTL language was used.
- List indicators in Form Page message were placed outside the paragraph pane.

## [1.8.0] - 2024-01-09
### IMPORTANT
- Support for PHP 5.6 has been discontinued. If you are running PHP 5.6, you MUST upgrade PHP before installing WPForms Form Pages 1.8.0. Failure to do that will disable WPForms Form Pages functionality.
- Support for WordPress 5.4 and below has been discontinued. If you are running any of those outdated versions, you MUST upgrade WordPress before installing WPForms Form Pages 1.8.0. Failure to do that will disable WPForms Form Pages functionality.

### Changed
- Minimum WPForms version supported is 1.8.6.

### Fixed
- The Submit button had incorrect styling on Form Pages when the Classic markup was set up.
- Incorrect error text was displayed when uploading a file of an illegal format in the Form Builder.

## [1.7.0] - 2023-08-15
### Changed
- Minimum WPForms version supported is 1.8.3.

### Fixed
- There was an incorrect link style on the form page in footer.
- Addon generated deprecation notices on the frontend when running on PHP 8.2.

## [1.6.0] - 2023-03-09
### Added
- Compatibility with the upcoming WPForms v1.8.1 release.

### Fixed
- Head Logo preview was not displayed in the Form Builder if the form contained any field with Image Choices turned on.

## [1.5.1] - 2022-08-31
### Fixed
- Form page permalink was saved incorrectly when a custom menu link with the same permalink existed.
- Incorrect information was displayed in form pages social previews.
- Text in Paragraph Text field turned red when a custom error message was used.
- `{page_title}` smart tag used form title instead of the title set in the addon.

## [1.5.0] - 2022-05-26
### IMPORTANT
- Support for PHP 5.5 has been discontinued. If you are running PHP 5.5, you MUST upgrade PHP before installing the new WPForms Form Pages. Failure to do that will disable the WPForms Form Pages plugin.

### Added
- Compatibility with WPForms 1.6.8 and the updated Form Builder.
- New filter `wpforms_form_pages_frontend_handle_request_form_data` that can be used to improve multi-language support.

### Changed
- Refine the way a form page title gets processed as a smart tag.
- Styles for basic HTML tag (lists, blockquote, link, etc.).

### Fixed
- Visibility issue with Google v2 reCAPTCHA.
- Incorrect canonical and `og:url` page meta values produced by the Yoast SEO plugin.
- Users with editor permissions were unable to save Form Pages slugs.
- Error message color for modern file uploader.
- Compatibility with Rich Text field.
- Color Scheme setting value was gone from the color picker's input after the Form Builder page refresh.

## [1.4.1] - 2020-11-16
### Fixed
- Display a form name instead of a form page title when using `{form_name}` smart tag inside the Message section.

## [1.4.0] - 2020-08-05
### Added
- Show a notice if permalinks are not configured.

### Changed
- Page Title tag and meta tags should always use Form Pages Title if set.
- oEmbed links are removed from the Form Page HTML.

## [1.3.1] - 2020-02-17
### Fixed
- Datepicker is not working for "Date/Time" field.

## [1.3.0] - 2020-01-09
### Added
- Meta tag 'robots' with filterable 'noindex,nofollow' value for all Form Pages.

### Fixed
- Popup Maker popup content displays in Form Pages.

## [1.2.1] - 2019-11-07
### Fixed
- "error404" body class may appear if the custom permalink structure is used.
- Form preview buttons open two tabs in Edge browser.
- "Cannot read property 'addEventListener' of null" JS error on form creation.

## [1.2.0] - 2019-10-14
### Added
- Hexcode option to color picker.

### Fixed
- Multiline form description is displayed as a single line on a frontend.

## [1.1.0] - 2019-07-23
### Added
- Complete translations for Spanish, Italian, Japanese, German, French and Portuguese (Brazilian).

### Fixed
- Some themes override Form Page's templates.

## [1.0.3]
### Fixed:
- Page title mismatching the Form Page title.

## [1.0.2] - 2019-02-21
### Added:
- Compatibility with Conversational Forms addon.

### Fixed
- Typos, grammar, and other i18n related issues.
- CSS enqueues filtering includes parents of the child themes.

## [1.0.1] - 2019-02-06
### Fixed:
- Missing `title` tag.
- Compatibility issue with Yoast SEO.

## [1.0.0] - 2019-01-22
### Added
- Initial release.
