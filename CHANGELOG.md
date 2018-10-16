# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
### ADDED
- add PLATFORM_SLAVE to PlatformConnectionGroup
- add PLATFORM_ONLY_DB_SLAVE to PlatformConnectionGroup
- add PLATFORM_BOOK_DB_SLAVE to PlatformConnectionGroup
- add CP_STATISTICS_SLAVE to PlatformConnectionGroup

## [0.3.0] - 2018-09-17
### CHANGED
- Change composer dependency: platform-gnfdb(^0.1.11 -> ^0.1.12) for using sqlInsertOrUpdateBulk

## [0.2.1] - 2018-08-07
### Added
- Merge `DateUtils` Between `ridibooks/php-src` and `ridibooks\cp` on Only Using.
- Move `FileUtils::escapeStringForAttachment` from `platform/admin`

### Removed
- Remove deprecated Class from `DateUtil`.

## [0.2.0] - 2018-02-13
### Added
- Move `MsgException` from `ridibooks/php-core`.
### Removed
- Remove deprecated paging methods from `AdminBaseService`.

## [0.1.15] - 2018-01-22
### Fixed
- Fixed function removeTag : Add `replace` double quotation marks to html escaping `&quot;`

## [0.1.14] - 2018-01-18
### Changed
- Change connection group class reference: ConnectionProvider -> PlatformConnectionGroup

## [0.1.13] - 2017-12-14
### Changed
- Change tab to space

### Added
- Added `closeConnection` to `CommonModuleBaseService`

## [0.1.12] - 2017-11-29
### Fixed
- Fix checkMailAddress in ValidationUtils
  ex) xxxx@aa-aaa.co.kr
  
## [0.1.11] - 2017-11-22
### Added
- Added `connection_group_name` property in `CommonModuleBaseService`

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
