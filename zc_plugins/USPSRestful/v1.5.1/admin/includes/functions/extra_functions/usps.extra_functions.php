<?php
/**
 * USPS Shipping (RESTful) for Zen Cart
 * Version 1.5.1
 *
 * @copyright Portions Copyright 2004-2025 Zen Cart Team
 * @copyright Portions adapted from 2012 osCbyJetta
 * @author Paul Williams (retched)
 * @version $Id: usps.extra_functions.php 2025-10-17 retched Version 1.5.1 $
 ****************************************************************************
    USPS Shipping (RESTful) for Zen Cart
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

// This should load the Front/Catalog version of this file
if (file_exists(DIR_FS_CATALOG . DIR_WS_FUNCTIONS . 'extra_functions/usps.extra_functions.php')) {
    // If this file exists, we're in the non plugin-managed install
    require DIR_FS_CATALOG . DIR_WS_FUNCTIONS . 'extra_functions/usps.extra_functions.php';
} else {
    // Encapsulated or plugin-managed install

    // Establish the three classes we need
    $pluginManagerClass = '\Zencart\PluginManager\PluginManager';
    $pluginControlClass = '\App\Models\PluginControl';
    $pluginControlVersionClass = '\App\Models\PluginControlVersion';

    // Check that the classes exist before trying to use them
    if (
        class_exists($pluginManagerClass)
        && class_exists($pluginControlClass)
        && class_exists($pluginControlVersionClass)
    ) {
        $plugin_manager = new $pluginManagerClass(
            new $pluginControlClass(),
            new $pluginControlVersionClass()
        );

        // Load the necessary file
        require $plugin_manager->getPluginVersionDirectory('USPSRestful', $plugin_manager->getInstalledPlugins())
            . 'catalog/' . DIR_WS_FUNCTIONS . 'extra_functions/usps.extra_functions.php';
    } 
}
