# Changelog

## [x]
- Improved Logging: log on update and only if data changed, added status to logs
- Fix: User set passwd
- OrgUnits are now Mapping Strategy Aware
- Improvement Log Table: allow usage of wildcard (%) for external i (%) for external id
- Fix Error notification was sent without errors occuring
- Fix course subscription types
- Competence management origin type
- Fix install log
- New default path to origins '/var/www/ilias/Customizing/global/origins/'
- Fix language keys with line break
- Fix read fallback parent ref id read
- Write rbac log when creating object
- Fix: mapping byTitle for Org Units
- Feature: User DTO with language
- Origins can now set course start and end date
- Assign ILIAS default role to user per default
- Mapping strategy by ext id
- Fix missing move session implementation
- Fix insert on update, if insert failed on create
- Feature: user origin config to keep login case (instead of all to lower case)

## [2.3.0]
- Support ILIAS 5.4
- Optimize `handleSort` in org unit
- Pass also the dto object to `handleDelete`
- Fix missing `srag\Plugins\Hub2\Origin\AbstractOriginImplementation` import in origin class template
- Refoctoring/Performance logs: Use native MySQL AutoIncrement and reset it after remove logs
- `wakeUpValue`/`sleepValue` for `DataTransferObject`
- Fix reset offset
- Remove `DICStatic::clearCache();` because replaced by pass by reference in DICTrait
- Config nove behavior for org units
- Config delete behavior for org units and org unit memberships

## [2.2.5]
- Optimized log table

## [2.2.4]
- Fix log table ext id filter
- Fix status table export contains Actions columns
- Logs table can now exported too
- Fix course membership readd on update

## [2.2.3]
- Fix connection getPath validation
- Fix getAppointmentColor is NULL
- Logs invalid origin implementation namespace\\class
- Fix log table origin type filter
- Store new possible ilias id on exception (`handleCreate`|`handleUpdate`|`handleDelete` returns now nothing anymore, but it will store the ilias object on `$this->current_ilias_object`, so it can read the ilias id also on exception)

## [2.2.2]
- Fix ILIAS file selector

## [2.2.0]
- Add ILIAS file as a origin connection type
- Improves config getters

## [2.1.0]
- Some improvments and fixes

## [2.0.0]
- Ask for removing data
- AdHoc feature
- CustomView feature
- GlobalHook feature
- New logs will be logged in database, not in files anymore. Also cron job to automatic delete old logs with config days
- More meaningful GUI for force update synch: Buttons. Also implement this for a single origin synch
- New origin sync status model
- New FAILED status, all exceptions will set the object to this status
- MappingStrategy ByImportId
- Sort origins feature
- Clean up status modal DTO
- Exceptions are converted in logs
- Logs are send in email
- handleExceptions is now handleLog
- Removed notifications which replaced with logs
- Fix: Add correct namespace for new origins
- Fix: Not delete data on origin delete
- Fix: Recreate deleted users
- Fix: OrgUnit restore from trash
- Refactoring, using new libraries
- PHP version checker
- Some other fixes and improvements
