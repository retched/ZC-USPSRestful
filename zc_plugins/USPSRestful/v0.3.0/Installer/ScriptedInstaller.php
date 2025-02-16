<?php
/**
 * USPS Shipping (RESTful) for Zen Cart
 * Version 0.3.0
 *
 * @package shippingMethod
 * @copyright Portions Copyright 2004-2024 Zen Cart Team
 * @copyright Portions adapted from 2012 osCbyJetta
 * @author Paul Williams (retched)
 * @version $Id: ScriptedInstaller.php 2025-02-12 retched Version 0.3.0 $
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
        // $version contains the old version being upgrade from.

        /**
         * [0.2.0 and onward] We need to change one of the ZenCart setting descriptions to remove the mention of inches from it.
         * Generally speaking, the cart will automatically be converted to inches one way or the other. So going forward,
         * change the dialog to indicate as such.
         *
         */

        // If the shipping weight units are CMs, changed the description to notify the storeowner that the measurements will be changed to inches.
        if(defined('SHIPPING_DIMENSION_UNITS') && SHIPPING_DIMENSION_UNITS == "centimeters") {
            $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_DIMMENSIONS', [
                'configuration_description' => 'The Minimum Length, Width and Height are used to determine shipping methods available for International Shipping.<br><br>While dimensions are not supported at this time, the Minimums are sent to USPS for obtaining Rate Quotes.<br><br>In most cases, these Minimums should never have to be changed.<br>These measurements will be converted to inches.<br>'
            ]);
        } else {
            $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_DIMMENSIONS', [
                'configuration_description' => 'The Minimum Length, Width and Height are used to determine shipping methods available for International Shipping.<br><br>While dimensions are not supported at this time, the Minimums are sent to USPS for obtaining Rate Quotes.<br><br>In most cases, these Minimums should never have to be changed.<br>These measurements should be in inches.<br>'
            ]);
        }

        // Change the Version of the module to match. (No need to reinstall.)
        $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_VERSION', ['configuration_value' => '0.3.0']);

        switch ($oldVersion) {
            case "v0.2.1":
            case "v0.2.0":
            case "v0.1.0":

                // Update the Configuration descriptions that had spelling errors.
                $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_PROCESSING_CLASS', [
                    'configuration_description' => 'Are your packages typically machinable?<br><br>\"Machinable\" means a mail piece that is designed and sized to be processed by automated postal equipment. Typically this is mail that is rigid, fits a certain shape, and is within a certain weight (roughly at least 6 ounces but no more than 35 pounds). If your normal packages are within these guidelines, set this flag to \"Machinable\". Otherwise, set this to \"Irregular\". (If your customer order\'s total weight falls outside of this limit, regardless of the setting, the module will set the package to \"Irregular\".)'
                ]);

                // Changing this to be a more descriptive description.
                $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT', [
                    'set_function' => 'zen_cfg_select_option([\'No\', \'Estimate Delivery\', \'Estimate Transit Time\'], '
                ]);

                // If the Constant is set to "Estimate Time, we should update the value too.
                if (MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT === 'Estimate Time') {
                    $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT', [
                        'configuration_value' => 'Estimate Transit Time'
                    ]);
                }

                // Changing the description of the USPSr API Key and Secret prompts to warn that you CANNOT use the WebTools credentials.
                $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_API_KEY', [
                    'configuration_description' => 'Enter your USPS API Consumer Key assigned to the app dedicated for this website.<br><br><strong>NOTE:</strong> This is NOT the same as the WebTools USERID and is NOT your USPS.com account Username.'
                ]);

                $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_API_SECRET', [
                    'configuration_description' => 'Enter the USPS API Consumer Secret assigned to the app dedicated for this website.<br><br><strong>NOTE:</strong> This is NOT the same as the WebTools PASSWORD and is NOT your USPS.com account Password.'
                ]);

                // Reset the module's selected shipping methods entirely.
                $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_TYPES', [
                    'configuration_value' => '0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00',
                ]);
                break;

        }

        // Cosmetic change: changing the description to match its new one. (This should only change the ONE line).
        $this->executeInstallerSql("UPDATE " . TABLE_PLUGIN_CONTROL . " SET description = 'This module provides sellers the ability to offer United States Postal Service (USPS) shipping rates to their customers during checkout. This is done by pulling the rates directly from the USPS\' REST API using OAuth.<br><br>This module supports versions 1.5.8 onward innately. (Support from 1.5.7 and backward is not necessarily guaranteed but is plausible.) This script was primarily written with PHP8 in mind. (It might have problems working with PHP7.)' WHERE unique_key = 'USPSRestful' ");

        return true;

    }

    protected function executeUninstall()
    {

        // Best practice > Use deleteConfigurationKeys
        $this->deleteConfigurationKeys([
            'MODULE_SHIPPING_USPSR_VERSION',
            'MODULE_SHIPPING_USPSR_STATUS',
            'MODULE_SHIPPING_USPSR_TITLE_SIZE',
            'MODULE_SHIPPING_USPSR_API_KEY',
            'MODULE_SHIPPING_USPSR_API_SECRET',
            'MODULE_SHIPPING_USPSR_QUOTE_SORT',
            'MODULE_SHIPPING_USPSR_HANDLING_DOMESTIC',
            'MODULE_SHIPPING_USPSR_HANDLING_INTL',
            'MODULE_SHIPPING_USPSR_HANDLING_METHOD',
            'MODULE_SHIPPING_USPSR_TAX_CLASS',
            'MODULE_SHIPPING_USPSR_TAX_BASIS',
            'MODULE_SHIPPING_USPSR_ZONE',
            'MODULE_SHIPPING_USPSR_PROCESSING_CLASS',
            'MODULE_SHIPPING_USPSR_PACKAGING_CLASS',
            'MODULE_SHIPPING_USPSR_CUBIC_PACKING_CLASS',
            'MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT',
            'MODULE_SHIPPING_USPSR_HANDLING_TIME',
            'MODULE_SHIPPING_USPSR_DIMMENSIONS',
            'MODULE_SHIPPING_USPSR_TYPES',
            'MODULE_SHIPPING_USPSR_MEDIA_MAIL_EXCLUDE',
            'MODULE_SHIPPING_USPSR_CONNECT_LOCAL_ZIP',
            'MODULE_SHIPPING_USPSR_DMST_SERVICES',
            'MODULE_SHIPPING_USPSR_INTL_SERVICES',
            'MODULE_SHIPPING_USPSR_PRICING',
            'MODULE_SHIPPING_USPSR_DISPATCH_CART_TOTAL',
            'MODULE_SHIPPING_USPSR_CONTRACT_TYPE',
            'MODULE_SHIPPING_USPSR_ACCT_NUMBER',
            'MODULE_SHIPPING_USPSR_DEBUG_MODE',
            'MODULE_SHIPPING_USPSR_SORT_ORDER',
        ]);

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
