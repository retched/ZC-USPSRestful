# USPS Shipping (RESTful) for Zen Cart

![ZC-USPSRestful](https://socialify.git.ci/retched/ZC-USPSRestful/image?custom_description=This+module+provides+sellers+the+ability+to+offer+US+Postal+Service+shipping+rates+to+their+customers+during+checkout.&description=1&font=Inter&forks=1&issues=1&language=1&name=1&owner=0&pattern=Signal&pulls=1&stargazers=1&theme=Auto)

![Coded with PHP](https://img.shields.io/badge/php-purple?style=flat&logo=php&logoColor=white&labelColor=black)  ![License via GPL 3.0](https://img.shields.io/badge/license-GPL-black?style=flat&logoColor=black&label=license&labelColor=black&color=851185) ![Last Commit](https://badgen.net/github/last-commit/retched/ZC-USPSRestful?color=851185&labelColor=white)  ![Latest Release](https://badgen.net/github/release/retched/ZC-USPSRestful/stable?color=851185&labelColor=black&labelColor=white)

This module provides ZenCart sellers the ability to offer United States Postal Service (USPS) shipping rates to their customers during checkout. This is done by pulling the rates directly from the USPS RestAPI.

This module will work with the most recent versions of ZenCart using PHP 7 or PHP 8. It has been tested with Zencart 1.5.5 and onward up to 2.1.0.

## Module Version

- Last Stable Release: 1.1.0  
_Released February 22, 2025 for ZenCart 2.1.0._
- Next Version Number: 1.1.1

### Version/Release History

- ???  
  Minor bug fixes with regards to the selection of cheapest shipping method. Also fixed a conflict with OPC that prevent any method from being selected. Additionally, fixed an issue with regards to shipping method squashing.
- 1.1.0  
  Added in plugin version check. (Will ping the ZenCart database to see if there is a new version available.) Now when you update the module, you no longer need to reset the module as a whole, the module will automatically whatever missing database keys and configs there are. Added ability to squash Ground Advantage Cubic and Ground Advantage rates to the lower one, same with Priority Mail Cubic and Priority Mail rates.
- 1.0.0  
 A LOT of changes. Including re-adding the min/max weight boxes from USPS, fixing the display of rates and quotes, adding error messages in the backend, fixing bad API returns, cleaned up the repository as a whole. Changes to allow the module to work with PHP8 and PHP7-based ZenCarts. (At least ZenCart 1.5.x or ZenCart 2.x and newer.)
- 0.2.0  
 Various bugfixing including the reintroduction of First Class Mail Package International Service to the quote pool.
- 0.1.0  
 First "release".
- 0.0.0  
  _Development version. This version contains an incomplete thought and should not be used in production unless you are sure what you are doing. The version from the ZenCart Module database [was only a placeholder](https://www.zen-cart.com/showthread.php?230478-pluginID-of-yet-to-be-submitted-encapsulated-plugin). Pulling from the Repository is not recommended._

## Additional Links

- [USPS API Documentation](https://developers.usps.com/apis) - This API takes advantage of four APIs: _Domestic Prices 3.0, International Prices 3.0, Service Standards 3.0, and OAuth 3.0._
- [ZenCart Plugins Directory Listing](https://www.zen-cart.com/downloads.php?do=file&id=2395) (or use the Releases function on the GitHub repository)
- [ZenCart Support Thread](https://www.zen-cart.com/showthread.php?230512-USPS-Shipping-(RESTful)-(USPSr)) - This thread is only for THIS version of the USPS module, for assistance with the original USPS module which uses the WebTools API, you should post it here in [its megathread](https://www.zen-cart.com/showthread.php?227284-USPS-Shipping-Module-Support-Thread).

## Setup, Install, and Upgrading

Both versions (encapsulated and non-encapsualted) are now shared in the same release file on ZenCart. (The GitHub repository will still have a separated file.)

- **Non-encapsulated** (ZC 1.5.5+)  
  If you want to install the non-encapsulated version of the module, copy **ONLY** the `admin/` and `includes/` directory in the root of the zip file to the matching directories in the root of your ZenCart installation. (**NOTE:** Be sure to rename the `admin/` directory to match your admin directory in your ZenCart installation. DO NOT copy the `zc_plugins/` directory.)

- **Encapsulated** (ZC 2.1.0 or ZC 2.0.x [with these modifications](https://gist.github.com/lat9/9deb64d3325081d18bb0db5534bcf142))  
  If you want to install the encapsulated version of the module, copy **ONLY** the contents of the `zc_plugins` directory into the matching `zc_plugins` directory of your ZenCart installation.

You can find the full instructions to install the module, including how to obtain your USPS API credentials, by reading the [related wiki page](https://github.com/retched/ZC-USPSRestful/wiki/Getting%20Started#installing) from the Github repository.

Upgrading the non-encapsulated version? **Overwrite ALL files of the old version.** (If you're using the encapsulated version, it is safe to upload the new version which exists in a separate folder.)

## Uninstallation

- **Non-encapsulated**  
  To uninstall the non-encapsulated version, first uninstall the module from your Shipping Modules page if it was active. Then delete each of the files listed below.

- **Encapsulated**  
  To uninstall the encapsulated version, simply visit your Plugin Manager (in the ZenCart admin area), then click on the row for USPS Restful, finally click "Uninstall". If desired, you can clean up the installation to have ZenCart delete the files for you.

## Frequently Asked Questions

This won't answer all the questions you may have, but it may answer some that I thought of.

### What version of ZenCart does this module support?

That can be answered with this chart:

|               |    Encapsulated    |  Non-Encapsulated  |
|---------------|:------------------:|:------------------:|
| ZenCart 1.5.5 |         :x:        | :white_check_mark: |
| ZenCart 1.5.6 |         :x:        | :white_check_mark: |
| ZenCart 1.5.7 |         :x:        | :white_check_mark: |
| ZenCart 1.5.8 |         :x:        | :white_check_mark: |
| ZenCart 2.0.0 |      :wrench:      | :white_check_mark: |
| ZenCart 2.0.1 |      :wrench:      | :white_check_mark: |
| ZenCart 2.1.0 | :white_check_mark: | :white_check_mark: |

* :white_check_mark: = Fully supported
* :x: = Not supported
* :wrench: = Can work but will need [core file edits](https://gist.github.com/lat9/9deb64d3325081d18bb0db5534bcf142) to make it work

### What is the difference between this version and the original USPS module?

The original USPS module works by using the older USPS WebTools API. For years, that API was the defacto API in use when it came to retrieving the estimated shipping costs of the USPS' various services as well as the estimated times of delivery. In 2024, the USPS began deprecating the Web Tools API. In 2025, the USPS announced the WebTools API will be fully out of service in 2026. The Web Tools API is being replaced with the new OAuth-based API which this codebase uses.

### I already have a `USERID` and `PASSWORD` from WebTools, but I'm getting error messages while I try to retrieve quotes. What happened?

The older `USERID` and `PASSWORD` from the WebTools API are not valid for the new system. You will need to provision new credentials under the new USPS API system. Additionally, you SHOULD create an entire new USPS Business Account provided that you don't already have one for your business. The process is explained [here](https://developers.usps.com/getting-started) and on the [related wiki page](https://github.com/retched/ZC-USPSRestful/wiki/Getting%20Started#installing) from the Github repository. If you end up not getting quotes or the module disables itself, check to make sure that you are using actual OAuth Credentials and not the old WebTools API. Additionally, make sure you have configured your cart to ship from the United States and a zipcode.

### Why should I use this version versus the one that's out there now?

The USPS created an "in-before-the-lock" situation concerning the original WebTools API. They will still allow access to the API by way of manually granting access but they will read you the "riot act" with regards to enabling them. If you are still using the Web Tools API and have no issues accessing or using them, continue to use them. But know that in 2026, the older WebTools API will be completely disabled, and at that point, everyone will have to use the RESTful version of the API going forward.

### What is this OAuth Token? Do I need to get one?

An OAuth token is effectively a (temporary) password meant to provide access to the OAuth API. (Similar to the WebTools API USERID and PASSWORD.)  You do not need to do anything to get one, this script will instead create the token for you (or at least your customer) as their cart requests the estimations of the costs of the USPS services. During checkout, using your API Key and Secret, the cart will request a token for use and then revoke it when it's done with the API call.

### I'm not seeing the USPS Connect rate even though I selected it, what's going on?

USPS Connect rates are only available to retailers who have specifically signed up for it at https://www.uspsconnect.com and have their USPS Business Accounts activated to enable USPS Connect. Additionally, you must select and choose to display the "Commercial" rates (formerly called "Online" in the WebTools module) to see the rates while providing a list of Zip Codes that can use Connect Local. If any of these details are missing, you will not see the rate pop up in the quote. Currently, these rates are only available when you're dropping off the packages at the DESTINATION Zip Code. (There is a USPS Connect Regional which does allow you to drop off packages at REGIONAL centers but it is not present here.)

### "The module is not showing at all even though I made my choices"

Make sure that your store is configured to ship out of the United States and that you made sure to enter a five-digit (or nine-digit) Zip Code where your orders are originating from. Additionally, make sure that you have chosen at least one shipping method to display. (This is part of the quotation process. Without these details, the module will fail and self-disable itself.)

### "I clicked the box to offer USPS Large Flat Rate Boxes APO/FPO/DPO but I don't see it as an option during checkout, what gives?"

That rate is only available for packages being sent to a known APO (Air/Army Post Office), DPO (Diplomatic Post Office), or FPO (Fleet Post Office) zip code. If the package's destination zip code is not one of those types of zip codes, the rate will not be offered. To obtain APO/DPO/FPO Flat Rate Boxes, visit the [USPS Postal Store online](https://store.usps.com/store/product/shipping-supplies/priority-mail-flat-rate-apofpo-box-P_MILI_FRB) and request them. (Remember these boxes can ONLY be used for APO/DPO/FPO Mail. If you use them for regular domestic addresses, you may end up having that package returned for insufficient postage.) If there is a valid APO/FPO/DPO address being given and the rate is not offered, please contact me right away. It's likely that the destination zip code was not a known APO, DPO, or FPO when I published this script.

### What is the handling field for?

There are two sets of handling fields. One that can be used on the order as a whole (domestic or international) and one that can be applied on a per-method basis.

The handling field next to the selection of methods is generally for adding a surcharge to certain kinds of shipping methods (or to the entire order, to each "box", or both). If you wish to charge a surcharge for certain kinds of shipping methods, you can enter an amount in the entry box next to the method and this amount that you enter will be added to the quoted shipping method. If instead, you want to add a surcharge to using USPS as a whole, you would use the single input boxes and not the individual method ones. (Or you can use both.)

### What is the min/max box for?

**NEW** The original USPS WebTools had a way to clamp the different modules based on the weight of each order. You would put two values into those boxes and then the rate for that method would be offered if the total weight of the order fell between those two numbers. This is completely optional to use and should be left alone to its defaults if you're not actively using them. (**NOTE:** The number entered here will be converted into pounds, so if you're using kilograms as your standard, enter the amount in kilograms here. If you're using pounds, enter pounds here.)

### Does this module use the Length, Width, and Height boxes of ZC 2.0.0+?

Not at this time. Research is still being done on how to work that into the quote. For now, you should still set those on the product details AND set the "average" package thresholds of this module. A future update will see these included.

### What happened to the ® and ™ symbols that were on the original module?

Those symbols don't appear within the new USPS API calls as they do on the original one. I can modify the script to place them but they might be more trouble than anything.

### My store's measurements are in centimeters and kilograms, do I have to convert everything?

SORT OF. You don't have the convert anything, but depending on the version of ZenCart you are running, you must make a configuration change.

- Running ZenCart 2.0.0 and newer? You must make sure that your settings in Shipping/Packaging are correct BEFORE installing the module. Namely "Shipping Weight Units" and "Shipping Dimension Units".
- Running ZenCart 1.5.8 or older? You must make a file edit to `/includes/modules/shipping/usps.php`. Around lines 48 and 54, you will see two constant defines that can be edited. Simply follow the instructions there. Be sure to leave single quotation marks and to match the values as listed. (That is you must enter either `"inches"` or `"centimeters"` (case sensitive) and `kgs` or `lbs` (case sensitive, no period).)

If you have these two defines set correctly, you do not have to convert anything. The module will take care of everything and will convert to imperial units as necessary.

## Known Limitations/Issues

- As mentioned above in the last FAQ, the registered trademark symbols do not appear in the API results sent from the server. This isn't something I care to fix although if asked or suggested, I could theoretically put them back in the appropriate places.
- Trying to visit `cmd=configuration&gID=6` while this module is active, will cause that admin configurator to break. This is likely because the display functions use custom functions that are cooked directly into the modules file itself and not loaded separately into a separate functions file. This will likely be fixed in a future version by moving the functions being referenced to a separate functions file. If you do need to visit that particular view while this module is installed, it is recommended that you disable and remove the module (not via the plugin manager but the shipping modules manager) to view what you need and then when you're ready, reenable it.
- Not all of the Observers/Notifier triggers made it here from the original USPS module. I eye-balled this and tried to place the original triggers and observers where I best guessed they fit in. But I'm not going to lie, I'm not too confident I got them all or even correctly applied them. If you are a developer/site owner and you used one or more of the notifiers/observers classes that I missed, please feel free to reach out to me via the ZenCart forums PM system or the ZenCart thread linked above. (I'm missing about six of them as of this release, but I'll pass through and re-add them as I can.)

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
- CONTRIBUTING.md
- LICENSE
- README.md (this file)
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
