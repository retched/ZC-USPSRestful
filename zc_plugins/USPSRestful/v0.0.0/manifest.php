<?php
/**
 * USPS Shipping (RESTful) for Zen Cart
 * Version 0.0.0
 *
 * @package shippingMethod
 * @copyright Portions Copyright 2004-2024 Zen Cart Team
 * @copyright Portions adapted from 2012 osCbyJetta
 * @author Paul Williams (retched)
 * @version $Id: manifest.php 0000-00-00 retched Version 0.0.0 $
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

return [
    'pluginVersion' => 'v0.0.0',
    'pluginName' => 'USPS Shipping (RESTful)',
    'pluginDescription' => "This module provides sellers the ability to offer United States Postal Service (USPS) shipping rates to their customers during checkout. This is done by pulling the rates directly from the USPS' REST API using OAuth.",
    'pluginAuthor' => 'Paul Williams (retched)',
    'pluginId' => 2395,
    'zcVersions' => ['v210'],
    'changelog' => 'https://github.com/retched/ZC-USPSRestful/releases',
    'github_repo' => 'https://github.com/retched/ZC-USPSRestful',
    'pluginGroups' => [],
];
