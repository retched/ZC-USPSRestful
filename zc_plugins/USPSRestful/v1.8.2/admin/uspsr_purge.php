<?php

/**
 * USPS Shipping (RESTful) for Zen Cart
 * Version 0.0.0
 *
 * @copyright Portions Copyright 2004-2026 Zen Cart Team
 * @copyright Portions adapted from 2012 osCbyJetta
 * @author Paul Williams (retched)
 * @version $Id: uspsr_purge.php 0000-00-00 retched Version 0.0.0 $
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

require 'includes/application_top.php';

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

// List of files with the module (traditional purge).
$file_list = [
    DIR_FS_CATALOG . 'includes/functions/extra_functions/usps.extra_functions.php',
    DIR_FS_CATALOG . 'includes/languages/english/modules/shipping/uspsr.php',
    DIR_FS_CATALOG . 'includes/languages/english/modules/shipping/lang.uspsr.php',
    DIR_FS_CATALOG . 'includes/modules/shipping/uspsr.php',
    // Not deleting the USPS logo as that came with ZenCart, so leave it.
    
    DIR_FS_ADMIN . 'uspsr_purge.php',
    DIR_FS_ADMIN . 'uspsr_uninstall.php',
    DIR_FS_ADMIN . 'includes/extra_datafiles/uspsr_uninstaller.php',
    DIR_FS_ADMIN . 'includes/functions/extra_functions/usps.extra_functions.php',
    DIR_FS_ADMIN . 'includes/languages/english/extra_definitions/uspsr.php',
    DIR_FS_ADMIN . 'includes/languages/english/extra_definitions/lang.uspsr.php',

];

// Only proceed with the uninstallation if the form was submitted with the correct action.
if (isset($_GET['action']) && $_GET['action'] === 'confirm' && $_POST['validate'] == '1') {

    // FIRST: clear the traditional files out from the list above.
    foreach ($file_list as $file) {
        if (file_exists($file)) unlink($file);
    }

    // NEXT: Delete the directory and entry from Plugins Manager
    if ($sniffer->table_exists("%plugin_control")) {
        // Does the Plugins Database table exist? If so, delete the entry for USPSRestful

        // If plugin_control exists, safe to assume plugin_control_versions exist as well.
        // Delete any mention of "USPSRestful" from both tables

        $db->Execute("DELETE FROM " . TABLE_PLUGIN_CONTROL . " WHERE unique_key = 'USPSRestful'");
        $db->Execute("DELETE FROM " . TABLE_PLUGIN_CONTROL_VERSIONS . " WHERE unique_key = 'USPSRestful'");

        // If plugin_control exists, safe to assume that the /zc_plugins/ directory also exists... if it does, delete all related files and folders out of there (including that folder itself).
        deleteDirectory(DIR_FS_CATALOG . "zc_plugins/USPSRestful");
    }

    // NEXT: Remove the configuration keys related to the module, if they haven't already.
    $db->Execute("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key LIKE 'MODULE_SHIPPING_USPSR_%'");

    // NEXT: we should force the module off by removing uspsr.php from the configuration value of MODULE_SHIPPING_INSTALLED (if it hasn't already)
    $module_listing = $db->Execute("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_SHIPPING_INSTALLED'");
    if ($module_listing->RecordCount() > 0) {
        $current_value = $module_listing->fields['configuration_value'];
        $new_value = preg_replace("/uspsr\.php;?/", '', $module_listing->fields['configuration_value']);
        $db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '" . zen_db_input($new_value) . "' WHERE configuration_key = 'MODULE_SHIPPING_INSTALLED'");
    }

    // NEXT: Delete the option for the Admin Page link for the module's uninstallation, if it exists.
    $db->Execute("DELETE FROM admin_pages WHERE page_key = 'uspsrUninstall'");

    // Alert the user that the uninstallation is complete and redirect them to the main admin page.
    $messageStack->add_session(MODULE_SHIPPING_USPSR_PURGE_COMPLETE, 'success');

    zen_redirect(zen_href_link(FILENAME_DEFAULT, '', 'SSL'));
} elseif (isset($_GET['action']) && $_GET['action'] === 'confirm') {
    // The button was clicked but not confirm, send a messageStack error
    $messageStack->add(MODULE_SHIPPING_USPSR_ERROR_NOCONFIRM, 'error');
}

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

?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
    <head>
        <?php require DIR_WS_INCLUDES . 'admin_html_head.php'; ?>
    </head>
    <body>
        <!-- header //-->
        <?php require DIR_WS_INCLUDES . 'header.php'; ?>
        <!-- header_eof //-->
        
        <div class="container-fluid">

            <h1><?=  MODULE_SHIPPING_USPSR_PURGE_HEADER ?></h1>
            <p><?= MODULE_SHIPPING_USPSR_PURGE_CONFIRM ?></p>
            <h2><?= MODULE_SHIPPING_USPSR_UNINSTALL_CONFIRM_WARNING ?></h2>
            <?= zen_draw_form('uninstall_uspsr', FILENAME_USPSR_PURGE, 'action=confirm', 'post'); ?>
                <p><?= zen_draw_checkbox_field('validate', 1, false, '', 'id="confirm"') ?> <?=  zen_draw_label(MODULE_SHIPPING_USPSR_CONFIRM_CHECKBOX, 'confirm') ?></p>
                <input type="submit" class="btn btn-danger" value="<?= MODULE_SHIPPING_USPSR_PURGE_BUTTON ?>">
            </form>
        </div>

        <!-- footer //-->
        <?php require DIR_WS_INCLUDES . 'footer.php'; ?>
        <!-- footer_eof //-->

    </body>
</html>