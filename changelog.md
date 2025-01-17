# Change Log

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## Planned

- More descriptive README for setting up the USPS Business Account and account credentials. (Likely as a separate page or to build the Wiki.)
- Readd the min/max weight per shipping method.
- Build a better logic to match the rates with the selected methods.

## [0.2.0] - 2025-01-17

### Added

- Module will now disable itself if it sees that `SHIPPING_ORIGIN_ZIP` is not a valid 5-digit or 9-digit ZIP Code.
- Additional warnings are now present on the module line in the listing of modules.
- Added a note to the Debug Log about what the shipping origin zip code setting is.
- (REPO HEALTH) Added `CONTRIBTING.md` to explain guidelines for contributing.
- (REPO HEALTH) Created a YAML Template form for Github for standardizing bug reports.

### Changed

- Changed the module's installation message with regards to the measurements. (Going forward, if the store owner changes the setting for `SHIPPING_WEIGHT_UNITS` at the time of installation, the message for the default measurements will also change.)
- Changed the ZenCart Plugin Manager message to be a bit more descriptive.
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
