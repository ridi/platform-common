# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.10] - 2017-11-22
### Changed
- Change connection group name to `\Gnf\Db\Base`
- Add composer dependency - `ridibooks/platform-gnfdb`

### Fixed
- Missing return type in `CommonModuleBaseService` class

## [0.1.9] - 2017-11-22
### Changed
- Change `CommonModuleBaseService` construct visibility - protected -> public

### Fixed
- Fix `CommonModuleBaseService` construct method typo

## [0.1.8] - 2017-11-21
### Added
- Added cron master util
- Added PlatformConnectionGroup, CommonModuleBaseService class
- Added method - replace non-breaking space to space
