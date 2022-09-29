# CTA Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 1.4.2- 2022-09-29
### Added
- Added SiteMenu to element modal, to enable linking across sites


## 1.4.1- 2021-03-25
### Added
- Config is can now be localized per site ([#5](https://github.com/statikbe/craft3-ctafield/issues/5))


## 1.4.0 - 2020-12-07
### Added
- CTA's can now be rendered as a span ([#2](https://github.com/statikbe/craft3-ctafield/issues/2))
- Better placeholder for url and e-mail fields ([#3](https://github.com/statikbe/craft3-ctafield/issues/3))
- CTA's that use a URL and open in a new window now get the rel='noopener' attribute ([#4](https://github.com/statikbe/craft3-ctafield/issues/4))

## 1.3.1 - 2018-10-26
### Added
- Added a missing use statement


## 1.3.0 - 2018-10-26
### Added
- Added `htmlLink` function to CTA model to make upgrades easier

## 1.2.0 - 2018-10-26
### Added
- Added the option to hide the classes dropdown per CTA field.

### Improved
- Moved the default classes definitions to their own function

## 1.1.0 - 2018-09-20
### Added
- Link classes can now also be defined in cta.php

## 1.0.2 - 2018-08-21
### Fixed
- Fixed an issue where the field was required and couldn't be save even when entered correctly

## 1.0.1 - 2018-08-17
### Fixed
- Fixed an issue where the class defined in settings was not being applied

## 1.0.0 - 2018-07-26
### Added
- Initial release
