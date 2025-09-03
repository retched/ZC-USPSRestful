<?php
/**
 * USPS Shipping (RESTful) for Zen Cart
 * Version 1.4.0
 *
 * @copyright Portions Copyright 2004-2025 Zen Cart Team
 * @author Paul Williams (retched)
 * @version $Id: ScriptedInstaller.php 2025-09-02 retched Version 1.4.0 $
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

        $active_modules = $this->getConfigurationKeyDetails('MODULE_SHIPPING_INSTALLED');
        $module_installed = preg_match('/uspsr.php/', $active_modules['configuration_value']);

        if ($module_installed) { // Regardless of prior version - check if the module is installed, if so, install/update the NEW keys
            // Have to add in support for the First Class Letters settings in the services table.
            if (version_compare(str_replace("v", "", MODULE_SHIPPING_USPSR_VERSION), "1.3.0", "<")) {
                /**
                 * Adding new methods into the shipping methods datatable.
                 * This is done by adding the value at the front for US First Class Mail Letter then splicing it into the datatable.
                 */
                // Regardless of the version, we need to update the data field for MODULE_SHIPPING_USPSR_TYPES.
                $original_methods = MODULE_SHIPPING_USPSR_TYPES;

                // Add the line for US First Class Mail Letter.
                if (defined('SHIPPING_WEIGHT_UNITS') && SHIPPING_WEIGHT_UNITS === 'kgs') {
                    $original_methods = "0, 0.099223, 0.00, " . $original_methods;
                } else {
                    $original_methods = "0, 0.21875, 0.00, " . $original_methods;
                }

                // Break apart the TYPES string into an array
                $config_methods = preg_split("/,\s+/", $original_methods);
                $method = 0; // Count how many methods
                for ($i = 0; $i <= (count($config_methods) - 1); $i++) {
                    $method += 1;

                    if ($method == 22) { // On the 22nd method on the list, break and add data for the First-Class Mail International Letter
                        array_splice($config_methods, $i, 0, [0, ((defined('SHIPPING_WEIGHT_UNITS') && SHIPPING_WEIGHT_UNITS === 'kgs') ? 0.099223 : 0.21875), "0.00"]);
                        break; // We're only adding ONE as the domestic method is already added. So one was already added, don't add anymore.
                    }

                    if (!is_numeric($config_methods[$i]))
                        $i += 3;
                    else
                        $i += 2;
                }

                // Rebuild the value and reinsert it into the database.
                $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_TYPES', [
                    'configuration_value' => implode(", ", $config_methods),
                    'set_function' => 'zen_cfg_uspsr_services([\'First-Class Mail Letter\', \'USPS Ground Advantage\', \'USPS Ground Advantage Cubic\', \'Media Mail\', \'Connect Local Machinable DDU\', \'Connect Local Machinable DDU Flat Rate Box\', \'Connect Local Machinable DDU Small Flat Rate Bag\', \'Connect Local Machinable DDU Large Flat Rate Bag\', \'Priority Mail\', \'Priority Mail Cubic\', \'Priority Mail Flat Rate Envelope\', \'Priority Mail Padded Flat Rate Envelope\', \'Priority Mail Legal Flat Rate Envelope\', \'Priority Mail Small Flat Rate Box\', \'Priority Mail Medium Flat Rate Box\', \'Priority Mail Large Flat Rate Box\', \'Priority Mail Large Flat Rate Box APO/FPO/DPO\', \'Priority Mail Express\', \'Priority Mail Express Flat Rate Envelope\', \'Priority Mail Express Padded Flat Rate Envelope\', \'Priority Mail Express Legal Flat Rate Envelope\', \'First-Class Mail International Letter\', \'First-Class Package International Service Machinable ISC Single-piece\', \'Priority Mail International ISC Single-piece\', \'Priority Mail International ISC Flat Rate Envelope\', \'Priority Mail International Machinable ISC Padded Flat Rate Envelope\', \'Priority Mail International ISC Legal Flat Rate Envelope\', \'Priority Mail International Machinable ISC Small Flat Rate Box\', \'Priority Mail International Machinable ISC Medium Flat Rate Box\', \'Priority Mail International Machinable ISC Large Flat Rate Box\', \'Priority Mail Express International ISC Single-piece\', \'Priority Mail Express International ISC Flat Rate Envelope\', \'Priority Mail Express International ISC Legal Flat Rate Envelope\', \'Priority Mail Express International ISC Padded Flat Rate Envelope\'], '
                ]);
            }

            switch ($oldVersion) {
                case "v0.3.0": // This version didn't officially get released but was the old format of the repository before the directory rename
                case "v0.2.0": // Released 2025-01-17
                case "v0.1.0": // Released 2024-12-22
                    // Created a function to either show the value or to show none
                    $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_ACCT_NUMBER', [
                        'use_function' => 'zen_cfg_uspsr_account_display',
                    ]);

                    // Change the Change the USPSr Version display to a read-only
                    $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_VERSION', [
                        'set_function' => 'zen_cfg_read_only('
                    ]);


                    // Change the Debug Mode to be a split selection between showing logs or showing errors
                    $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_DEBUG_MODE', [
                        'configuration_title' => 'Debug Mode',
                        'configuration_value' => ((defined('MODULE_SHIPPING_USPSR_DEBUG_MODE') && MODULE_SHIPPING_USPSR_DEBUG_MODE === 'Logs') ? "Generate Logs" : "--none--"),
                        'configuration_description' => 'Would you like to enable debug modes?<br><br><em>"Generate Logs"</em> - This module will generate log files for each and every call to the USPS API Server (including the admin side viability check).<br><br>"<em>Display errors</em>" - If set, this means that any API errors that are caught will be displayed in the storefront.<br><br><em>CAUTION:</em> Each long file is at least 300KB big.',
                        'set_function' => 'zen_cfg_select_multioption([\'Generate Logs\', \'Show Errors\'], ',
                        'date_added' => 'now()'
                    ]);

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

                    // Add Squash alike methods together
                    $this->addConfigurationKey('MODULE_SHIPPING_USPSR_SQUASH_OPTIONS', [
                        'configuration_title' => 'Squash Alike Methods Together',
                        'configuration_value' => '--none--',
                        'configuration_description' => 'If you are offering Priority Mail and Priority Mail Cubic or Ground Advantage and Ground Advantage Cubic in the same quote, do you want to "squash" them together and offer the lower of each pair?<br><br>This will only work if the quote returned from USPS has BOTH options (Cubic and Normal) in it, otherwise it will be ignored.',
                        'configuration_group_id' => 6,
                        'sort_order' => 0,
                        'set_function' => 'zen_cfg_select_multioption([\'Squash Ground Advantage\', \'Squash Priority Mail\'], '
                    ]);

                    // Update and reset the Shipping Methods Table and configuration.
                    if (defined('SHIPPING_WEIGHT_UNITS') && SHIPPING_WEIGHT_UNITS === 'kgs') {
                        $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_TYPES', [
                            'configuration_title' => 'Shipping Methods (Domestic and International)',
                            'configuration_value' => '0, 31.7514, 0.00, 0, 9.0718, 0.00, 0, 31.7514, 0.00, 0, 11.3398, 0.00, 0, 11.3398, 0.00, 0, 11.3398, 0.00, 0, 11.3398, 0.00, 0, 31.7514, 0.00, 0, 9.0718, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 1.8143, 0.00, 0, 31.7514, 0.00, 0, 1.8143, 0.00, 0, 1.8143, 0.00, 0, 1.8143, 0.00, 0, 1.8143, 0.00, 0, 9.0718, 0.00, 0, 9.0718, 0.00, 0, 31.7514, 0.00, 0, 1.8143, 0.00, 0, 1.8143, 0.00, 0, 1.8143, 0.00',
                            'configuration_description' => 'Choose the services that you want to offer to your customers.<br><br><b>Checkbox:</b> Select the services to be offered (Can also click on the service name in certain browsers.)<br><br><b>Min/Max</b> Choose a custom minimum/maximum for the selected service. If the cart as a whole (the items plus any tare settings) fail to make weight, the method will be skipped. Keep in mind that each service also has its own maximums that will be controlled regardless of what was set here. (Example: entering 5 lbs for International First-Class Mail will be ignored since the International First-Class Mail has a hard limit of 4 lbs.)<br><br><b>Handling:</b> A handling charge for that particular method (will be added on to the quote plus any services charges that are applicable).<br><br>USPS returns methods based on cart weights. Enter the weights in your site\'s configured standard. (The cart will handle conversions as necessary.)',
                            'configuration_group_id' => 6,
                            'sort_order' => 0,
                            'set_function' => 'zen_cfg_uspsr_services([\'USPS Ground Advantage\', \'USPS Ground Advantage Cubic\', \'Media Mail\', \'Connect Local Machinable DDU\', \'Connect Local Machinable DDU Flat Rate Box\', \'Connect Local Machinable DDU Small Flat Rate Bag\', \'Connect Local Machinable DDU Large Flat Rate Bag\', \'Priority Mail\', \'Priority Mail Cubic\', \'Priority Mail Flat Rate Envelope\', \'Priority Mail Padded Flat Rate Envelope\', \'Priority Mail Legal Flat Rate Envelope\', \'Priority Mail Small Flat Rate Box\', \'Priority Mail Medium Flat Rate Box\', \'Priority Mail Large Flat Rate Box\', \'Priority Mail Large Flat Rate Box APO/FPO/DPO\', \'Priority Mail Express\', \'Priority Mail Express Flat Rate Envelope\', \'Priority Mail Express Padded Flat Rate Envelope\', \'Priority Mail Express Legal Flat Rate Envelope\', \'First-Class Package International Service Machinable ISC Single-piece\', \'Priority Mail International ISC Single-piece\', \'Priority Mail International ISC Flat Rate Envelope\', \'Priority Mail International Machinable ISC Padded Flat Rate Envelope\', \'Priority Mail International ISC Legal Flat Rate Envelope\', \'Priority Mail International Machinable ISC Small Flat Rate Box\', \'Priority Mail International Machinable ISC Medium Flat Rate Box\', \'Priority Mail International Machinable ISC Large Flat Rate Box\', \'Priority Mail Express International ISC Single-piece\', \'Priority Mail Express International ISC Flat Rate Envelope\', \'Priority Mail Express International ISC Legal Flat Rate Envelope\', \'Priority Mail Express International ISC Padded Flat Rate Envelope\'], ',
                            'use_function' => 'zen_cfg_uspsr_showservices',
                            'date_added' => 'now()'
                        ]);
                    } else {
                        $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_TYPES', [
                            'configuration_title' => 'Shipping Methods (Domestic and International)',
                            'configuration_value' => '0, 70, 0.00, 0, 20, 0.00, 0, 70, 0.00, 0, 25, 0.00, 0, 25, 0.00, 0, 25, 0.00, 0, 25, 0.00, 0, 70, 0.00, 0, 20, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 4, 0.00, 0, 70, 0.00, 0, 4, 0.00, 0, 4, 0.00, 0, 4, 0.00, 0, 4, 0.00, 0, 20, 0.00, 0, 20, 0.00, 0, 70, 0.00, 0, 4, 0.00, 0, 4, 0.00, 0, 4, 0.00',
                            'configuration_description' => 'Choose the services that you want to offer to your customers.<br><br><b>Checkbox:</b> Select the services to be offered (Can also click on the service name in certain browsers.)<br><br><b>Min/Max</b> Choose a custom minimum/maximum for the selected service. If the cart as a whole (the items plus any tare settings) fail to make weight, the method will be skipped. Keep in mind that each service also has its own maximums that will be controlled regardless of what was set here. (Example: entering 5 lbs for International First-Class Mail will be ignored since the International First-Class Mail has a hard limit of 4 lbs.)<br><br><b>Handling:</b> A handling charge for that particular method (will be added on to the quote plus any services charges that are applicable).<br><br>USPS returns methods based on cart weights. Enter the weights in your site\'s configured standard. (The cart will handle conversions as necessary.)',
                            'configuration_group_id' => 6,
                            'sort_order' => 0,
                            'set_function' => 'zen_cfg_uspsr_services([\'USPS Ground Advantage\', \'USPS Ground Advantage Cubic\', \'Media Mail\', \'Connect Local Machinable DDU\', \'Connect Local Machinable DDU Flat Rate Box\', \'Connect Local Machinable DDU Small Flat Rate Bag\', \'Connect Local Machinable DDU Large Flat Rate Bag\', \'Priority Mail\', \'Priority Mail Cubic\', \'Priority Mail Flat Rate Envelope\', \'Priority Mail Padded Flat Rate Envelope\', \'Priority Mail Legal Flat Rate Envelope\', \'Priority Mail Small Flat Rate Box\', \'Priority Mail Medium Flat Rate Box\', \'Priority Mail Large Flat Rate Box\', \'Priority Mail Large Flat Rate Box APO/FPO/DPO\', \'Priority Mail Express\', \'Priority Mail Express Flat Rate Envelope\', \'Priority Mail Express Padded Flat Rate Envelope\', \'Priority Mail Express Legal Flat Rate Envelope\', \'First-Class Package International Service Machinable ISC Single-piece\', \'Priority Mail International ISC Single-piece\', \'Priority Mail International ISC Flat Rate Envelope\', \'Priority Mail International Machinable ISC Padded Flat Rate Envelope\', \'Priority Mail International ISC Legal Flat Rate Envelope\', \'Priority Mail International Machinable ISC Small Flat Rate Box\', \'Priority Mail International Machinable ISC Medium Flat Rate Box\', \'Priority Mail International Machinable ISC Large Flat Rate Box\', \'Priority Mail Express International ISC Single-piece\', \'Priority Mail Express International ISC Flat Rate Envelope\', \'Priority Mail Express International ISC Legal Flat Rate Envelope\', \'Priority Mail Express International ISC Padded Flat Rate Envelope\'], ',
                            'use_function' => 'zen_cfg_uspsr_showservices',
                            'date_added' => 'now()'
                        ]);
                    }

                    $messageStack->add_session('<strong>USPSr Warning:</strong> Due to changes in configuration, you must now go to <a href="' . zen_href_link(FILENAME_DEFAULT, 'cmd=modules&set=shipping&module=uspsr') . '">Modules > Shipping > USPSr</a> and reselect your desired USPS Shipping Methods.', 'warning');

                    // Rename MODULE_SHIPPING_USPSR_PROCESSING_CLASS to MODULE_SHIPPING_USPSR_MEDIA_CLASS
                    $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET configuration_key = 'MODULE_SHIPPING_USPSR_MEDIA_CLASS' WHERE configuration_key = 'MODULE_SHIPPING_USPSR_PROCESSING_CLASS' ");

                    if ($module_installed) {
                        // The PROCESSING_CLASS, now MEDIA_CLASS, changed quite a bit.
                        $this->updateConfigurationKey(
                            'MODULE_SHIPPING_USPSR_MEDIA_CLASS',
                            [
                                'configuration_title' => 'Packaging Class - Media Mail',
                                'configuration_description' => 'For Media Mail only, are your packages typically machinable?<br><br>\"Machinable\" means a mail piece designed and sized to be processed by automated postal equipment. Typically this is rigid mail, that fits a certain shape and is within a certain weight (no more than 25 pounds for Media Mail). If your normal packages are within these guidelines, set this flag to \"Machinable\". Otherwise, set this to \"Nonstandard\". (If your customer order\'s total weight or package size falls outside this limit, regardless of the setting, the module will set the package to \"Nonstandard\".) (If your customer order\'s total weight or package size falls outside of this limit, regardless of the setting, the module will set the package to \"Nonstandard\".) <br><br>This applies only to Media Mail. All other mail services will have their \"Machinability\" status determined by the weight of the cart and the size of the package entered below.',
                                'set_function' => 'zen_cfg_select_option([\'Machinable\', \'Nonstandard\'], ',
                            ]
                        );

                        // Language error in the description of Exclusions from Media Mail
                        $this->updateConfigurationKey(
                            'MODULE_SHIPPING_USPSR_MEDIA_MAIL_EXCLUDE',
                            [
                                'configuration_title' => 'Categories to Excluded from Media Mail',
                            ]
                        );

                        // The description Domestic and International Services changed
                        $this->updateConfigurationKey(
                            'MODULE_SHIPPING_USPSR_DMST_SERVICES',
                            [
                                'configuration_description' => 'Pick which add-ons you wish to offer as a part of the shipping cost quote for domestic packages. (The USPS API will do the math as necessary.)<br><br><strong>CAUTION:</strong> Not all options apply to all services.<br>',
                            ]
                        );

                        $this->updateConfigurationKey(
                            'MODULE_SHIPPING_USPSR_INTL_SERVICES',
                            [
                                'configuration_description' => 'Pick which add-ons you wish to offer as a part of the shipping cost quote for international packages. (The USPS API will do the math as necessary.)<br><br><strong>CAUTION:</strong> Not all options apply to all services.<br>',
                            ]
                        );

                        // Language changed for USPSR
                        $this->updateConfigurationKey(
                            'MODULE_SHIPPING_USPSR_CONTRACT_TYPE',
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

                        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_DIMENSIONAL_CLASS', [
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
                    
                    }

                    // Cosmetic change: changing the description to match its new one. (This should only change the ONE line).
                    $this->executeInstallerSql("UPDATE " . TABLE_PLUGIN_CONTROL . " SET description = 'This module provides sellers the ability to offer United States Postal Service (USPS) shipping rates to their customers during checkout. This is done by pulling the rates directly from the USPS\' REST API using OAuth.<br><br>This module supports versions 1.5.8 onward innately. (Support from 1.5.7 and backward is not necessarily guaranteed but is plausible.) This script was primarily written with PHP8 in mind. (It might have problems working with PHP7.)' WHERE unique_key = 'USPSRestful' ");

                case "v1.1.2": // Released 2025-03-07
                case "v1.1.1": // Released 2025-03-07, subsequently deleted and replaced with 1.1.2
                case "v1.0.0": // Released 2025-02-18
                    // Changes to the database from v1.0.0 should be put here. (No keys should be ADDED here, only updates.)
                    $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_DEBUG_MODE', [
                        'configuration_description' => 'Would you like to enable debug modes?<br><br><em>"Generate Logs"</em> - This module will generate log files for each and every call to the USPS API Server (including the admin side viability check).<br><br>"<em>Display errors</em>" - If set, this means that any API errors that are caught will be displayed in the storefront.<br><br><em>CAUTION:</em> Each log file can be as big as 300KB in size.',
                    ]);

                case "v1.2.0": // Released 2025-03-15
                case "v1.3.0": // Released 2025-08-17
                case "v1.3.1": // Released 2025-08-24 (There aren't any changes module was between 1.3.1 and 1.3.2 but it doesn't hurt to rerun)
                    // New clickable row - Changing the description
                    $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_TYPES', [
                        'configuration_description' => 'Choose the services that you want to offer to your customers.<br><br><b>Checkbox:</b> Select the services to be offered. (Can also click on the service name in certain browsers.)<br><br><b>Min/Max</b> Choose a custom minimum/maximum for the selected service. If the cart as a whole (the items plus any tare settings) fail to make weight, the method will be skipped. Keep in mind that each service also has its own maximums that will be controlled regardless of what was set here. (Example: entering 5 lbs for International First-Class Mail will be ignored since the International First-Class Mail has a hard limit of 4 lbs.)<br><br><b>Handling:</b> A handling charge for that particular method (will be added on to the quote plus any services charges that are applicable).<br><br>USPS returns methods based on cart weights. Enter the weights in your site\'s configured standard. (The cart will handle conversions as necessary.)',
                    ]);

                    // No need to check for ZenCart version, if you're running encapsulated... this MUST be 2.x.x+
                    $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_HANDLING_TIME', [ 
                        'configuration_description' => 'In whole numbers, how many days does it take for you to dispatch your packages to the USPS. (Enter as a whole number only. Between 0 and 30. This will be added to the estimated delivery date or time as needed.)',
                        'set_function' => '',
                        'val_function' => '{"error":"MODULE_SHIPPING_USPSR_HANDLING_DAYS","id":"FILTER_VALIDATE_INT","options":{"options":{"min_range": 0, "max_range": 30}}}',
                    ]);

                    // Changing descriptions
                    $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_DMST_SERVICES', [
                        'configuration_title' => 'Shipping Add-ons (Domestic Packages)',
                    ]);

                    $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_INTL_SERVICES', [
                        'configuration_title' => 'Shipping Add-ons (International Packages)',
                    ]);


                    // Adding new key for Domestic and International Letter Services.
                    if (!defined('MODULE_SHIPPING_USPSR_DMST_LETTER_SERVICES')) {
                        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_DMST_LETTER_SERVICES', [
                            'configuration_title' => 'Shipping Add-ons (Domestic Letters)',
                            'configuration_value' => '',
                            'configuration_description' => 'Pick which add-ons you wish to offer as a part of the shipping cost quote for domestic letters (First Class Mail Letters). (The USPS API will do the math as necessary.)<br>',
                            'configuration_group_id' => 6,
                            'sort_order' => 0,
                            'set_function' => 'zen_cfg_uspsr_extraservices(\'domestic-letters\', ',
                            'use_function' => 'zen_cfg_uspsr_extraservices_display',
                            'date_added' => 'now()'
                        ]);
                    }

                    if (!defined('MODULE_SHIPPING_USPSR_INTL_LETTER_SERVICES')) {
                        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_INTL_LETTER_SERVICES', [
                            'configuration_title' => 'Shipping Add-ons (International Letters)',
                            'configuration_value' => '',
                            'configuration_description' => 'Pick which add-ons you wish to offer as a part of the shipping cost quote for international letters (First Class International Letters). (The USPS API will do the math as necessary.)<br>',
                            'configuration_group_id' => 6,
                            'sort_order' => 0,
                            'set_function' => 'zen_cfg_uspsr_extraservices(\'intl-letters\', ',
                            'use_function' => 'zen_cfg_uspsr_extraservices_display',
                            'date_added' => 'now()'
                        ]);
                    }

                    if (!defined('MODULE_SHIPPING_USPSR_LTR_DIMMENSIONS')) {
                        // Adding in support Letter Dimmensions
                        if (defined('SHIPPING_DIMENSION_UNITS') && SHIPPING_DIMENSION_UNITS == "centimeters") {
                            $this->addConfigurationKey('MODULE_SHIPPING_USPSR_LTR_DIMMENSIONS', [
                                'configuration_title' => 'Typical Letter Dimensions (Domestic and International)',
                                'configuration_value' => '21.9075, 21.9075, 13.6525, 13.6525, 4.1275, 4.1275',
                                'configuration_description' => 'The Minimum Length, Height, and Thickness are used to determine shipping methods available for sending of letters.<br><br>While per-item dimensions are not supported by this module at this time, the minimums listed below are sent to USPS for obtaining Rate Quotes.<br><br>In most cases, these Minimums should never have to be changed.<br><br><em>These measurements will be converted to inches as part of the quoting process as your cart was set to centimeters when it was installed. If you change your cart setting, you will need to reenter these values.<br>',
                                'configuration_group_id' => 6,
                                'sort_order' => 0,
                                'set_function' => 'zen_cfg_uspsr_ltr_dimmensions(',
                                'use_function' => 'zen_cfg_uspsr_showdimmensions',
                                'date_added' => 'now()'
                            ]);
                        } else {
                            $this->addConfigurationKey('MODULE_SHIPPING_USPSR_LTR_DIMMENSIONS', [
                                'configuration_title' => 'Typical Letter Dimensions (Domestic and International)',
                                'configuration_value' => '4.125, 4.125, 9.5, 9.5, 0.007, 0.007',
                                'configuration_description' => 'The Minimum Minimum Length, Height, and Thickness are used to determine shipping methods available for sending of letters.<br><br>While per-item dimensions are not supported at this time, the minimums listed below are sent to USPS for obtaining Rate Quotes.<br><br>In most cases, these Minimums should never have to be changed.<br>These measurements should be in inches.<br>',
                                'configuration_group_id' => 6,
                                'sort_order' => 0,
                                'set_function' => 'zen_cfg_uspsr_ltr_dimmensions(',
                                'use_function' => 'zen_cfg_uspsr_showdimmensions',
                                'date_added' => 'now()'
                            ]);
                        }
                    }

                    if (!defined('MODULE_SHIPPING_USPSR_LTR_PROCESSING')) {
                        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_LTR_PROCESSING', [
                            'configuration_title' => 'Packaging Class - Letters',
                            'configuration_value' => 'Letters',
                            'configuration_description' => 'How would you class the packaging of your letters?<br><br><em>\"Letters\"</em> refers to packaging that is rigid in shape and form, like a plain white envelope (#10). A letter is a rectangular piece no more than 6.125" by 11.5" with a thickness no greater than .25" inches. (Anything greater than this or smaller than the minimums will be treated as non-machineable.<br><br><em>\"Flats\"</em> typically refer to large envelopes, newsletters, and magazines. Flats must be no greater than 12 inches by 15 inches with a thickness no greater than .75 inches.<br><br><em>\"Cards\"</em> plainly mean simple postcards with specific measurements.<br><br>Choose the style that best fits how you (on average) ship out your packages.<br><em>This selection only applies to First Class Mail Letters and First Class Mail International Letters.</em><br>',
                            'configuration_group_id' => 6,
                            'sort_order' => 0,
                            'set_function' => 'zen_cfg_select_option([\'Letters\', \'Flats\', \'Cards\'], ',
                            'date_added' => 'now()'
                        ]);
                    }

                    if (!defined('MODULE_SHIPPING_USPSR_LTR_MACHINEABLE_FLAGS')) {
                        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_LTR_MACHINEABLE_FLAGS', [
                            'configuration_title' => 'Machineability Flags (First-Class Mail Letter)',
                            'configuration_value' => '--none--',
                            'configuration_description' => 'When sending items via USPS First-Class Mail, check below if any applies to the typical method of how you send your orders.<br><br>- <em>Polybagged</em>: Is the letter/flat/card polybagged, polywrapped, enclosed in any plastic material, or has an exterior surface made of a material that is not paper. Windows in envelopes made of paper do not make mailpieces nonmachinable. Attachments allowable under applicable eligibility standards do not make mailpieces nonmachinable.<br><br>- <em>ClosureDevices</em>: Does the letter/flat/card have clasps, strings, buttons, or similar closure devices?<br><br>- <em>LooseItems</em>: Does the letter/flat/card contain items such as pens, pencils, keys, or coins that cause the thickness of the mailpiece to be uneven; or loose keys or coins or similar objects not affixed to the contents within the mailpiece. Loose items may cause a letter to be nonmailable when mailed in paper envelopes.<br><br>- <em>Rigid</em>: Is the letter/flat/card too rigid?<br><br>- <em>SelfMailer</em>: Is your item a folded self-mailer?<br><br>- <em>Booklet</em>: Is the letter/flat/card a booklet?',
                            'configuration_group_id' => 6,
                            'sort_order' => 0,
                            'set_function' => 'zen_cfg_select_multioption([\'Polybagged\', \'ClosureDevices\', \'LooseItems\', \'Rigid\', \'SelfMailer\', \'Booklet\'], ',
                            'use_function' => '',
                            'date_added' => 'now()'
                        ]);
                    }

                    case "v1.3.2": // Released 2025-08-25: No database changes made from 1.3.2 to 1.4.0. All changes were to the module itself.
                    break;
            }
        }

        // Update the version setting to match the new version. (This happens regardless of version, so this should sit outside version check.)
        // 1.4.0+ change: Updated to display the correct "read_only" function.
        $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_VERSION', [
            'configuration_value' => $this->version,
            'set_function' => "zen_cfg_read_only([\'$this->version\'], ",
            'use_function' => '',
            'val_function' => '',
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
            'MODULE_SHIPPING_USPSR_SQUASH_OPTIONS',
            'MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT',
            'MODULE_SHIPPING_USPSR_HANDLING_TIME',
            'MODULE_SHIPPING_USPSR_DIMMENSIONS',
            'MODULE_SHIPPING_USPSR_LTR_DIMMENSIONS',
            'MODULE_SHIPPING_USPSR_LTR_PROCESSING',
            'MODULE_SHIPPING_USPSR_TYPES',
            'MODULE_SHIPPING_USPSR_MEDIA_MAIL_EXCLUDE',
            'MODULE_SHIPPING_USPSR_CONNECT_LOCAL_ZIP',
            'MODULE_SHIPPING_USPSR_DMST_SERVICES',
            'MODULE_SHIPPING_USPSR_INTL_SERVICES',
            'MODULE_SHIPPING_USPSR_DMST_LETTER_SERVICES',
            'MODULE_SHIPPING_USPSR_INTL_LETTER_SERVICES',
            'MODULE_SHIPPING_USPSR_LTR_MACHINEABLE_FLAGS',
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
