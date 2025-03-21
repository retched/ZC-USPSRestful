# Change Log

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## Planned

- Looking into following what the RESTful versions of UPS and FedEX do and put the generated token into the `$_SESSION` variable and retrieve it there. (Currently the module generates an access token, uses it to generate a set of quotes, then revokes it rather than letting it expire.)
- Add a \<label\> tag to checkboxes for the addon section and possibly with the main methods section.
- Add Domestic and International letters rate cards to the output.

## [1.2.0] - 2025-03-15

### Changed

- Changed the tab spacing to four hard spaces in the debug log output for the JSON.
- Changed description of logs in the configuration section. (It's logs not long.)

### Fixed

- Fixed an issue where the estimated delivery and estimated day count would repeat twice. (Ex: "USPS (Priority Mail [est. delivery 03/09/2025] [est. delivery 03/09/2025])) Still not sure where it came from but it's resolved. [#36](https://github.com/retched/ZC-USPSRestful/issues/36)
- Fixed an issue where an older version of ZenCart would try to invoke zen_db_perform with capitalized commands (`UPDATE` instead of `update`) and ZC just doesn't know what to do. [#40](https://github.com/retched/ZC-USPSRestful/issues/40)
- Fixed an issue where after selecting add-ons, you couldn't clear all of them off in bulk. In short, you had to leave one up and deselect the others. [#42](https://github.com/retched/ZC-USPSRestful/issues/42)
- Fixed an issue where the originating JSON request wasn't being attached to the log. [#43](https://github.com/retched/ZC-USPSRestful/issues/43)
- Fixed an issue that caused a crash when using the "Shipping Zones" function to limit where the module should be allowed. (This evidently was also an issue in lat9's USPS module as they were trying to move away from the legacy form of traversing `$db` output. Whenever the `MoveNext()` functionality is removed, that will cause a breaking change in the module as older ZC's will be left out.) [#44](https://github.com/retched/ZC-USPSRestful/issues/44)

## [1.1.2] - 2025-03-07

(Yes, it's released the same day as `1.1.1`. `1.1.1`'s release was deleted and replaced with 1.1.2 instead.)

### Fixed

- Resolved issue that users had with selecting a shipping method and having it "stick". (There was an unused method variable that was set by ZenCart that stored the selected method which is used to carry it forward.)

## [1.1.1] - 2025-03-07

### Fixed

- Resolved all issues with regards to the selection of shipping methods during checkout. (Issues [#28](https://github.com/retched/ZC-USPSRestful/issues/28), [#29](https://github.com/retched/ZC-USPSRestful/issues/29), [#30](https://github.com/retched/ZC-USPSRestful/issues/30), [#31](https://github.com/retched/ZC-USPSRestful/issues/31)) (Long and short run, there was a problem with the counting of each module presented and it caused issues.)
- Fixed an issue that came up when trying to squash options but you were also using estimated dates/times (the squashing was ignored and all options were presented anyway, bad regex matching). (Issue [#32](https://github.com/retched/ZC-USPSRestful/issues/32))

## [1.1.0] - 2025-02-22

### Added

- The module will now call into the ZenCart Plugin database to see if a new version is available. If there is, you will see a banner on the top of the page alerting you. [#19](https://github.com/retched/ZC-USPSRestful/issues/19)
- Ground Advantage and Ground Advantage Cubic will now be squashed into the cheaper method being offered. Same with Priority Mail and Priority Mail Cubic. [#23](https://github.com/retched/ZC-USPSRestful/issues/23). (In short, if you have both Priority Mail and Priority Mail Cubic quoted, with the toggle made for Priority Mail, the module will choose the cheaper of the two methods and display that.)

### Removed

- Removed the check and comparison to see what format the site is using for shipping and length measurements on upgrades. (It was supposed to check if kilograms was the rate at the time of installation and then convert the defaults to that. Now, the module will check on install, place those defaults, and leave it. This means if you change the measuring standard, you'll have to reset the limits of the shipping methods.)
- Removed the unit of measure from the shipping methods table.

### Changed

- Debug mode now has two separate modes: Display Errors, and Generate Logs. If errors are found, they are hidden from the customer view unless toggled on. Additionally, you can generate logs for all requests. (TODO: On any error, generate a log regardless of setting.)

### Fixed

- There was a spelling error for "Priority Mail" which made "Priorty Mail". That was fixed.
- Resolved [#25](https://github.com/retched/ZC-USPSRestful/issues/25): Some error messages still bled through even though there was a series of flags and checks to make sure not to bother with launching the quote. Now the logic is: If the order country is bound to the United States and there is no zip code, the module will not try to get a quote. If the quote is requested for an order going elsewhere, the Zip Code is less important.

## [1.0.0] - 2025-02-18

### Breaking

- Due to a change in the configuration for the shipping methods, the selection of current shipping methods will be reset with this version. You must now reselect your shipping methods to use under USPS. (I tried to avoid these kind of breaking changes but with the way how the selection of the modules are done and the changes to the table holding them, it's unavoidable. This will normally happen if I have to change the way how the USPS identify their services.)
- Going forward: if you are using encapsulated version `v0.0.0` or `0.0.0`, aka the version pulled straight from the GitHub repository "`main`" branch, the upgrader will fail. You must do a clean install by uninstalling the module from Plugin Manager from your backend and then installing the new version. The development version of `v0.0.0` is to be considered an incomplete thought and should NOT be used in active productions. Non-encapsulated versions will still have to do the same until an upgrader is put in place in the module.

### Added

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

### Fixed

- Full compatibility with ZenCart 1.5.7 AND PHP7. (Technically speaking, the code base was compatible with PHP 7.3 and onward, but 7.1 still required Heredocs to end at the first column of the line. There was an extra indent involved.)
- Added a catch all to prevent a bugged API response for Media Mail. In short, the values for Nonstandard Basic was being duplicated. USPS is aware of this but there's is no telling of when a fix will come. In the interim, the module will filter out the other response and proceed with just one. This does mean that your Machinable packages will be treated as Nonstandard. (In most cases, the price should still be the same.)
- Filtered off the PMOD (Priority Mail Open and Distribute) responses as well as duplicated domestic Flat Rates.
- Improved filtering from Media Mail, Ground Advantage Cubic, Priority Mail, and Priority Mail Express services. (Fixes [issue #13](https://github.com/retched/ZC-USPSRestful/issues/13) from the Github.)
- More "industry" terms filtered out. (Open and Distribute methods are filtered out.)
- Changed USPS Ground Advantage to just read Ground Advantage instead.
- Fixed validation of zipcodes: module now tries to see if the order has a US destination. If so, disable the module if someone enters something that isn't a 5 or 9 digit zip code.

### Changed

- Error messages in the admin backend now use `$messageStack` instead of attaching it to the row of details.
- Improved README to give directions on how to create an API credentials. (Wiki was also created and articles created there.)
- The internal handling of some of the shipping methods has changed. (Namely USPS Ground Advantage, Priority Mail, Media, and Priority Mail Express. Each of these has a weird naming scheme in the API that was either causing rates to not appear or appear more than once in a non-descriptive way. Also see [issue #13](https://github.com/retched/ZC-USPSRestful/issues/13) and various comments on the repository.)
- Changed machinability flag to only apply with Media Mail. The USPS API will automatically determine if a package would be machinable or nonstandard, the term irregular has been retired for other services. How ever for Media Mail, the seller will need to provide a bit of details.
- Renamed `MODULE_SHIPPING_USPSR_PROCESSING_CLASS` to `MODULE_SHIPPING_USPSR_MEDIA_CLASS`. This is an internal only change. Makes it easier to identify in the code.
- Sanitized the debug logs by hiding the "client secret" from the JSON file that is saved in the log. (It's still dispatched but seeing the secret is not necessary.)

### Removed

- Deleted old versions from repository files. The old versions will live in the releases section of the GitHub and ZenCart Release. (This makes it easier to version track.) Going forward, the main directory of the module on repository will be renamed to 0.0.0 and then each release will have a separate branch with any necessary changes. The "tags" will be based off that targeted branch.

## [0.2.0] - 2025-01-17

### Added

- Module will now disable itself if it sees that `SHIPPING_ORIGIN_ZIP` is not a valid 5-digit or 9-digit ZIP Code.
- Additional warnings are now present on the module line in the listing of modules.
- Added a note to the Debug Log about what the shipping origin zip code setting is.
- (REPO HEALTH) Added `CONTRIBTING.md` to explain guidelines for contributing.
- (REPO HEALTH) Created a YAML Template form for Github for standardizing bug reports.

### Changed

- Changed the module's installation message with regards to the measurements. (Going forward, if the store owner changes the setting for `SHIPPING_WEIGHT_UNITS` at the time of installation, the message for the default measurements will also change.)
- Changed the value of "Estimate Time" to "Estimate Transit Time" for the `MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT` constant for clarity. During the upgrade, if "Estimate Time" is the option, it will change to "Estimate Transit Time" automatically.
- Changed the encapsulated plugin's "description" to read a bit better. (This has no function on the operation of the plugin but it was irritating to look at as it referenced parts not available in the regular parts of the module.)

### Fixed

- ~~[BUG] Resolved issue [#4](https://github.com/retched/ZC-USPSRestful/issues/4): (Loading the file via Plugin Manager generates a crash). This was resolved by an emergency patch to the release files but is being properly deployed now.~~ (This patch has been undone as the module is now active on the ZenCart Plugins Database.)
- [BUG] Resolved issue [#5](https://github.com/retched/ZC-USPSRestful/issues/5): Stops error message complaining about a `NULL` value for `$order->delivery['street_address']` when searching if a delivery address is either a `PO_BOX` or `STREET` address. This normally happens when you try to use the module on something like the Shipping Estimator where there is no address set.
- [BUG] Resolved issue [#7](https://github.com/retched/ZC-USPSRestful/issues/7):  First Class Mail International Service missing from quotes. This was apparently repaired by the USPS API Tech Team. The quote is now reinstated alongside other international quotes. Additionally, both quotes now will carry the estimated value of the cart alongside it. This is important as there are no limits on domestic shipments but there is a limit of \$400 for First Class Mail Package Service and a limit of about $600 for other services. If the request is too much, the quote will not return anything as it is outside of the range.
- [BUG] Resolved issue [#8](https://github.com/retched/ZC-USPSRestful/issues/8): Shipping Modules listing crashes when module loads. This happens when the `TABLE_ORDERS` has a `shipping_method` column set to `TEXT` and is filled with larger data. Originally the module, during the `_check()` process, would see if the column was set to be a `VARCHAR(255)`. If it was set to anything but a `VARCHAR(255)`, the module would try to issue an `ALTER TABLE` SQL command and make it a `VARCHAR(255)`. This would backfire as ZenCart would catch a MYSQL error on the tune of "Data too big" and just crash the entire backend. This change will now ignore the column if it's set to anything bigger than a `VARCHAR`.
- [BUG] Fixed double spacing present on all quoted process names. (These extra spaces weren't visible anywhere else other than the logfile and maybe the raw source code too if you looked hard enough for it.)

### Removed

- Dropped International Return Receipt as that service has been deprecated since January 19, 2025.
- Deleted the changelog attached to the release directory and removed the reference from the encapsulated manifest file. Going forward, the manifest should link to the releases tab on the repository at GitHub. The 0.1.0 release manifest also has been changed to match.

## [0.1.0] - 2024-12-21

- Initial Release

## [0.0.0] - 2024-12-21

- Placeholder Release (should not be downloaded from ZenCart database)
