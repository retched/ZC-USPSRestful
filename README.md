# USPS Shipping (RESTful) for Zen Cart

This module provides sellers a chance to offer United States Postal Service (USPS) shipping rates to customers during checkout. This is done by pulling the rates directly from the USPS' API.

This module supports versions 1.5.8 onward innately. (Support from 1.5.7 and backward is not necessarily guaranteed but is plausible. Read the Installation steps below for more details.)

## Current Version: 0.0.1 - Beta

This is the initial version of the new USPS Restful Module for ZenCart.

## Version History

- 0.0.0
    Nothing. This is a placeholder for the module and should NOT be download nor installed.

- 0.0.1 Beta
    Initial release. Merged some of the code of the original USPS Module but changed everything to use JSON instead. The module works the same way but some key differences. (Especially how to get the rates. Namely the full name, not just the "friendly name", is used to retrieve the rate. Through filtering shenanigans, the API returns the friendly name though.) One of which is the ability to specify EPS (Enterprise Payment System), meter account, or permit account numbers with the USPS to gather contract specific pricing. Filtering of carts to disable USPS Media Mail if certain categories are used. (Remember, Media Mail has strict requirements to what to allow.) In a future update, products can be listed by way of product ID to include.

#### Additional Links

USPS API Documentation: https://developers.usps.com/apis  
_This API takes advantage of three API's: Domestic Prices 3.0, International Prices 3.0, and OAuth 3.0._

ZenCart Plugins Download: (or use the Releases function on the GitHub repository)  
ZenCart support thread: 

** This above thread is only for THIS version of the USPS module, for assistance with the original USPS WebTools API module, you should post it here: https://www.zen-cart.com/showthread.php?227284-USPS-Shipping-Module-Support-Thread

## Setup and Install

### ZenCart 1.5.8 and above

This module is an encapsulated plugin. You can take the contents of the zc_plugins Directory (which is just one folder named `USPSRestful`) and upload it into the same directory in the ROOT of your ZenCart install. Once uploaded, open your ZenCart dashboard backend and visit `Modules > Plugin Manager`. Find the USPS Restful entry and click `Install`. You should then be able to proceed to the Shipping Module (`ADMIN > Modules > Shipping`) manager and find an entry for USPSRestful (code: `uspsr`) and then click Install. From there, simply provided the details requested, customize the module to your content.

### ZenCart 1.5.7 and before

Backwards compatibility with earlier ZenCart's is not guaranteed. You're welcome to try to use the module on earlier ZenCart versions but you cannot use the Plugin Manager. (Unless you make various changes to your core code.) Instead, you can extract the files from the folders  listing below and upload them to the appropriate places in your installation. To activate the module for use, simply visit the Shipping Module manager (again: `ADMIN > Modules > Shipping`). Customize your module to your content.

### Uninstallation

Regardless of which version of ZenCart you are using, **DISABLE THE SHIPPING MODULE FIRST BEFORE UNINSTALLING**. That is, go to `ADMIN > Modules > Shipping` and then click "Remove Module" next to the USPSR module.

If you installed this module through the Plugin Manager, you should then be able to click the `Un-Install` button. If you do not plan on using the module again, you should delete the module's folder from the `zc_plugins` folder. If you installed the plugin by copying the files from the list below, you should now delete them as well.

## Frequently Asked Questions

### What is the difference between this version and the original USPS module?

The original USPS module works by using the older USPS Web Tools API. For years, that API was the defacto API in use when it came to retrieving the estimated shipping costs of the USPS' various services as well as the estimate times of delivery. In 2024, the USPS began deprecating the Web Tools API and they will be out of service fully in 2026. The Web Tools API is being replaced with the new USPS API that takes advantage of OAuth tokens which this codebase uses.

### I already have a `USERID` and `PASSWORD` under the old system, but I'm getting error messages while I try to retrieve quotes.

The older `USERID` and `PASSWORD` are not valid for the new system. You will need to provision new credentials under the new USPS API system. Additionally, you SHOULD create an entire new USPS Business Account provided that you don't already have one for your business. The process is explained here: https://developers.usps.com/getting-started

### Why should I use this versus the one that's out there now?

The USPS created an in-before-the-lock situation with regards to the API Tools. They will, seldomly, allow access to the API by way of manually granting access but they will read you the "Riot Act" with regards to enabling them. If you are still using the Web Tools API and have no issues accessing or using them, continue to use them. But know that in roughly 2026 (possibly sooner), the older API's will be completely disabled and at that point everyone will have to use the RESTful version of the API going forward.

### Why do I have to sign up for both the Domestic and International Price API's?

You don't? You only need to sign up for the services that you plan on using. If you're shipping domestically only, you need the Domestic Prices API. If you're shipping internationally only, you need to sign up for Domestic Prices API. If you're doing both, you need both. I do not know why USPS have chosen to do this.

### What happened to the estimations of delivery that were available in the Web Tools version?

Currently, as of the time of this writing, the API calls for "service-standards" aren't working. I tried every iteration of sending the call through POSTMAN and I get nothing but 400 errors. I'm waiting to hear back from the Postal Service technical team with regards to the problems but I guess I'll wait. Meanwhile, the estimations posted will instead display the "service guarantees" of the service being used. Not all services have such a service guarantee and they are removed entirely from International shipments. (I didn't want to use "Various" on every other shipping method. If requests are made, I can re-enable them to be displayed on all of them.)

### What is this OAuth Token? Do I need to get one?

An OAuth token is a unique string of characters that allows a third-party application to access a user's data without exposing their password. Effectively making it a temporary password of sorts. You do not need to do anything to get one, this software will instead create the token for you (or at least your customer) as their cart requests the estimations of cost of the USPS services. During checkout, using your API Key and Secret, the cart will request a token for use and then let it expire when done.

### Why am I being asked for credit card/payment details during the creation of this account for the API?

That is for the EPS (Enterprise Payment System) of the USPS. It is necessary to sign up, however this code does NOT trigger any of the API which may generate a charge. (Requesting costs are free.)

### I'm not seeing the USPS Connect rate even though I selected it, what's going on?

USPS Connect rates are only available to retailers who have specifically signed up for it at https://www.uspsconnect.com and have their USPS Business Accounts activated to enable USPS Connect. Additionally, you must select and choose to enable the "Commercial" rates (formerly called "Online" in the other module) to actually see the rates while providing a list of Zip Codes that can use Connect Local. Currently, these rates are only available when you're dropping off the packages at the DESTINATION Zip Code.

### The module is not showing at all even though I made my choices.

Make sure that you store is configured to ship out of the United States and that you made sure to enter a Zip Code where your orders are originating from. (This is part of the quotation process. Without these two details, the module will fail and self-disable itself.)

### What is the handling field for? Where is the min/max fields of the original USPS module?

The handling field of the methods selection is generally for adding a surcharge to certain kinds of shipping methods (or to the entire order, to each "box", or both). If you wish to charge a surcharge for certain kinds of shipping methods, you can enter an amount (please be sensible) and this amount that you enter will be added onto the quoted shipping method. 

The original module had a set of Min/Max which restrained which methods were available to use based on the weight. (The USPS limits still constrained the order, no matter what.) For right now, those fields are not present in this version of the module but may be present in a future update.

### Does this module use the Length, Width, and Height boxes of ZC 2.0.0+?

Not at this time. Research is still being done on how to work that in to the quote. For now, you should still set those AND set the "minimum" package thresholds in the backend.

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
~~~
