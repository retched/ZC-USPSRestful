<?php
/**
 * USPS Shipping (RESTful) for Zen Cart
 * Version 0.0.0
 *
 * @copyright Portions Copyright 2004-2025 Zen Cart Team
 * @author Paul Williams (retched)
 * @version $Id: ScriptedInstaller.php 0000-00-00 retched Version 0.0.0 $
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
         * No, do not install the module from here!
         * You should install the module from the Shipping Module section of the admin.
         */

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
            $updated_listing = preg_replace("/uspsr.php;?/", '', $module_listing->fields['configuration_value']);

            $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . $updated_listing . "' WHERE configuration_key = 'MODULE_SHIPPING_INSTALLED'");
        }

        return true;

    }
}
