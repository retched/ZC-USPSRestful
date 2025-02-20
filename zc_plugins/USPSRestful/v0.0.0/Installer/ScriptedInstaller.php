<?php
/**
 * USPS Shipping (RESTful) for Zen Cart
 * Version 0.0.0
 *
 * @package shippingMethod
 * @copyright Portions Copyright 2004-2025 Zen Cart Team
 * @copyright Portions adapted from 2012 osCbyJetta
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
        global $messageStack;
        // $version contains the old version being upgrade from.

        /**
         * [0.2.0 and onward] We need to change one of the ZenCart setting descriptions to remove the mention of inches from it.
         * Generally speaking, the cart will automatically be converted to inches one way or the other. So going forward,
         * change the dialog to indicate as such.
         *
         */

        switch ($oldVersion) {
            case "v1.0.0":
                // Changes to the database from v1.0.0 should be put here.
                break;
            
            case "v0.3.0":
            case "v0.2.0":
            case "v0.1.0":

                // Changing this to be a more descriptive description.
                $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT', [
                    'set_function' => 'zen_cfg_select_option([\'No\', \'Estimate Delivery\', \'Estimate Transit Time\'], '
                ]);

                // If the Constant is set to "Estimate Time, we should update the value too.
                if (defined('MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT') && MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT === 'Estimate Time') {
                    $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT', [
                        'configuration_value' => 'Estimate Transit Time',
                        'configuration_description' => 'Would you like to display an estimated delivery date (ex. \"est. delivery: 12/25/2025\") or estimate delivery time (ex. \"est. 2 days\") for the service? This is pulled from the service guarantees listed by the USPS. If the service doesn\'t have a set guideline, no time quote will be displayed.<br><br>Only applies to US based deliveries.',
                    ]);
                }

                // Changing the description of the USPSr API Key and Secret prompts to warn that you CANNOT use the WebTools credentials.
                $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_API_KEY', [
                    'configuration_description' => 'Enter your USPS API Consumer Key assigned to the app dedicated for this website.<br><br><strong>NOTE:</strong> This is NOT the same as the WebTools USERID and is NOT your USPS.com account Username.'
                ]);

                $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_API_SECRET', [
                    'configuration_description' => 'Enter the USPS API Consumer Secret assigned to the app dedicated for this website.<br><br><strong>NOTE:</strong> This is NOT the same as the WebTools PASSWORD and is NOT your USPS.com account Password.'
                ]);

                $messageStack->add_session('<strong>USPSr Warning:</strong> Due to changes in configuration, if USPSr was enabled and already installed, you must now go to <a href="' . zen_href_link(FILENAME_DEFAULT, 'cmd=modules&set=shipping&module=uspsr') . '">Modules > Shipping > USPSr</a> and reselect your desired USPS Shipping Methods.', 'warning');

                // Rename MODULE_SHIPPING_USPSR_PROCESSING_CLASS to MODULE_SHIPPING_USPSR_MEDIA_CLASS
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET configuration_key = 'MODULE_SHIPPING_USPSR_MEDIA_CLASS' WHERE configuration_key = 'MODULE_SHIPPING_USPSR_PROCESSING_CLASS' ");

                // The PROCESSING_CLASS, now MEDIA_CLASS, changed quite a bit.
                $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_MEDIA_CLASS',
                    [
                        'configuration_title' => 'Packaging Class - Media Mail',
                        'configuration_description' => 'For Media Mail only, are your packages typically machinable?<br><br>\"Machinable\" means a mail piece designed and sized to be processed by automated postal equipment. Typically this is rigid mail, that fits a certain shape and is within a certain weight (no more than 25 pounds for Media Mail). If your normal packages are within these guidelines, set this flag to \"Machinable\". Otherwise, set this to \"Nonstandard\". (If your customer order\'s total weight or package size falls outside this limit, regardless of the setting, the module will set the package to \"Nonstandard\".) (If your customer order\'s total weight or package size falls outside of this limit, regardless of the setting, the module will set the package to \"Nonstandard\".) <br><br>This applies only to Media Mail. All other mail services will have their \"Machinability\" status determined by the weight of the cart and the size of the package entered below.',
                        'set_function' => 'zen_cfg_select_option([\'Machinable\', \'Nonstandard\'], ',
                    ]
                );

                // Language error in the description of Exclusions from Media Mail
                $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_MEDIA_MAIL_EXCLUDE',
                    [
                        'configuration_title' => 'Categories to Excluded from Media Mail',
                    ]
                );

                // The description Domestic and International Services changed
                $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_DMST_SERVICES',
                    [
                        'configuration_description' => 'Pick which add-ons you wish to offer as a part of the shipping cost quote for domestic packages. (The USPS API will do the math as necessary.)<br><br><strong>CAUTION:</strong> Not all options apply to all services.<br>',
                    ]
                );

                $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_INTL_SERVICES',
                    [
                        'configuration_description' => 'Pick which add-ons you wish to offer as a part of the shipping cost quote for international packages. (The USPS API will do the math as necessary.)<br><br><strong>CAUTION:</strong> Not all options apply to all services.<br>',
                    ]
                );

                // Language changed for USPSR
                $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_CONTRACT_TYPE',
                    [
                        'configuration_description' => 'What kind of payment account do you have with the US Postal Service?<br><br><em>EPS</em> - Enterprise Payment System<br><br><em>Permit</em> - If you have a Mailing Permit whcih would entitle you a special discount on postage pricing, choose this option.<br><br><em>Meter</em> - If you have a licensed postage meter that grants you a special discount with the USPS, choose this option.',
                    ]
                );

                // NEW SETTINGS, Dispatch Cart Total, Dimensional Class Pricing, Cubic Class Pricing
                $this->addConfigurationKey('MODULE_SHIPPING_USPSR_DISPATCH_CART_TOTAL', [
                    'configuration_title' => 'Send cart total as part of quote?',
                    'configuration_value' => 'Yes',
                    'configuration_description' => 'As part of the quoting process, you can send the customer\'s order total to the USPS API for it to calculate Insurance and eligibility for international shipping. (The USPS puts a limit on how much merchandise can be sent to certain countries and by certain methods.) If you choose \"No\", the module will send a cart value of $5 to be processed.<br><br><strong>CAUTION:</strong> If you don\'t send the total, your customer will not receive inaccurate price details from the USPS and you may end up paying more for the actual postage.',
                    'configuration_group_id' => 6,
                    'sort_order' => 0,
                    'set_function' => 'zen_cfg_select_option([\'Yes\', \'No\'], ', 
                ]);

                $this->addConfigurationKey('MODULE_SHIPPING_USPSR_DIMENSIONAL_CLASS' , [
                    'configuration_title' => 'Packaging Class - Dimensional Pricing',
                    'configuration_value' => 'Rectangular', 
                    'configuration_description' => 'Are your packages typically rectangular?<br><br><em>\"Rectangular\"</em> means a mail piece that is a standard four-corner box shape that is not significantly curved or oddly angled. Something like a typical cardboard shipping box would fit this. If you use any kind of bubble mailer or poly mailer instead of a basic box, you should choose Nonrectangular.<br><br><em>Typically this would only really apply under extreme quotes like extra heavy or big packages.</em>',
                    'configuration_group_id' => 6,
                    'sort_order' => 0,
                    'set_function' => 'zen_cfg_select_option([\'Rectangular\', \'Nonrectangular\'], ',
                ]);

                $this->addConfigurationKey('MODULE_SHIPPING_USPSR_CUBIC_CLASS', [
                    'configuration_title' => 'Packaging Class - Cubic Pricing',
                    'configuration_value' => 'Non-Soft',
                    'configuration_description' => 'How would you class the packaging of your items?<br><br><em>\"Non-Soft\"</em> refers to packaging that is rigid in shape and form, like a box.<br><br><em>\"Soft\"</em> refers to packaging that is usually cloth, plastic, or vinyl packaging that is flexible enough to adhere closely to the contents being packaged and strong enough to securely contain the contents.<br><br>Choose the style that best fits how you (on average) ship out your packages.<br><em>This selection only applies to Cubic Pricing such as Ground Advantage Cubic, Priority Mail Cubic, Priority Mail Express Cubic</em>', 
                    'configuration_group_id' => 6, 
                    'sort_order' => 0, 
                    'set_function' => 'zen_cfg_select_option([\'Non-Soft\', \'Soft\'], '
                ]);

                
                // Cosmetic change: changing the description to match its new one. (This should only change the ONE line).
                $this->executeInstallerSql("UPDATE " . TABLE_PLUGIN_CONTROL . " SET description = 'This module provides sellers the ability to offer United States Postal Service (USPS) shipping rates to their customers during checkout. This is done by pulling the rates directly from the USPS\' REST API using OAuth.<br><br>This module supports versions 1.5.8 onward innately. (Support from 1.5.7 and backward is not necessarily guaranteed but is plausible.) This script was primarily written with PHP8 in mind. (It might have problems working with PHP7.)' WHERE unique_key = 'USPSRestful' ");
                break;

            case "0.0.0":
            case "v0.0.0":
                $messageStack->addSession("<strong>USPSr Installation error:</strong> You are using the developmental version (v0.0.0) from the GitHub. To upgrade, you must COMPLETELY uninstall this version before installing the new version.", 'error');
                return false;
        }
        
        // Update the version setting to match the new version. (This happens regardless of version, so this should sit outside version check.)
        $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_VERSION', [
            'configuration_value' => $this->version,
            'set_function' => "zen_cfg_select_option([\'$this->version\'], "
        ]);

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
            'MODULE_SHIPPING_USPSR_MEDIA_CLASS',
            'MODULE_SHIPPING_USPSR_DIMENSIONAL_CLASS',
            'MODULE_SHIPPING_USPSR_CUBIC_CLASS',
            'MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT',
            'MODULE_SHIPPING_USPSR_HANDLING_TIME',
            'MODULE_SHIPPING_USPSR_DIMMENSIONS',
            'MODULE_SHIPPING_USPSR_TYPES',
            'MODULE_SHIPPING_USPSR_MEDIA_MAIL_EXCLUDE',
            'MODULE_SHIPPING_USPSR_CONNECT_LOCAL_ZIP',
            'MODULE_SHIPPING_USPSR_DMST_SERVICES',
            'MODULE_SHIPPING_USPSR_INTL_SERVICES',
            'MODULE_SHIPPING_USPSR_PRICING',
            'MODULE_SHIPPING_USPSR_CONTRACT_TYPE',
            'MODULE_SHIPPING_USPSR_ACCT_NUMBER',
            'MODULE_SHIPPING_USPSR_DISPATCH_CART_TOTAL',
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
