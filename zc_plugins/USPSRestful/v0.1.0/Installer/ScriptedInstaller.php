<?php
/**
 * USPS Shipping (RESTful) for Zen Cart
 * Version 0.1.0
 * 
 * @package shippingMethod
 * @copyright Portions Copyright 2004-2024 Zen Cart Team
 * @copyright Portions adapted from 2012 osCbyJetta
 * @author Paul Williams (retched) 
 * @version $Id: uspsr.php 2024-12-12 retched Version 0.1.0 $
****************************************************************************
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
****************************************************************************/

use Zencart\PluginSupport\ScriptedInstaller as ScriptedInstallBase;

class ScriptedInstaller extends ScriptedInstallBase
{
    protected function executeInstall(): void
    {
        /**
         * No, do not install the module from here! 
         * You should install the module from the Shipping Module section of the admin.
         */ 

    }

    protected function executeUninstall(): void
    {
        global $db;

        // On uninstallation, remove the configuration values from the table.
        $uninstall_sql = "DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key LIKE '%USPSR_%'";

        $this->executeInstallerSql($uninstall_sql);

        // Additionally, we should force the module off by removing uspsr.php from the configuration value of MODULE_SHIPPING_INSTALLED
        $module_listing = $db->Execute("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_SHIPPING_INSTALLED'");

        // Shouldn't be empty... there SHOULD be a key returned as it's part of ZenCart's base install... but...
        if (zen_not_null($module_listing->fields['configuration_value'])) {
            $updated_listing = preg_replace("/uspsr.php;?/", '', $module_listing->fields['configuration_value']);
            
            $db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . $updated_listing . "' WHERE configuration_key = 'MODULE_SHIPPING_INSTALLED'");
        }

    }
}