<?php
/**
 * USPS Shipping (RESTful) for Zen Cart
 * Version 1.6.0
 * 
 * @copyright Portions Copyright 2004-2025 Zen Cart Team
 * @author Paul Williams (retched)
 * @version $Id: uspsr.php 2025-12-17 retched Version 1.6.0 $
****************************************************************************
    USPS Shipping (w/REST API) for Zen Cart
    A shipping module for ZenCart, an ecommerce platform
    Copyright (C) 2025  Paul Williams (retched / retched@hotmail.com)

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
****************************************************************************/

define('MODULE_SHIPPING_USPSR_ERROR' , '<strong>USPSr Error:</strong> '); // Leave the trailing space
define('MODULE_SHIPPING_USPSR_NOTICE', '<strong>USPSr Notice:</strong> '); // Leave the trailing space

define('MODULE_SHIPPING_USPSR_ERROR_NO_QUOTES' , MODULE_SHIPPING_USPSR_ERROR . 'No services selected for USPSr module.');
define('MODULE_SHIPPING_USPSR_ERROR_BAD_ORIGIN_ZIPCODE' , MODULE_SHIPPING_USPSR_ERROR . 'An invalid shipment origin ZIP code has been detected. Please enter a valid ZIP Code in the <a href="' . zen_href_link(FILENAME_DEFAULT, "cmd=configuration&gID=7&cID=211&action=edit") .'">Configuration > Shipping/Package > Postal Code</a> config setting.');
define('MODULE_SHIPPING_USPSR_ERROR_BAD_ORIGIN_COUNTRY' , MODULE_SHIPPING_USPSR_ERROR . 'USPS can only ship from USA, but your store is configured with another country as its origin! See <a href="' . zen_href_link(FILENAME_DEFAULT, "cmd=configuration&gID=7&cID=210&action=edit") .'">Admin > Configuration > Shipping/Packaging > Country of Origin</a>.');
define('MODULE_SHIPPING_USPSR_ERROR_BAD_CREDENTIALS' , MODULE_SHIPPING_USPSR_ERROR . 'You didn\'t enter the API Key and Secret. Please follow the instructions and log in to the developers dashboard to obtain your Consumer Secret and Consumer Key.');
define('MODULE_SHIPPING_USPSR_ERROR_NO_CREDENTIALS' , MODULE_SHIPPING_USPSR_ERROR . 'You didn\'t enter an API Key and Secret. Please follow the instructions and log in to the developers dashboard to obtain your Consumer Secret and Consumer Key.');
define('MODULE_SHIPPING_USPSR_ERROR_NO_CONTRACT' , MODULE_SHIPPING_USPSR_ERROR . 'You chose to have Contract pricing but you entered invalid Account details. Check your settings.');
define('MODULE_SHIPPING_USPSR_ERROR_REJECTED_CREDENTIALS' , MODULE_SHIPPING_USPSR_ERROR . 'Unable to obtain bearer token. Check to make sure that you entered valid API Credentials and that those credentials have permission to access the API. Check your settings.');
define('MODULE_SHIPPING_USPSR_ERROR_NO_UPGRADE' , MODULE_SHIPPING_USPSR_ERROR . "You are using the repository version (0.0.0) of USPSr, you <strong>CANNOT</strong> upgrade this version directly. You must uninstall and reinstall the module to apply any upgrade.");

define('MODULE_SHIPPING_USPSR_UPGRADE_SUCCESS', MODULE_SHIPPING_USPSR_NOTICE . "Success! The USPSr module has been updated to version <code>%s</code> .");
define('MODULE_SHIPPING_USPSR_UPGRADE_AVAILABLE', MODULE_SHIPPING_USPSR_NOTICE . 'There is a new version of the USPSr module available! To download it, visit the listing on the <a href="https://www.zen-cart.com/downloads.php?do=file&id=2395" target="_blank">ZenCart Plugins and Modules database</a>.');
define('MODULE_SHIPPING_USPSR_DEVELOPMENTAL' , MODULE_SHIPPING_USPSR_NOTICE . "You are running a <strong>developmental</strong> version of the USPSr  module. Some things may not work as intended. Please be sure to report any problems on the <a href=\"https://github.com/retched/ZC-USPSRestful/issues\" target=\"_blank\">GitHub repository</a> or <a href=\"https://www.zen-cart.com/showthread.php/230512-USPS-Shipping-(RESTful)-(USPSr)\" target=\"_blank\">ZenCart support thread</a>.");
define('MODULE_SHIPPING_USPSR_HANDLING_DAYS', MODULE_SHIPPING_USPSR_ERROR . 'The number of handling days must be a positive integer between 0 and 30! ');

/**
 * Adding for the backarea
 */

define('MODULE_SHIPPING_USPSR_TEXT_DAY' , 'day');
define('MODULE_SHIPPING_USPSR_TEXT_DAYS' , 'days');
define('MODULE_SHIPPING_USPSR_TEXT_WEEKS' , 'weeks');
define('MODULE_SHIPPING_USPSR_TEXT_VARIES' , 'Varies');
define('MODULE_SHIPPING_USPSR_TEXT_ESTIMATED' , 'est.');
define('MODULE_SHIPPING_USPSR_TEXT_ESTIMATED_DELIVERY' , 'est. delivery');
