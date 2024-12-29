<?php
/**
 * USPS Shipping (w/REST API) for Zen Cart
 * Version 0.1.1
 * @copyright Portions Copyright 2004-2024 Zen Cart Team
 * @author Paul Williams (retched) 
 * @version $Id: manifest.php 2024-12-12 retched Version 0.1.1 $
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
    'pluginVersion' => 'v0.1.1',
    'pluginName' => 'USPS Shipping (w/REST API) for Zen Cart',
    'pluginDescription' => 'This module provides sellers a chance to offer United States Postal Service (USPS) shipping rates to customers during checkout. This is done by pulling the rates directly from the USPS\' REST API.<br><br>This module supports versions 1.5.8 onward innately. (Support from 1.5.7 and backward is not necessarily guaranteed but is plausible.) This script was primarily written with PHP8 in mind. (It might have problems working with PHP7.)',
    'pluginAuthor' => 'Paul Williams (retched)',
    'pluginId' => 2395,
    'zcVersions' => ['v158', 'v200', 'v201'],
    'changelog' => 'https://github.com/retched/ZC-USPSRestful/releases', 
    'github_repo' => 'https://github.com/retched/ZC-USPSRestful',
    'pluginGroups' => [],
];
