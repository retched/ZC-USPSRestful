<?php
/**
 * USPS Shipping (RESTful) for Zen Cart
 * Version 0.2.0
 * @copyright Portions Copyright 2004-2024 Zen Cart Team
 * @author Paul Williams (retched)
 * @version $Id: manifest.php 2025-01-17 retched Version 0.2.0 $
****************************************************************************
    USPS Shipping (w/REST API) for Zen Cart
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
****************************************************************************/
$define = [
    'MODULE_SHIPPING_USPSR_TEXT_TITLE' => 'United States Postal Service',
    'MODULE_SHIPPING_USPSR_TEXT_SHORT_TITLE' => 'USPS',
    'MODULE_SHIPPING_USPSR_TEXT_TITLE_ADMIN' => 'United States Postal Service (RESTful)',
    'MODULE_SHIPPING_USPSR_TEXT_DESCRIPTION' => '<strong>United States Postal Service</strong> (RESTful)<br><br>You will need to have registered an account with USPS at <a href="https://developers.usps.com/" target="_blank">https://developers.usps.com/</a> to use this module.<br><br>This new process will need you to use a <strong>BUSINESS</strong> account. If you\'re making a new account, be sure to create a <strong>BUSINESS</strong> account. The APIs will NOT work as well with a Personal Account. You can find the full onboarding process in the ReadMe.<br><br>USPS expects you to use pounds as the weight measure for your products. <em>This module will convert all metric measurements to imperial if you configured the site to use metric.</em>',

    'MODULE_SHIPPING_USPSR_TEXT_SERVER_ERROR' => 'An error occurred in obtaining USPS shipping quotes.<br>If you prefer to use USPS as your shipping method, please try refreshing this page, or contact the store owner. If the error persists, please contact us and provide the following error:',
    'MODULE_SHIPPING_USPSR_TEXT_ERROR' => 'We are unable to find a USPS shipping quote suitable for your mailing address and the shipping methods we typically use.<br><br>If you prefer to use USPS as your shipping method, please contact us for assistance. (Please check that your Zip/Postal Code is entered correctly.)',

    'MODULE_SHIPPING_USPSR_TEXT_DAY' => 'day',
    'MODULE_SHIPPING_USPSR_TEXT_DAYS' => 'days',
    'MODULE_SHIPPING_USPSR_TEXT_WEEKS' => 'weeks',
    'MODULE_SHIPPING_USPSR_TEXT_VARIES' => 'Varies',
    'MODULE_SHIPPING_USPSR_TEXT_ESTIMATED' => 'est.',
    'MODULE_SHIPPING_USPSR_TEXT_ESTIMATED_DELIVERY' => 'est. delivery',

];

return $define;
