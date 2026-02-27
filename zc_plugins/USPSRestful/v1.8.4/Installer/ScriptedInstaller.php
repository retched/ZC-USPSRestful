<?php
/**
 * USPS Shipping (RESTful) for Zen Cart
 * Version 1.8.4
 *
 * @copyright Portions Copyright 2004-2026 Zen Cart Team
 * @author Paul Williams (retched)
 * @version $Id: ScriptedInstaller.php 2026-02-27 retched Version 1.8.4 $
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

use Zencart\PluginSupport\ScriptedInstaller as ScriptedInstallBase;

class ScriptedInstaller extends ScriptedInstallBase
{

    private $db_keys = [
            'MODULE_SHIPPING_USPSR_ACCT_NUMBER',
            'MODULE_SHIPPING_USPSR_API_KEY',
            'MODULE_SHIPPING_USPSR_API_SECRET',
            'MODULE_SHIPPING_USPSR_BEARER_TOKEN',
            'MODULE_SHIPPING_USPSR_BEARER_TOKEN_EXPIRATION',
            'MODULE_SHIPPING_USPSR_CONNECT_LOCAL_ZIP',
            'MODULE_SHIPPING_USPSR_CONTRACT_TYPE',
            'MODULE_SHIPPING_USPSR_CUBIC_CLASS',
            'MODULE_SHIPPING_USPSR_DEBUG_MODE',
            'MODULE_SHIPPING_USPSR_DIMENSIONAL_CLASS',
            'MODULE_SHIPPING_USPSR_DIMMENSIONS',
            'MODULE_SHIPPING_USPSR_DISPATCH_CART_TOTAL',
            'MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT',
            'MODULE_SHIPPING_USPSR_DMST_LETTER_SERVICES',
            'MODULE_SHIPPING_USPSR_DMST_SERVICES',
            'MODULE_SHIPPING_USPSR_HANDLING_DOMESTIC',
            'MODULE_SHIPPING_USPSR_HANDLING_INTL',
            'MODULE_SHIPPING_USPSR_HANDLING_METHOD',
            'MODULE_SHIPPING_USPSR_HANDLING_TIME',
            'MODULE_SHIPPING_USPSR_INTL_LETTER_SERVICES',
            'MODULE_SHIPPING_USPSR_INSTALL',
            'MODULE_SHIPPING_USPSR_INTL_SERVICES',
            'MODULE_SHIPPING_USPSR_LTR_DIMMENSIONS',
            'MODULE_SHIPPING_USPSR_LTR_MACHINEABLE_FLAGS',
            'MODULE_SHIPPING_USPSR_LTR_PROCESSING',
            'MODULE_SHIPPING_USPSR_MEDIA_CLASS',
            'MODULE_SHIPPING_USPSR_MEDIA_MAIL_EXCLUDE',
            'MODULE_SHIPPING_USPSR_PRICING',
            'MODULE_SHIPPING_USPSR_QUOTE_SORT',
            'MODULE_SHIPPING_USPSR_REFRESH_TOKEN',
            'MODULE_SHIPPING_USPSR_REFRESH_TOKEN_EXPIRATION',
            'MODULE_SHIPPING_USPSR_SORT_ORDER',
            'MODULE_SHIPPING_USPSR_SQUASH_OPTIONS',
            'MODULE_SHIPPING_USPSR_STATUS',
            'MODULE_SHIPPING_USPSR_TAX_BASIS',
            'MODULE_SHIPPING_USPSR_TAX_CLASS',
            'MODULE_SHIPPING_USPSR_TITLE_SIZE',
            'MODULE_SHIPPING_USPSR_TYPES',
            'MODULE_SHIPPING_USPSR_VERSION',
            'MODULE_SHIPPING_USPSR_ZONE',
    ];
    
    protected function executeInstall()
    {

        /**
         * Install ONE key to show that the module is installed, but the rest of the keys will be installed when you enable the module from the Shipping Modules section of the admin. 
         * This key needs to be installed with a specific value so that the rest of the module knows not to install it to the database.
         */

        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_INSTALL', [
            'configuration_title' => 'USPSr Module Installation Type',
            'configuration_value' => '1', // Simple flag. The value doesn't matter, the important part is that it's installed. The rest of the keys will be installed when you enable the module from the Shipping Modules section of the admin. This is to prevent the uninstallation script from being visible on the menu.
            'configuration_description' => '<strong>FOR INTERNAL USE ONLY!</strong> This shows that the module is an encapsulated install. If this key is present, the uninstallation link for the module will not appear on the menu, and the module will be uninstalled by removing this key. This is to prevent users from accidentally uninstalling an encapsulated version of the module and breaking their site.',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'set_function' => '',
            'use_function' => '',
            'date_added' => 'now()'
        ]);

        return true;

    }

    protected function executeUpgrade($oldVersion)
    {

        /**
         * No, do not upgrade the module from here!
         * You should upgrade the module from the Shipping Module section of the admin.
         * 
         * However, we will set a message to the admin to inform them that they need to go to the Shipping Modules section to complete the upgrade.
         */

        global $messageStack;

        if (!defined('MODULE_SHIPPING_USPSR_INSTALL')) {
            $this->addConfigurationKey('MODULE_SHIPPING_USPSR_INSTALL', [
                'configuration_title' => 'USPSr Module Installation Type',
                'configuration_value' => '1', // Simple flag. The value doesn't matter, the important part is that it's installed. The rest of the keys will be installed when you enable the module from the Shipping Modules section of the admin. This is to prevent the uninstallation script from being visible on the menu.
                'configuration_description' => '<strong>FOR INTERNAL USE ONLY!</strong> This shows that the module is an encapsulated install. If this key is present, the uninstallation link for the module will not appear on the menu, and the module will be uninstalled by removing this key. This is to prevent users from accidentally uninstalling an encapsulated version of the module and breaking their site.',
                'configuration_group_id' => 6,
                'sort_order' => 0,
                'set_function' => '',
                'use_function' => '',
                'date_added' => 'now()'
            ]);
        }
        
        $messageStack->add_session('USPSRestful has started its upgrade. To finish the upgrade, please go to the <a href="' . zen_href_link(FILENAME_DEFAULT, "cmd=modules&set=shipping&module=uspsr") .'">Shipping Modules</a> section of the admin to complete the upgrade!', 'warning');

        return true;

    }

    protected function executeUninstall()
    {

        // Best practice > Use deleteConfigurationKeys
        $this->deleteConfigurationKeys($this->db_keys);

        // Additionally, we should force the module off by removing uspsr.php from the configuration value of MODULE_SHIPPING_INSTALLED
        $module_listing = $this->executeInstallerSelectQuery("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_SHIPPING_INSTALLED'");

        // Shouldn't be empty... there SHOULD be a key returned as it's part of ZenCart's base install... but...
        // we're going to remove the uspsr.php; bit of it.
        if (zen_not_null($module_listing->fields['configuration_value'])) {
            $updated_listing = preg_replace("/uspsr\.php;?/", '', $module_listing->fields['configuration_value']);

            $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . $updated_listing . "' WHERE configuration_key = 'MODULE_SHIPPING_INSTALLED'");
        }

        return true;

    }
}
