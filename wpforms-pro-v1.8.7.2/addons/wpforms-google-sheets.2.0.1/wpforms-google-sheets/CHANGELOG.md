# Changelog
All notable changes to this project will be documented in this file, formatted via [this recommendation](https://keepachangelog.com/).

## [2.0.1] - 2024-01-12
### IMPORTANT
- All users who are sending entries to Google Sheets will need to re-authenticate their Google connection once version 2.0.1 is installed to avoid interruptions in service.

### Changed
- Lowered minimum WPForms version supported to 1.8.3.
- Lowered minimum PHP version supported to 5.6.
- Lowered minimum WordPress version supported to 5.2.

## [2.0.0] - 2024-01-11
### IMPORTANT
- All users who are sending entries to Google Sheets will need to re-authenticate their Google connection once version 2.0.0 is installed to avoid interruptions in service.
- Support for PHP 5.6 has been discontinued. If you are running PHP 5.6, you MUST upgrade PHP before installing WPForms Google Sheets 2.0.0. Failure to do that will disable WPForms Google Sheets functionality.
- Support for WordPress 5.4 and below has been discontinued. If you are running any of those outdated versions, you MUST upgrade WordPress before installing WPForms Google Sheets 2.0.0. Failure to do that will disable WPForms Google Sheets functionality.

### Changed
- Minimum WPForms version supported is 1.8.4.

## [1.1.0] - 2023-04-20
### Added
- Support for form field smart tags in field mapping of custom values.

### Fixed
- Attempt to activate the addon with WPForms version prior to 1.7.3 resulted in a fatal error.
- Field Mapping layout had incorrect subfield widths in Safari.

## [1.0.0] - 2022-10-25
- Initial release.
