# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [1.0.8] - 2020-04-28
### Fixed
- Fixed null|string type error when variable $values is empty string or null 
even though it is set from `getValuesFromContent` in `RequestUtils`
- Fixed condition checking empty array or empty string 
not to use StringUtils from `getValuesFromContent` in `RequestUtils`
- Fixed error using default values from `getValuesFromContent` in `RequestUtils`

## [1.0.4] - 2020-04-27
### Changed
- Changed using Request to `Illuminate\Http\Request` in `RequestUtils`
- Changed using Response to `Illuminate\Http\Response` in `CsvResponse`

## [1.0.3] - 2020-04-24
### Added
- Add `AWSHelper` having common functions for AWS
### Removed
- Removed to deprecated `AWSConfigDto` not to use namespace `AWS`

## [1.0.2] - 2020-04-23
### Fixed
- Fixed error from using Const in `SentryClientInterface`
- Changed visibility of `DEFAULT_OPTIONS` in `SentryClientInterface`

## [1.0.1] - 2020-04-23
### Fixed
- Activated Stack Trace in `Sentry`

## [1.0.0] - 2020-04-22
### Changed
- Update package - `sentry/sentry` , `monolog/monolog`
- Set `symfony/http-foundation` 5.x package minimum version (Security issue)
### Removed
- Remove `ridibooks/core` dependency
### Deprecated
- Deprecate root `*Utils` classes

## [0.21.1] - 2020-04-14
### Changed
- Allow `monolog/monolog` ^2.0 version

## [0.21.0] - 2020-03-31
### Changed
- Allow `illuminate/http` ^7.0 version

## [0.20.0] - 2020-03-31
### Changed
- Allow `symfony/http-foundation` ^5.0 version

## [0.19.0] - 2020-03-24
- Add composer packages
  - `illuminate/http`
- Add `Authenticate` in `Middleware`

## [0.18.6] - 2020-03-19
### Fixed
- Fixed logging error instead of triggering error when happened exception to connect in `RedisCache`

## [0.18.5] - 2020-03-16
### Fixed
- Fixed logic in `RedisCache`
- Check if the connection is alive first and, or not retry to connect, and then try to get/set value

## [0.18.4] - 2020-03-13
### Fixed
- Fixed `RedisCache` to check if the connection is alive and, or not retry to connect

## [0.18.3] - 2020-03-11
### Fixed
- Fixed `RedisCache` as checking condition whether the connection is alive when getting/setting value 

## [0.18.2] - 2020-03-06
### Add
- Added function `removeDoubleSpace` to get rid of the spaces over one in `StringUtils`

## [0.18.1] - 2020-02-21
### Fixed
- Fixed property `client` to protected in `RedisCache`

## [0.18.0] - 2020-02-21
### Add
- Add `RedisCache`

## [0.17.6] - 2020-02-20
### Add
- Add Function `getQueueAttributes` and Dto `SqsQueueAttributeDto` in `SqsService`

### Fixed
- Fix Function `sendMessage` that doesn't contain delay time in param to send message to use default value
: `If you don't specify a value, the default value for the queue applies.`
: https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sqs-2012-11-05.html#sendmessage

### Removed
- Removed fourth param `delay_seconds` from function `sendMessage`: ref #Fixed-01

## [0.17.5] - 2020-02-17
### Removed
- Removed `PlatformDtoTrait`

## [0.17.4] - 2020-02-17
### Fixed
- Fix Functions to protected in `PlatformDtoTrait`

## [0.17.3] - 2020-02-17
### Added
- Add Trait `PlatformDtoTrait`
- Add Functions `importFromRequest` and `importFromArray` in `PlatformDtoTrait`

## [0.17.2] - 2020-02-13
### Added
- Add Function `getValuesByKey` and `getValuesByKeys` in `RequestUtils`

## [0.17.1] - 2020-02-12
### Fixed
- Fix Function Params to be more flexible in `SsmService`

## [0.17.0] - 2020-02-12
## Added
- Add `SsmService` in AWS
- Add Functions `getParameter`, `getParameterAsMap`, `setParameter` and `setParameterFromMap` in `SsmService`

## [0.16.8] - 2020-02-10
### Changed
- Remove `WaitTimeSeconds` property in `SqsService::receiveMessages`

## [0.16.7] - 2020-02-04
### Added
- Add static function `clean` in `TempFileService`
- Add `php-http/guzzle6-adapter` ^1.1 version

## [0.16.6] - 2020-01-06
### Added
- Add `RequestUtils`
- Add Functions `getContent` and `getAllParams` in `RequestUtils`

## [0.16.5] - 2019-12-18
### Fixed
- Fix hangul `original_filename` of function `createPresignedUrl` in `S3Service`

## [0.16.4] - 2019-12-18
### Added
- Add `changeMessageVisibility` in `S3Service`
### Changed
- Support `original_filename` of function `createPresignedUrl` in `S3Service`

## [0.16.3] - 2019-12-03
### Changed
- Change `symfony/http-foundation` package minor version

## [0.16.2] - 2019-11-25
### Fixed
- Fix `JwtAuthorizationFactory::$auth_dtos` usage

## [0.16.1] - 2019-11-14
### Added
- Add static function `encode` in `JwtUtils`
### Changed
- Change visibility to protected of function `getClient` in `AbstractAwsService`

## [0.16.0] - 2019-11-07
### Changed
- Allow `symfony/http-foundation` ^4.3 version

## [0.15.0] - 2019-11-06
### Changed
- Change required version of `firebase/php-jwt`

## [0.14.0] - 2019-10-31
### Changed
- Change `Batch::SignalManager` to `System::SignalManager`

## [0.13.1] - 2019-10-31
### Added
- Add `SignalManager` in `Batch` namespace

## [0.13.0] - 2019-10-22
### Changed
- Allow `symfony/http-foundation` ^3.4 version

## [0.12.0] - 2019-10-22
### Added
- Add `TempFileService` in `File` namespace
- Add Jwt Authorization classes
- Add composer packages
  - `symfony/http-foundation`
  - `firebase/php-jwt`
### Fixed
- Fix using `illuminate/support` functions in `SqsService`

## [0.11.14] - 2019-10-21
### Added
- Add `ElapsedTime`

## [0.11.13] - 2019-10-15
### Fixed
- Remove `DelaySeconds` in `SqsService::sendMessageToFifoQueue`

## [0.11.12] - 2019-10-15
### Fixed
- Change `MsgExpcetion` to `AwsException` in `SqsService`
- Fix fail message description in `SqsService::sendMessage`
### Added
- Add `SqsService::sendMessageToFifoQueue` method

## [0.11.11] - 2019-10-15
### Fixed
- Use trigger_error `createPresignedUrl`, `doesObjectExist`  in `S3Service`

## [0.11.10] - 2019-10-12
### Fixed
- Fix support collections in `HtmlTableDto::importFromTitleAndDicts` 

## [0.11.9] - 2019-10-10
### Added
- Add `Email` Services
- Add `mailgun/mailgun-php` in composer.json

## [0.11.8] - 2019-10-10
### Added
- Add Functions `receiveMessages`, `deleteMessage` in `SqsService`

## [0.11.7] - 2019-10-07
### Fixed
- Fix support `null` schema in `FileUtils::isS3Scheme`, `isSameScheme` 

## [0.11.6] - 2019-10-07
### Added
- Add Functions `doesObjectExist` in `S3Service`
- Add Exception in s3Service

## [0.11.5] - 2019-10-01
### Added
- Add Functions `createPresignedUrl` in `S3Service`
- Add Exception in s3Service

## [0.11.4] - 2019-09-25
### Fixed
- Fix `S3Service::__construct` visibility

## [0.11.3] - 2019-09-25
### Added
- Add Functions `headObject` and `parseUri` in `S3Service`
### Changed
- Changed Function `addMessage` to `sendMessage` in `SqsService`

## [0.11.2] - 2019-09-25
### Added
- Add Functions in `FileUtils`
### Fixed
- Fix Function `transferFile` in `S3Service`

## [0.11.1] - 2019-09-25
### Fixed
- Fix calling abstract method of `AbstractAwsService`

## [0.11.0] - 2019-09-25
### Changed
- Changed all functions to be reusable in `S3Service`

## [0.10.6] - 2019-09-23
### Added
- Add classes for `S3Service` and `SqsService` in `AWS`
- Add constant `DEFAULT_DATETIME_WITHOUT_FORMAT` in DateUtils
- Add constants for date format in DateUtils
- Deprecated Classes that don't have classified namespace

## [0.10.5] - 2019-09-19
### Added
- Add classes `DbUtils`, `ExceptionUtils`, `PingService`

## [0.10.4] - 2019-09-19
### Fixed
- Fix `S3Utils` credentials typo

## [0.10.3] - 2019-09-19
### Added
- Add classes `ReplicationUtils` for check replication status between master and slave

## [0.10.2] - 2019-09-19
### Added
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
