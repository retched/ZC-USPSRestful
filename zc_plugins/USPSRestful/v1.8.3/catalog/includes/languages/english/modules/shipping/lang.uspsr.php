<?php
/**
 * USPS Shipping (RESTful) for Zen Cart
 * Version 1.8.3
 * 
 * @copyright Portions Copyright 2004-2026 Zen Cart Team
 * @author Paul Williams (retched)
 * @version $Id: lang.uspsr.php 2026-02-26 retched Version 1.8.3 $
****************************************************************************
    USPS Shipping (w/REST API) for Zen Cart
    A shipping module for ZenCart, an ecommerce platform
    Copyright (C) 2026 Paul Williams (retched / retched@hotmail.com)

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
$define = [
    'MODULE_SHIPPING_USPSR_TEXT_TITLE' => 'United States Postal Service',
    'MODULE_SHIPPING_USPSR_TEXT_SHORT_TITLE' => 'USPS',
    'MODULE_SHIPPING_USPSR_TEXT_TITLE_ADMIN' => 'United States Postal Service (RESTful)',
    'MODULE_SHIPPING_USPSR_TEXT_DESCRIPTION' => '<strong>United States Postal Service</strong> (RESTful)<br><br>You will need to have registered an account with USPS and API credentials made at <a href="https://cop.usps.com/" target="_blank">https://cop.usps.com/</a> to use this module.<br><br>You <strong>CANNOT</strong> use your WebTools API Credentials with this module! You must obtain your credentials by creating a USPS.com account, if you don\'t have one already. If you\'re making a new account, make a <strong>BUSINESS</strong> account. The APIs might not work as well with a Personal Account. You can find the full <a href="https://github.com/retched/ZC-USPSRestful/wiki/Getting-Started#obtaining-credentials-from-usps" target="_blank">steps to make credentials</a> on the repository\'s site.<br><br>USPS expects you to use pounds as the weight measure for your products. <em>This module will convert all metric measurements to imperial if you configured the site to use metric.</em>',

    'MODULE_SHIPPING_USPSR_TEXT_SERVER_ERROR' => 'An error occurred in obtaining USPS shipping quotes.<br>If you prefer to use USPS as your shipping method, please try refreshing this page, or contact the store owner. If the error persists, please <a target="_blank" href="' . zen_href_link(FILENAME_CONTACT_US) . '">contact the store owner</a> and provide the following error(s):',
    'MODULE_SHIPPING_USPSR_TEXT_ERROR' => 'We are unable to find a USPS shipping quote suitable for your mailing address and the shipping methods we typically use.<br><br>If you prefer to use USPS as your shipping method, please <a href="' . zen_href_link(FILENAME_CONTACT_US) . '" target="_blank">contact us</a> for assistance. (Please check that your Zip/Postal Code is entered correctly.)',

    'MODULE_SHIPPING_NO_WEIGHT' => 'The weight of this order is not defined or it is set to zero. Please <a target="_blank" href="' . zen_href_link(FILENAME_CONTACT_US) . '">contact us</a> for assistance.',

    'MODULE_SHIPPING_USPSR_TEXT_DAY' => 'day',
    'MODULE_SHIPPING_USPSR_TEXT_DAYS' => 'days',
    'MODULE_SHIPPING_USPSR_TEXT_WEEKS' => 'weeks',
    'MODULE_SHIPPING_USPSR_TEXT_VARIES' => 'Varies',
    'MODULE_SHIPPING_USPSR_TEXT_ESTIMATED' => 'est.',
    'MODULE_SHIPPING_USPSR_TEXT_ESTIMATED_DELIVERY' => 'est. delivery',

    'MODULE_SHIPPING_USPSR_TEXT_VARIES_BY_DESTINATION' => 'varies by destination country',
];

return $define;
