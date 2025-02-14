# USPS Shipping (RESTful) for Zen Cart

![ZC-USPSRestful](https://socialify.git.ci/retched/ZC-USPSRestful/image?custom_description=This+module+provides+sellers+the+ability+to+offer+US+Postal+Service+shipping+rates+to+their+customers+during+checkout.&description=1&font=Inter&forks=1&issues=1&language=1&name=1&owner=0&pattern=Signal&pulls=1&stargazers=1&theme=Auto)

![Coded with PHP](https://img.shields.io/badge/php-purple?style=flat&logo=php&logoColor=white&labelColor=black)  ![License via GPL 3.0](https://img.shields.io/badge/license-GPL-black?style=flat&logoColor=black&label=license&labelColor=black&color=851185) ![Last Commit](https://badgen.net/github/last-commit/retched/ZC-USPSRestful?color=851185&labelColor=white)  ![Latest Release](https://badgen.net/github/release/retched/ZC-USPSRestful/stable?color=851185&labelColor=black&labelColor=white)

This module provides ZenCart sellers the ability to offer United States Postal Service (USPS) shipping rates to their customers during checkout. This is done by pulling the rates directly from the USPS RestAPI.

This module supports ZenCart versions 1.5.8 onward innately. (Support for 1.5.7 and backward is not necessarily guaranteed but is plausible. Read the Installation steps below for more details.) This script was primarily written with PHP8 in mind. (It might have problems working with PHP7.)

## Version

Last Stable Release: 0.2.0  
Released January 17, 2025 for ZenCart 2.1.0. (Has been tested with ZenCart 2.0.0 and ZenCart 2.1.0)

Current Developmental Version: 0.3.0  
_- In Development -_

### Version History

- 0.0.0  
  _Nothing. This was a placeholder for the module to obtain the plugin ID for the encapsulated version. This version should not be downloaded as it only contains a simplified README\.md._
- 0.1.0  
  First "release".
- 0.2.0  
  Various bugfixing including the reintroduction of First Class Mail Package International Service to the quote pool.
- 0.3.0 _(in development)_  
  Improved debugging messaging in backend and disabling the module when invalid credentials are entered.

## Additional Links

- [USPS API Documentation](https://developers.usps.com/apis) - This API takes advantage of four APIs: _Domestic Prices 3.0, International Prices 3.0, Service Standards 3.0, and OAuth 3.0._
- [ZenCart Plugins Directory Listing](https://www.zen-cart.com/downloads.php?do=file&id=2395) (or use the Releases function on the GitHub repository)
- [ZenCart Support Thread](https://www.zen-cart.com/showthread.php?230512-USPS-Shipping-(RESTful)-(USPSr)) - This thread is only for THIS version of the USPS module, for assistance with the original USPS module which uses the WebTools API, you should post it here in [its megathread](https://www.zen-cart.com/showthread.php?227284-USPS-Shipping-Module-Support-Thread).

## Setup, Install, and Upgrading

You can find full instructions to install the module by reading the [related wiki page](https://github.com/retched/ZC-USPSRestful/wiki/Getting%20Started#installing)

## Uninstallation

If you installed this module through the Plugin Manager, you should then be able to click the `Un-Install` button as it appears in the Plugin Manager. (The module will be disabled and removed.) If you do not plan on using the module again, you should delete the module's folder from the `zc_plugins` folder or click the "Clean Up" button to handle it for you.

If you are running this as an unencapsulated module, visit `Modules > Shipping` and disable the USPSr module **FIRST** before removing the module and its files. (This way you can make sure that you aren't still trying to load it and generating errors in ZenCart.) Use the file list below as a checklist to make sure you uninstall. (If you plan on going back to WebTools API, make sure that you do **NOT** delete the logo file found in `/includes/templates/template_default/images/icons/shipping_usps.gif` or you can overwrite it.)

## Frequently Asked Questions

This won't answer all the questions you may have, but it may answer some that I thought of.

### What is the difference between this version and the original USPS module?

The original USPS module works by using the older USPS Web Tools API. For years, that API was the defacto API in use when it came to retrieving the estimated shipping costs of the USPS' various services as well as the estimated times of delivery. In 2024, the USPS began deprecating the Web Tools API and they will be out of service fully in 2026. The Web Tools API is being replaced with the new USPS API that takes advantage of OAuth tokens which this codebase uses.

### I already have a `USERID` and `PASSWORD` under the old system, but I'm getting error messages while I try to retrieve quotes.

The older `USERID` and `PASSWORD` are not valid for the new system. You will need to provision new credentials under the new USPS API system. Additionally, you SHOULD create an entire new USPS Business Account provided that you don't already have one for your business. The process is explained [here](https://developers.usps.com/getting-started). If you end up not getting quotes or the module disables itself, check to make sure that you are using actual OAuth Credentials and not the old WebTools API.

### Why should I use this versus the one that's out there now?

The USPS created an "in-before-the-lock" situation concerning the original WebTools API. They will, seldomly, allow access to the API by way of manually granting access but they will read you the "Riot Act" with regards to enabling them. If you are still using the Web Tools API and have no issues accessing or using them, continue to use them. But know that in roughly 2026 (possibly sooner), the older APIs will be completely disabled and at that point, everyone will have to use the RESTful version of the API going forward. This is just a head start to that process.

### What is this OAuth Token? Do I need to get one?

An OAuth token is a unique string of characters that allows a third-party application (like this one) to access a user's data without exposing their password. Effectively making it a temporary (generated) password of sorts. You do not need to do anything to get one, this script will instead create the token for you (or at least your customer) as their cart requests the estimations of the cost of the USPS services. During checkout, using your API Key and Secret, the cart will request a token for use and then revoke it when it's done with the API call.

### I'm not seeing the USPS Connect rate even though I selected it, what's going on?

USPS Connect rates are only available to retailers who have specifically signed up for it at https://www.uspsconnect.com and have their USPS Business Accounts activated to enable USPS Connect. Additionally, you must select and choose to display the "Commercial" rates (formerly called "Online" in the other module) to see the rates while providing a list of Zip Codes that can use Connect Local. If any of these details are missing, you will not see the rate pop up in the quote. Currently, these rates are only available when you're dropping off the packages at the DESTINATION Zip Code. (There is a USPS Connect Regional which does allow you to drop off packages at REGIONAL centers but it is not present here to be activated.)

### "The module is not showing at all even though I made my choices"

Make sure that your store is configured to ship out of the United States and that you made sure to enter a five digit Zip Code where your orders are originating from. Additionally, make sure that you have chosen shipping methods to display. (This is part of the quotation process. Without these details, the module will fail and self-disable itself.)

### "I clicked the box to offer USPS Large Flat Rate Boxes APO/FPO/DPO but I don't see it as an option during checkout, what gives?"

That rate is only available for packages being sent to a known APO (Air/Army Post Office), DPO (Diplomatic Post Office), or FPO (Fleet Post Office) zip code. If the package's destination zip code is not one of those types of zip codes, the rate will not be offered. To obtain APO/DPO/FPO Flat Rate Boxes, visit the [USPS Postal Store online](https://store.usps.com/store/product/shipping-supplies/priority-mail-flat-rate-apofpo-box-P_MILI_FRB) and request them. (Remember these boxes can ONLY be used for APO/FPO Mail. If you use them for regular domestic addresses, you may end up having that package returned for insufficient postage.) If there is a valid APO/FPO/DPO address being given and the rate is not offered, please contact me right away. It's likely that the destination zip code was not a known APO, DPO, or FPO when I published this script.

### What is the handling field for? Where are the min/max fields of the original USPS module?

There are two sets of handling fields. One that can be used on the order as a whole (domestic or international) and one that can be applied on a per-method basis.

The handling field next to the selection of methods is generally for adding a surcharge to certain kinds of shipping methods (or to the entire order, to each "box", or both). If you wish to charge a surcharge for certain kinds of shipping methods, you can enter an amount in the entry box next to the method and this amount that you enter will be added to the quoted shipping method. If instead, you want to add a surcharge to using USPS as a whole, you would use the single input boxes and not the individual method ones. (Or you can use both.)

The original module had a set of Min/Max which restrained which methods were available to use based on the weight. For example, if you entered a maximum of six pounds for the Priority Mail method, the method would only be offered if the total weight is under six pounds. (The USPS limits still apply to the order, no matter what. 

For example, if you entered 80 pounds as the limit for Priority Mail, the method wouldn't be offered as USPS Priority Mail's limit is 70 pounds.) 

For right now, those fields are not present in this version of this module but may be present in a future update.

### Does this module use the Length, Width, and Height boxes of ZC 2.0.0+?

Not at this time. Research is still being done on how to work that into the quote. For now, you should still set those on the product details AND set the "estimated" package thresholds of this module. A future update will see these included.

### What happened to the ® and ™ symbols that were on the original module?

Those symbols don't appear within the new USPS API calls as they do on the original one. I can modify the script to place them but they might be more trouble than anything.

### My store's measurements are in centimeters and kilograms, do I have to convert everything?

**DEPENDS**. This script will look for the setting of `SHIPPING_WEIGHT_UNITS` and `SHIPPING_DIMENSION_UNITS` in the admin back area.

If these settings are present:

- On installation of the module, the script will convert the "default" box measurements to the measurement of the cart. If you choose to measure in centimeters, the script will multiply all measurements by 2.54 and will set those as the default size. During checkout, any number input there will be divided by 2.54 to reverse from centimeters to inches. That number will be sent along with the order details to make up the quote. If it's in inches, no conversion will be done.
- During checkout with a cart configured with kilograms as the cart's weight, the total weight of the cart will be divided by (approximately) 2.205 to obtain the total number of pounds and will dispatch that as part of the quoting process.

The USPS API needs the size and weight values to be sent in imperial units.

If these settings are NOT present or are not a part of the typical ZC installation:

- Yes. You will have to convert everything MANUALLY, on your own, to be pounds. This means that if you set up your store to ship out using kilograms and centimeters, by way of brute force or other means, you will have to set everything back up as pounds and inches as this script will not be able to pick it up. (This script will dispatch a quote request to the USPS assuming everything is in pounds and inches already.)

## Known Limitations/Issues

- As mentioned above in the last FAQ, the registered trademark symbols do not appear in the API results sent from the server. This isn't something I care to fix although if asked or suggested, I could theoretically put them back in the appropriate places.
- Trying to visit `cmd=configuration&gID=6` while this module is active, will cause that admin configurator to break. This is likely because the display functions use custom functions that are cooked directly into the modules file itself and not loaded separately into a separate functions file. This will likely be fixed in a future version by moving the functions being referenced to a separate functions file. If you do need to visit that particular view while this module is installed, it is recommended that you disable and remove the module (not via the plugin manager but the shipping modules manager) to view what you need and then when you're ready, reenable it.
- Not all of the Observers/Notifier triggers made it here from the original USPS module. I kind of eye-balled this and tried to place the original triggers and observers where I best guessed they fit in. But I'm not going to lie, I'm not too confident I got them all or even correctly applied them. If you are a developer/site owner and you used one or more of the notifiers/observers classes that I missed, please feel free to reach out to me via the ZenCart forums PM system or the ZenCart thread linked above. (Missing about six of them as of this release, but I'll pass through and re-add them as I can.)

## Credits

For the original module

- Ajeh, the original poster of the ZC 1.5 module
- lat9
- The ZenCart Team

For the update

- retched (me)

## File Listing

These are the file lists that should be included with this module, depending on which version you're running.

### Encapsulated File Listing

``` text
- CONTRIBUTING.md
- LICENSE
- README.md (this file)
- changelog.md
- \zc_plugins\USPSRestful\v0.3.0\manifest.php
- \zc_plugins\USPSRestful\v0.3.0\admin\includes\languages\english\extra_definitions\lang.uspsr.php
- \zc_plugins\USPSRestful\v0.3.0\catalog\includes\languages\english\modules\shipping\lang.uspsr.php
- \zc_plugins\USPSRestful\v0.3.0\catalog\includes\modules\shipping\uspsr.php
- \zc_plugins\USPSRestful\v0.3.0\catalog\includes\templates\template_default\images\icons\shipping_usps.gif
- \zc_plugins\USPSRestful\v0.3.0\Installer\ScriptedInstaller.php
```

### Non-encapsulated File Listing

``` txt
- CONTRIBUTING.md
- LICENSE
- README.md (this file)
- changelog.md
- admin\includes\languages\english\extra_definitions\lang.uspsr.php (NEW)
- catalog\includes\languages\english\modules\shipping\lang.uspsr.php
- catalog\includes\modules\shipping\uspsr.php
- catalog\includes\templates\template_default\images\icons\shipping_usps.gif
```

## Support the author
<!-- Should this repository be forked, please remove this section -->

[![Support via CashApp](https://img.shields.io/badge/cashapp-green?style=flat&logo=cashapp&logoColor=white&logoSize=auto&labelColor=black&color=purple&link=https%3A%2F%2Fcash.app%2Fretched)](https://cash.app/$retched) [![Support via PayPal](https://img.shields.io/badge/paypal-blue?style=flat&logo=paypal&logoColor=white&logoSize=auto&labelColor=black&color=purple)](https://paypal.me/retched)  [![Support via Patreon](https://img.shields.io/badge/patreon-white?style=flat&logo=patreon&logoColor=white&labelColor=black&color=purple&link=https%3A%2F%2Fwww.patreon.com%2Fretched)](https://www.patreon.com/retched)  [![Support via BuyMeACoffee](https://img.shields.io/badge/buymeacoffee-white?style=flat&logo=buymeacoffee&logoColor=white&labelColor=black&color=purple&link=https%3A%2F%2Fbuymeacoffee.com%2Fretched)](https://buymeacoffee.com/retched)  [![Support via Kofi](https://img.shields.io/badge/kofi-white?style=flat&logo=buymeacoffee&logoColor=white&labelColor=black&color=purple&link=https%3A%2F%2Fkofi.com%2Fretched)](https://kofi.com/retched)  

## License

``` text
USPS Shipping (RESTful) for Zen Cart
A shipping module for ZenCart, an e-commerce platform
Copyright (C) 2024 Paul Williams (retched / retched@hotmail.com)

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
