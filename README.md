# USPS Shipping (RESTful) for Zen Cart

![ZC-USPSRestful](https://socialify.git.ci/retched/ZC-USPSRestful/image?custom_description=This+module+provides+sellers+the+ability+to+offer+US+Postal+Service+shipping+rates+to+their+customers+during+checkout.&description=1&font=Inter&forks=1&issues=1&language=1&name=1&owner=0&pattern=Signal&pulls=1&stargazers=1&theme=Auto)

![Coded with PHP](https://img.shields.io/badge/php-purple?style=flat&logo=php&logoColor=white&labelColor=black)  ![License via GPL 3.0](https://img.shields.io/badge/license-GPL-black?style=flat&logoColor=black&label=license&labelColor=black&color=851185) ![Last Commit](https://badgen.net/github/last-commit/retched/ZC-USPSRestful?color=851185&labelColor=white)  ![Latest Release](https://badgen.net/github/release/retched/ZC-USPSRestful/stable?color=851185&labelColor=black&labelColor=white)

This module provides ZenCart sellers the ability to offer United States Postal Service (USPS) shipping rates to their customers during checkout. This is done by pulling the rates directly from the USPS RestAPI.

This module will work with the most recent versions of ZenCart using PHP 7 or PHP 8. It has been tested with Zencart 1.5.5 and onward up to 2.1.0.

## Module Version

- Latest Release: [1.4.1](https://github.com/retched/ZC-USPSRestful/releases/latest)  
_Released September 3, 2025 for ZenCart 2.1.0._
- Next Version Number: 1.5.0-dev

### Version/Release History

- 1.5.0: [No Download]  
  Fixed an issue with the quote when sending abnormally large boxes. Fixed the MAJOR issue with regards to the speed of requesting quotes. (The module will no longer try to double iterate over the entire request and instead, indexes the resultant USPS calls and pulls them up as needed. Same with Standards calls.) Fixed an issue with the math of the quotes themselves.
- 1.4.1: [Download](https://github.com/retched/ZC-USPSRestful/releases/tag/v1.4.1)  
  Minor bug fix to ScriptedInstaller.php (encapsulated only)
- 1.4.0: [Download](https://github.com/retched/ZC-USPSRestful/releases/tag/v1.4.0)  
  Makes a change to the module to obey the selected shipping zone in the backend. In other words, if you choose to restrict the order to the continental US (all US states and territories except Alaska, Hawaii, Puerto Rico, Virgin Islands, Guam, etc.), using the shipping estimator (either version) will not return USPS quotes. Additionally made further bug fixes discovered in 1.3.2 (namely an errant comma in the old language file).
- 1.3.2: [Download](https://github.com/retched/ZC-USPSRestful/releases/tag/v1.3.2)  
  Fixes a code bug that was released with 1.3.1. v1.3.1 has been deleted and v1.3.2 replaces that.
- 1.3.1  
  Minor bug fix to re-add the four-cent difference between Metered First-Class Mail and non-metered First-Class Mail. Major bug fix for 1.3.0 that could potentially leave out configuration keys.
- 1.3.0: [Download](https://github.com/retched/ZC-USPSRestful/releases/tag/v1.3.0)  
  Added First Class Mail options to the queue of services available. Changed AuthToken storage to be based on the PHP Session instead of calling on every page.
- 1.2.0: [Download](https://github.com/retched/ZC-USPSRestful/releases/tag/v1.2.0)  
  Fixed an issue where estimated dates and travel times were posted twice as part of the quote. Fixed an issue regarding compatibility with older versions of ZenCart.
- 1.1.2: [Download](https://github.com/retched/ZC-USPSRestful/releases/tag/v1.1.2)  
  Bugfix to mend the selection criteria being ignored during normal ZenCart checkout. (A shipping method would be selected, but would be ignored in favor of the "first" shipping method listed.)
- 1.1.1  
  Minor bug fixes with regards to the selection of the cheapest shipping method. Also fixed a conflict with OPC that prevented any method from being selected. Additionally, fixed an issue with regards to shipping method squashing.
- 1.1.0: [Download](https://github.com/retched/ZC-USPSRestful/releases/tag/v1.1.0)  
  Added in plugin version check. (Will ping the ZenCart database to see if there is a new version available.) Now, when you update the module, you no longer need to reset the module as a whole; the module will automatically handle whatever missing database keys and configs there are. Added ability to squash Ground Advantage Cubic and Ground Advantage rates to the lower one, same with Priority Mail Cubic and Priority Mail rates.
- 1.0.0: [Download](https://github.com/retched/ZC-USPSRestful/releases/tag/v1.0.0)  
 A LOT of changes. Including re-adding the min/max weight boxes from USPS, fixing the display of rates and quotes, adding error messages in the backend, fixing bad API returns, cleaned up the repository as a whole. Changes to allow the module to work with PHP8 and PHP7-based ZenCarts. (At least ZenCart 1.5.x or ZenCart 2.x and newer.)
- 0.2.0: [Download](https://github.com/retched/ZC-USPSRestful/releases/tag/v0.2.0)  
 Various bugfixing, including the reintroduction of First Class Mail Package International Service to the quote pool.
- 0.1.0: [Download](https://github.com/retched/ZC-USPSRestful/releases/tag/v0.1.0)  
 First "release".
- 0.0.0  
  _Development version. This version contains an incomplete thought and should not be used in production unless you are sure what you are doing. The version from the ZenCart Module database [was only a placeholder](https://www.zen-cart.com/showthread.php?230478-pluginID-of-yet-to-be-submitted-encapsulated-plugin). Pulling from the repository is not recommended. Use the [Releases](https://github.com/retched/ZC-USPSRestful/releases) instead._

## Additional Links

- [USPS API Documentation](https://developers.usps.com/apis) - This API takes advantage of four APIs: _Domestic Prices 3.0, International Prices 3.0, Service Standards 3.0, and OAuth 3.0._
- [ZenCart Plugins Directory Listing](https://www.zen-cart.com/downloads.php?do=file&id=2395) (or use the Releases function on the GitHub repository)
- [ZenCart Support Thread](https://www.zen-cart.com/showthread.php?230512-USPS-Shipping-(RESTful)-(USPSr)) - This thread is only for THIS version of the USPS module. For assistance with the original USPS module, which uses the WebTools API, you should post in [its megathread](https://www.zen-cart.com/showthread.php?227284-USPS-Shipping-Module-Support-Thread) on the ZenCart forums.
- [Personal Discord Server](https://discord.gg/cZCJ8za7zg) - You can use this reach out to me via DMs or by commenting in the appropriate channel. Please be mindful of etiquette and rules.

## Setup, Install, and Upgrading

Both versions (encapsulated and non-encapsulated) are now shared in the same release file on ZenCart. (The GitHub repository will still have a separate file.)

- **Non-encapsulated** (ZC 1.5.5+)  
  If you want to install the non-encapsulated version of the module, copy **ONLY** the `admin/` and `includes/` directories in the root of the zip file to the matching directories in the root of your ZenCart installation. (**NOTE:** Be sure to rename the `admin/` directory to match your admin directory in your ZenCart installation. **DO NOT copy the `zc_plugins/` directory.**)

- **Encapsulated** (ZC 2.1.0 or ZC 2.0.x [with these modifications](https://gist.github.com/lat9/9deb64d3325081d18bb0db5534bcf142))  
  If you want to install the encapsulated version of the module, copy **ONLY** the contents of the `zc_plugins` directory into the matching `zc_plugins` directory of your ZenCart installation. **DO NOT** rename the `admin/` directory inside of the `zc_plugins/` directory!!! Copy the directories **AS IS**!!!

You can find the full instructions to install the module, including how to obtain your USPS API credentials, by reading the [related wiki page](https://github.com/retched/ZC-USPSRestful/wiki/Getting%20Started#installing) from the Github repository.

### Upgrading/updating

- Non-encapsulated version: **Overwrite ALL files of the old version.**
- Encapsulated version: Simply upload the new release into the same `zc_plugins/` directory. Each release will have a separate folder containing that new version. (Example: Version `v1.0.0` will be uploaded into a folder named `v1.0.0`.) Once uploaded, visit the Plugin Manager in your admin area, selected the USPSRestful line in the table, and hit the "Upgrade Available" button and follow the prompts. When done, you can choose to delete the older versions.

## Uninstallation

- **Non-encapsulated**  
  To uninstall the non-encapsulated version, first uninstall the module from your Shipping Modules page if it was active. Then delete each of the files listed below.

- **Encapsulated**  
  To uninstall the encapsulated version, simply visit your Plugin Manager (in the ZenCart admin area), then click on the row for USPS Restful, finally click "Uninstall". If desired, you can clean up the installation to have ZenCart delete the files for you.

## Contributions

Contributions are welcome. I try to follow the GitHub flow with regards to the process of resolving issues. For more details, you should check out the [CONTRIBUTING.md](.github/CONTRIBUTING.md) document before making the contribution..

## Frequently Asked Questions

This won't answer all the questions you may have, but it may answer some that I thought of.

### What version of ZenCart does this module support?

Only ZenCart versions 1.5.5 and onward work with the module. This module is NOT tested with ZenCart versions 1.5.4 and before. (ZenCart 1.5.4 and before only work with PHP versions earlier than PHP 5. You can see more details about the compatibility on the [ZenCart server requirements](https://docs.zen-cart.com/user/first_steps/server_requirements/#php-version) page. This module has only been tested with PHP 7 and PHP 8.) 

**UPDATE**: There IS a [clone of this module](https://www.zen-cart.com/showthread.php/230512-USPS-Shipping-(RESTful)-(USPSr)?p=1408764#post1408764) available that will allow you to use it with PHP 5.6 but it might be a bit behind in regards to updates. I will work on this one separately. I'm primarily focused on newer versions of PHP and ZenCart. (Please upgrade if you can.)

|               |    Encapsulated    |  Non-Encapsulated  |
|---------------|:------------------:|:------------------:|
| ZenCart 1.5.5 |         :x:        | :white_check_mark: |
| ZenCart 1.5.6 |         :x:        | :white_check_mark: |
| ZenCart 1.5.7 |         :x:        | :white_check_mark: |
| ZenCart 1.5.8 |         :x:        | :white_check_mark: |
| ZenCart 2.0.0 |      :wrench:      | :white_check_mark: |
| ZenCart 2.0.1 |      :wrench:      | :white_check_mark: |
| ZenCart 2.1.0 | :white_check_mark: | :white_check_mark: |
| ZenCart 2.2.0 |     :clipboard:    |     :clipboard:    |

- :white_check_mark: = Fully supported
- :x: = Not supported
- :clipboard: = In testing, BUT it SHOULD work.
- :wrench: = Can work but will need [core file edits](https://gist.github.com/lat9/9deb64d3325081d18bb0db5534bcf142) to make it work

### What is the difference between this version and the original USPS module?

The original USPS module works by using the older USPS WebTools API. For years, that API was the defacto API in use when it came to retrieving the estimated shipping costs of the USPS' various services as well as the estimated times of delivery. In 2024, the USPS began deprecating the Web Tools API. In 2025, the USPS announced that the WebTools API will be fully out of service in 2026. The Web Tools API is being replaced with the new OAuth-based API which this codebase uses.

### I already have a `USERID` and `PASSWORD` from WebTools, but I'm getting error messages while I try to retrieve quotes. What happened?

The older `USERID` and `PASSWORD` from the WebTools API are not valid for the new system. You will need to provision new credentials under the new USPS API system. Additionally, you SHOULD create an entire new USPS Business Account provided that you don't already have one for your business. The process is explained [on the USPS Developers website](https://developers.usps.com/getting-started) and on the [related wiki page](https://github.com/retched/ZC-USPSRestful/wiki/Getting%20Started#installing) from the Github repository. If you end up not getting quotes or the module disables itself, check to make sure that you are using actual OAuth Credentials and not the old WebTools API. Additionally, make sure you have configured your cart to ship from the United States and a zipcode.

### Why should I use this version versus the one that's out there now?

The USPS created an "in-before-the-lock" situation concerning the original WebTools API. They will still allow access to the API by way of manually granting access, but they will read you the "riot act" with regards to enabling them. If you are still using the Web Tools API and have no issues accessing or using it, continue to use it. But know that in 2026, the older WebTools API will be completely disabled, and at that point, everyone will have to use the RESTful version of the API going forward.

**UPDATE** USPS has begun forcing people off of WebTools if they believe your account is "inactive". Inactive can mean a bunch of things including not using the WebTools API to make a call in a long time, not acknowledging the deprecation changes, and more. If you're using WebTools currently, understand that USPS is set on forcing everyone to the OAuth version (which this module provides).

### What is this OAuth Token? Do I need to get one?

An OAuth token is effectively a (temporary) password meant to provide access to the OAuth API. (Similar to the WebTools API USERID and PASSWORD.)  You do not need to do anything to get one, this script will instead create the token for you (or at least your customer) as their cart requests the estimations of the costs of the USPS services. During checkout, using your API Key and Secret, the cart will request a token for use and then revoke it when it's done with the API call.

### I'm not seeing the USPS Connect rate even though I selected it, what's going on?

USPS Connect rates are only available to retailers who have specifically signed up for it at the [USPS Connect website](https://www.uspsconnect.com) and have their USPS Business Accounts activated to enable USPS Connect. Additionally, you must select and choose to display the "Commercial" rates (formerly called "Online" in the WebTools module) to see the rates while providing a list of Zip Codes that can use Connect Local. If any of these details are missing, you will not see the rate pop up in the quote. Currently, these rates are only available when you're dropping off the packages at the DESTINATION Zip Code. (There is a USPS Connect Regional which does allow you to drop off packages at REGIONAL centers but it is not present here.)

### "The module is not showing at all even though I made my choices"

Make sure that your store is configured to ship out of the United States and that you made sure to enter a five-digit (or nine-digit) Zip Code where your orders are originating from. Additionally, make sure that you have chosen at least one shipping method to display. (This is part of the quotation process. Without these details, the module will fail and self-disable itself.)

### "I clicked the box to offer USPS Large Flat Rate Boxes APO/FPO/DPO but I don't see it as an option during checkout, what gives?"

That rate is only available for packages being sent to a known APO (Air/Army Post Office), DPO (Diplomatic Post Office), or FPO (Fleet Post Office) zip code. If the package's destination zip code is not one of those types of zip codes, the rate will not be offered. To obtain APO/DPO/FPO Flat Rate Boxes, visit the [USPS Postal Store online](https://store.usps.com/store/product/shipping-supplies/priority-mail-flat-rate-apofpo-box-P_MILI_FRB) and request them. (Remember these boxes can ONLY be used for APO/DPO/FPO Mail. If you use them for regular domestic addresses, you may end up having that package returned for insufficient postage.) If there is a valid APO/FPO/DPO address being given and the rate is not offered, please contact me right away. It's likely that the destination zip code was not a known APO, DPO, or FPO when I published this script.

### What is the handling field for?

There are two sets of handling fields. One that can be used on the order as a whole (domestic or international) and one that can be applied on a per-method basis.

The handling field next to the selection of methods is generally for adding a surcharge to certain kinds of shipping methods (or to the entire order, to each "box", or both). If you wish to charge a surcharge for certain kinds of shipping methods, you can enter an amount in the entry box next to the method, and this amount that you enter will be added to the quoted shipping method. If instead, you want to add a surcharge to using USPS as a whole, you would use the single input boxes and not the individual method ones. (Or you can use both.)

### What is the min/max box for?

The original USPS WebTools had a way to clamp the different modules based on the weight of each order. You would put two values into those boxes, and then the rate for that method would be offered if the total weight of the order fell between those two numbers. This is completely optional to use and should be left alone to its defaults if you're not actively using them. (**NOTE:** The number entered here will be converted into pounds, so if you're using kilograms as your standard, enter the amount in kilograms here. If you're using pounds, enter pounds here.)

### Does this module use the Length, Width, and Height boxes of ZC 2.0.0+?

Not at this time. Research is still being done on how to work that into the quote. For now, you should still set those on the product details AND set the "average" package thresholds of this module. A future update will see these included.

### What happened to the ® and ™ symbols that were on the original module?

Those symbols don't appear within the new USPS API calls as they do on the original one. I can modify the script to place them but they might be more trouble than anything.

### My store's measurements are in centimeters and kilograms. Do I have to convert everything?

SORT OF. You don't have the convert anything, but depending on the version of ZenCart you are running, you must make a configuration change.

- Running ZenCart 2.0.0 and newer? You must make sure that your settings in Shipping/Packaging are correct BEFORE installing the module. Namely "Shipping Weight Units" and "Shipping Dimension Units".
- Running ZenCart 1.5.8 or older? You must make a file edit to `/includes/modules/shipping/usps.php`. Around lines 44 and 50, you will see two constant defines that can be edited. Simply follow the instructions there. Be sure to leave single quotation marks and to match the values as listed. (That is, you must enter either `"inches"` or `"centimeters"` (case sensitive) and `kgs` or `lbs` (case sensitive, and no period at the end).)

If you have these two defines set correctly, you do not have to convert anything. The module will take care of everything and will convert to imperial units as necessary.

## Known Limitations/Issues

- As mentioned above in the last FAQ, the registered trademark symbols do not appear in the API results sent from the server. This isn't something I care to fix, although if asked or suggested, I could theoretically put them back in the appropriate places.

## Credits

For the original module

- Ajeh, the original poster of the ZC 1.5 module
- lat9
- The ZenCart Team

For the update

- retched (me)

## File Listing

These are the file lists that should be included with this module, depending on which version you're running.

``` text
- LICENSE
- README.md (this file)
- README.html
- changelog.md
- admin\includes\languages\english\extra_definitions\lang.uspsr.php
- admin\includes\languages\english\extra_definitions\uspsr.php
- includes\languages\english\modules\shipping\lang.uspsr.php
- includes\languages\english\modules\shipping\uspsr.php
- includes\modules\shipping\uspsr.php
- includes\templates\template_default\images\icons\shipping_usps.gif
- \zc_plugins\USPSRestful\v0.0.0\manifest.php
- \zc_plugins\USPSRestful\v0.0.0\admin\includes\languages\english\extra_definitions\lang.uspsr.php
- \zc_plugins\USPSRestful\v0.0.0\catalog\includes\languages\english\modules\shipping\lang.uspsr.php
- \zc_plugins\USPSRestful\v0.0.0\catalog\includes\modules\shipping\uspsr.php
- \zc_plugins\USPSRestful\v0.0.0\catalog\includes\templates\template_default\images\icons\shipping_usps.gif
- \zc_plugins\USPSRestful\v0.0.0\Installer\ScriptedInstaller.php
```

## Support the author
<!-- Should this repository be forked, please remove this section -->

[![Support via CashApp](https://img.shields.io/badge/cashapp-green?style=flat&logo=cashapp&logoColor=white&logoSize=auto&labelColor=black&color=purple&link=https%3A%2F%2Fcash.app%2Fretched)](https://cash.app/$retched) [![Support via PayPal](https://img.shields.io/badge/paypal-blue?style=flat&logo=paypal&logoColor=white&logoSize=auto&labelColor=black&color=purple)](https://paypal.me/retched)  [![Support via Patreon](https://img.shields.io/badge/patreon-white?style=flat&logo=patreon&logoColor=white&labelColor=black&color=purple&link=https%3A%2F%2Fwww.patreon.com%2Fretched)](https://www.patreon.com/retched)  [![Support via BuyMeACoffee](https://img.shields.io/badge/buymeacoffee-white?style=flat&logo=buymeacoffee&logoColor=white&labelColor=black&color=purple&link=https%3A%2F%2Fbuymeacoffee.com%2Fretched)](https://buymeacoffee.com/retched)  [![Support via Kofi](https://img.shields.io/badge/kofi-white?style=flat&logo=buymeacoffee&logoColor=white&labelColor=black&color=purple&link=https%3A%2F%2Fkofi.com%2Fretched)](https://kofi.com/retched)  

## License

``` text
USPS Shipping (RESTful) for Zen Cart
A shipping module for ZenCart, an e-commerce platform
Copyright (C) 2025 Paul Williams (retched / retched@hotmail.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <https://www.gnu.org/licenses/>.
```
