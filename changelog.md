# Change Log

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## Planned

- More descriptive README for setting up the USPS Business Account and account credentials. (Likely as a separate page or to build the Wiki.)
- Readd the min/max weight per shipping method.
- Build a better logic to match the rates with the selected methods.

## [0.1.1] - [UNRELEASED]

### Added

- Module will now disable itself if it sees that `SHIPPING_ORIGIN_ZIP` is not a valid 5-digit or 9-digit ZIP Code.
- Added a note to the Debug Log about what the shipping origin zip code setting is.
- (REPO HEALTH) Added `CONTRIBTING.md` to explain guidelines for contributing.
- (REPO HEALTH) Created a YAML Template form for Github for standardizing bug reports.

### Changed

- Changed the module's installation message with regards to the measurements. (Going forward, if the store owner changes the setting for `SHIPPING_WEIGHT_UNITS` at the time of installation, the message for the default measurements will also change.)
- Changed the ZenCart Plugin Manager message to be a bit more descriptive.
- Deleted the changelog attached to the release directory and removed the reference from the encapsulated manifest file. Going forward will instead hot link to the releases tab on the repository at GitHub. The 0.1.0 release manifest has been changed to redirect.

### Fixed

- ~~[BUG] Resolved issue [#4](https://github.com/retched/ZC-USPSRestful/issues/4): (Loading the file via Plugin Manager generates a crash). This was resolved by an emergency patch to the release files but is being properly deployed now.~~
  This patch has been undone as the module is now active on the ZenCart Plugins Database.
- [BUG] Resolved issue [#5](https://github.com/retched/ZC-USPSRestful/issues/5): Stops error message complaining about a `NULL` value for `$order->delivery['street_address']` when searching if a delivery address is either a `PO_BOX` or `STREET` address. This normally happens when you try to use the module on something like the Shipping Estimator where there is no address set.
- [BUG] Resolved issue [#7](https://github.com/retched/ZC-USPSRestful/issues/7):  First Class Mail International Service missing from quotes. This was apparently repaired by the USPS API Tech Team. The quote is now reinstated. Additionally, both quotes now will carry the estimated value of the cart alongside it. This is important as there are no limits on domestic shipments but there is a limit of $400 for First Class Mail Package Service and a limit of about $600 for other services. If the request is too much, the quote will not return anything.

## [0.1.0] - 2024-12-21

- Initial Release
