# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [0.10.2] - 2019-09-19
## Added
- Add classes `S3Utils` for S3 stream wrapper

## [0.10.1] - 2019-09-18
### Changed
- Change usage `Connection` to `PDO` in function `getSlaveStatus` from `ReplicationStatusWatcher`

## [0.10.0] - 2019-09-18
### Changed
- Change usage `GnfConnectionProvider` of `platform-common` instead of `Ridibooks/Core`

## [0.9.0] - 2019-09-18
### Changed
- Change Variable Type of `connection_pool` from `Doctrine/Connection` to `PDO base` for compatibility of usage
### Removed
- Remove Function `getConnectionWithAutoReconnection` in `GnfConnectionProvider`

## [0.8.1] - 2019-09-17
### Added
- Added Function `renameFile` in `FileUtils`

## [0.8.0] - 2019-09-05
### Added
- Added Classes `CustomPdoConnection`, `ReplicationStatusWatcher`, `SlaveServer`, `SlaveStatus`, `SlaveStatusConstant`
for DB connections and monitoring
- Added Classes `FileLock`, `MonologHelper`, `UrlHelper`, `AdaptableCache` and `CountryUtil`
- Added Classes `ValidationHelper`, `CustomEmailValidator`, `RfcEmailValidator` and `PhoneNumberValidator` for validation
- Added Function `getNow` in `DateUtils`

## [0.7.6] - 2019-09-05
### Added
- Added function `rmdirRecursively` in `FileUtils` 

## [0.7.5] - 2019-08-14
### Changed
- Modified function `getArrayDiffRecursively` in `ArrayUtils` 

## [0.7.4] - 2019-08-05
### Changed
- Modified function `getArrayDiffRecursively` in `ArrayUtils` 

## [0.7.3] - 2019-05-28
### Added
- Added `CountryUtils` in Util 

### Changed
- Changed Namespace of `MonologHelper`

## [0.7.2] - 2019-05-27
### Added
- Added `CustomEmailValidator`, `PhoneNumberValidator`, `RfcEmailValidator`, and `ValidationHelper` in Validation 

### Changed
- Revert usage of `GnfConnectionProvider` in PlatformBaseModel & CommonModuleBaseService
 
## [0.7.1] - 2019-05-23
### Changed
- Changed `Ridibooks\Platform\Library\SentryHelper` to `Ridibooks\Platform\Common\Util\SentryHelper`
- Changed `Ridibooks\Platform\Library\DB\GnfConnectionProvider` to `Ridibooks\Platform\Common\DB\GnfConnectionProvider`

## [0.7.0] - 2019-05-22
### Added
- Added Classes `GnfConnectionProvider`, `AdaptableCache` and `SentryHelper` to remove dependency of `Php-Core`

## [0.6.3] - 2019-04-23
### Added
- Added function `excludeNull` to exclude only null values in param array in Util/ArrayUtils  

## [0.6.2] - 2019-04-23
### Added
- Added function `excludeNull` to exclude only null values in param array  

## [0.6.1] - 2019-04-17
### Changed
- Changed `Ridibooks\Exception\MsgException` to `Ridibooks\Platform\Common\Exception\MsgException`

## [0.6.0] - 2019-04-17
### Changed
- Changed `Ridibooks\Platform\Common\Exception\MsgException` to `Ridibooks\Exception\MsgException`

### Removed
- Remove `Constant\CpSummaryConstant`

## [0.5.0] - 2019-04-05
### Removed
- Remove `AdminBaseModel`, `AdminBaseService`(deprecated)
- Remove `PlatformBaseModel` not being in Base folder

## [0.4.6] - 2019-02-21
### Added
- Added `FileLockUtils`

## [0.4.5] - 2019-02-20
### Changed
Fix Namespace & deprecated/delete Class Unused

- Changed usage to Common/Util of StringUtils in JsonDto
- Deprecated AdminBaseService
- Delete AwsUtils in Util Namespace

## [0.4.4] - 2019-02-19
### Added
- Added `AbstractRequestApi` In Super
- Required `GuzzleHttp ^6.3` in Composer

## [0.4.3] - 2019-02-19
### Fixed
- Fixed Namespace Error of PlatformBaseModel

## [0.4.2] - 2019-02-19
### Added
- Copied PlatformBaseModel to Base
- Copied XXXUtils to Util
- Deleted Cron

## [0.4.1] - 2019-01-23
### Added
- Added `checkInPeriod`, `normalizeDateTimeString` In DateUtils

## [0.4.0] - 2018-12-03
### Added
- Added `AbstractAwsUtils`, `AwsConfigDto` for AWS Utils
- Added `SQSUtils`
- Added composer dependency - `aws/aws-sdk-php`

## [0.3.1] - 2018-10-16
### Added
- add PLATFORM_SLAVE to PlatformConnectionGroup
- add PLATFORM_ONLY_DB_SLAVE to PlatformConnectionGroup
- add PLATFORM_BOOK_DB_SLAVE to PlatformConnectionGroup
- add CP_STATISTICS_SLAVE to PlatformConnectionGroup

## [0.3.0] - 2018-09-17
### Changed
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
