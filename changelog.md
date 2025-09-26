# USPS Shipping (RESTful) for Zen Cart

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## Planned

- Create an uninstall script for non-encapsulated installations. [[#61](https://github.com/retched/ZC-USPSRestful/issues/61)]

## [UPCOMING: 1.5.0-dev] - ????-??-??

### Changed in UPCOMING: 1.5.0-dev

- Improved the building of quotes. Broadly instead of the module doing a double iteration over the full list of selected methods and the entire resultant request from the USPS API, the module now indexes that pull and will hunt down the desired method by named key now. This should greatly help with the problem of slow API pulls. [[#50](https://github.com/retched/ZC-USPSRestful/issues/50)]

### Fixed in UPCOMING: 1.5.0-dev

- Fixed an issue where the cart would reduce the weight of the quote if it reached ZenCart's maximum weight. (Example: If the order is 85 pounds, ZenCart makes the order send a quote for 42.5 pounds instead.) The quote is supposed to be multiplied for each "box" in the order when configured for such. [[#74](https://github.com/retched/ZC-USPSRestful/issues/74)] Additionally, the "order" fee is also multiplied.
- Fixed an issue that would generate a warning when the module would try to request standards for services that don't provide them. (ie. Connect Local and all international shipments.) That problem is now squelched as the module will check to see such a result exist first before trying to access it.

## [1.4.1] - 2025-09-03

### Fixed in 1.4.1

- Fixed bug in ScriptedInstaller.php for Encapsulated installs. (Nothing changed between 1.4.0 and 1.4.1 otherwise. If you have a non-encapsulated install, you can skip this install if you have 1.4.0 installed already.)

## [1.4.0] - 2025-09-02

### Fixed in 1.4.0

- Fixed logic behind testing if the USPS module should be enabled for a group [[#69](https://github.com/retched/ZC-USPSRestful/issues/69)]
- Fixed logic on the same for the shipping estimator
- Removed extra comma from old style language file.
- Changed the function being used to display the version to a read-only function instead of a radio select.

## [1.3.2] - 2025-08-25 + ~~[1.3.1] - 2025-08-24~~

_Version 1.3.1 is considered superceded by 1.3.2 and should not be used. It has been deleted from the ZenCart Module Directory and the repository releases tab._

### Fixed in 1.3.2

- Errant "hint code" somehow became a part of the build and was pushed out as part of 1.3.1, that bit has been removed.
- Made the four-cent change only apply to US destinations as international rates pulled by the API don't seem to be affected.

### Added in 1.3.1

- Added a `<label>` field to the services list to give people a bigger "target" for selecting services.

### Changed in 1.3.1

- Changed the logic of how upgrade tests and actions are performed. Instead of looking at the newer, more recent versions first, the module will now start with the oldest and go forward.
- Changed the logic in checking for newer versions of the module. (It should NOT be trying to compare the ZenCart stored version versus 0.0.0, there will ALWAYS be a "newer" version than 0.0.0 there.)

### Fixed in 1.3.1

- Fixed an issue where unencapsulated installs could potentially leave out necessary configuration keys. [[#59](https://github.com/retched/ZC-USPSRestful/issues/59)]
- Fixed an issue where the retrieved First Class Mail Letters quote would be short by $0.04. [[#57](https://github.com/retched/ZC-USPSRestful/issues/57)]

## [1.3.0] - 2025-08-16

### Added in 1.3.0

- Added support for First Class Mail and First Class Mail International to the module. Both will be automagically added to the table of services upon upgrading to 1.3.0. Additionally added processing class flags: Letters, Flats (or Large Envelopes) and Cards (like Postcards).
- Added a toggle to determine which API endpoint to use (the apis\.usps\.com endpoints should be used as the USPS API team is nudging everyone to that).
- Added new `<label>` tag to checkboxes as needed.

### Changed in 1.3.0

- AuthTokens are now stored in the `$_SESSION` in PHP. Rather than generate a new AuthToken on every page visit, the module will check if there is already an AuthToken already made and if there is not, THEN make a new AuthToken at that point. (AuthTokens last for roughly eight hours. So long as there isn't more than 60 requests per hour. If there is a chance you might go above that, no problem... simply request the API Team to provision you more requests.)
- As part of the AuthToken request, the module will now send of the vversion of the module and the ZenCart version as the USERAGENT portion of the CURL request.
- Running the `v0.0.0` version from the Repository will now have a warning banner appear in the admin backend. That warning uses the `messageStack` system and will link to the ZenCart thread and this repository on GitHub.

### Fixed in 1.3.0

- Fixed an issue that could potentially cause a blank or dead API return call to break an install. [[#56](https://github.com/retched/ZC-USPSRestful/issues/56)]
- Fixed an issue that would generate error debug messages when trying to revoke tokens. [[#51](https://github.com/retched/ZC-USPSRestful/issues/51)]
- Fixed an issue that would cause the entered number of handling days to not be saved. (Side note: This will cause validation to not be performed on older variations of ZenCart as that functionality is not present.) [[#52](https://github.com/retched/ZC-USPSRestful/issues/52)]

### Removed in 1.3.0

- Removed the modules ability to revoke AuthTokens in the module. (USPS API Support Team reported that there is a breaking fault on their side. To avoid the headaches, just removed the revokeToken command and procedure. Will likely add in again later.)

## [1.2.0] - 2025-03-15

### Changed in 1.2.0

- Changed the tab spacing to four hard spaces in the debug log output for the JSON.
- Changed description of logs in the configuration section. (It's logs not long.)

### Fixed in 1.2.0

- Fixed an issue where the estimated delivery and estimated day count would repeat twice. (Ex: "USPS (Priority Mail [est. delivery 03/09/2025] [est. delivery 03/09/2025])) Still not sure where it came from but it's resolved. [[#36](https://github.com/retched/ZC-USPSRestful/issues/36)]
- Fixed an issue where an older version of ZenCart would try to invoke zen_db_perform with capitalized commands (`UPDATE` instead of `update`) and ZC just doesn't know what to do. [[#40](https://github.com/retched/ZC-USPSRestful/issues/40)]
- Fixed an issue where after selecting add-ons, you couldn't clear all of them off in bulk. In short, you had to leave one up and deselect the others. [[#42](https://github.com/retched/ZC-USPSRestful/issues/42)]
- Fixed an issue where the originating JSON request wasn't being attached to the log. [[#43](https://github.com/retched/ZC-USPSRestful/issues/43)]
- Fixed an issue that caused a crash when using the "Shipping Zones" function to limit where the module should be allowed. (This evidently was also an issue in lat9's USPS module as they were trying to move away from the legacy form of traversing `$db` output. Whenever the `MoveNext()` functionality is removed, that will cause a breaking change in the module as older ZC's will be left out.) [[#44](https://github.com/retched/ZC-USPSRestful/issues/44)]

## [1.1.2] + ~~[1.1.1]~~ - 2025-03-07

(Yes, it's released the same day as `1.1.1`. `1.1.1`'s release was deleted and replaced with 1.1.2 instead.)

### Fixed in 1.1.2

- Resolved issue that users had with selecting a shipping method and having it "stick". (There was an unused method variable that was set by ZenCart that stored the selected method which is used to carry it forward.)

### Fixed in 1.1.1

- Resolved all issues with regards to the selection of shipping methods during checkout. (Issues [[#28](https://github.com/retched/ZC-USPSRestful/issues/28)], [[#29](https://github.com/retched/ZC-USPSRestful/issues/29)], [[#30](https://github.com/retched/ZC-USPSRestful/issues/30)], [[#31](https://github.com/retched/ZC-USPSRestful/issues/31)]) (Long and short run, there was a problem with the counting of each module presented and it caused issues.)
- Fixed an issue that came up when trying to squash options but you were also using estimated dates/times (the squashing was ignored and all options were presented anyway, bad regex matching). (Issue [[#32](https://github.com/retched/ZC-USPSRestful/issues/32)])

## [1.1.0] - 2025-02-22

### Added in 1.1.0

- The module will now call into the ZenCart Plugin database to see if a new version is available. If there is, you will see a banner on the top of the page alerting you. [[#19](https://github.com/retched/ZC-USPSRestful/issues/19)]
- Ground Advantage and Ground Advantage Cubic will now be squashed into the cheaper method being offered. Same with Priority Mail and Priority Mail Cubic. [[#23](https://github.com/retched/ZC-USPSRestful/issues/23)]. (In short, if you have both Priority Mail and Priority Mail Cubic quoted, with the toggle made for Priority Mail, the module will choose the cheaper of the two methods and display that.)

### Removed in 1.1.0

- Removed the check and comparison to see what format the site is using for shipping and length measurements on upgrades. (It was supposed to check if kilograms was the rate at the time of installation and then convert the defaults to that. Now, the module will check on install, place those defaults, and leave it. This means if you change the measuring standard, you'll have to reset the limits of the shipping methods.)
- Removed the unit of measure from the shipping methods table.

### Changed in 1.1.0

- Debug mode now has two separate modes: Display Errors, and Generate Logs. If errors are found, they are hidden from the customer view unless toggled on. Additionally, you can generate logs for all requests. (TODO: On any error, generate a log regardless of setting.)

### Fixed in 1.1.0

- There was a spelling error for "Priority Mail" which made "Priorty Mail". That was fixed.
- Resolved [[#25](https://github.com/retched/ZC-USPSRestful/issues/25)]: Some error messages still bled through even though there was a series of flags and checks to make sure not to bother with launching the quote. Now the logic is: If the order country is bound to the United States and there is no zip code, the module will not try to get a quote. If the quote is requested for an order going elsewhere, the Zip Code is less important.

## [1.0.0] - 2025-02-18

### Breaking in 1.0.0

- Due to a change in the configuration for the shipping methods, the selection of current shipping methods will be reset with this version. You must now reselect your shipping methods to use under USPS. (I tried to avoid these kind of breaking changes but with the way how the selection of the modules are done and the changes to the table holding them, it's unavoidable. This will normally happen if I have to change the way how the USPS identify their services.)
- Going forward: if you are using encapsulated version `v0.0.0` or `0.0.0`, aka the version pulled straight from the GitHub repository "`main`" branch, the upgrader will fail. You must do a clean install by uninstalling the module from Plugin Manager from your backend and then installing the new version. The development version of `v0.0.0` is to be considered an incomplete thought and should NOT be used in active productions. Non-encapsulated versions will still have to do the same until an upgrader is put in place in the module.

### Added in 1.0.0

- Module now does a brief test to pull a token. If it succeeds, the module remains enabled. But if it fails for any reason whatsoever, the module turns off. This also generates an error message in the Admin area, additionally the module "soft-disables". (The light turns yellow as if it you disabled it.)
- Created language define file for Admin side error messages. (Both the admin and client side language files are both array based for ZC 1.5.8+. As stated, a 1.5.7 version will be developed soon.)
- New flag: `MODULE_SHIPPING_USPSR_DIMENSIONAL_CLASS`. This will ask if the packages are "Rectangular" or "Nonrectangular". (Are you shipping your orders in cube-like packaging or are you using something like soft poly bags.) This flag only comes into play if you set the packages to be REALLY big or are shipping packages REALLY heavy. The quote will take care of and include any necessary surcharges.
- New flag: `MODULE_SHIPPING_USPSR_CUBIC_CLASS`. Sets a flag to either "Non-Soft" or "Soft" packaging types for Cubic pricing. (Same question as Dimensional Class but different pricing scheme.)
- Created a toggle to allow the shop to send the full value of the customer's cart as part of the quoting process or to cap it at $5.00. (Some shipping methods in the USPS are limited based on how much value is being sent through it. This especially applies to international shipments as the USPS caps service modes based not only on destination but also the value of the items being sent.) If this toggle is enabled, the insurance calculations will fall short as the value of the insurance is based on how much the calculated cart total is. Use this setting at your own peril.
- A flag for the source of the log file has been made. Any log file generated by an admin's action will be precluded with an `_adm_` suffix. (Also a warning that each log file is rougly 300KB. Nothing can be done about that as, for diagnostic purposes, I need not just the diagnostic report but also the raw JSONs being sent and received when trying to figure out what went wrong with the module.)
- Created a debug counter for Ground Advantage Cubic, Priority Mail Cubic, and Priority Mail Express Cubic. This helps for deciding on whether to filter the rate for these services further or to just use what is received.
- Expanded the packaging classes into three questions: one pertaining to Media Mail (Machinable/Nonstandard) and Ground Advantage Cubic and Priority Mail Cubic (Rectangular/Nonrectangular and Soft/Non-Soft).
- Expanded definitions and explanations of various configuration settings.
- Added min/max fields from original USPS module to shipping method selection. These fields allow you place weight-based clamps on each individual method. For example: If you entered 1 and 5 in the appropriate boxes (order does not matter, the software will sort it out for you) for USPS Ground Advantage, the module will only offer your customers the USPS Ground Advantage rate if the total package weight (that is items and tare) is within those two settings.  
  **NOTE:** This does not mean you can offer services that are outside of the USPS limits. (Example: Entering 80 (lbs) as a maximum does not mean you can offer Ground Advantage to your customers as 70 lbs is the maximum serviceable weight for USPS Ground Advantage.)  
  **NOTE:** The number entered here should be in the unit of measurement of the cart. If your site measures in pounds, then the amount you enter here should be in pounds. If your site measures in kilograms, then the amount you enter here should be in kilograms.
- For parity with ZenCart installations older than 2.0.0, there is a file edit that the storeowner should use to define the measurement standard of the site. This is to make sure that everything is dispatched to the USPS in imperial units. If you are using 2.0.0 or newer, do not edit the file at all.
- (REPO HEALTH) Added a new Feature Request YAML form plus created a Pull Request template.

### Fixed in 1.0.0

- Full compatibility with ZenCart 1.5.7 AND PHP7. (Technically speaking, the code base was compatible with PHP 7.3 and onward, but 7.1 still required Heredocs to end at the first column of the line. There was an extra indent involved.)
- Added a catch all to prevent a bugged API response for Media Mail. In short, the values for Nonstandard Basic was being duplicated. USPS is aware of this but there's is no telling of when a fix will come. In the interim, the module will filter out the other response and proceed with just one. This does mean that your Machinable packages will be treated as Nonstandard. (In most cases, the price should still be the same.)
- Filtered off the PMOD (Priority Mail Open and Distribute) responses as well as duplicated domestic Flat Rates.
- Improved filtering from Media Mail, Ground Advantage Cubic, Priority Mail, and Priority Mail Express services. (Fixes [[#13](https://github.com/retched/ZC-USPSRestful/issues/13)] from the Github.)
- More "industry" terms filtered out. (Open and Distribute methods are filtered out.)
- Changed USPS Ground Advantage to just read Ground Advantage instead.
- Fixed validation of zipcodes: module now tries to see if the order has a US destination. If so, disable the module if someone enters something that isn't a 5 or 9 digit zip code.

### Changed in 1.0.0

- Error messages in the admin backend now use `$messageStack` instead of attaching it to the row of details.
- Improved README to give directions on how to create an API credentials. (Wiki was also created and articles created there.)
- The internal handling of some of the shipping methods has changed. (Namely USPS Ground Advantage, Priority Mail, Media, and Priority Mail Express. Each of these has a weird naming scheme in the API that was either causing rates to not appear or appear more than once in a non-descriptive way. Also see [[#13](https://github.com/retched/ZC-USPSRestful/issues/13)] and various comments on the repository.)
- Changed machinability flag to only apply with Media Mail. The USPS API will automatically determine if a package would be machinable or nonstandard, the term irregular has been retired for other services. How ever for Media Mail, the seller will need to provide a bit of details.
- Renamed `MODULE_SHIPPING_USPSR_PROCESSING_CLASS` to `MODULE_SHIPPING_USPSR_MEDIA_CLASS`. This is an internal only change. Makes it easier to identify in the code.
- Sanitized the debug logs by hiding the "client secret" from the JSON file that is saved in the log. (It's still dispatched but seeing the secret is not necessary.)

### Removed in 1.0.0

- Deleted old versions from repository files. The old versions will live in the releases section of the GitHub and ZenCart Plugin database. (This makes it easier to version track.) Going forward, the main directory of the module on repository will be renamed to 0.0.0 and then each release will have a separate branch with any necessary changes. The "tags" will be based off that targeted branch.

## [0.2.0] - 2025-01-17

### Added in 0.2.0

- Module will now disable itself if it sees that `SHIPPING_ORIGIN_ZIP` is not a valid 5-digit or 9-digit ZIP Code.
- Additional warnings are now present on the module line in the listing of modules.
- Added a note to the Debug Log about what the shipping origin zip code setting is.
- (REPO HEALTH) Added `CONTRIBTING.md` to explain guidelines for contributing.
- (REPO HEALTH) Created a YAML Template form for Github for standardizing bug reports.

### Changed in 0.2.0

- Changed the module's installation message with regards to the measurements. (Going forward, if the store owner changes the setting for `SHIPPING_WEIGHT_UNITS` at the time of installation, the message for the default measurements will also change.)
- Changed the value of "Estimate Time" to "Estimate Transit Time" for the `MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT` constant for clarity. During the upgrade, if "Estimate Time" is the option, it will change to "Estimate Transit Time" automatically.
- Changed the encapsulated plugin's "description" to read a bit better. (This has no function on the operation of the plugin but it was irritating to look at as it referenced parts not available in the regular parts of the module.)

### Fixed in 0.2.0

- ~~[BUG] Resolved issue [[#4](https://github.com/retched/ZC-USPSRestful/issues/4)]: (Loading the file via Plugin Manager generates a crash). This was resolved by an emergency patch to the release files but is being properly deployed now.~~ (This patch has been undone as the module is now active on the ZenCart Plugins Database.)
- [BUG] Resolved issue [[#5](https://github.com/retched/ZC-USPSRestful/issues/5)]: Stops error message complaining about a `NULL` value for `$order->delivery['street_address']` when searching if a delivery address is either a `PO_BOX` or `STREET` address. This normally happens when you try to use the module on something like the Shipping Estimator where there is no address set.
- [BUG] Resolved issue [[#7](https://github.com/retched/ZC-USPSRestful/issues/7)]:  First Class Mail International Service missing from quotes. This was apparently repaired by the USPS API Tech Team. The quote is now reinstated alongside other international quotes. Additionally, both quotes now will carry the estimated value of the cart alongside it. This is important as there are no limits on domestic shipments but there is a limit of \$400 for First Class Mail Package Service and a limit of about $600 for other services. If the request is too much, the quote will not return anything as it is outside of the range.
- [BUG] Resolved issue [[#8](https://github.com/retched/ZC-USPSRestful/issues/8)]: Shipping Modules listing crashes when module loads. This happens when the `TABLE_ORDERS` has a `shipping_method` column set to `TEXT` and is filled with larger data. Originally the module, during the `_check()` process, would see if the column was set to be a `VARCHAR(255)`. If it was set to anything but a `VARCHAR(255)`, the module would try to issue an `ALTER TABLE` SQL command and make it a `VARCHAR(255)`. This would backfire as ZenCart would catch a MYSQL error on the tune of "Data too big" and just crash the entire backend. This change will now ignore the column if it's set to anything bigger than a `VARCHAR`.
- [BUG] Fixed double spacing present on all quoted process names. (These extra spaces weren't visible anywhere else other than the logfile and maybe the raw source code too if you looked hard enough for it.)

### Removed in 0.2.0

- Dropped International Return Receipt as that service has been deprecated since January 19, 2025.
- Deleted the changelog attached to the release directory and removed the reference from the encapsulated manifest file. Going forward, the manifest should link to the releases tab on the repository at GitHub. The 0.1.0 release manifest also has been changed to match.

## [0.1.0] - 2024-12-21

- Initial Release

## [0.0.0] - 2024-12-21

- Placeholder Release (should not be downloaded from ZenCart database). This was done to obtain a Plugin ID to refer to in the codebase itself. (Effectively a paradox, to get a Plugin ID, you need a plugin submitted but the plugin submitted must be functional, etc. etc.)
