<?php
/**
 * USPS Shipping (RESTful) for Zen Cart
 * Version 1.8.1
 *
 * @copyright Portions Copyright 2004-2026 Zen Cart Team
 * @copyright Portions adapted from 2012 osCbyJetta
 * @author Paul Williams (retched)
 * @version $Id: purge_uspsr.php 2026-02-21 retched Version 1.8.1 $
 ****************************************************************************
    USPS Shipping (RESTful) for Zen Cart
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

// Edit this file and put the full path to your admin directory (with a trailing /). 
// (This file will not stay on the server once it's fully run. If you're on Windows, you MUST use front slashes and not backslashes.)
$admin_area = '';

if (empty($admin_area)) die("Please edit this file and define the directory where your admin is, then run it again.");

// -------------------------------------------------
// DO NOT EDIT BELOW THIS LINE!!!!
// -------------------------------------------------

chdir('..');
require 'includes/application_top.php';

// We're doing the "master" purge of everything for USPSr. 
// This is an ultra destructive uninstall. Any trace of USPSRestful (minus the logo file) will be removed.

// First, delete the module from the Plugins Database
if ($sniffer->table_exists("%plugin_control")) {
    // Does the Plugins Database table exist? If so, delete the entry for USPSRestful

    // If plugin_control exists, safe to assume plugin_control_versions exist as well.
    // Delete any mention of "USPSRestful" from both tables

    $db->Execute("DELETE FROM " . TABLE_PLUGIN_CONTROL . " WHERE unique_key = 'USPSRestful'");
    $db->Execute("DELETE FROM " . TABLE_PLUGIN_CONTROL_VERSIONS . " WHERE unique_key = 'USPSRestful'");

    // If plugin_control exists, safe to assume that the /zc_plugins/ directory also exists... if it does, delete all related files out of there.
    deleteDirectory(DIR_FS_CATALOG . "zc_plugins/USPSRestful");
}

// NEXT: Delete the entries out from the database...
// Delete everything out of there that could even possibly be from USPSR
$db->Execute("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key LIKE 'MODULE_SHIPPING_USPSR_%' ");

// NEXT: Remove the module from ZenCart's active shipping modules
$module_listing = $db->Execute("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_SHIPPING_INSTALLED'");

// Shouldn't be empty... there SHOULD be a key returned as it's part of ZenCart's base install... but...
// we're going to remove the uspsr.php; bit of it.
if (!empty($module_listing->fields['configuration_value'])) {
    $updated_listing = preg_replace("/uspsr\.php;?/", '', $module_listing->fields['configuration_value']);

    // Add it back
    $db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . $updated_listing . "' WHERE configuration_key = 'MODULE_SHIPPING_INSTALLED'");
}


// NEXT: Delete all the files from the traditional install.

// If the last character is not a slash, add it.
$admin_area = rtrim($admin_area, '/\\') . DIRECTORY_SEPARATOR;
$file_list = [
    DIR_FS_CATALOG . 'includes/functions/extra_functions/usps.extra_functions.php',
    DIR_FS_CATALOG . 'includes/languages/english/modules/shipping/uspsr.php',
    DIR_FS_CATALOG . 'includes/languages/english/modules/shipping/lang.uspsr.php',
    DIR_FS_CATALOG . 'includes/modules/shipping/uspsr.php',
    // Not deleting the USPS logo as that came with ZenCart, so leave it.
    
    $admin_area . 'uspsr_uninstall.php',
    $admin_area . 'includes/extra_datafiles/uspsr_uninstaller.php',
    $admin_area . 'includes/functions/extra_functions/usps.extra_functions.php',
    $admin_area . 'includes/languages/english/extra_definitions/uspsr.php',
    $admin_area . 'includes/languages/english/extra_definitions/lang.uspsr.php',
    
    DIR_FS_CATALOG . 'extras/purge_uspsr.php',
];
foreach ($file_list as $file) {
    if (file_exists($file)) unlink($file);
}

// Lastly: Delete the page key, if it exists, from the menu.
$db->Execute("DELETE FROM " . TABLE_ADMIN_PAGES . " WHERE page_key = 'uspsrUninstall'");


// At this point all the files are now deleted. 
echo "Unless error messages are shown above, USPSRestful has been purged from all places in the ZenCart system. (This file also no longer exists on your server.)";

function deleteDirectory(string $dirPath)
{
    if (!is_dir($dirPath)) {
        return false; // Not a directory
    }

    // Normalize path
    $dirPath = rtrim($dirPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

    $files = scandir($dirPath);
    if ($files === false) {
        return false;
    }

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $fullPath = $dirPath . $file;

        if (is_dir($fullPath) && !is_link($fullPath)) {
            deleteDirectory($fullPath); // Recursive call
        } else {
            unlink($fullPath); // Delete file or symlink
        }
    }

    return rmdir($dirPath); // Remove the now-empty directory
}
