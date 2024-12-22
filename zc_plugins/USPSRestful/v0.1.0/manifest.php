<?php
/**
 * USPS Shipping (w/REST API) for Zen Cart
 * Version 0.1.0
 * @copyright Portions Copyright 2004-2024 Zen Cart Team
 * @author Paul Williams (retched) 
 * @version $Id: manifest.php 2024-12-12 retched Version 0.1.0 $
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
    'pluginVersion' => 'v0.1.0',
    'pluginName' => 'USPS Shipping (w/REST API) for Zen Cart',
    'pluginDescription' => 'USPS Shipping Module, RESTful edition',
    'pluginAuthor' => 'Paul Williams (retched)',
    'pluginId' => 0, // Temporarily numbering this to 0 until approved by ZenCart.
    'zcVersions' => ['v158', 'v200', 'v201'],
    'changelog' => 'changelog.md', 
    'github_repo' => 'https://github.com/retched/ZC-USPSRestful',
    'pluginGroups' => [],
];