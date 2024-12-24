# USPS Shipping (RESTful) for Zen Cart

![Coded with PHP 8](https://badgen.net/badge/icon/php?icon=php&label&color=purple)  ![Licensed via GPL-3.0](https://badgen.net/github/license/retched/ZC-USPSRestful?color=purple)  ![Last Commit](https://badgen.net/github/last-commit/retched/ZC-USPSRestful?color=purple)  ![Latest Release](https://badgen.net/github/release/retched/ZC-USPSRestful/stable?color=purple)

This module provides sellers a chance to offer United States Postal Service (USPS) shipping rates to customers during checkout. This is done by pulling the rates directly from the USPS' REST API.

This module supports versions 1.5.8 onward innately. (Support from 1.5.7 and backward is not necessarily guaranteed but is plausible. Read the Installation steps below for more details.) This script was primarily written with PHP8 in mind. (It might have problems working with PHP7.)

## Current Version: 0.1.0

This is the initial version of the new USPS RESTful Module for ZenCart.

## Version History

- _0.0.0
    Nothing. This is a placeholder for the module and should NOT be downloaded nor installed._

- 0.1.0
    First "release".

## Additional Links

[USPS API Documentation](https://developers.usps.com/apis)
_This API takes advantage of four API's: Domestic Prices 3.0, International Prices 3.0, Service Standards 3.0, and OAuth 3.0._

[ZenCart Plugins Download](https://www.zen-cart.com/downloads.php?do=file&id=2395) (or use the Releases function on the GitHub repository)  
[ZenCart Support Thread](https://www.zen-cart.com/showthread.php?230512-USPS-Shipping-(RESTful)-(USPSr))

** This above thread is only for THIS version of the USPS module, for assistance with the original USPS module which uses the WebTools API, you should post it here in [its megathread](https://www.zen-cart.com/showthread.php?227284-USPS-Shipping-Module-Support-Thread).

## Setup and Install

### ZenCart 1.5.8 and above

This module is an encapsulated plugin. You can take the contents of the zc_plugins Directory (which is just one folder named `USPSRestful`) and upload it into the same directory in the ROOT of your ZenCart install. Once uploaded, open your ZenCart dashboard backend and visit `Modules > Plugin Manager`. Find the USPS Restful entry and click `Install`. You should then be able to proceed to the Shipping Module manager (`ADMIN > Modules > Shipping`) and find an entry for "United States Postal Service (RESTful)" (code: `uspsr`) and then click Install. From there, simply provided the details requested, and customize the module to your content. Make sure to provide your API Credentials AND select at least ONE service.

### ZenCart 1.5.7 and before

Backwards compatibility with earlier ZenCart's is not guaranteed. You're welcome to try to use the module on earlier ZenCart versions but you cannot use the Plugin Manager directly. (Unless you make various changes to your core code.) Instead, you can extract the files from the folders  listing below and upload them to the appropriate places in your installation. To activate the module for use, simply visit the Shipping Module manager (again: `ADMIN > Modules > Shipping`). Customize your module to your content.

### Uninstallation

If you installed this module through the Plugin Manager, you should then be able to click the `Un-Install` button. If you do not plan on using the module again, you should delete the module's folder from the `zc_plugins` folder or click the "Clean Up" button to handle it for you. 

If you installed this module by extracting the uspsr.php and other files into your catalog directory, visit `Modules > Shipping` and disable the USPSr module **FIRST** before removing the module and its files. (This way you can make sure that you aren't still trying to load it and generating errors in ZenCart.) Use the filelist below as a checklist to make sure you uninstall. (If you plan on going back to WebTools API, make sure that you do **NOT** delete the logo file found in `/includes/templates/template_default/images/icons/shipping_usps.gif`) or you can overwrite it.

### Upgrading

To update the module, copy the entire folder onto itself. If you are using the Encapsulated version, be sure to visit the plugin manager and run the "Upgrade" option when prompted. Your database settings will be fine. If you're running the non-encapsulated version, simply overwrite all files in the appropriate directories.

## Frequently Asked Questions

This won't answer all the questions you may have, but it may answer some that I thought of.

### What is the difference between this version and the original USPS module?

The original USPS module works by using the older USPS Web Tools API. For years, that API was the defacto API in use when it came to retrieving the estimated shipping costs of the USPS' various services as well as the estimate times of delivery. In 2024, the USPS began deprecating the Web Tools API and they will be out of service fully in 2026. The Web Tools API is being replaced with the new USPS API that takes advantage of OAuth tokens which this codebase uses.

### I already have a `USERID` and `PASSWORD` under the old system, but I'm getting error messages while I try to retrieve quotes.

The older `USERID` and `PASSWORD` are not valid for the new system. You will need to provision new credentials under the new USPS API system. Additionally, you SHOULD create an entire new USPS Business Account provided that you don't already have one for your business. The process is explained [here](https://developers.usps.com/getting-started).

### Why should I use this versus the one that's out there now?

The USPS created an "in-before-the-lock" situation with regards to the API Tools. They will, seldomly, allow access to the API by way of manually granting access but they will read you the "Riot Act" with regards to enabling them. If you are still using the Web Tools API and have no issues accessing or using them, continue to use them. But know that in roughly 2026 (possibly sooner), the older API's will be completely disabled and at that point everyone will have to use the RESTful version of the API going forward. This is just a head start to that process.

### What is this OAuth Token? Do I need to get one?

An OAuth token is a unique string of characters that allows a third-party application (like this one) to access a user's data without exposing their password. Effectively making it a temporary (generated) password of sorts. You do not need to do anything to get one, this script will instead create the token for you (or at least your customer) as their cart requests the estimations of cost of the USPS services. During checkout, using your API Key and Secret, the cart will request a token for use and then revoke it when it's done with the API call.

### I'm not seeing the USPS Connect rate even though I selected it, what's going on?

USPS Connect rates are only available to retailers who have specifically signed up for it at https://www.uspsconnect.com and have their USPS Business Accounts activated to enable USPS Connect. Additionally, you must select and choose to display the "Commercial" rates (formerly called "Online" in the other module) to actually see the rates while providing a list of Zip Codes that can use Connect Local. If any of these details are missing, you will not see the rate pop up in the quote. Currently, these rates are only available when you're dropping off the packages at the DESTINATION Zip Code. (There is a USPS Connect Regional which does allow you to drop off packages at REGIONAL centers but it is not present here to be activated.)

### "The module is not showing at all even though I made my choices"

Make sure that you store is configured to ship out of the United States and that you made sure to enter a Zip Code where your orders are originating from. Additionally, make sure that you have chosen shipping methods to display. (This is part of the quotation process. Without these details, the module will fail and self-disable itself.)

### "I clicked the box to offer USPS Large Flat Rate Boxes APO/FPO/DPO but I don't see it as an option during checkout, what gives?"

That rate is only available for packages being sent to a known APO (Air/Army Post Office), DPO (Diplomatic Post Office), or FPO (Fleet Post Office) zip code. If the packages destination is not to one of those types of zip codes, the rate will not be offered. To obtain APO/DPO/FPO Flat Rate Boxes, visit the [USPS Postal Store online](https://store.usps.com/store/product/shipping-supplies/priority-mail-flat-rate-apofpo-box-P_MILI_FRB) and request them. (Remember these boxes can ONLY be used for APO/FPO Mail. If you use them for regular domestic addresses, you may end up having that package returned for insufficient postage.) If there is a valid APO/FPO/DPO address being given and the rate is not offered, please contact me right away. It's likely that the destination zip code was not a known APO, DPO, or FPO when I published this script.

### What is the handling field for? Where are the min/max fields of the original USPS module?

There are two sets of handling fields. One that can be used on the order as a whole (domestic or international) and one that can be applied on a per method basis.

The handling field of the methods selection is generally for adding a surcharge to certain kinds of shipping methods (or to the entire order, to each "box", or both). If you wish to charge a surcharge for certain kinds of shipping methods, you can enter an amount in the entry box next to the method and this amount that you enter will be added onto the quoted shipping method. If instead you want to add a surcharge to using USPS as a whole, you would use the single input boxes and not the individual method ones. (Or you can use both.)

The original module had a set of Min/Max which restrained which methods were available to use based on the weight. For example, if you entered a maximum of six pounds to Priority Mail, the method would only be offered if the total weight is under six pounds. (The USPS also limits still constrained the order, no matter what.) For right now, those fields are not present in this version of this module but may be present in a future update.

### Does this module use the Length, Width, and Height boxes of ZC 2.0.0+?

Not at this time. Research is still being done on how to work that in to the quote. For now, you should still set those on the product details AND set the "estimated" package thresholds of this module. A future update will see these included.

### What happened to the ® and ™ symbols that were on the original module?

Those symbols don't appear within the new USPS API calls like they do on the original one. I can modify the script to place them but they might be more trouble than anything.

### My store's measurements are in centimeters and kilograms, do I have to convert everything?

**DEPENDS**. This script will look for the setting of `SHIPPING_WEIGHT_UNITS` and `SHIPPING_DIMENSION_UNITS` in the admin backarea.

If these settings are present:

- On installation of the module, the script will convert the "default" box measurements to the measurement of the cart. If you chose to measure in centimeters, the script will multiply all measurements by 2.54 and will set those as the default size. During checkout, any number input there will be divided by 2.54 to reverse from centimeters to inches. That number will be sent along with the order details to make up the quote. If it's in inches, no conversion will be done.
- During checkout with a cart configured with kilograms as the cart's weight, the total weight of the cart will be divided by (approximately) 2.205 to obtain the total number of pounds and will dispatch that as part of the quoting process. 

The USPS API needs the size and weight values to be sent in imperial units.

If these settings are NOT present or are not a part of the typical ZC installation:

- Yes. You will have to convert everything MANUALLY, on your own, to be pounds. This means that if you set up your store as to ship out using kgs and cms, by way of brute force or other means, you will have to set everything back up as pounds and inches as this script will not be able to pick it up. (This script will dispatch a quote request to the USPS assuming everything is in pounds and inches already.)

## Known Limitations/Issues

- For some reason, the API does not return rates for First-Class Mail International Package Service when one of the selected add-ons is insurance or if there is a declared value. To that end, the script will not present the Insurance option to the API for calculation by default. This can be overridden by commenting out the line 1291 and uncommenting line 1300.
- As mentioned above in the last FAQ, the registered trademark symbols do not appear in the API results sent from the server. This isn't something I care to fix although if asked or suggested, I could theoretically put them back in the appropriate places.
- Trying to visit `cmd=configuration&gID=6` while this module is active, will cause that admin configurator to break. This is likely because the display functions uses custom functions that are cooked directly into the modules file itself and not loaded separately into a separate functions file. This will likely be fixed in a future version by moving the functions being referenced to a separate functions file. If you do need to visit that particular view while this module is installed, it is recommended that you disable and remove the module (not via the plugin manager but the shipping modules manager) to view what you need and then when you're ready, reenable it.
- Not all of the Observers/Notifier triggers made it here from the original USPS module. I kind of eye-balled this and tried to place the original triggers and observers where I best-guessed they fit in at. But I'm not going to lie, I'm not too confident I got them all or even applied them in the correct manner. If you are a developer/siteowner and you used one or more of the notifiers/observers classes that I missed, please feel free to reach out to me via the ZenCart forums PM system or the ZenCart thread linked above. (Missing about six of them as of this release, but I'll pass through and readd them as I can.)

## Credits

For the orignal module

- Ajeh, original poster of the ZC 1.5 module
- lat9
- The ZenCart Team

For the update

- retched (me)

## File Listing

``` text
- CONTRIBUTING.md
- LICENSE
- README.md (this file)
- changelog.md
- \zc_plugins\USPSRestful\v0.1.0\manifest.php
- \zc_plugins\USPSRestful\v0.1.0\catalog\includes\languages\english\modules\shipping\lang.uspsr.php
- \zc_plugins\USPSRestful\v0.1.0\catalog\includes\modules\shipping\uspsr.php
- \zc_plugins\USPSRestful\v0.1.0\catalog\includes\templates\template_default\images\icons\shipping_usps.gif
- \zc_plugins\USPSRestful\v0.1.0\Installer\ScriptedInstaller.php
- \zc_plugins\USPSRestful\v0.1.1\manifest.php
- \zc_plugins\USPSRestful\v0.1.1\catalog\includes\languages\english\modules\shipping\lang.uspsr.php
- \zc_plugins\USPSRestful\v0.1.1\catalog\includes\modules\shipping\uspsr.php
- \zc_plugins\USPSRestful\v0.1.1\catalog\includes\templates\template_default\images\icons\shipping_usps.gif
- \zc_plugins\USPSRestful\v0.1.1\Installer\ScriptedInstaller.php
```

## Support the author
<!-- Should this repository be forked, please remove this section -->

[![Support via CashApp](https://img.shields.io/badge/cashapp-green?style=flat&logo=cashapp&logoColor=white&logoSize=auto&labelColor=black&color=purple&link=https%3A%2F%2Fcashapp.com%2Fretched)](https://cashapp.com/$retched) [![Support via PayPal](https://img.shields.io/badge/paypal-blue?style=flat&logo=paypal&logoColor=white&logoSize=auto&labelColor=black&color=purple)](https://paypal.com/retched)  [![Support via Patreon](https://badgen.net/badge/icon/patreon?icon=patreon&label&color=purple)](https://www.patreon.com/retched)  [![Support via BuyMeACoffee](https://badgen.net/badge/icon/buymeacoffee?icon=buymeacoffee&label&color=purple)](https://buymeacoffee.com/retched)  [![Support via Kofi](https://badgen.net/badge/icon/kofi?icon=kofi&label&color=purple)](https://kofi.com/retched)  

## License

``` txt
USPS Shipping (RESTful) for Zen Cart
A shipping module for ZenCart, an ecommerce platform
Copyright (C) 2024  Paul Williams (retched / retched@hotmail.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>.
```
