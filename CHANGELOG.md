# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
### Added
* Use semantic version numbers (http://semver.org/)
* New column in user list table that shows subscription status of each user. (Column key: `iac_subscriptions`)
* Add this CHANGELOG.md file
* Add setup for PHPUnit tests and tests for `Iac_Profile_Settings::save_user_settings()`

### Fixed
* Fix logical bug in `Iac_Profile_Settings::get_user_settings()`. Default opt-in setting is now evaluated correctly.
* Fix bug in scheduled mail dispatching
* Fixed bug in notification string in settings section
* Fixed bug in de_DE translation
* Fixed bug in `Iac_Profile_Settings::save_user_settings()`. Default opt-in setting is now evaluated correctly.

## [0.0.7]
### Added
* Send mails in smaller groups and schedule sending of groups
* add new Filter Hooks
    * `iac_get_members`
    * `iac_single_email_address`
    * `iac_email_address_chunk`
    * `iac_mail_to_chunking`
    * `iac_mail_to_chunksize`

## 0.0.6
* Change Hook for send only on published posts
* Change Mail-header for use in all systems
* Codestyling
* API for e-mail signature Hook `iac_signature_separator`; Function `append_signature( $message, $signature = '' )`
* BCC possibility
* Add new filters, like shortocodes
* Add settings to global default options on 'Reading'
* Start to handle medias, attachments
* Change autoloader to also usable without spl
* Change hook to `transition_post_status` to change send mail - now send only on new posts, not update posts
* Update language files

## 0.0.5

* Option to send email by Bcc-header
* API to change the plugins default behaviour (opt-in/opt-out)
* Fix for update on user profile in WP 3.4*

## 0.0.4
* small fix for hook to use static method

## 0.0.3
* first release on wp.org

## 0.0.1
* Release first version

[unreleased]:https://github.com/inpsyde/Inform-about-Content/compare/v0.0.7...master/
[0.0.7]:https://github.com/inpsyde/Inform-about-Content/compare/v0.0.5...v0.0.7