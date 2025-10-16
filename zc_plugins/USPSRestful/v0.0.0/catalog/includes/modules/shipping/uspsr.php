<?php
/**
 * USPS Shipping (RESTful) for Zen Cart
 * Version 0.0.0
 *
 * @copyright Portions Copyright 2004-2025 Zen Cart Team
 * @copyright Portions adapted from 2012 osCbyJetta
 * @author Paul Williams (retched)
 * @version $Id: uspsr.php 0000-00-00 retched Version 0.0.0 $
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
if (!defined('IS_ADMIN_FLAG')) {
    exit('Illegal Access');
}

/**
 * If you're using a version of ZenCart older than 2.0.0, you should probably change
 * these to match your cart BEFORE you install the module in the backend.
 *
 * The settings are natural to 2.0.0 onward and should not be set here but rather in the backend.
 */
if (version_compare(PROJECT_VERSION_MAJOR . "." . PROJECT_VERSION_MINOR, '2.0.0', "<")) {

    /**
     * The length measurement standard of the ZenCart installation
     * Valid values: "inches" or "centimeters"
     */
    define('SHIPPING_DIMENSION_UNITS', 'inches');

    /**
     * The weight measurement standard of the ZenCart installation
     * Valid values: "lbs" (for pounds) or "kgs" (for kilograms). (DO NOT USE A PERIOD)
     */
    define('SHIPPING_WEIGHT_UNITS', 'lbs');
}

class uspsr extends base
{
    public $code, $icon, $title, $enabled, $description, $tax_class, $tax_basis, $sort_order = 0, $quotes = [];
    /**
     * Flag to see if Debug mode is enabled, print error_logs where necessary.
     *
     * @var bool
     */

    protected $debug_enabled = FALSE, $typeCheckboxesSelected = [], $debug_filename, $bearerToken, $quote_weight, $_check, $machinable, $shipment_value = 0, $insured_value = 0, $uninsured_value = 0, $orders_tax = 0, $is_us_shipment, $is_apo_dest = FALSE, $usps_countries, $enable_media_mail;
    protected $api_base = 'https://apis.usps.com/';
    protected $_standard, $ltrQuote, $pkgQuote, $uspsStandards, $uspsLetter;


    protected $commError, $commErrNo, $commInfo;

    private const USPSR_CURRENT_VERSION = 'v0.0.0';
    private const ZEN_CART_PLUGIN_ID = 2395;

    /**
     * This holds all of the USPS Zip Codes which are either APO (Air/Army Post Office), FPOs (Fleet Post Office), and
     * DPOs (Diplomatic Post Offices). This should not be removed as it will disable the APO/FPO/DPO Flat Rate.
     * @var array
     */
    private const USPSR_MILITARY_MAIL_ZIP = [
        '09002', '09003', '09004', '09005', '09006', '09008', '09009', '09010', '09011', '09012',
        '09013', '09014', '09015', '09016', '09017', '09018', '09020', '09021', '09034', '09044',
        '09046', '09049', '09053', '09060', '09067', '09068', '09069', '09079', '09094', '09095',
        '09096', '09101', '09103', '09104', '09107', '09112', '09114', '09116', '09118', '09123',
        '09126', '09128', '09131', '09135', '09136', '09138', '09140', '09142', '09154', '09160',
        '09170', '09171', '09172', '09173', '09174', '09175', '09176', '09177', '09178', '09179',
        '09180', '09181', '09186', '09203', '09204', '09205', '09211', '09213', '09214', '09216',
        '09227', '09240', '09241', '09242', '09250', '09261', '09263', '09264', '09265', '09266',
        '09276', '09277', '09278', '09279', '09280', '09281', '09282', '09283', '09284', '09285',
        '09287', '09288', '09289', '09290', '09291', '09292', '09293', '09294', '09295', '09296',
        '09297', '09298', '09299', '09301', '09304', '09305', '09306', '09307', '09309', '09310',
        '09311', '09312', '09315', '09316', '09321', '09330', '09333', '09343', '09348', '09357',
        '09365', '09366', '09401', '09403', '09410', '09421', '09424', '09447', '09454', '09456',
        '09459', '09461', '09463', '09464', '09467', '09468', '09469', '09470', '09487', '09488',
        '09489', '09490', '09491', '09494', '09498', '09501', '09502', '09503', '09504', '09505',
        '09506', '09507', '09508', '09509', '09510', '09511', '09512', '09513', '09514', '09516',
        '09517', '09522', '09523', '09524', '09532', '09533', '09534', '09541', '09542', '09543',
        '09544', '09545', '09550', '09554', '09556', '09564', '09565', '09566', '09567', '09568',
        '09569', '09570', '09573', '09575', '09576', '09577', '09578', '09579', '09581', '09582',
        '09583', '09586', '09587', '09588', '09590', '09591', '09592', '09593', '09594', '09595',
        '09596', '09599', '09600', '09602', '09603', '09604', '09605', '09606', '09608', '09609',
        '09610', '09613', '09614', '09618', '09620', '09621', '09622', '09623', '09624', '09625',
        '09627', '09630', '09633', '09634', '09636', '09642', '09643', '09645', '09647', '09648',
        '09649', '09701', '09702', '09703', '09704', '09705', '09706', '09707', '09708', '09709',
        '09710', '09711', '09712', '09714', '09715', '09716', '09717', '09718', '09719', '09720',
        '09722', '09723', '09724', '09725', '09726', '09727', '09728', '09729', '09730', '09731',
        '09732', '09733', '09734', '09735', '09736', '09737', '09738', '09739', '09741', '09742',
        '09743', '09744', '09745', '09748', '09749', '09750', '09751', '09752', '09753', '09754',
        '09755', '09756', '09759', '09761', '09762', '09769', '09777', '09780', '09801', '09802',
        '09803', '09804', '09805', '09806', '09807', '09808', '09809', '09810', '09811', '09812',
        '09813', '09814', '09815', '09816', '09817', '09818', '09819', '09820', '09821', '09822',
        '09823', '09824', '09825', '09826', '09827', '09828', '09829', '09830', '09831', '09832',
        '09834', '09835', '09836', '09837', '09838', '09842', '09844', '09845', '09846', '09847',
        '09848', '09853', '09854', '09855', '09856', '09857', '09858', '09859', '09860', '09862',
        '09864', '09867', '09869', '09870', '09873', '09874', '09875', '09877', '09880', '09892',
        '09895', '09898', '09902', '09903', '09904', '09905', '09974', '09975', '09976', '09977',
        '09978', '09980', '09981', '09982', '09983', '09984', '34001', '34002', '34004', '34007',
        '34008', '34009', '34010', '34011', '34012', '34020', '34021', '34022', '34023', '34024',
        '34025', '34030', '34031', '34032', '34033', '34034', '34035', '34036', '34037', '34039',
        '34041', '34042', '34055', '34058', '34060', '34066', '34067', '34068', '34069', '34071',
        '34072', '34078', '34080', '34081', '34082', '34083', '34084', '34085', '34086', '34087',
        '34088', '34089', '34090', '34091', '34092', '34093', '34094', '34095', '96201', '96202',
        '96203', '96204', '96205', '96206', '96207', '96208', '96209', '96210', '96212', '96213',
        '96214', '96215', '96218', '96224', '96251', '96257', '96258', '96260', '96262', '96264',
        '96266', '96269', '96271', '96273', '96275', '96276', '96278', '96283', '96284', '96297',
        '96301', '96303', '96306', '96309', '96310', '96311', '96315', '96319', '96321', '96322',
        '96326', '96328', '96331', '96336', '96337', '96338', '96339', '96343', '96346', '96347',
        '96349', '96350', '96351', '96362', '96365', '96367', '96368', '96370', '96371', '96372',
        '96373', '96374', '96375', '96376', '96377', '96378', '96379', '96380', '96382', '96384',
        '96385', '96386', '96387', '96388', '96389', '96401', '96502', '96503', '96504', '96505',
        '96506', '96507', '96511', '96515', '96516', '96517', '96520', '96521', '96530', '96531',
        '96532', '96534', '96535', '96537', '96540', '96542', '96543', '96548', '96549', '96550',
        '96551', '96552', '96553', '96554', '96555', '96557', '96562', '96577', '96578', '96595',
        '96598', '96599', '96601', '96602', '96603', '96604', '96605', '96607', '96610', '96611',
        '96612', '96613', '96615', '96616', '96619', '96620', '96628', '96629', '96632', '96633',
        '96641', '96642', '96643', '96644', '96645', '96649', '96650', '96657', '96660', '96661',
        '96662', '96663', '96664', '96665', '96666', '96667', '96668', '96669', '96670', '96671',
        '96672', '96673', '96674', '96675', '96677', '96678', '96679', '96681', '96682', '96683',
        '96686', '96691', '96692', '96693', '96694', '96695', '96696', '96698'
    ];

    /**
     * Main constructor class.
     *
     * A shipping-module’s class constructor performs initialization of its
     * class variables and determines whether the shipping module is
     * enabled for the current order. Upon completion, the class
     * variable enabled identifies whether (true) or not (false) the
     * module is to be enabled for storefront processing.
     */
    public function __construct()
    {
        global $template, $current_page_base, $current_page;

        $this->bearerToken = $_SESSION['usps_token'] ?? NULL;
        $this->machinable = (defined('MODULE_SHIPPING_USPSR_MACHINABLE') ? MODULE_SHIPPING_USPSR_MACHINABLE : 'Machinable');
        $this->_standard = (defined('SHIPPING_WEIGHT_UNITS') ? SHIPPING_WEIGHT_UNITS : 'lbs');

        $this->code = 'uspsr';

        if (IS_ADMIN_FLAG === true) {
            $this->title = MODULE_SHIPPING_USPSR_TEXT_TITLE_ADMIN;
        } else {
            $this->title = (defined('MODULE_SHIPPING_USPSR_TITLE_SIZE') && MODULE_SHIPPING_USPSR_TITLE_SIZE === 'Short' ? MODULE_SHIPPING_USPSR_TEXT_SHORT_TITLE : MODULE_SHIPPING_USPSR_TEXT_TITLE);
        }

        $this->description = MODULE_SHIPPING_USPSR_TEXT_DESCRIPTION;
        $this->sort_order = (defined('MODULE_SHIPPING_USPSR_SORT_ORDER')) ? MODULE_SHIPPING_USPSR_SORT_ORDER : null;

        if ($this->sort_order === null) {
            return false;
        }

        $this->enabled = (defined('MODULE_SHIPPING_USPSR_STATUS')) ? (MODULE_SHIPPING_USPSR_STATUS === 'True') : FALSE;
        $this->tax_class = (defined('MODULE_SHIPPING_USPSR_TAX_CLASS')) ? (int) MODULE_SHIPPING_USPSR_TAX_CLASS : NULL;
        $this->tax_basis = (defined('MODULE_SHIPPING_USPSR_TAX_BASIS')) ? MODULE_SHIPPING_USPSR_TAX_BASIS : NULL;

        // -----
        // Set debug-related variables for use by the uspsrDebug method.
        //
        $this->debug_enabled = (MODULE_SHIPPING_USPSR_DEBUG_MODE !== '--none--');
        $this->debug_filename = DIR_FS_LOGS . '/SHIP_uspsr_Debug_' . (IS_ADMIN_FLAG ? 'adm_' : '') . date('Ymd_His') . '.log';

        $this->typeCheckboxesSelected = explode(', ', MODULE_SHIPPING_USPSR_TYPES);
        $this->update_status();

        // -----
        // If the shipping module is enabled, some additional environment-specific checks are
        // needed to see if the module can remain enabled and/or to notify the current admin
        // of any configuration issues.
        //
        if ($this->enabled === true) {
            // -----
            // Admin-specific processing, limited to "Modules :: Shipping" so that any additions
            // to the shipping-module's name don't show up during 'Edit Orders' (and possibly other
            // admin plugins).
            //
            if (IS_ADMIN_FLAG === true) {
                // -----
                // During admin processing (Modules :: Shipping), let the admin know of some test
                // conditions.  Limiting to that script so that the additions don't show up during
                // Edit Orders (and possibly others).
                //
                if ($current_page === 'modules.php') {
                    $this->adminInitializationChecks();
                }
                // -----
                // Otherwise, storefront checks and initializations.
                //
            } else {
                if (isset($template)) {
                    $this->icon = $template->get_template_dir('shipping_usps.gif', DIR_WS_TEMPLATE, $current_page_base, 'images/icons') . '/shipping_usps.gif';
                }
                $this->storefrontInitialization();
            }
        }

        $this->notify('NOTIFY_SHIPPING_USPS_CONSTRUCTOR_COMPLETED');
    }

    protected function storefrontInitialization()
    {
        global $order;

        /**
         * Quick return if the shipping-module's configuration will not allow
         * it to gather valid USPS quotes.  The shipping-module is disabled
         * if this is the case.
         */

        if ($this->checkConfiguration() === false) {
            return;
        }

        /**
         * Issue a notification to let a site-specific observer 'disallow' the
         * current shopping-cart contents to be shipped via USPS.
         *
         * Sourced from original ZC USPS Module
         */
        $contents_ok = true;
        $this->notify('NOTIFY_USPS_SHIPPING_CHECK_CART', 'uspsr', $contents_ok);
        if ($contents_ok === false) {
            $this->enabled = false;
            return;
        }

        /**
         * use USPS translations for US shops (USPS treats certain regions as
         * "US States" instead of as different "countries", so we translate here)
         */
        $this->usps_countries = $this->usps_translation();

        // If the order doesn't have a zip code or have a valid zip code (5 or 9 digit), and it is a US order: STOP
        // (typically because you're visiting the shopping cart estimator)
        $delivery_postcode = (array_key_exists('postcode', $order->delivery) && !empty($order->delivery['postcode']) ? $order->delivery['postcode'] : NULL);

        // Is this going to the US and has a VALID (5 or 9 digit) zipcode?
        if ($this->is_us_shipment && !uspsr_validate_zipcode($delivery_postcode)) { // If this is a US bound package and a valid zip code
            $this->enabled = false; // Disable the module
            return;
        } else { // Otherewise it's a not US Bound package OR it's a US bound package and HAS a zipcode.
            $this->enabled = true;
            return;
        }

    }

    protected function usps_translation()
    {
        $this->notify('NOTIFY_SHIPPING_USPS_TRANSLATION');
        global $order;
        $delivery_country = 'US';
        if (SHIPPING_ORIGIN_COUNTRY === '223') {
            switch ($order->delivery['country']['iso_code_2']) {
                case 'AS': // Samoa American
                case 'GU': // Guam
                case 'MP': // Northern Mariana Islands
                case 'PW': // Palau
                case 'PR': // Puerto Rico
                case 'VI': // Virgin Islands US
                // which is right
                case 'FM': // Micronesia, Federated States of
                    break;
                default:
                    $delivery_country = $order->delivery['country']['iso_code_2'];
                    break;
            }
        } else {
            $delivery_country = $order->delivery['country']['iso_code_2'];
        }

        // -----
        // If the delivery country is the US, set a multi-use processing flag
        // to simplify the remaining code.
        //
        $this->is_us_shipment = ($delivery_country === 'US');

        return $delivery_country;
    }

    public function quote($method = '')
    {
        global $order, $shipping_weight, $shipping_num_boxes, $currencies;

        // Make a token for the API
        // If the Bearer Token is empty, make it.
        if (!zen_not_null($this->bearerToken))
            $this->getBearerToken();

        // What unit are we working with?
        switch ($this->_standard) {
            case 'kgs':
                // 1 kgs = 2.2046226218487758 lbs
                $this->quote_weight = $shipping_weight * 2.2046226218487758;
                break;
            default:
            case 'lbs':
                // Since this is in pounds, no conversion is necessary.
                // Additionally, this API doesn't want the weight in ounces and pounds, it only wants pounds and parts there of. So no changing.
                $this->quote_weight = $shipping_weight;
                break;

        }

        // Set the weight back 

        /**
         * Determine if package is machinable or not - Media Mail Only
         * API will either return both the machinable rate and non-machinable rate or one or the other.
         *
         * The store owner will choose a preference. If the preference can be met, show that rate. If it can't be met, but there is only
         * one rate available... show THAT rate.
         *
         * By definition, Media Mail Machinable parcels must weight less than 25lbs with no minimum. Additionally, a package to be machineable
         * cannot be more than 22 inches long, 18 inches wide, 15 inches high. The USPS considers the longest measurement given to the length, the
         * 2nd longest measurement is considered it's width, and the third longest it's height. (Actually it considers "length is the measurement of
         * the longest dimension and girth is the distance around the thickest part".)
         *
         * If all else fails, follow the module setting.
         *
         * For all other services, this is handled by the API.
         */

        // Rebuild the dimmensions array
        $pkg_dimmensions = array_filter(explode(', ', MODULE_SHIPPING_USPSR_DIMMENSIONS));
        $pkg_dimmensions = [$pkg_dimmensions[0], $pkg_dimmensions[2], $pkg_dimmensions[4]]; // Media Mail is only a US only service, so we'll only use those three.
        array_walk($pkg_dimmensions, function (&$value) {
            $value = trim($value);
        }); // Quickly remove white space
        rsort($pkg_dimmensions);

        switch (true) {
            case ($this->quote_weight > 25):
                // override admin choice, too heavy, 25 lbs is the limit.
                $this->machinable = 'Nonstandard';
                break;

            // override admin choice, package cannot be more than 22 inches at it's longest side, 18 inches wide, 15 inches tall
            case ($pkg_dimmensions[0] > 22 || $pkg_dimmensions[1] > 18 || $pkg_dimmensions[2] > 15):
                $this->machinable = 'Nonstandard';
                break;

            default:
                // admin choice on what to use
                $this->machinable = MODULE_SHIPPING_USPSR_MEDIA_CLASS;
                break;
        }

        // -----
        // Log, if enabled, the base USPS configuration for this quote request.
        //
        $this->_calcCart();
        $this->quoteLogConfiguration();

        // request quotes
        $this->notify('NOTIFY_SHIPPING_USPS_BEFORE_GETQUOTE', [], $order, $this->quote_weight, $shipping_num_boxes);

        // Create the main quotes (both letters and packages)
        $this->_getQuote();

        // There are two quote fields being used a package

        // Start with package quote
        $uspsQuote = json_decode($this->pkgQuote, TRUE);

        // Take the Letters Quote and add it to a temp holder
        $_letter = json_decode($this->ltrQuote, TRUE);

        // If there isn't a quote in letters don't bother.
        if (isset($_letter['rates'])) {
            // Force the details of the Letter Request to match the other pieces from packages (adding a Mail Class to match Standards result, productName, and processingCategory)
            $_letter['rates'][0]['mailClass'] .= "_" . strtoupper(MODULE_SHIPPING_USPSR_LTR_PROCESSING); // This should yield something: FIRST-CLASS_MAIL_FLATS
            $_letter['rates'][0]['productName'] = ($this->usps_countries == 'US' ? 'First-Class Mail Letter' : 'First-Class Mail International Letter' );
            $_letter['rates'][0]['processingCategory'] = MODULE_SHIPPING_USPSR_LTR_PROCESSING;

            # Bug fix for letters since the Domestic metered rate from the API is four cents off. (International seems to come through as normal.)
            # @todo Maybe toggle if First Class Mail is metered or not?
            if ($this->usps_countries == 'US') {
                $_letter['rates'][0]['price'] += 0.04;
                $_letter['totalBasePrice'] += 0.04;
            }

            $uspsQuote['rateOptions'][] = $_letter;
        }

        if (!empty($uspsQuote)) {

            // Was a standards call made? If so, load it up.
            if (zen_not_null($this->uspsStandards)) {
                $uspsStandards = $this->uspsStandards;
            } else $uspsStandards = [];

            // ----
            // Selected Methods Builder

            // Notifier brought forward
            $this->notify('NOTIFY_SHIPPING_USPS_AFTER_GETQUOTE', [], $order, $this->quote_weight, $shipping_num_boxes, $uspsQuote);

            // Go through each of the $this->typeCheckboxesSelected and build a list.
            $selected_methods = [];
            $build_quotes = [];
            for ($i = 0; $i <= count($this->typeCheckboxesSelected) - 1; $i++) {
                if (!is_numeric($this->typeCheckboxesSelected[$i]) && zen_not_null($this->typeCheckboxesSelected[$i])) {
                    // Fool proofing the entry of the two values.
                    $limits = [(double) $this->typeCheckboxesSelected[$i + 1], (double) $this->typeCheckboxesSelected[$i + 2]];

                    // Does this need to be converted into pounds?
                    if (defined('SHIPPING_WEIGHT_UNITS') && SHIPPING_WEIGHT_UNITS == 'kgs') {
                        $limits[0] *= 2.2046226218487758;
                        $limits[1] *= 2.2046226218487758;

                        // 1 kgs = 2.2046226218487758 lbs.
                    }

                    $selected_methods[] = [
                        'min_weight' => min($limits),
                        'max_weight' => max($limits),
                        'method' => $this->typeCheckboxesSelected[$i],
                        'handling' => $this->typeCheckboxesSelected[$i + 3]
                    ];

                }
            }

            $message = '';
            $message .= "\n" . '===============================================' . "\n";
            $message .= 'Reviewing selected method options...' . "\n";
            $message .= print_r($selected_methods, TRUE);
            $this->uspsrDebug($message);

            
            // Order Handling Costs
            if ($order->delivery['country']['id'] === SHIPPING_ORIGIN_COUNTRY || $this->is_us_shipment === true) {
                // domestic/national
                $usps_handling_fee = (double) MODULE_SHIPPING_USPSR_HANDLING_DOMESTIC;
            } else {
                // international
                $usps_handling_fee = (double) MODULE_SHIPPING_USPSR_HANDLING_INTL;
            }

            // -----
            // Give an observer the opportunity to modify the overall USPS handling fee for the order.
            //
            $this->notify('NOTIFY_SHIPPING_USPS_AFTER_HANDLING', [], $order, $shipping_weight, $shipping_num_boxes, $usps_handling_fee);

            // ----
            // We have the new uni-quote (packages and letters)
            // Now build the mapping array
            $lookup = [];

            // Build lookup from rates
            foreach ($uspsQuote['rateOptions'] as $opt) {
                
                // Base Price of the rate, more in a second.
                $totalBasePrice = $opt['totalBasePrice'] ?? null; // get totalBasePrice if it exists

                // Main rates
                foreach ($opt['rates'] as $rate) {

                    // ---------------------------------------------
                    // Skip OPEN_AND_DISTRIBUTE rates, we don't use those.
                    // ---------------------------------------------
                    if ($rate['processingCategory'] === 'OPEN_AND_DISTRIBUTE') continue;

                    // ---------------------------------------------
                    // Setup the key (if productName is blank, use description instead)
                    // ---------------------------------------------
                    if (!empty($rate['productName'])) $name = $rate['productName'];
                    else {
                        $name = $rate['description'];
                        $rate['productName'] = $rate['description'];
                    }

                    // ---------------------------------------------
                    // Trim the extra characters off (looking at you 'Connect Local Machinable DDU ')
                    // ---------------------------------------------
                    $name = trim($name);
                    
                    // ---------------------------------------------
                    // Test to see what the totalBasePrice 
                    // For Packages: This will be the base fee plus any special fees. Will not include any services.
                    // For Letters: This will automatically add any special fees but will NOT add services
                    // ---------------------------------------------
                    $rate['totalBasePrice'] = $totalBasePrice ?? $rate['price']; // default to price if null/unset

                    // ---------------------------------------------
                    // Possible outcomes from the rate listings
                    // Possible outcomes:
                    // Priority Mail: Machinable + SP or Nonstandard + SP/DR/DN
                    // Priority Mail Express: Machinable + SP or Nonstandard + PA/DR/DN
                    // Ground Advantage: Machinable + SP or Nonstandard + SP/DR/DN/LO
                    // Media Mail: Machinable / Nonstandard, both with SP
                    // Priority Mail Cubic: Machinable , CP or Px/Qx
                    // Ground Advantage Cubic: Machinable , CP or Px/Qx
                    // Connect Local: LC/LF/LL/LS/LO
                    //
                    // International shipments only have one class, regardless. So in total, this will ignore everything.
                    // ---------------------------------------------
                    
                    // ---------------------------------------------
                    // Media Mail
                    // ---------------------------------------------
                    if (strpos($name, "Media Mail") !== FALSE) {
                        // Only allow Single Piece (SP)
                        if ($rate['rateIndicator'] === "SP") {
                            if ((MODULE_SHIPPING_USPSR_MEDIA_CLASS == 'Nonstandard' && strpos($name, "Nonstandard") !== FALSE) ||
                                (MODULE_SHIPPING_USPSR_MEDIA_CLASS == 'Machinable' && strpos($name, "Machinable") !== FALSE)) {
                                $name = "Media Mail"; 
                                $rate['productName'] = "Media Mail"; // Have to add "Media Mail" as productName is otherwise blank.
                            } else {
                                continue 2;
                            }
                        } else {
                            continue 2;
                        }
                    }

                    // ---------------------------------------------
                    // Cubic Options (Priority Mail Cubic / Ground Advantage Cubic)
                    // ---------------------------------------------
                    elseif (strpos($name, "Priority Mail Cubic") !== FALSE || strpos($name, "Ground Advantage Cubic") !== FALSE) {
                        if (preg_match('/^(CP|[CPQ]\d)$/', $rate['rateIndicator'])) {
                            if (MODULE_SHIPPING_USPSR_CUBIC_CLASS == "Non-Soft" && $rate['rateIndicator'] !== "CP") continue 2;
                            if (MODULE_SHIPPING_USPSR_CUBIC_CLASS == "Soft" && !preg_match('/^([PQ]\d)$/', $rate['rateIndicator'])) continue 2;
                        } else {
                            continue 2;
                        }
                    }

                    // ---------------------------------------------
                    // Nonstandard cases (Priority / Express / Ground / Connect Local)
                    // ---------------------------------------------
                    elseif ($rate['processingCategory'] === 'NONSTANDARD') {
                        // Priority Mail
                        if (strpos($name, "Priority Mail") !== FALSE && $rate['rateIndicator'] === "SP") {
                            // allow
                        }
                        // Priority Mail Express
                        elseif (strpos($name, "Priority Mail Express") !== FALSE && $rate['rateIndicator'] === "PA") {
                            // allow
                        }
                        // Ground Advantage
                        elseif (strpos($name, "Ground Advantage") !== FALSE && $rate['rateIndicator'] === "SP") {
                            // allow
                        }
                        // Ground Advantage OS
                        elseif (strpos($name, "Ground Advantage") !== FALSE && $rate['rateIndicator'] === "OS") {
                            // allow
                        }
                        // Connect Local LO
                        elseif (strpos($name, "Connect Local") !== FALSE && $rate['rateIndicator'] === "LO") {
                            $rate['productName'] = $rate['description'];
                            // otherwise allow
                        }
                        // Dimensional Class fallback (DR / DN)
                        elseif (MODULE_SHIPPING_USPSR_DIMENSIONAL_CLASS == 'Rectangular' && $rate['rateIndicator'] !== 'DR') {
                            continue 2;
                        } elseif (MODULE_SHIPPING_USPSR_DIMENSIONAL_CLASS == 'Nonrectangular' && $rate['rateIndicator'] !== 'DN') {
                            continue 2;
                        }
                    }

                    // ---------------------------------------------
                    // Machinable cases (Priority / Express / Ground)
                    // ---------------------------------------------
                    elseif ($rate['processingCategory'] === 'Machinable') {
                        // Priority Mail, Express, Ground → must be SP
                        if (($rate['rateIndicator'] !== "SP") &&
                            (strpos($name, "Priority Mail") !== FALSE ||
                            strpos($name, "Priority Mail Express") !== FALSE ||
                            strpos($name, "Ground Advantage") !== FALSE)) {
                            continue 2;
                        }
                    }

                    // ---------------------------------------------
                    // Connect Local: the "Product Names" do not appear in the API, force them in to match.
                    // ---------------------------------------------
                    if (strpos($name, "Connect Local") !== FALSE) $rate['productName'] = $rate['description'];

                    // ---------------------------------------------
                    // Default: All is OK, add it to the list
                    // ---------------------------------------------
                    $lookup[$name] = $rate;
                }

                // ---------------------------------------------
                // Extra services: Tack that onto the main roster of returns.
                // ---------------------------------------------
                if (isset($opt['extraServices'])) {
                    foreach ($opt['extraServices'] as $svc) {
                        $lookup[$name]['extraService'][$svc['extraService']] = $svc;
                    }
                }

            } // Done with iterating the returned rates

            $message = "\n";
            $message .= '===============================================' . "\n";
            $message .= 'Lookup lists' . "\n";
            $message .= print_r($lookup, TRUE) . "\n";
            $message .= '===============================================' . "\n";
            // $this->uspsrDebug($message); // Hiding to reduce log file size

            $m = 0; //Index for ZenCart quote builder (ie. "usps0")

            // Extra Services
            if ($this->is_us_shipment) {
                $ltr_services = array_map('intval', explode(',', MODULE_SHIPPING_USPSR_DMST_LETTER_SERVICES));
                $pkg_services = array_map('intval', explode(',', MODULE_SHIPPING_USPSR_DMST_SERVICES));
            } else {
                $ltr_services = array_map('intval', explode(',', MODULE_SHIPPING_USPSR_INTL_LETTER_SERVICES));
                $pkg_services = array_map('intval', explode(',', MODULE_SHIPPING_USPSR_INTL_SERVICES));
            }

            // If either list has the insurance code (930), add the other one.
            if (in_array(930, $ltr_services)) $ltr_services[] = 931;
            if (in_array(930, $pkg_services)) $pkg_services[] = 931;
            
            // Now go through the list of SELECTED services from the configurator and do the work on THOSE
            foreach ($selected_methods as $method_item) {

                // If the $method_item['method'] is it the lookup, continue, otherwise, pass
                if (isset($lookup[$method_item['method']])) {
                    
                    $quotes = [];
                    $method_to_add = TRUE;
                    $match = TRUE;
                    $made_weight = FALSE;
                    $quote_message = '';
                    $services_total = 0;

                    // If this package is NOT going to an APO/FPO/DPO, skip and continue to the next
                    // Currently this is the only rate which has a different rate for APO/FPO/DPO rates.
                    if (!$this->is_apo_dest && ($method_item['method'] === 'Priority Mail Large Flat Rate APO/FPO/DPO'))
                        continue;

                    $price = $lookup[$method_item['method']]['totalBasePrice'];
                    
                    // Go through and add up the appropriate amount as necessary.
                    $services = strpos($method_item['method'], "Letter") !== false ? $ltr_services : $pkg_services;

                    // For packages, cycle through and add the services. (For letters, the price is baked into the request and result. Don't do it.)
                    $servicesList = '';
                    $extraServices = 0;
                    if (strpos($method_item['method'], "First-Class") === FALSE) {
                        $method_name   = $method_item['method'];
                        $method_extra_services  = 0;         // tracks price total for this method
                        $method_labels = [];        // tracks names of extra services

                        foreach ($services as $s) {
                            if (isset($lookup[$method_name]['extraService'][$s]['price'])) {
                                $method_price = $lookup[$method_name]['extraService'][$s]['price'];
                                $extraServices += $price;

                                // Add service name if available, otherwise fall back to the code
                                $label = $lookup[$method_name]['extraService'][$s]['name'] ?? $s;
                                $method_labels[] = $label . " (" . $currencies->format($method_price) . ")";
                            }
                        }

                        // Add this method’s service total into the running total
                        $services_total += $method_extra_services;

                        // Convert collected labels into a comma-separated string
                        $servicesList = implode(", ", $method_labels);
                    }

                    // Extra Services for method
                    $price += $extraServices;

                    // Handling as defined by the method
                    $price += $method_item['handling'];

                    // Handling for the USPS as a whole.
                    // @todo: Should this be multiplied per order or per box?
                    $price += $usps_handling_fee;

                    // Final math (Final Quote = (Quoted Price + Handling Fee be Method + Handling Fee Overall + any surcharges/services) * number of boxes)
                    $price *= (MODULE_SHIPPING_USPSR_HANDLING_METHOD === 'Box' ? $shipping_num_boxes : 1);

                    // Okay, we have the methods, we have the quotes: start building.
                    $quotes = [
                        'id' => 'usps'.$m,
                        'title' => uspsr_filter_gibberish($lookup[$method_item['method']]['productName']),
                        'cost' => $price,
                        'mailClass' => $lookup[$method_item['method']]['mailClass'],
                        'servicesAdded' => $servicesList, // For debugging
                    ];
                    $m++;
                    
                    // Holdover observer from original USPS module. Simple put:
                    // -----
                    // $method_item['method']  Contains the "Friendly Name" of the desired method, can be used to check
                    // $method_to_add boolean. Should be TRUE to be added.
                    // $quotes['title'] Output Title, sent to ZenCart
                    // $quotes['cost']  Cost. Sent to ZenCart, should be a number. Not a currency.
                    $this->notify('NOTIFY_USPS_UPDATE_OR_DISALLOW_TYPE', $method_item['method'], $method_to_add, $quotes['title'], $quotes['cost']);
                    
                    // If everything passes their checks (match, observer, make weight....) add it.
                    
                    // If $method is not empty, compare it to the $quotes id. If it matches, add it
                    if (!empty($method) && ($method !== $quotes['id'])) $match = FALSE;

                    // Did the order make weight?
                    if ($this->quote_weight >= $method_item['min_weight'] && $this->quote_weight <= $method_item['max_weight']) $made_weight = TRUE;

                    if ($match && $method_to_add && $made_weight) {
                        // If everything checks out... Add it to the 
                        $build_quotes[] = $quotes;
                        $quote_message .= "\n" . 'Adding option : ' . $quotes['title'] . "\n";
                        $quote_message .= 'Price From Quote : ' . $currencies->format($lookup[$method_item['method']]['totalBasePrice']) . " , Method Handling : " . $currencies->format((double) $method_item['handling']) . " , Order Handling : " . $currencies->format($usps_handling_fee) . " , Extra Services: " . $currencies->format($extraServices) . "\n";
                        $quote_message .= "Final Price (Quote + Handling + Order Handling + Services) * # of Boxes ($shipping_num_boxes) : " . $currencies->format($price) . "\n";
                    } elseif (!$match) {
                        // Order failed to match
                        $quote_message .= "\n" . 'Skipping the method :"' . $quotes['title'] . '" because it did not match the target.' . "\n";
                    } elseif (!$method_to_add) {
                        // Observer blocked this
                        $quote_message .= 'An observer class blocked the method "' . $quotes['title'] . '" from being added to the list. So it was set aside.';
                    } elseif (!$made_weight) {
                        // Order failed to make weight
                        $quote_message .= "Order failed to make weight for " . $method_item['method'] . ". (Minimum Weight : " . $method_item['min_weight'] . " , Maximum Weight: " . $method_item['max_weight'] . ")\n";
                    } else {
                        $quote_message .= "Something else went wrong...";
                    }

                    if (!empty($quote_message)) $this->uspsrDebug($quote_message);
                } 
            }

            // Squash Ground Advantage
            if (strpos(MODULE_SHIPPING_USPSR_SQUASH_OPTIONS, "Ground Advantage") !== FALSE) {
                $groundOptions = [];
                $pattern = '/Ground Advantage/'; // There is no flat rate Ground Advantage, so you're dealing with the only two outcomes.

                // Loop through the array to collect priority mail options
                foreach ($build_quotes as $key => $option) {
                    if (preg_match($pattern, $option['title'])) {
                        $groundOptions[] = [
                            'key' => $key,
                            'cost' => $option['cost']
                        ];
                    }
                }

                // If both variants exist, remove the more expensive one
                if (count($groundOptions) == 2) {
                    //if (isset($groundOptions['Ground Advantage']) && isset($groundOptions['Ground Advantage Cubic'])) {
                    $removeKey = ($groundOptions[0]['cost'] > $groundOptions[1]['cost'])
                        ? $groundOptions[0]['key']
                        : $groundOptions[1]['key'];

                    $removal_message = '';
                    $removal_message .= "\n" . 'SQUASHED option : ' . $build_quotes[$removeKey]['title'] . "\n";

                    unset($build_quotes[$removeKey]);
                    $this->uspsrDebug($removal_message);
                }

                $build_quotes = array_values($build_quotes);
            }

            // Squash Priority Mail
            if (strpos(MODULE_SHIPPING_USPSR_SQUASH_OPTIONS, "Priority Mail") !== FALSE) {
                $priorityOptions = [];
                $pattern = '/^Priority Mail(?: Cubic)*$/';

                // Loop through the array to collect priority mail options
                foreach ($build_quotes as $key => $option) {
                    if (preg_match($pattern, $option['title'])) {
                        $priorityOptions[] = [
                            'key' => $key,
                            'cost' => $option['cost']
                        ];
                    }
                }

                // If both variants exist, remove the more expensive one
                if (count($priorityOptions) == 2) {
                    //if (isset($priorityOptions['Priority Mail']) && isset($priorityOptions['Priority Mail Cubic'])) {
                    $removeKey = ($priorityOptions[0]['cost'] > $priorityOptions[1]['cost'])
                        ? $priorityOptions[0]['key']
                        : $priorityOptions[1]['key'];

                    // Removal Message for Debug
                    $removal_message = '';
                    $removal_message .= "\n" . 'SQUASHED option : ' . $build_quotes[$removeKey]['title'] . "\n";

                    unset($build_quotes[$removeKey]);
                    $this->uspsrDebug($removal_message);
                }
            }

            // Build Estimates Attachment
            if (!empty($uspsStandards)) {
                switch (MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT) {
                    case "Estimate Transit Time":
                        foreach ($build_quotes as &$quote) {
                            if (isset($uspsStandards[$quote['mailClass']]['serviceStandard'])) $quote['title'] .= " [" . MODULE_SHIPPING_USPSR_TEXT_ESTIMATED . " " . zen_uspsr_estimate_days($uspsStandards[$quote['mailClass']]['serviceStandard']) . "]";
                        }
                        break;
                    case "Estimate Delivery":
                        foreach ($build_quotes as &$quote) {

                            if (isset($uspsStandards[$quote['mailClass']]['delivery']['scheduledDeliveryDateTime'])) {
                                $est_delivery_raw = new DateTime($uspsStandards[$quote['mailClass']]['delivery']['scheduledDeliveryDateTime']);
                                $est_delivery = $est_delivery_raw->format(DATE_FORMAT);

                                $quote['title'] .= " [" . MODULE_SHIPPING_USPSR_TEXT_ESTIMATED_DELIVERY . " " . $est_delivery . "]";
                            }
                        }
                        break;
                }
            }

            // Okay we have our list of Build Quotes, so now... we need to sort pursurant to options
            switch (MODULE_SHIPPING_USPSR_QUOTE_SORT) {
                case 'Alphabetical':
                    usort($build_quotes, function ($a, $b) {
                        return $a['title'] <=> $b['title'];
                    });
                    break;
                case 'Price-LowToHigh':
                    usort($build_quotes, function ($a, $b) {
                        return $a['cost'] <=> $b['cost'];
                    });
                    break;
                case 'Price-HighToLow':
                    usort($build_quotes, function ($a, $b) {
                        return $b['cost'] <=> $a['cost'];
                    });
                    break;
                case 'Unsorted':
                    // Do nothing, leave it as is
                    break;
            }

            $message = "\n";
            $message .= '===============================================' . "\n";
            $message .= 'Displayed options' . "\n";
            $message .= 'Sorting the returned quotes by: ' . MODULE_SHIPPING_USPSR_QUOTE_SORT . "\n";
            $message .= print_r($build_quotes, TRUE) . "\n";
            $message .= '===============================================' . "\n";

            $this->uspsrDebug($message);

            if (count($build_quotes) > 0) {
                // Close off and make the final array.
                $this->quotes = [
                    'id' => $this->code,
                    'icon' => zen_image($this->icon),
                    'module' => $this->title,
                    'methods' => $build_quotes,
                    'tax' => ($this->tax_class > 0) ? zen_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']) : null,
                ];
                // Should there be a warning that the dates are estimations?

            } else { // This means nothing was built. Report it back as such.

                // Only show this during debugging
                if ($this->debug_enabled === true && (strpos(MODULE_SHIPPING_USPSR_DEBUG_MODE, "Error") !== FALSE)) {
                    $this->quotes = [
                        'id' => $this->code,
                        'icon' => zen_image($this->icon),
                        'methods' => [],
                        'module' => $this->title,
                        'error' => MODULE_SHIPPING_USPSR_TEXT_ERROR,
                    ];
                } else {
                    $this->enabled = false;
                }

            }

        } else { // If there isn't a 'rateOptions' filed, that means we have an 'error' field. Output that along with an error message.

            // Only show this during debugging
            if ($this->debug_enabled === true && (strpos(MODULE_SHIPPING_USPSR_DEBUG_MODE, "Error") !== FALSE)) {

                $this->quotes = [
                    'id' => $this->code,
                    'icon' => zen_image($this->icon),
                    'methods' => [],
                    'module' => $this->title,
                    'error' => MODULE_SHIPPING_USPSR_TEXT_SERVER_ERROR . '<br><br><pre style="white-space: pre-wrap;word-wrap: break-word;">' . $uspsQuote['error']['message'] . "</pre>"
                ];

                $this->enabled = false;
            }
        }

        $this->notify('NOTIFY_SHIPPING_USPS_QUOTES_READY_TO_RETURN');
        return $this->quotes;
    }

    public function check()
    {
        global $db;

        if (!isset($this->_check)) {
            $check_query = $db->Execute("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_SHIPPING_USPSR_STATUS' LIMIT 1");
            $this->_check = $check_query->RecordCount();
        }

        global $sniffer;

        $is_text = $sniffer->field_type(TABLE_ORDERS, 'shipping_method', 'text');
        $is_mediumtext = $sniffer->field_type(TABLE_ORDERS, 'shipping_method', 'mediumtext');
        $is_blob = $sniffer->field_type(TABLE_ORDERS, 'shipping_method', 'blob');

        # If the column is the column is a text, mediumtext, or blob: DO NOTHING.
        # Otherwise, safe to assume it's VARCHAR() or TINYTEXT. (Some modules change the shipping_method to text, change THOSE to be VARCHAR(255))
        if (($is_text || $is_mediumtext || $is_blob) === FALSE) {
            $db->Execute("ALTER TABLE " . TABLE_ORDERS . " MODIFY shipping_method varchar(255) NOT NULL DEFAULT ''");
        }

        $this->notify('NOTIFY_SHIPPING_USPS_CHECK');
        return $this->_check;
    }

    public function install()
    {
        // Build the options for the module.

        /**
         * Display the version number
         */
        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_VERSION', [
            'configuration_title' => 'USPSr Version',
            'configuration_value' => self::USPSR_CURRENT_VERSION,
            'configuration_description' => 'You have installed:',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'set_function' => 'zen_cfg_read_only( ',
            'date_added' => 'now()',
        ]);

        /**
         * Toggle to enable USPS Shipping
         */
        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_STATUS', [
            'configuration_title' => 'Enable USPS Shipping',
            'configuration_value' => 'True',
            'configuration_description' => 'Do you want to offer USPS shipping?',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'set_function' => 'zen_cfg_select_option([\'True\', \'False\'], ',
            'date_added' => 'now()'
        ]);


        /**
         * Toggle to display the full or abbreviated name
         */
        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_TITLE_SIZE', [
            'configuration_title' => 'Full Name or Short Name',
            'configuration_value' => 'Short',
            'configuration_description' => 'Do you want to use the Long (United States Postal Service) or Short name (USPS) for USPS shipping?',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'set_function' => 'zen_cfg_select_option([\'Long\', \'Short\'], ',
            'date_added' => 'now()'
        ]);

        /**
         * API Credentials
         */
        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_API_KEY', [
            'configuration_title' => 'Enter the USPS API Consumer Key',
            'configuration_value' => 'NONE',
            'configuration_description' => 'Enter your USPS API Consumer Key assigned to the app dedicated for this website.<br><br><strong>NOTE:</strong> This is NOT the same as the WebTools USERID and is NOT your USPS.com account Username.',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'use_function' => 'zen_cfg_password_display',
            'date_added' => 'now()'
        ]);

        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_API_SECRET', [
            'configuration_title' => 'Enter the USPS API Consumer Secret',
            'configuration_value' => 'NONE',
            'configuration_description' => 'Enter the USPS API Consumer Secret assigned to the app dedicated for this website.<br><br><strong>NOTE:</strong> This is NOT the same as the WebTools PASSWORD and is NOT your USPS.com account Password.',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'use_function' => 'zen_cfg_password_display',
            'date_added' => 'now()'
        ]);

        /**
         * Module Sort Order
         */
        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_QUOTE_SORT', [
            'configuration_title' => 'Quote Sort Order',
            'configuration_value' => 'Price-LowToHigh',
            'configuration_description' => 'Sorts the returned quotes using the service name Alphanumerically or by Price. Unsorted will give the order provided by USPS.',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'set_function' => 'zen_cfg_select_option([\'Unsorted\',\'Alphabetical\', \'Price-LowToHigh\', \'Price-HighToLow\'], ',
            'date_added' => 'now()'
        ]);

        /**
         * Handling Fees
         */
        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_HANDLING_DOMESTIC', [
            'configuration_title' => 'Overall Handling Fee - US',
            'configuration_value' => '0',
            'configuration_description' => 'Domestic Handling fee for this shipping method.',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'date_added' => 'now()'
        ]);

        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_HANDLING_INTL', [
            'configuration_title' => 'Overall Handling Fee - International',
            'configuration_value' => '0',
            'configuration_description' => 'International Handling fee for this shipping method.',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'date_added' => 'now()'
        ]);

        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_HANDLING_METHOD', [
            'configuration_title' => 'Handling Per Order or Per Box',
            'configuration_value' => 'Order',
            'configuration_description' => 'Do you want to charge Handling Fee Per Order or Per Box?<br><br><em>Boxes are defined by ZenCart\'s estimation of what will fit in a box.</em>',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'set_function' => 'zen_cfg_select_option([\'Order\', \'Box\'], ',
            'date_added' => 'now()'
        ]);

        /**
         * Sales Tax Handling
         */
        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_TAX_CLASS', [
            'configuration_title' => 'Tax Class',
            'configuration_value' => '0',
            'configuration_description' => 'Use the following tax class on the shipping fee.',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'set_function' => 'zen_cfg_pull_down_tax_classes(',
            'use_function' => 'zen_get_tax_class_title',
            'date_added' => 'now()'
        ]);

        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_TAX_BASIS', [
            'configuration_title' => 'Tax Basis',
            'configuration_value' => 'Shipping',
            'configuration_description' => 'On what basis is Shipping Tax calculated. Options are<br>Shipping - Based on the customer\'s Shipping Address<br>Billing Based on the customer\'s Billing address<br>Store - Based on Store address if Billing/Shipping Zone equals Store zone',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'set_function' => 'zen_cfg_select_option([\'Shipping\', \'Billing\', \'Store\'], ',
            'date_added' => 'now()'
        ]);

        /**
         * Only allow this Zone to use USPS
         */
        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_ZONE', [
            'configuration_title' => 'Shipping Zones',
            'configuration_value' => '0',
            'configuration_description' => 'If a zone is selected, only enable this shipping method for that zone.',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'set_function' => 'zen_cfg_pull_down_zone_classes(',
            'use_function' => 'zen_get_zone_class_title',
            'date_added' => 'now()'
        ]);

        /**
         * Machinability Options
         */
        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_MEDIA_CLASS', [
            'configuration_title' => 'Packaging Class - Media Mail',
            'configuration_value' => 'Machinable',
            'configuration_description' => 'For Media Mail only, are your packages typically machinable?<br><br>\"Machinable\" means a mail piece designed and sized to be processed by automated postal equipment. Typically this is rigid mail, that fits a certain shape and is within a certain weight (no more than 25 pounds for Media Mail). If your normal packages are within these guidelines, set this flag to \"Machinable\". Otherwise, set this to \"Nonstandard\". (If your customer order\'s total weight or package size falls outside this limit, regardless of the setting, the module will set the package to \"Nonstandard\".) (If your customer order\'s total weight or package size falls outside of this limit, regardless of the setting, the module will set the package to \"Nonstandard\".) <br><br>This applies only to Media Mail. All other mail services will have their \"Machinability\" status determined by the weight of the cart and the size of the package entered below.',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'set_function' => 'zen_cfg_select_option([\'Machinable\', \'Nonstandard\'], ',
            'date_added' => 'now()'
        ]);

        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_DIMENSIONAL_CLASS', [
            'configuration_title' => 'Packaging Class - Dimensional Pricing',
            'configuration_value' => 'Rectangular',
            'configuration_description' => 'Are your packages typically rectangular?<br><br><em>\"Rectangular\"</em> means a mail piece that is a standard four-corner box shape that is not significantly curved or oddly angled. Something like a typical cardboard shipping box would fit this. If you use any kind of bubble mailer or poly mailer instead of a basic box, you should choose Nonrectangular.<br><br><em>Typically this would only really apply under extreme quotes like extra heavy or big packages.</em>',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'set_function' => 'zen_cfg_select_option([\'Rectangular\', \'Nonrectangular\'], ',
            'date_added' => 'now()'
        ]);

        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_CUBIC_CLASS', [
            'configuration_title' => 'Packaging Class - Cubic Pricing',
            'configuration_value' => 'Non-Soft',
            'configuration_description' => 'How would you class the packaging of your items?<br><br><em>\"Non-Soft\"</em> refers to packaging that is rigid in shape and form, like a box.<br><br><em>\"Soft\"</em> refers to packaging that is usually cloth, plastic, or vinyl packaging that is flexible enough to adhere closely to the contents being packaged and strong enough to securely contain the contents.<br><br>Choose the style that best fits how you (on average) ship out your packages.<br><em>This selection only applies to Cubic Pricing such as Ground Advantage Cubic, Priority Mail Cubic, Priority Mail Express Cubic</em>',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'set_function' => 'zen_cfg_select_option([\'Non-Soft\', \'Soft\'], ',
            'date_added' => 'now()'
        ]);

        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_SQUASH_OPTIONS', [
            'configuration_title' => 'Squash Alike Methods Together',
            'configuration_value' => '--none--',
            'configuration_description' => 'If you are offering Priority Mail and Priority Mail Cubic or Ground Advantage and Ground Advantage Cubic in the same quote, do you want to "squash" them together and offer the lower of each pair?<br><br>This will only work if the quote returned from USPS has BOTH options (Cubic and Normal) in it, otherwise it will be ignored.',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'set_function' => 'zen_cfg_select_multioption([\'Squash Ground Advantage\', \'Squash Priority Mail\'], '
        ]);


        /**
         * Transit time display
         */
        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT', [
            'configuration_title' => 'Display Transit Time',
            'configuration_value' => 'No',
            'configuration_description' => 'Would you like to display an estimated delivery date (ex. \"est. delivery: 12/25/2025\") or estimate delivery time (ex. \"est. 2 days\") for the service? This is pulled from the service guarantees listed by the USPS. If the service doesn\'t have a set guideline, no time quote will be displayed.<br><br>Only applies to US based deliveries.',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'set_function' => 'zen_cfg_select_option([\'No\', \'Estimate Delivery\', \'Estimate Transit Time\'], ',
            'date_added' => 'now()'
        ]);


        $insert_handling_array = [
            'configuration_title' => 'Handling Time',
            'configuration_value' => '1',
            'configuration_description' => 'In whole numbers, how many days does it take for you to dispatch your packages to the USPS. (Enter as a whole number only. Between 0 and 30. This will be added to the estimated delivery date or time as needed.)',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'use_function' => 'zen_uspsr_estimate_days',
            'date_added' => 'now()'
        ];

        // If this is ZenCart 1.5.5, the val_function column doesn't exist. So in 1.5.6 and high, add:
        if (version_compare(PROJECT_VERSION_MAJOR . "." . PROJECT_VERSION_MINOR, '1.5.6', ">=")) $insert_handling_array['val_function'] = '{"error":"MODULE_SHIPPING_USPSR_HANDLING_DAYS","id":"FILTER_VALIDATE_INT","options":{"options":{"min_range": 0, "max_range": 30}}}';
        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_HANDLING_TIME', $insert_handling_array);

        /**
         * Package Dimensions
         * The Small Flat Rate Box is 8-5/8" x 5-3/8" x 1-5/8". That's the minimum.
         * These two rows control the same functionality, but only one will be inserted.
         *
         * @todo Figure out how the new ZenCart uses the product length, width, and height fields.
         */

        if (defined('SHIPPING_DIMENSION_UNITS') && SHIPPING_DIMENSION_UNITS == "centimeters") {
            $this->addConfigurationKey('MODULE_SHIPPING_USPSR_DIMMENSIONS', [
                'configuration_title' => 'Typical Package Dimensions (Domestic and International)',
                'configuration_value' => '21.9075, 21.9075, 13.6525, 13.6525, 4.1275, 4.1275',
                'configuration_description' => 'The Minimum Length, Width and Height are used to determine shipping methods available for International Shipping.<br><br>While per-item dimensions are not supported by this module at this time, the minimums listed below are sent to USPS for obtaining Rate Quotes.<br><br>In most cases, these Minimums should never have to be changed.<br><br><em>These measurements will be converted to inches as part of the quoting process as your cart was set to centimeters when it was installed. If you change your cart setting, you will need to reenter these values.<br>',
                'configuration_group_id' => 6,
                'sort_order' => 0,
                'set_function' => 'zen_cfg_uspsr_dimmensions(',
                'use_function' => 'zen_cfg_uspsr_showdimmensions',
                'date_added' => 'now()'
            ]);
        } else {
            $this->addConfigurationKey('MODULE_SHIPPING_USPSR_DIMMENSIONS', [
                'configuration_title' => 'Typical Package Dimensions (Domestic and International)',
                'configuration_value' => '8.625, 8.625, 5.375, 5.375, 1.625, 1.625',
                'configuration_description' => 'The Minimum Length, Width and Height are used to determine shipping methods available for International Shipping.<br><br>While per-item dimensions are not supported at this time, the minimums listed below are sent to USPS for obtaining Rate Quotes.<br><br>In most cases, these Minimums should never have to be changed.<br>These measurements should be in inches.<br>',
                'configuration_group_id' => 6,
                'sort_order' => 0,
                'set_function' => 'zen_cfg_uspsr_dimmensions(',
                'use_function' => 'zen_cfg_uspsr_showdimmensions',
                'date_added' => 'now()'
            ]);
        }

        /**
         * Letter Dimensions
         * A #10 sized envelope is 4-1/8 x 9-1/2 x 0.007 inches. That's the minimum.
         * These two rows control the same functionality, but only one will be inserted.
         *
         * @todo Figure out how the new ZenCart uses the product length, width, and height fields.
         */

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

        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_LTR_PROCESSING', [
            'configuration_title' => 'Packaging Class - Letters',
            'configuration_value' => 'Letters',
            'configuration_description' => 'How would you class the packaging of your letters?<br><br><em>\"Letters\"</em> refers to packaging that is rigid in shape and form, like a plain white envelope (#10). A letter is a rectangular piece no more than 6.125" by 11.5" with a thickness no greater than .25" inches. (Anything greater than this or smaller than the minimums will be treated as non-machineable.<br><br><em>\"Flats\"</em> typically refer to large envelopes, newsletters, and magazines. Flats must be no greater than 12 inches by 15 inches with a thickness no greater than .75 inches.<br><br><em>\"Cards\"</em> plainly mean simple postcards with specific measurements.<br><br>Choose the style that best fits how you (on average) ship out your packages.<br><em>This selection only applies to First Class Mail Letters and First Class Mail International Letters.</em><br>',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'set_function' => 'zen_cfg_select_option([\'Letters\', \'Flats\', \'Cards\'], ',
            'date_added' => 'now()'
        ]);


        /**
         * Shipping Methods
         * Since the modules are now including the weights for min/max again, we need to make sure that those fields now have
         * minimum and maximum weights in kilograms where available.
         *
         */

        if (defined('SHIPPING_WEIGHT_UNITS') && SHIPPING_WEIGHT_UNITS === 'kgs') {
            $this->addConfigurationKey('MODULE_SHIPPING_USPSR_TYPES', [
                'configuration_title' => 'Shipping Methods (Domestic and International)',
                'configuration_value' => '0, 0.0992233, 0.00, 0, 31.7514, 0.00, 0, 9.0718, 0.00, 0, 31.7514, 0.00, 0, 11.3398, 0.00, 0, 11.3398, 0.00, 0, 11.3398, 0.00, 0, 11.3398, 0.00, 0, 31.7514, 0.00, 0, 9.0718, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 0.0992233, 0.00, 0, 1.8143, 0.00, 0, 31.7514, 0.00, 0, 1.8143, 0.00, 0, 1.8143, 0.00, 0, 1.8143, 0.00, 0, 1.8143, 0.00, 0, 9.0718, 0.00, 0, 9.0718, 0.00, 0, 31.7514, 0.00, 0, 1.8143, 0.00, 0, 1.8143, 0.00, 0, 1.8143, 0.00',
                'configuration_description' => 'Choose the services that you want to offer to your customers.<br><br><strong>Checkbox:</strong> Select the services to be offered. (Can also click on the service name in certain browsers.)<br><br><strong>Min/Max</strong> Choose a custom minimum/maximum for the selected service. If the cart as a whole (the items plus any tare settings) fail to make weight, the method will be skipped. Keep in mind that each service also has its own maximums that will be controlled regardless of what was set here. (Example: entering 5 lbs for International First-Class Mail will be ignored since the International First-Class Mail has a hard limit of 4 lbs.)<br><br><strong>Handling:</strong> A handling charge for that particular method (will be added on to the quote plus any services charges that are applicable).<br><br>USPS returns methods based on cart weights. Enter the weights in your site\'s configured standard. (The cart will handle conversions as necessary.)',
                'configuration_group_id' => 6,
                'sort_order' => 0,
                'set_function' => 'zen_cfg_uspsr_services([\'First-Class Mail Letter\',\'USPS Ground Advantage\', \'USPS Ground Advantage Cubic\', \'Media Mail\', \'Connect Local Machinable DDU\', \'Connect Local Machinable DDU Flat Rate Box\', \'Connect Local Machinable DDU Small Flat Rate Bag\', \'Connect Local Machinable DDU Large Flat Rate Bag\', \'Priority Mail\', \'Priority Mail Cubic\', \'Priority Mail Flat Rate Envelope\', \'Priority Mail Padded Flat Rate Envelope\', \'Priority Mail Legal Flat Rate Envelope\', \'Priority Mail Small Flat Rate Box\', \'Priority Mail Medium Flat Rate Box\', \'Priority Mail Large Flat Rate Box\', \'Priority Mail Large Flat Rate APO/FPO/DPO\', \'Priority Mail Express\', \'Priority Mail Express Flat Rate Envelope\', \'Priority Mail Express Padded Flat Rate Envelope\', \'Priority Mail Express Legal Flat Rate Envelope\', \'First-Class Mail International Letter\', \'First-Class Package International Service Machinable ISC Single-piece\', \'Priority Mail International ISC Single-piece\', \'Priority Mail International ISC Flat Rate Envelope\', \'Priority Mail International Machinable ISC Padded Flat Rate Envelope\', \'Priority Mail International ISC Legal Flat Rate Envelope\', \'Priority Mail International Machinable ISC Small Flat Rate Box\', \'Priority Mail International Machinable ISC Medium Flat Rate Box\', \'Priority Mail International Machinable ISC Large Flat Rate Box\', \'Priority Mail Express International ISC Single-piece\', \'Priority Mail Express International ISC Flat Rate Envelope\', \'Priority Mail Express International ISC Legal Flat Rate Envelope\', \'Priority Mail Express International ISC Padded Flat Rate Envelope\'], ',
                'use_function' => 'zen_cfg_uspsr_showservices',
                'date_added' => 'now()'
            ]);
        } else {
            $this->addConfigurationKey('MODULE_SHIPPING_USPSR_TYPES', [
                'configuration_title' => 'Shipping Methods (Domestic and International)',
                'configuration_value' => '0, 0.21875, 0.00, 0, 70, 0.00, 0, 20, 0.00, 0, 70, 0.00, 0, 25, 0.00, 0, 25, 0.00, 0, 25, 0.00, 0, 25, 0.00, 0, 70, 0.00, 0, 20, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 0.21875, 0.00, 0, 4, 0.00, 0, 70, 0.00, 0, 4, 0.00, 0, 4, 0.00, 0, 4, 0.00, 0, 4, 0.00, 0, 20, 0.00, 0, 20, 0.00, 0, 70, 0.00, 0, 4, 0.00, 0, 4, 0.00, 0, 4, 0.00',
                'configuration_description' => 'Choose the services that you want to offer to your customers.<br><br><strong>Checkbox:</strong> Select the services to be offered (Can also click on the service name in certain browsers.)<br><br><strong>Min/Max</strong> Choose a custom minimum/maximum for the selected service. If the cart as a whole (the items plus any tare settings) fail to make weight, the method will be skipped. Keep in mind that each service also has its own maximums that will be controlled regardless of what was set here. (Example: entering 5 lbs for International First-Class Mail will be ignored since the International First-Class Mail has a hard limit of 4 lbs.)<br><br><strong>Handling:</strong> A handling charge for that particular method (will be added on to the quote plus any services charges that are applicable).<br><br>USPS returns methods based on cart weights. Enter the weights in your site\'s configured standard. (The cart will handle conversions as necessary.)',
                'configuration_group_id' => 6,
                'sort_order' => 0,
                'set_function' => 'zen_cfg_uspsr_services([\'First-Class Mail Letter\',\'USPS Ground Advantage\', \'USPS Ground Advantage Cubic\', \'Media Mail\', \'Connect Local Machinable DDU\', \'Connect Local Machinable DDU Flat Rate Box\', \'Connect Local Machinable DDU Small Flat Rate Bag\', \'Connect Local Machinable DDU Large Flat Rate Bag\', \'Priority Mail\', \'Priority Mail Cubic\', \'Priority Mail Flat Rate Envelope\', \'Priority Mail Padded Flat Rate Envelope\', \'Priority Mail Legal Flat Rate Envelope\', \'Priority Mail Small Flat Rate Box\', \'Priority Mail Medium Flat Rate Box\', \'Priority Mail Large Flat Rate Box\', \'Priority Mail Large Flat Rate APO/FPO/DPO\', \'Priority Mail Express\', \'Priority Mail Express Flat Rate Envelope\', \'Priority Mail Express Padded Flat Rate Envelope\', \'Priority Mail Express Legal Flat Rate Envelope\', \'First-Class Mail International Letter\', \'First-Class Package International Service Machinable ISC Single-piece\', \'Priority Mail International ISC Single-piece\', \'Priority Mail International ISC Flat Rate Envelope\', \'Priority Mail International Machinable ISC Padded Flat Rate Envelope\', \'Priority Mail International ISC Legal Flat Rate Envelope\', \'Priority Mail International Machinable ISC Small Flat Rate Box\', \'Priority Mail International Machinable ISC Medium Flat Rate Box\', \'Priority Mail International Machinable ISC Large Flat Rate Box\', \'Priority Mail Express International ISC Single-piece\', \'Priority Mail Express International ISC Flat Rate Envelope\', \'Priority Mail Express International ISC Legal Flat Rate Envelope\', \'Priority Mail Express International ISC Padded Flat Rate Envelope\'], ',
                'use_function' => 'zen_cfg_uspsr_showservices',
                'date_added' => 'now()'
            ]);
        }

        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_MEDIA_MAIL_EXCLUDE', [
            'configuration_title' => 'Categories to Excluded from Media Mail',
            'configuration_value' => '',
            'configuration_description' => 'Enter the Category ID of the categories (separated by commas, white spaces surrounding the comma are OK) that fail Media Mail standards.<br><br>During checkout, if a product matches a category listed here, it will cause that entire order to be disqualified from Media Mail.<br>',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'use_function' => 'uspsr_get_categories',
            'date_added' => 'now()'
        ]);

        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_CONNECT_LOCAL_ZIP', [
            'configuration_title' => 'Zip Codes Allowed for USPS Connect Local',
            'configuration_value' => '',
            'configuration_description' => 'Enter the list of zip codes (only the five digit part, separated by commas) of the zip codes that can be offered any of the USPS Connect Local options.',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'use_function' => 'uspsr_get_connect_zipcodes',
            'date_added' => 'now()'
        ]);

        /**
         * Shipping Add-ons
         */
        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_DMST_SERVICES', [
            'configuration_title' => 'Shipping Add-ons (Domestic Packages)',
            'configuration_value' => '',
            'configuration_description' => 'Pick which add-ons you wish to offer as a part of the shipping cost quote for domestic packages. (The USPS API will do the math as necessary.)<br><br><strong>CAUTION:</strong> Not all options apply to all services.<br>',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'set_function' => 'zen_cfg_uspsr_extraservices(\'domestic\', ',
            'use_function' => 'zen_cfg_uspsr_extraservices_display',
            'date_added' => 'now()'
        ]);

        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_INTL_SERVICES', [
            'configuration_title' => 'Shipping Add-ons (International Packages)',
            'configuration_value' => '',
            'configuration_description' => 'Pick which add-ons you wish to offer as a part of the shipping cost quote for international packages. (The USPS API will do the math as necessary.)<br><br><strong>CAUTION:</strong> Not all options apply to all services.<br>',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'set_function' => 'zen_cfg_uspsr_extraservices(\'international\', ',
            'use_function' => 'zen_cfg_uspsr_extraservices_display',
            'date_added' => 'now()'
        ]);

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

        /**
         * Pricing Levels
         */
        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_PRICING', [
            'configuration_title' => 'Pricing Levels',
            'configuration_value' => 'Retail',
            'configuration_description' => 'What pricing level do you want to display to the customer?<br><br><em>Retail</em> - This is the price as if you went to the counter at the post office to buy the postage for your package.<br><br><em>Commercial</em> - This is the price you would pay if you\'re buying the label online via an authorized USPS reseller or through USPS Click-N-Ship on a Business account.<br><br><em>Contract</em> - If you have a negotiated service agreement or some other kind of contract with the USPS, select Contract. Then be sure to specify what kind of contract and the contract number you have in the appropriate options below.',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'set_function' => 'zen_cfg_select_option([\'Retail\', \'Commercial\', \'Contract\'], ',
            'date_added' => 'now()'
        ]);

        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_CONTRACT_TYPE', [
            'configuration_title' => 'NSA Contract Type',
            'configuration_value' => 'None',
            'configuration_description' => 'What kind of payment account do you have with the US Postal Service?<br><br><em>EPS</em> - Enterprise Payment System<br><br><em>Permit</em> - If you have a Mailing Permit whcih would entitle you a special discount on postage pricing, choose this option.<br><br><em>Meter</em> - If you have a licensed postage meter that grants you a special discount with the USPS, choose this option.',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'set_function' => 'zen_cfg_select_option([\'None\', \'EPS\', \'Permit\', \'Meter\'], ',
            'date_added' => 'now()'
        ]);

        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_ACCT_NUMBER', [
            'configuration_title' => 'USPS Account Number',
            'configuration_value' => '',
            'configuration_description' => 'What is the associated EPS Account Number or Meter Number you have with the United States Postal Service. (Leave blank if none.)',
            'configuration_group_id' => 6,
            'use_function' => 'zen_cfg_uspsr_account_display',
            'sort_order' => 0,
            'date_added' => 'now()'
        ]);

        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_DISPATCH_CART_TOTAL', [
            'configuration_title' => 'Send cart total as part of quote?',
            'configuration_value' => 'Yes',
            'configuration_description' => 'As part of the quoting process, you can send the customer\'s order total to the USPS API for it to calculate Insurance and eligibility for international shipping. (The USPS puts a limit on how much merchandise can be sent to certain countries and by certain methods.) If you choose \"No\", the module will send a cart value of $5 to be processed.<br><br><strong>CAUTION:</strong> If you don\'t send the total, your customer will not receive accurate price details from the USPS and you may end up paying more for the actual postage.',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'set_function' => 'zen_cfg_select_option([\'Yes\', \'No\'], ',
            'date_added' => 'now()'
        ]);

        /**
         * Debug Logging Options
         */

        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_DEBUG_MODE', [
            'configuration_title' => 'Debug Mode',
            'configuration_value' => '--none--',
            'configuration_description' => 'Would you like to enable debug modes?<br><br><em>"Generate Logs"</em> - This module will generate log files for each and every call to the USPS API Server (including the admin side viability check).<br><br>"<em>Display errors</em>" - If set, this means that any API errors that are caught will be displayed in the storefront.<br><br><em>CAUTION:</em> Each log file can be as big as 300KB in size.',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'set_function' => 'zen_cfg_select_multioption([\'Generate Logs\', \'Show Errors\'], ',
            'date_added' => 'now()'
        ]);

        /**
         * Sort Order
         */
        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_SORT_ORDER', [
            'configuration_title' => 'Sort Order',
            'configuration_value' => '0',
            'configuration_description' => 'Sort order of the modules display. <small>(where do you want this to be place along the other shipping modules)</small>',
            'configuration_group_id' => 6,
            'sort_order' => 0,
            'date_added' => 'now()'
        ]);

        $this->notify('NOTIFY_SHIPPING_USPS_INSTALLED');
    }

    /**
     * Settings responsible for the shipping module.
     *
     * @var array
     */
    public function keys()
    {
        $keys_list = [
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
        ];

        $this->notify('NOTIFY_SHIPPING_USPS_KEYS', '', $keys_list);
        return $keys_list;
    }

    public function remove()
    {
        global $db;
        $db->Execute("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key LIKE 'MODULE\_SHIPPING\_USPSR\_%' ");
        $this->notify('NOTIFY_SHIPPING_USPS_UNINSTALLED');
    }

    /**
     * If debug-logging is enabled, write the requested message to the log-file determined in this
     * module's class-constructor.
     */
    protected function uspsrDebug($message)
    {
        if ($this->debug_enabled === true && (strpos(MODULE_SHIPPING_USPSR_DEBUG_MODE, "Logs") !== FALSE)) {
            error_log(date('Y-m-d H:i:s') . ': ' . $message . PHP_EOL, 3, $this->debug_filename);
        }
    }

    protected function quoteLogConfiguration()
    {
        global $order, $currencies, $shipping_num_boxes;

        if ($this->debug_enabled === false) {
            return;
        }

        /**
         * Pull the LWH values from the database..
         */
        $pkg_dimmensions = array_filter(explode(', ', MODULE_SHIPPING_USPSR_DIMMENSIONS));
        array_walk($pkg_dimmensions, function (&$value) {
            $value = trim($value);
        }); // Quickly remove white space

        $ltr_dimmensions = array_filter(explode(', ', MODULE_SHIPPING_USPSR_LTR_DIMMENSIONS));
        array_walk($ltr_dimmensions, function (&$value) {
            $value = trim($value);
        });

        $domm_pkg_length = (double) $pkg_dimmensions[0];
        $intl_pkg_length = (double) $pkg_dimmensions[1];

        $domm_pkg_width = (double) $pkg_dimmensions[2];
        $intl_pkg_width = (double) $pkg_dimmensions[3];

        $domm_pkg_height = (double) $pkg_dimmensions[4];
        $intl_pkg_height = (double) $pkg_dimmensions[5];

        $domm_ltr_length = (double) $ltr_dimmensions[0];
        $intl_ltr_length = (double) $ltr_dimmensions[1];

        $domm_ltr_width = (double) $ltr_dimmensions[2];
        $intl_ltr_width = (double) $ltr_dimmensions[3];

        $domm_ltr_height = (double) $ltr_dimmensions[4];
        $intl_ltr_height = (double) $ltr_dimmensions[5];

        $message = '' . "\n\n";
        $message .= "USPSRestful Configuration Report\n";
        $message .= "=========================================================\n";
        $message .= 'USPSr build: ' . MODULE_SHIPPING_USPSR_VERSION . "\n\n";
        $message .= 'USPSr Endpoint URI: ' . $this->api_base . "\n";
        $message .= 'Quote Request Rate Type: ' . MODULE_SHIPPING_USPSR_PRICING . "\n";
        $message .= 'Quote from main_page: ' . $_GET['main_page'] . "\n";
        $message .= 'Display Transit Time: ' . MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT . "\n";

        $message .= 'Site Weights Based in: ' . SHIPPING_WEIGHT_UNITS . ' (' . (SHIPPING_WEIGHT_UNITS == 'lbs' ? 'Pounds' : 'Kilograms') . ')' . "\n";
        $message .= 'Site Measurements Based in: ' . (defined('SHIPPING_DIMENSION_UNITS') ? ucwords(SHIPPING_DIMENSION_UNITS) : "Inches") . "\n";
        $message .= 'Shipping ZIP Code Origin: ' . SHIPPING_ORIGIN_ZIP . "\n\n";

        if (SHIPPING_WEIGHT_UNITS == 'lbs') {
            $cart_pounds = floor($_SESSION['cart']->weight);
            $cart_ounces =  ($_SESSION['cart']->weight - $cart_pounds) * 16;
            $quote_pounds = floor($this->quote_weight);
            $quote_ounces = ($this->quote_weight - $quote_pounds) * 16;

            $message .= 'Cart Weight: ' . $_SESSION['cart']->weight . " " . SHIPPING_WEIGHT_UNITS . " ( " . $cart_pounds . " lbs. , " . $cart_ounces . " oz. )" . "\n";
            $message .= 'Total Quote Weight: ' . $this->quote_weight . ' lbs. ( Pounds: ' . $quote_pounds . ', Ounces: ' . $quote_ounces . " , Number of Boxes : $shipping_num_boxes )\n";

        } else { // means it has to be kgs
            $message .= 'Cart Weight: ' . $_SESSION['cart']->weight . " " . SHIPPING_WEIGHT_UNITS . "\n";
            $message .= 'Total Quote Weight: ' . $this->quote_weight . ' kgs.' . " ( Number of Boxes : $shipping_num_boxes )\n";
        }

        $message .= 'Maximum: ' . SHIPPING_MAX_WEIGHT . ' ' . SHIPPING_WEIGHT_UNITS . (SHIPPING_WEIGHT_UNITS == 'kgs' ? " (" . (double) SHIPPING_MAX_WEIGHT * 0.453592 . " lbs)" : '') . ' , Tare Rates: Small/Medium: ' . SHIPPING_BOX_WEIGHT . ' Large: ' . SHIPPING_BOX_PADDING . "\n";
        $message .= 'Order Handling method: ' . MODULE_SHIPPING_USPSR_HANDLING_METHOD . ', Handling fee Domestic (Order): ' . $currencies->format(MODULE_SHIPPING_USPSR_HANDLING_DOMESTIC) . ', Handling fee International (Order): ' . $currencies->format(MODULE_SHIPPING_USPSR_HANDLING_INTL) . "\n";


        $message .= "\n" . 'Services Selected: ' . "\n" . strip_tags(zen_cfg_uspsr_showservices(MODULE_SHIPPING_USPSR_TYPES)) . "\n";
        $message .= "Services being squashed: " . str_replace("Squash ", "", MODULE_SHIPPING_USPSR_SQUASH_OPTIONS) . "\n\n";
        $message .= "Categories Excluded from Media Mail: " . strip_tags(uspsr_get_categories(MODULE_SHIPPING_USPSR_MEDIA_MAIL_EXCLUDE)) . "\n";
        $message .= "Zip Codes Allowed for USPS Connect : " . uspsr_get_connect_zipcodes(MODULE_SHIPPING_USPSR_CONNECT_LOCAL_ZIP) . "\n";

        $message .= 'Package Add-Ons Enabled (Domestic): ' . strip_tags(zen_cfg_uspsr_extraservices_display(MODULE_SHIPPING_USPSR_DMST_SERVICES)) . "\n";
        $message .= 'Package Add-Ons Enabled (International): ' . strip_tags(zen_cfg_uspsr_extraservices_display(MODULE_SHIPPING_USPSR_INTL_SERVICES)) . "\n\n";

        $message .= 'Letters Add-Ons Enabled (Domestic): ' . strip_tags(zen_cfg_uspsr_extraservices_display(MODULE_SHIPPING_USPSR_DMST_LETTER_SERVICES)) . "\n";
        $message .= 'Letters Add-Ons Enabled (International): ' . strip_tags(zen_cfg_uspsr_extraservices_display(MODULE_SHIPPING_USPSR_INTL_LETTER_SERVICES)) . "\n\n";

        $message .= 'Assumed Domestic Package Size - Length: ' . $domm_pkg_length . ', Width: ' . $domm_pkg_width . ', Height: ' . $domm_pkg_height . "\n";
        $message .= 'Assumed International Package Size - Length: ' . $intl_pkg_length . ', Width: ' . $intl_pkg_width . ', Height: ' . $intl_pkg_height . "\n\n";

        $message .= 'Assumed Domestic Letter Size - Length: ' . $domm_ltr_length . ', Height: ' . $domm_ltr_width . ', Thickness: ' . $domm_ltr_height . "\n";
        $message .= 'Assumed International Letter Size - Length: ' . $intl_ltr_length . ', Height: ' . $intl_ltr_width . ', Thickness: ' . $intl_ltr_height . "\n\n";

        $message .= 'Media Mail Pricing Class : ' . $this->machinable . (MODULE_SHIPPING_USPSR_MEDIA_CLASS !== $this->machinable ? " Overriden!!" : "") . "\n";
        $message .= 'Dimensional Pricing Class : ' . MODULE_SHIPPING_USPSR_DIMENSIONAL_CLASS . "\n";
        $message .= 'Cubic Pricing Class : ' . MODULE_SHIPPING_USPSR_CUBIC_CLASS . "\n";
        $message .= 'First-Class Mail Machineable Flags : ' . MODULE_SHIPPING_USPSR_LTR_MACHINEABLE_FLAGS . "\n\n";

        $message .= 'Sort the returned quotes by: ' . MODULE_SHIPPING_USPSR_QUOTE_SORT . "\n";
        $message .= 'Order is eligible for Media Mail ? ' . ($this->enable_media_mail ? 'YES' : 'NO') . "\n\n";

        $message .= 'Order SubTotal: ' . $currencies->format($order->info['subtotal']) . "\n";
        $message .= 'Order Total: ' . $currencies->format($order->info['total']) . (MODULE_SHIPPING_USPSR_DISPATCH_CART_TOTAL == "Yes" ? '' : " (Cart total is being capped at " . $currencies->format(5) . ")") . "\n";
        $message .= 'Uninsurable Portion: ' . $currencies->format($this->uninsured_value) . "\n";
        $message .= 'Insurable Price: ' . (MODULE_SHIPPING_USPSR_DISPATCH_CART_TOTAL == "Yes" ? $currencies->format($this->shipment_value) : $currencies->format(5)) . "\n";

        $this->uspsrDebug($message);
    }

    public function update_status()
    {
        global $order, $db;
        if (IS_ADMIN_FLAG === true) {
            return;
        }

        // disable only when entire cart is free shipping
        if (!zen_get_shipping_enabled($this->code)) {
            $this->enabled = false;
        }

        // This is the check to see if ZenCart should enable the module only for the GeoZone defined
        // in the back end.
        if ($this->enabled === true && isset($order) && (int)MODULE_SHIPPING_USPSR_ZONE > 0) {
            $check_flag = false;
            $check = $db->Execute(
                "SELECT zone_id
                   FROM " . TABLE_ZONES_TO_GEO_ZONES . "
                  WHERE geo_zone_id = " . (int)MODULE_SHIPPING_USPSR_ZONE . "
                    AND zone_country_id = " . (int)$order->delivery['country']['id'] . "
                  ORDER BY zone_id ASC"
            );

            // -----
            // NOTE: Using the legacy form of traversing the $db output; will be updated once support
            // is dropped for Zen Cart versions prior to v1.5.7!
            while (!$check->EOF) {
                if ($check->fields['zone_id'] < 1 || $check->fields['zone_id'] == $order->delivery['zone_id']) {
                    $check_flag = true;
                    break;
                }
                $check->MoveNext();
            }

            // Shipping Estimator fallback?
            // If the order is being estimated, and the search above yielded something, run this anyway
            if (!zen_is_logged_in() && ($_GET['main_page'] === 'shopping_cart' || $_GET['main_page'] === 'popup_shipping_estimator')) {

                $selectedState = $_POST['state'];
                $selected_state_id = $_POST['zone_id'] ?? 0;

                if (!zen_not_null($selectedState) || $selected_state_id < 1) {
                    // If there is no number
                    $zone_id = $db->Execute(
                        "SELECT zone_id
                           FROM ". TABLE_ZONES . "
                           WHERE
                           zone_name LIKE '" . $_POST['state']  . "'
                           OR
                           zone_code LIKE '" . $_POST['state'] .  "'");

                    $selected_state_id = $zone_id->fields['zone_id'];
                }

                if (zen_not_null($selected_state_id)) { // No $zone_id, don't check the result list.
                    // Reset $check
                    $check->rewind();
                    while(!$check->EOF) {
                        if ($selected_state_id == $check->fields['zone_id']) {
                            $check_flag = true;
                            break;
                        }
                        $check->MoveNext();
                    }
                }

            }

            if ($check_flag === false) {
                $this->enabled = false;
            }

        }


        $this->notify('NOTIFY_SHIPPING_USPS_UPDATE_STATUS');
    }

    protected function adminInitializationChecks()
    {
        global $messageStack;

        if ($this->debug_enabled === true) {
            $this->title .= '<span class="alert"> (Debug is ON: ' . MODULE_SHIPPING_USPSR_DEBUG_MODE . ')</span>';
        }

        // -----
        // If still enabled, check to make sure that at least one shipping-method has been chosen (otherwise,
        // no quotes can be returned on the storefront.  If the condition is found, indicate that the module
        // is disabled so that the amber warning symbol appears in the admin shipping-modules' listing.
        //
        if ($this->enabled === true) {
            $this->checkConfiguration();
        }


        /**
         * Test to see if the upgrader should run?
         *
         * If this is encapsulated, the upgrader already ran. (Any missing keys would have been added and any values would be updated. More importantly, the versions would already match.)
         * If this is not encapsulated, the version in the database would fall short. So check that.
         * 
         * @todo This whole section needs a revamp.
         */

        // The versions don't match. So upgrade what we have to. This only applies to version 1.0.0 and forward.
        if ((MODULE_SHIPPING_USPSR_VERSION !== self::USPSR_CURRENT_VERSION) && MODULE_SHIPPING_USPSR_VERSION !== "v0.0.0") {

            // Add new versions to the bottom of this. Do not put a mitigating "break" in between.
            switch (MODULE_SHIPPING_USPSR_VERSION) {

                // BREAKING CHANGE... The data table was changed!
                case "v0.1.0": // Released 2024-12-22
                case "v0.2.0": // Released 2025-01-17
                case "v0.3.0": // This version didn't officially get released but was the old format of the repository before the directory rename
                    // Any changes to the database from v1.0.0 should go here
                    // v0.3.0 and before didn't have the Min/Max table. Let's add it.

                    // Check to see if the module is active?
                    if (preg_match("/uspsr.php/", MODULE_SHIPPING_INSTALLED)) {
                        // Add Squash alike methods together
                        $this->addConfigurationKey('MODULE_SHIPPING_USPSR_SQUASH_OPTIONS', [
                            'configuration_title' => 'Squash Alike Methods Together',
                            'configuration_value' => '--none--',
                            'configuration_description' => 'If you are offering Priority Mail and Priority Mail Cubic or Ground Advantage and Ground Advantage Cubic in the same quote, do you want to "squash" them together and offer the lower of each pair?<br><br>This will only work if the quote returned from USPS has BOTH options (Cubic and Normal) in it, otherwise it will be ignored.',
                            'configuration_group_id' => 6,
                            'sort_order' => 0,
                            'set_function' => 'zen_cfg_select_multioption([\'Squash Ground Advantage\', \'Squash Priority Mail\'], '
                        ]);

                        // Change the Debug Mode to be a split selection between showing logs or showing errors
                        $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_DEBUG_MODE', [
                            'configuration_title' => 'Debug Mode',
                            'configuration_value' => (MODULE_SHIPPING_USPSR_DEBUG_MODE === 'Logs' ? "Generate Logs" : "--none--"),
                            'configuration_description' => 'Would you like to enable debug modes?<br><br><em>"Generate Logs"</em> - This module will generate log files for each and every call to the USPS API Server (including the admin side viability check).<br><br>"<em>Display errors</em>" - If set, this means that any API errors that are caught will be displayed in the storefront.<br><br><em>CAUTION:</em> Each log file is at least 300KB big.',
                            'set_function' => 'zen_cfg_select_multioption([\'Generate Logs\', \'Show Errors\'], ',
                            'date_added' => 'now()'
                        ]);

                        // Created a function to either show the value or to show none
                        $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_ACCT_NUMBER', [
                            'use_function' => 'zen_cfg_uspsr_account_display',
                        ]);
                    }

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

                    // Reset the module's selected shipping methods entirely.
                    if (defined('SHIPPING_WEIGHT_UNITS') && SHIPPING_WEIGHT_UNITS === 'kgs') {
                    $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_TYPES', [
                        'configuration_value' => '0, 31.7514, 0.00, 0, 9.0718, 0.00, 0, 31.7514, 0.00, 0, 11.3398, 0.00, 0, 11.3398, 0.00, 0, 11.3398, 0.00, 0, 11.3398, 0.00, 0, 31.7514, 0.00, 0, 9.0718, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 31.7514, 0.00, 0, 1.8143, 0.00, 0, 31.7514, 0.00, 0, 1.8143, 0.00, 0, 1.8143, 0.00, 0, 1.8143, 0.00, 0, 1.8143, 0.00, 0, 9.0718, 0.00, 0, 9.0718, 0.00, 0, 31.7514, 0.00, 0, 1.8143, 0.00, 0, 1.8143, 0.00, 0, 1.8143, 0.00',
                        'configuration_description' => 'Choose the services that you want to offer to your customers.<br><br><strong>Checkbox:</strong> Select the services to be offered<br><br><strong>Min/Max</strong> Choose a custom minimum/maximum for the selected service. If the cart as a whole (the items plus any tare settings) fail to make weight, the method will be skipped. Keep in mind that each service also has its own maximums that will be controlled regardless of what was set here. (Example: entering 5 lbs for International First-Class Mail will be ignored since the International First-Class Mail has a hard limit of 4 lbs.)<br><br><strong>Handling:</strong> A handling charge for that particular method (will be added on to the quote plus any services charges that are applicable).<br><br>USPS returns methods based on cart weights. Enter the weights in your site\'s configured standard. (The cart will handle conversions as necessary.)',
                        'configuration_title' => 'Shipping Methods (Domestic and International)',
                    ]);
                    } else {
                    $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_TYPES', [
                        'configuration_value' => '0, 0.21875, 0.00, 0, 70, 0.00, 0, 20, 0.00, 0, 70, 0.00, 0, 25, 0.00, 0, 25, 0.00, 0, 25, 0.00, 0, 25, 0.00, 0, 70, 0.00, 0, 20, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 70, 0.00, 0, 0.21875, 0.00, 0, 4, 0.00, 0, 70, 0.00, 0, 4, 0.00, 0, 4, 0.00, 0, 4, 0.00, 0, 4, 0.00, 0, 20, 0.00, 0, 20, 0.00, 0, 70, 0.00, 0, 4, 0.00, 0, 4, 0.00, 0, 4, 0.00',
                        'configuration_description' => 'Choose the services that you want to offer to your customers.<br><br><strong>Checkbox:</strong> Select the services to be offered<br><br><strong>Min/Max</strong> Choose a custom minimum/maximum for the selected service. If the cart as a whole (the items plus any tare settings) fail to make weight, the method will be skipped. Keep in mind that each service also has its own maximums that will be controlled regardless of what was set here. (Example: entering 5 lbs for International First-Class Mail will be ignored since the International First-Class Mail has a hard limit of 4 lbs.)<br><br><strong>Handling:</strong> A handling charge for that particular method (will be added on to the quote plus any services charges that are applicable).<br><br>USPS returns methods based on cart weights. Enter the weights in your site\'s configured standard. (The cart will handle conversions as necessary.)',
                        'configuration_title' => 'Shipping Methods (Domestic and International)',
                    ]);
                    }
                    $messageStack->add_session('<strong>USPSr Warning:</strong> Due to changes in configuration, if USPSr was enabled and already installed, you must now go to <a href="' . zen_href_link(FILENAME_DEFAULT, 'cmd=modules&set=shipping&module=uspsr') . '">Modules > Shipping > USPSr</a> and reselect your desired USPS Shipping Methods.', 'warning');

                    $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_MEDIA_CLASS', [
                        'configuration_key' => 'MODULE_SHIPPING_USPSR_MEDIA_CLASS'
                    ]);
                    // The PROCESSING_CLASS, now MEDIA_CLASS, changed quite a bit.
                    $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_MEDIA_CLASS', [
                        'configuration_title' => 'Packaging Class - Media Mail',
                        'configuration_description' => 'For Media Mail only, are your packages typically machinable?<br><br>\"Machinable\" means a mail piece designed and sized to be processed by automated postal equipment. Typically this is rigid mail, that fits a certain shape and is within a certain weight (no more than 25 pounds for Media Mail). If your normal packages are within these guidelines, set this flag to \"Machinable\". Otherwise, set this to \"Nonstandard\". (If your customer order\'s total weight or package size falls outside this limit, regardless of the setting, the module will set the package to \"Nonstandard\".) (If your customer order\'s total weight or package size falls outside of this limit, regardless of the setting, the module will set the package to \"Nonstandard\".) <br><br>This applies only to Media Mail. All other mail services will have their \"Machinability\" status determined by the weight of the cart and the size of the package entered below.',
                        'set_function' => 'zen_cfg_select_option([\'Machinable\', \'Nonstandard\'], ',
                    ]);

                    // Language error in the description of Exclusions from Media Mail
                    $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_MEDIA_MAIL_EXCLUDE', [
                        'configuration_title' => 'Categories to Excluded from Media Mail',
                    ]);

                    // The description Domestic and International Services changed
                    $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_DMST_SERVICES',[
                        'configuration_description' => 'Pick which add-ons you wish to offer as a part of the shipping cost quote for domestic packages. (The USPS API will do the math as necessary.)<br><br><strong>CAUTION:</strong> Not all options apply to all services.<br>',
                    ]);

                    $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_INTL_SERVICES', [
                        'configuration_description' => 'Pick which add-ons you wish to offer as a part of the shipping cost quote for international packages. (The USPS API will do the math as necessary.)<br><br><strong>CAUTION:</strong> Not all options apply to all services.<br>',
                    ]);

                    // Language changed for USPSR
                    $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_CONTRACT_TYPE', [
                        'configuration_description' => 'What kind of payment account do you have with the US Postal Service?<br><br><em>EPS</em> - Enterprise Payment System<br><br><em>Permit</em> - If you have a Mailing Permit whcih would entitle you a special discount on postage pricing, choose this option.<br><br><em>Meter</em> - If you have a licensed postage meter that grants you a special discount with the USPS, choose this option.',
                    ]);

                    if (preg_match("/uspsr.php/", MODULE_SHIPPING_INSTALLED)) {
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


                // Next group of changes
                case "v1.0.0": // Released 2025-02-18
                case "v1.1.1": // Released 2025-03-07, subsequently deleted and replaced with 1.1.2
                case "v1.1.2": // Released 2025-03-07
                    $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_HANDLING_TIME', [
                        'configuration_description' => 'In whole numbers, how many days does it take for you to dispatch your packages to the USPS. (Enter as a whole number only. Between 0 and 30. This will be added to the estimated delivery date or time as needed.)',
                        'set_function' => ''
                    ]);

                    $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_DMST_SERVICES', [
                        'configuration_title' => 'Shipping Add-ons (Domestic Packages)',
                    ]);

                    $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_INTL_SERVICES', [
                        'configuration_title' => 'Shipping Add-ons (International Packages)',
                    ]);

                    // New change, fixing a spelling error in the description of Debug Mode.
                    $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_DEBUG_MODE', [
                        'configuration_description' => 'Would you like to enable debug modes?<br><br><em>"Generate Logs"</em> - This module will generate log files for each and every call to the USPS API Server (including the admin side viability check).<br><br>"<em>Display errors</em>" - If set, this means that any API errors that are caught will be displayed in the storefront.<br><br><em>CAUTION:</em> Each log file can be as big as 300KB in size.',
                    ]);

                case "v1.2.0": // Released 2025-03-15
                case "v1.3.0": // Released 2025-08-17 (Had an issue with this one, some installs saw some keys get skipped... )
                case "v1.3.1": // Released 2025-08-24 (There aren't any changes module was between 1.3.1 and 1.3.2 but it doesn't hurt to rerun)
                    if (preg_match("/uspsr.php/", MODULE_SHIPPING_INSTALLED)) { // Only should be run if the module is already installed.
                        // Changing the description
                        $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_TYPES', [
                            'configuration_description' => 'Choose the services that you want to offer to your customers.<br><br><strong>Checkbox:</strong> Select the services to be offered. (Can also click on the service name in certain browsers.)<br><br><strong>Min/Max</strong> Choose a custom minimum/maximum for the selected service. If the cart as a whole (the items plus any tare settings) fail to make weight, the method will be skipped. Keep in mind that each service also has its own maximums that will be controlled regardless of what was set here. (Example: entering 5 lbs for International First-Class Mail will be ignored since the International First-Class Mail has a hard limit of 4 lbs.)<br><br><strong>Handling:</strong> A handling charge for that particular method (will be added on to the quote plus any services charges that are applicable).<br><br>USPS returns methods based on cart weights. Enter the weights in your site\'s configured standard. (The cart will handle conversions as necessary.)',
                        ]);

                        // Get rid of the numeric updown function
                        $update_handling_time['set_function'] = '';
                        if (version_compare(PROJECT_VERSION_MAJOR . "." . PROJECT_VERSION_MINOR, '1.5.6', ">=")) {
                            $update_handling_time['val_function'] = '{"error":"MODULE_SHIPPING_USPSR_HANDLING_DAYS","id":"FILTER_VALIDATE_INT","options":{"options":{"min_range": 0, "max_range": 30}}}';
                        }
                        $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_HANDLING_TIME', $update_handling_time);

                        $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_HANDLING_TIME', [
                            'configuration_description' => 'In whole numbers, how many days does it take for you to dispatch your packages to the USPS. (Enter as a whole number only. Between 0 and 30. This will be added to the estimated delivery date or time as needed.)',
                            'set_function' => '',
                        ]);

                        // Letter Dimmensions
                        if (!defined('MODULE_SHIPPING_USPSR_LTR_DIMMENSIONS')) { // If the module is installed but the key isn't defined... install it.
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
                    }
                    case "v1.3.2": // Released 2025-08-25: No database changes made from 1.3.2 to 1.4.1. All changes were to the module itself.
                    case "v1.4.0": // Released 2025-09-02: No database changes
                    case "v1.4.1": // Released 2025-09-03: No database changes
                        $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_TYPES', [
                            'set_function' => 'zen_cfg_uspsr_services([\'First-Class Mail Letter\',\'USPS Ground Advantage\', \'USPS Ground Advantage Cubic\', \'Media Mail\', \'Connect Local Machinable DDU\', \'Connect Local Machinable DDU Flat Rate Box\', \'Connect Local Machinable DDU Small Flat Rate Bag\', \'Connect Local Machinable DDU Large Flat Rate Bag\', \'Priority Mail\', \'Priority Mail Cubic\', \'Priority Mail Flat Rate Envelope\', \'Priority Mail Padded Flat Rate Envelope\', \'Priority Mail Legal Flat Rate Envelope\', \'Priority Mail Small Flat Rate Box\', \'Priority Mail Medium Flat Rate Box\', \'Priority Mail Large Flat Rate Box\', \'Priority Mail Large Flat Rate APO/FPO/DPO\', \'Priority Mail Express\', \'Priority Mail Express Flat Rate Envelope\', \'Priority Mail Express Padded Flat Rate Envelope\', \'Priority Mail Express Legal Flat Rate Envelope\', \'First-Class Mail International Letter\', \'First-Class Package International Service Machinable ISC Single-piece\', \'Priority Mail International ISC Single-piece\', \'Priority Mail International ISC Flat Rate Envelope\', \'Priority Mail International Machinable ISC Padded Flat Rate Envelope\', \'Priority Mail International ISC Legal Flat Rate Envelope\', \'Priority Mail International Machinable ISC Small Flat Rate Box\', \'Priority Mail International Machinable ISC Medium Flat Rate Box\', \'Priority Mail International Machinable ISC Large Flat Rate Box\', \'Priority Mail Express International ISC Single-piece\', \'Priority Mail Express International ISC Flat Rate Envelope\', \'Priority Mail Express International ISC Legal Flat Rate Envelope\', \'Priority Mail Express International ISC Padded Flat Rate Envelope\'], ',
                        ]);
                        break;
            }

            // After all this, update the modules version number as necessary.
            $this->updateConfigurationKey('MODULE_SHIPPING_USPSR_VERSION', [
                'configuration_value' => self::USPSR_CURRENT_VERSION,
                'set_function' => "zen_cfg_read_only("

            ]);

            // The applies to all versions BEFORE 1.3.0
            if (version_compare(str_replace("v", "", MODULE_SHIPPING_USPSR_VERSION), "1.3.0", "<")) {
                /**
                 * Adding new methods into the shipping methods datatable.
                 *
                 * This is done by adding the value at the front for US First Class Mail Letter then splicing it into the datatable.
                 */
                // Regardless of the version, we need to update the data field for MODULE_SHIPPING_USPSR_TYPES.

                if (defined('MODULE_SHIPPING_USPSR_TYPES')) {

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
                        'set_function' => 'zen_cfg_uspsr_services([\'First-Class Mail Letter\',\'USPS Ground Advantage\', \'USPS Ground Advantage Cubic\', \'Media Mail\', \'Connect Local Machinable DDU\', \'Connect Local Machinable DDU Flat Rate Box\', \'Connect Local Machinable DDU Small Flat Rate Bag\', \'Connect Local Machinable DDU Large Flat Rate Bag\', \'Priority Mail\', \'Priority Mail Cubic\', \'Priority Mail Flat Rate Envelope\', \'Priority Mail Padded Flat Rate Envelope\', \'Priority Mail Legal Flat Rate Envelope\', \'Priority Mail Small Flat Rate Box\', \'Priority Mail Medium Flat Rate Box\', \'Priority Mail Large Flat Rate Box\', \'Priority Mail Large Flat Rate APO/FPO/DPO\', \'Priority Mail Express\', \'Priority Mail Express Flat Rate Envelope\', \'Priority Mail Express Padded Flat Rate Envelope\', \'Priority Mail Express Legal Flat Rate Envelope\', \'First-Class Mail International Letter\', \'First-Class Package International Service Machinable ISC Single-piece\', \'Priority Mail International ISC Single-piece\', \'Priority Mail International ISC Flat Rate Envelope\', \'Priority Mail International Machinable ISC Padded Flat Rate Envelope\', \'Priority Mail International ISC Legal Flat Rate Envelope\', \'Priority Mail International Machinable ISC Small Flat Rate Box\', \'Priority Mail International Machinable ISC Medium Flat Rate Box\', \'Priority Mail International Machinable ISC Large Flat Rate Box\', \'Priority Mail Express International ISC Single-piece\', \'Priority Mail Express International ISC Flat Rate Envelope\', \'Priority Mail Express International ISC Legal Flat Rate Envelope\', \'Priority Mail Express International ISC Padded Flat Rate Envelope\'], ',
                    ]);

                }
            }

            $messageStack->add_session(sprintf(MODULE_SHIPPING_USPSR_UPGRADE_SUCCESS, self::USPSR_CURRENT_VERSION), 'success');

        }

        /**
         * Is there an upgrade available?
         *
         * Make a call into the ZenCart Module DB and compare the returned result versus the number.
         * Don't make this call if the current version is v0.0.0. (There will always be a "better" version than v0.0.0)
         */
        $check_for_new_version = plugin_version_check_for_updates(self::ZEN_CART_PLUGIN_ID, MODULE_SHIPPING_USPSR_VERSION);

        if (MODULE_SHIPPING_USPSR_VERSION !== "v0.0.0" && $check_for_new_version) {
            $messageStack->add_session(MODULE_SHIPPING_USPSR_UPGRADE_AVAILABLE, 'caution');
        }

        /**
         * Are you using 0.0.0? Seriously, stop.
         * Starting in 1.5.0, -rc version will designate the release as a release candidate... Should be prepared for stuff to not work.
         */
        if (self::USPSR_CURRENT_VERSION === "v0.0.0" || strpos(self::USPSR_CURRENT_VERSION, "-rc") !== FALSE)
            // If this version is v0.0.0 or contains -dev, this is a developmental release. Proceed with caution.
            $messageStack->add_session(MODULE_SHIPPING_USPSR_DEVELOPMENTAL, 'warning');
    }

    /**
     * Common storefront/admin configuration checking.  Called from adminInitializationChecks
     * and storefrontInitialization.  Will auto-disable the shipping method if either no services
     * have been selected or the country-of-origin is not the US.
     */
    protected function checkConfiguration()
    {
        global $messageStack;

        // Try to get a bearer token
        if (!zen_not_null($this->bearerToken))
            $this->getBearerToken();


        // Need to have at least one method enabled
        $usps_shipping_methods_cnt = 0;
        foreach ($this->typeCheckboxesSelected as $requested_type) {
            if (is_numeric($requested_type)) {
                continue;
            }
            $usps_shipping_methods_cnt++;
        }

        if ($usps_shipping_methods_cnt === 0) {
            $this->enabled = false;
            if (IS_ADMIN_FLAG === true) {
                $messageStack->add_session(MODULE_SHIPPING_USPSR_ERROR_NO_QUOTES, 'error');
            }
        }

        // If the Origin Zip Code fails validation... stop.
        if (!uspsr_validate_zipcode(SHIPPING_ORIGIN_ZIP)) {
            $this->enabled = false;
            if (IS_ADMIN_FLAG === true) {
                $messageStack->add_session(MODULE_SHIPPING_USPSR_ERROR_BAD_ORIGIN_ZIPCODE, 'error');
            }
        }

        // If the origin country isn't the United States, you can't use USPS (APO/DPO/FPO counts as United States)... stop.
        if (SHIPPING_ORIGIN_COUNTRY !== '223') {
            $this->enabled = false;
            if (IS_ADMIN_FLAG === true) {
                $messageStack->add_session(MODULE_SHIPPING_USPSR_ERROR_BAD_ORIGIN_COUNTRY, 'error');
            }
        }

        // If either the API Key or Secret are blank, stop, you can't use USPS
        if (!zen_not_null(MODULE_SHIPPING_USPSR_API_KEY) || !zen_not_null(MODULE_SHIPPING_USPSR_API_SECRET)) {
            $this->enabled = false;
            if (IS_ADMIN_FLAG === true) {
                $messageStack->add_session(MODULE_SHIPPING_USPSR_ERROR_BAD_CREDENTIALS, 'error');
            }
        }

        // If either the API Key or Secret are duds, stop, you can't use USPS... you didn't provide proper access credentials.
        if ((strtolower(MODULE_SHIPPING_USPSR_API_KEY) == 'none') || strtolower(MODULE_SHIPPING_USPSR_API_SECRET) == 'none') {
            $this->enabled = false;
            if (IS_ADMIN_FLAG === true) {
                $messageStack->add_session(MODULE_SHIPPING_USPSR_ERROR_NO_CREDENTIALS, 'error');
            }
        }

        // If the Contract option is selected but either the Contract Type is set to None OR the Account Number is blank, stop.
        if (MODULE_SHIPPING_USPSR_PRICING == 'Contract' && (MODULE_SHIPPING_USPSR_CONTRACT_TYPE == 'None' || !zen_not_null(MODULE_SHIPPING_USPSR_ACCT_NUMBER))) {
            $this->enabled = false;
            if (IS_ADMIN_FLAG === true) {
                $messageStack->add_session(MODULE_SHIPPING_USPSR_ERROR_NO_CONTRACT, 'error');
            }
        }

        // If the module is NOT able to get a Bearer Token, disable the module. Something is wrong.
        if (((strtolower(MODULE_SHIPPING_USPSR_API_KEY) != 'none') && (strtolower(MODULE_SHIPPING_USPSR_API_KEY)) != 'none') && !zen_not_null($this->bearerToken)) {
            $this->enabled = false;
            if (IS_ADMIN_FLAG === true) {
                $messageStack->add_session(MODULE_SHIPPING_USPSR_ERROR_REJECTED_CREDENTIALS, 'error');
            }

        }

        return $this->enabled;
    }

    protected function _getQuote()
    {
        global $order, $shipping_weight;

        $focus = '';
        /**
         * Build array of shipping values
         */
        $pkg_dimmensions = array_filter(explode(', ', MODULE_SHIPPING_USPSR_DIMMENSIONS));
        array_walk($pkg_dimmensions, function (&$value) {
            $value = trim($value);
        }); // Quickly remove white space

        $ltr_dimmensions = array_filter(explode(', ', MODULE_SHIPPING_USPSR_LTR_DIMMENSIONS));
        array_walk($ltr_dimmensions, function (&$value) {
            $value = trim($value);
        }); // Quickly remove white space

        // Check if the measurement setting exists and if it does, check that it's in inches.
        // If it doesn't or if it is set to inches, do nothing.
        if (defined('SHIPPING_DIMENSION_UNITS') && SHIPPING_DIMENSION_UNITS !== "inches") {
            foreach ($pkg_dimmensions as &$dimmension) {
                $dimmension = (double) $dimmension / 2.54;
            }

            foreach ($ltr_dimmensions as &$dimmension) {
                $dimmension = (double) $dimmension / 2.54;
            }
        }

        // The order won't matter as the USPS considers the biggest measurement to the length, then the width, then the height.
        $pkg_domm_length = max((double) $pkg_dimmensions[0], 8.625);
        $pkg_intl_length = max((double) $pkg_dimmensions[1], 8.625);
        $pkg_domm_width = max((double) $pkg_dimmensions[2], 5.375);
        $pkg_intl_width = max((double) $pkg_dimmensions[3], 5.375);
        $pkg_domm_height = max((double) $pkg_dimmensions[4], 1.625);
        $pkg_intl_height = max((double) $pkg_dimmensions[5], 1.625);

        $ltr_domm_length = max((double) $ltr_dimmensions[0], 6.125);
        $ltr_intl_length = max((double) $ltr_dimmensions[1], 6.125);
        $ltr_domm_height = max((double) $ltr_dimmensions[2], 11.5);
        $ltr_intl_height = max((double) $ltr_dimmensions[3], 11.5);
        $ltr_domm_thickness = max((double) $ltr_dimmensions[4], .25);
        $ltr_intl_thickness = max((double) $ltr_dimmensions[5], .25);

        /**
         * Build the JSON Call to the server
         */
        // Prepare a Standards Query
        $standards_query = [];

        // Prepare a Packages Query
        $pkg_body = [];

        // Prepare a Letters Query
        $ltr_body = [];

        if ($this->usps_countries == 'US') {

            // There are only three classes needed: Ground Advantage, Priority Mail, Priority Mail Express
            $mailClasses = [
                "USPS_GROUND_ADVANTAGE",
                "PRIORITY_MAIL",
                "PRIORITY_MAIL_EXPRESS"
            ];

            /**
                * Is this package going to a APO/FPO/DPO?
            */
            $this->is_apo_dest = in_array(uspsr_validate_zipcode($order->delivery['postcode']), self::USPSR_MILITARY_MAIL_ZIP);

            /**
                * Check to see if the products in the cart are ALL eligible for USPS Media Mail.
            */
            if ($this->enable_media_mail) {
                $mailClasses[] = "MEDIA_MAIL";
            }

            // Check to see if the order fits for USPS Connect Local
            if (uspsr_check_connect_local($order->delivery['postcode']))
                $mailClasses[] = "USPS_CONNECT_LOCAL";

            $destination_zip = uspsr_validate_zipcode($order->delivery['postcode']);

            // Package Request Body
            $pkg_body = [
                'originZIPCode' => uspsr_validate_zipcode(SHIPPING_ORIGIN_ZIP),
                'destinationZIPCode' => $destination_zip,
                'weight' => $shipping_weight,
                'length' => $pkg_domm_length,
                'width' => $pkg_domm_width,
                'height' => $pkg_domm_height,
                'mailClasses' => $mailClasses,
                'priceType' => strtoupper(MODULE_SHIPPING_USPSR_PRICING),
                'itemValue' => (MODULE_SHIPPING_USPSR_DISPATCH_CART_TOTAL == "Yes" ? $this->shipment_value : 5),
            ];

            // USPS Letters Services (still need to be attached to the Letter Request, Packages will be processed separately)
            $services_ltr_dmst = array_filter(explode(', ', MODULE_SHIPPING_USPSR_DMST_LETTER_SERVICES));
            $services_ltr_intl = array_filter(explode(', ', MODULE_SHIPPING_USPSR_INTL_LETTER_SERVICES));
            
            if (in_array(930, $services_ltr_dmst)) {
                $services_ltr_dmst[] = 931;
            }

            if (in_array(930, $services_ltr_intl)) {
                $services_ltr_intl[] = 931;
            }

            $services_ltr_dmst = array_values(array_filter(array_map('intval', $services_ltr_dmst), function ($service) {
                return $service > 0; // Keep only positive integers
            }));

            $services_ltr_intl = array_values(array_filter(array_map('intval', $services_ltr_intl), function ($service) {
                return $service > 0; // Keep only positive integers
            }));

            $services_ltr = $this->is_us_shipment ? $services_ltr_dmst : $services_ltr_intl;


            // Letter Request Body
            $ltr_body = [
                "weight" => $shipping_weight * 16, // The cart weight is in pounds, the letters API takes the request in ounces
                "length" => $ltr_domm_length,
                "height" => $ltr_domm_height,
                "thickness" => $ltr_domm_thickness,
                "processingCategory" => strtoupper(MODULE_SHIPPING_USPSR_LTR_PROCESSING),
                "nonMachinableIndicators" =>
                [
                    "isPolybagged" => strpos(MODULE_SHIPPING_USPSR_LTR_MACHINEABLE_FLAGS, "Polybagged") !== false,
                    "hasClosureDevices" => strpos(MODULE_SHIPPING_USPSR_LTR_MACHINEABLE_FLAGS, "ClosureDevices") !== false,
                    "hasLooseItems" => strpos(MODULE_SHIPPING_USPSR_LTR_MACHINEABLE_FLAGS, "LooseItems") !== false,
                    "isRigid" => strpos(MODULE_SHIPPING_USPSR_LTR_MACHINEABLE_FLAGS, "Rigid") !== false,
                    "isSelfMailer" => strpos(MODULE_SHIPPING_USPSR_LTR_MACHINEABLE_FLAGS, "SelfMailer") !== false,
                    "isBooklet" => strpos(MODULE_SHIPPING_USPSR_LTR_MACHINEABLE_FLAGS, "Booklet") !== false,
                ],
                "extraServices" => $services_ltr,
                "itemValue" => (MODULE_SHIPPING_USPSR_DISPATCH_CART_TOTAL == "Yes" ? $this->shipment_value : 5),
            ];

            // Let's make a standards request now.
            $standards_query = [
                'originZIPCode' => uspsr_validate_zipcode(SHIPPING_ORIGIN_ZIP),
                'destinationZIPCode' => $destination_zip,
                'mailClass' => 'ALL',
                'weight' => $shipping_weight
            ];

            $todays_date = new DateTime();
            $daystoadd = (int) MODULE_SHIPPING_USPSR_HANDLING_TIME;

            $todays_date_plus = $todays_date->modify("+{$daystoadd} days");
            $standards_query['acceptanceDate'] = $todays_date_plus->format('Y-m-d');

            $street_address = (array_key_exists('street_address', $order->delivery) ? trim($order->delivery['street_address']) : '');

            // If the address contains "PO BOX" or "BOX" in the address line 1, that makes it a PO BOX.
            if (preg_match("/^(PO BOX|BOX)/i", $street_address)) {
                $standards_query['destinationType'] = "PO_BOX";
            } else {
                $standards_query['destinationType'] = "STREET";
            }


            // Send pkg_body to make pkgQuote.
            $this->pkgQuote = $this->_makeQuotesCall($pkg_body, 'package-domestic');
            $this->ltrQuote = $this->_makeQuotesCall($ltr_body, 'letters-domestic');

            $this->notify('NOTIFY_SHIPPING_USPS_US_DELIVERY_REQUEST_READY', [], $pkg_body, $ltr_body);
        } else { // It's not going to the US, so it's international

            $pkg_body = [
                "originZIPCode" => uspsr_validate_zipcode(SHIPPING_ORIGIN_ZIP),
                "foreignPostalCode" => $order->delivery['postcode'],
                "destinationCountryCode" => $order->delivery['country']['iso_code_2'],
                "weight" => $shipping_weight,
                'length' => $pkg_intl_length,
                'width' => $pkg_intl_width,
                'height' => $pkg_intl_height,
                "priceType" => strtoupper(MODULE_SHIPPING_USPSR_PRICING),
                "mailClass" => "ALL", // Do not change this. There is no "mailClasses" on the International API, so we have to pull all of them.
                'itemValue' => (MODULE_SHIPPING_USPSR_DISPATCH_CART_TOTAL == "Yes" ? $this->shipment_value : 5),
            ];

                        // Letter Request Body
            $ltr_body = [
                "weight" => $shipping_weight,
                "length" => $ltr_intl_length,
                "height" => $ltr_intl_height,
                "thickness" => $ltr_intl_thickness,
                "processingCategory" => strtoupper(MODULE_SHIPPING_USPSR_LTR_PROCESSING),
                "nonMachinableIndicators" =>
                [
                    "isPolybagged" => strpos(MODULE_SHIPPING_USPSR_LTR_MACHINEABLE_FLAGS, "Polybagged") !== false,
                    "hasClosureDevices" => strpos(MODULE_SHIPPING_USPSR_LTR_MACHINEABLE_FLAGS, "ClosureDevices") !== false,
                    "hasLooseItems" => strpos(MODULE_SHIPPING_USPSR_LTR_MACHINEABLE_FLAGS, "LooseItems") !== false,
                    "isRigid" => strpos(MODULE_SHIPPING_USPSR_LTR_MACHINEABLE_FLAGS, "Rigid") !== false,
                    "isSelfMailer" => strpos(MODULE_SHIPPING_USPSR_LTR_MACHINEABLE_FLAGS, "SelfMailer") !== false,
                    "isBooklet" => strpos(MODULE_SHIPPING_USPSR_LTR_MACHINEABLE_FLAGS, "Booklet") !== false,
                ],
                "itemValue" => (MODULE_SHIPPING_USPSR_DISPATCH_CART_TOTAL == "Yes" ? $this->shipment_value : 5),
                "destinationCountryCode" => $order->delivery['country']['iso_code_2'],
            ];

            // If the Pricing is Contract, add the Contract Type and AccountNumber
            if (MODULE_SHIPPING_USPSR_PRICING == 'Contract') {
                $pkg_body['accountType'] = MODULE_SHIPPING_USPSR_CONTRACT_TYPE;
                $pkg_body['accountNumber'] = MODULE_SHIPPING_USPSR_ACCT_NUMBER;
                $ltr_body['accountType'] = MODULE_SHIPPING_USPSR_CONTRACT_TYPE;
                $ltr_body['accountNumber'] = MODULE_SHIPPING_USPSR_ACCT_NUMBER;
            }


        // Send pkg_body to make pkgQuote.
        $this->pkgQuote = $this->_makeQuotesCall($pkg_body, 'package-intl');
        $this->ltrQuote = $this->_makeQuotesCall($ltr_body, 'letters-intl');

        $this->notify('NOTIFY_SHIPPING_USPS_INTL_DELIVERY_REQUEST_READY', [], $pkg_body, $ltr_body);
        
        }

        // If the Pricing is Contract, add the Contract Type and AccountNumber
        if (MODULE_SHIPPING_USPSR_PRICING == 'Contract') {
            $pkg_body['accountType'] = MODULE_SHIPPING_USPSR_CONTRACT_TYPE;
            $pkg_body['accountNumber'] = MODULE_SHIPPING_USPSR_ACCT_NUMBER;
            $ltr_body['accountType'] = MODULE_SHIPPING_USPSR_CONTRACT_TYPE;
            $ltr_body['accountNumber'] = MODULE_SHIPPING_USPSR_ACCT_NUMBER;
        }

        // Okay we have our request body ready.

        // Are we looking up the time frames? If not, don't send the request for Standards
        if (defined('MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT') && MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT !== 'No' && $this->is_us_shipment) {

            
            foreach (json_decode($this->_makeStandardsCall($standards_query), TRUE) as $item) {
                $this->uspsStandards[$item['mailClass']] = $item;
            }
            
            // Holdover observer, instead of modifiying the request, you'll modify the result. Use a DEBUG file to see what is available to modify.
            $this->notify('NOTIFY_SHIPPING_USPS_CUSTOM_TRANSIT_TIME', $this->uspsStandards);
            

        }

        // If there is a request for either version of letter, send that request.

    }

    protected function _makeStandardsCall($query)
    {
        global $request_type;

        /**
         * cURL Call to USPS server.
         *
         * There is only one server, the production, to reach.
         * We need to figure out are we calling the Domestic (US) or International API
         *
         * That will be handled by the $method parameter
         *
         */

        $paramsBuild = '';
        $paramsBuild = http_build_query($query);

        $ch = curl_init();
        $curl_options = [
            CURLOPT_URL => $this->api_base . 'service-standards/v3/estimates?' . $paramsBuild,
            CURLOPT_REFERER => ($request_type == 'SSL') ? (HTTPS_SERVER . DIR_WS_HTTPS_CATALOG) : (HTTP_SERVER . DIR_WS_CATALOG),
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $this->bearerToken],
            CURLOPT_VERBOSE => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_USERAGENT => 'ZenCart v' . PROJECT_VERSION_MAJOR . "." . PROJECT_VERSION_MINOR . " + USPSr Module " . MODULE_SHIPPING_USPSR_VERSION,
        ];

        if (CURL_PROXY_REQUIRED === 'True') {
            $curl_options[CURLOPT_HTTPPROXYTUNNEL] = !defined('CURL_PROXY_TUNNEL_FLAG') || strtoupper(CURL_PROXY_TUNNEL_FLAG) !== 'FALSE';
            $curl_options[CURLOPT_PROXYTYPE] = CURLPROXY_HTTP;
            $curl_options[CURLOPT_PROXY] = CURL_PROXY_SERVER_DETAILS;
        }
        curl_setopt_array($ch, $curl_options);

        // -----
        // Log the starting time of the to-be-sent USPS request.
        //
        $message = '';
        $message .= "\n" . '====================================================' . "\n";
        $message .= 'Sending Standards request to USPS' . "\n";
        $message .= "Standards Build: " . "\n";
        $message .= print_r($query, TRUE) . "\n";

        $this->uspsrDebug($message);

        // -----
        // Submit the request to USPS via CURL.
        //
        $body = curl_exec($ch);
        $this->commError = curl_error($ch);
        $this->commErrNo = curl_errno($ch);
        $this->commInfo = curl_getinfo($ch);

        // done with CURL, so close connection
        // Deprecated in PHP 8.5
        if (PHP_VERSION_ID < 80000) {
            curl_close($ch);
        }
        

        // -----
        // Log the CURL response (will also capture the time the response was received) to capture any
        // CURL-related errors and the shipping-methods being requested.  If a CURL error was returned,
        // no JSON is returned in the response (aka $body).
        //
        //if communication error, return -1 because no quotes were found, and user doesn't need to see the actual error message (set DEBUG mode to get the messages logged instead)
        if ($this->commErrNo != 0) {
            return -1;
        }
        // EOF CURL

        // -----
        // A valid JSON response was received from USPS, log the information to the debug-output file.
        //
        $this->quoteLogJSONResponse($body);

        return $body;
    }

    protected function quoteLogCurlBody($request)
    {
        global $order;

        if ($this->debug_enabled === false) {
            return;
        }

        // The response should be formatted in JSON.... so, should we pretty print that?
        $message =
            "\n" . '==================================' . "\n\n" .
            'REQUEST FROM STORE:' . "\n\n" . uspsr_pretty_json_print($request) . "\n\n";
        $message .= "\n" . '---------------------------------' . "\n";
        $message .= 'CommErr (should be 0): ' . $this->commErrNo . ' - ' . $this->commError . "\n\n";

        $message .= '==================================' . "\n\n" . 'USPS Country - $order->delivery[country][iso_code_2]: ' . $order->delivery['country']['iso_code_2'] . "\n";

        $this->uspsrDebug($message);
    }

    protected function quoteLogCurlResponse($request)
    {
        global $order;

        if ($this->debug_enabled === false) {
            return;
        }

        // The response should be formatted in JSON.... so, should we pretty print that?
        $message =
            "\n" . '==================================' . "\n\n" .
            'TOKEN RESPONSE FROM USPS:' . "\n\n" . uspsr_pretty_json_print($request) . "\n\n";
        $message .= "\n" . '---------------------------------' . "\n";
        $message .= 'CommErr (should be 0): ' . $this->commErrNo . ' - ' . $this->commError . "\n\n";

        $message .= '==================================' . "\n\n" . (isset($order) ? 'USPS Country - $order->delivery[country][iso_code_2]: ' . $order->delivery['country']['iso_code_2'] : '') . "\n";


        $this->uspsrDebug($message);
    }

    protected function quoteLogJSONResponse($response)
    {
        if ($this->debug_enabled === false) {
            return;
        }

        $message = "\n" . '==================================' . "\n";
        $message .= "RAW JSON FROM USPS:\n\n" . uspsr_pretty_json_print($response) . "\n\n";

        $this->uspsrDebug($message);
    }

    protected function getBearerToken()
    {
        global $request_type;

        $call_body = json_encode([
            "grant_type" => 'client_credentials',
            'client_id' => MODULE_SHIPPING_USPSR_API_KEY,
            'client_secret' => MODULE_SHIPPING_USPSR_API_SECRET,
            'scope' => 'domestic-prices addresses international-prices service-standards shipments'
        ]);

        $ch = curl_init();
        $curl_options = [
            CURLOPT_URL => $this->api_base . 'oauth2/v3/token',
            CURLOPT_REFERER => ($request_type == 'SSL') ? (HTTPS_SERVER . DIR_WS_HTTPS_CATALOG) : (HTTP_SERVER . DIR_WS_CATALOG),
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_VERBOSE => 0,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $call_body,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_USERAGENT => 'Zen Cart',
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json"
            )
        ];

        if (CURL_PROXY_REQUIRED === 'True') {
            $curl_options[CURLOPT_HTTPPROXYTUNNEL] = !defined('CURL_PROXY_TUNNEL_FLAG') || strtoupper(CURL_PROXY_TUNNEL_FLAG) !== 'FALSE';
            $curl_options[CURLOPT_PROXYTYPE] = CURLPROXY_HTTP;
            $curl_options[CURLOPT_PROXY] = CURL_PROXY_SERVER_DETAILS;
        }
        curl_setopt_array($ch, $curl_options);

        // -----
        // Log the starting time of the to-be-sent USPS request.
        //
        $message = '';
        $message .= "\n" . 'No token detected, requesting session token from USPS' . "\n";
        $message .= 'Token Request' . "\n" . uspsr_pretty_json_print($call_body) . "\n";

        $this->uspsrDebug($message);
        // -----
        // Submit the request to USPS via CURL.
        //
        $body = curl_exec($ch);
        $this->commError = curl_error($ch);
        $this->commErrNo = curl_errno($ch);
        $this->commInfo = curl_getinfo($ch);

        // done with CURL, so close connection
        // Deprecated in PHP 8.5
        if (PHP_VERSION_ID < 80000) {
            curl_close($ch);
        }
        

        // -----
        // Log the CURL response (will also capture the time the response was received) to capture any
        // CURL-related errors and the shipping-methods being requested.  If a CURL error was returned,
        // no JSON is returned in the response (aka $body).
        //
        $this->quoteLogCurlResponse($body);

        //if communication error, return -1 because no quotes were found, and user doesn't need to see the actual error message (set DEBUG mode to get the messages logged instead)
        if ($this->commErrNo != 0) {
            return -1;
        }
        // EOF CURL

        // Return JUST the token
        $body = json_decode($body, TRUE);

        if (is_array($body)) $_SESSION['usps_token'] = (array_key_exists('access_token', $body) ? $body['access_token'] : NULL);
        $this->bearerToken = $_SESSION['usps_token'] ?? NULL;
        return;
    }

    protected function _makeQuotesCall($call_body, $method)
    {
        global $request_type;

        $call_body = json_encode($call_body);
        /**
            * cURL Call to USPS server.
            *
            * We need to figure out are we calling the Domestic (US) or International API
            * That will be handled by the $method parameter
            */

        $usps_calls = [
            'package-domestic' => (string)$this->api_base . 'prices/v3/total-rates/search',
            'letters-domestic' => (string)$this->api_base . 'prices/v3/letter-rates/search',
            'package-intl' => (string)$this->api_base . 'international-prices/v3/total-rates/search',
            'letters-intl' => (string)$this->api_base . 'international-prices/v3/letter-rates/search',
        ];

        $ch = curl_init();
        $curl_options = [
            CURLOPT_URL => $usps_calls[$method],
            CURLOPT_REFERER => ($request_type == 'SSL') ? (HTTPS_SERVER . DIR_WS_HTTPS_CATALOG) : (HTTP_SERVER . DIR_WS_CATALOG),
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $this->bearerToken],
            CURLOPT_VERBOSE => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_USERAGENT => 'ZenCart v' . PROJECT_VERSION_MAJOR . "." . PROJECT_VERSION_MINOR . " + USPSr Module " . MODULE_SHIPPING_USPSR_VERSION,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $call_body
        ];

        if (CURL_PROXY_REQUIRED === 'True') {
            $curl_options[CURLOPT_HTTPPROXYTUNNEL] = !defined('CURL_PROXY_TUNNEL_FLAG') || strtoupper(CURL_PROXY_TUNNEL_FLAG) !== 'FALSE';
            $curl_options[CURLOPT_PROXYTYPE] = CURLPROXY_HTTP;
            $curl_options[CURLOPT_PROXY] = CURL_PROXY_SERVER_DETAILS;
        }
        curl_setopt_array($ch, $curl_options);

        // -----
        // Log the starting time of the to-be-sent USPS request.
        //
        $this->uspsrDebug('Sending ' . $method . ' request to USPS');

        // -----
        // Submit the request to USPS via CURL.
        //
        $body = curl_exec($ch);
        $this->commError = curl_error($ch);
        $this->commErrNo = curl_errno($ch);
        $this->commInfo = curl_getinfo($ch);

        // done with CURL, so close connection
        // Deprecated in PHP 8.5
        if (PHP_VERSION_ID < 80000) {
            curl_close($ch);
        }
        

        // -----
        // Log the CURL response (will also capture the time the response was received) to capture any
        // CURL-related errors and the shipping-methods being requested.  If a CURL error was returned,
        // no JSON is returned in the response (aka $body).
        //
        $this->quoteLogCurlBody($call_body);

        //if communication error, return -1 because no quotes were found, and user doesn't need to see the actual error message (set DEBUG mode to get the messages logged instead)
        if ($this->commErrNo != 0) {
            return -1;
        }
        // EOF CURL

        // -----
        // A valid JSON response was received from USPS, log the information to the debug-output file.
        //
        $this->quoteLogJSONResponse($body);
        $this->notify('NOTIFY_SHIPPING_USPS_QUOTES_RECEIVED');

        return $body;

    }

    /**
     * This order is going to iterate through the customer's cart, build up a total,
     * and remove any form of digital items (products_virtual)
     *
     * @return void
     */
    protected function _calcCart()
    {
        global $order, $uninsurable_value;

        $this->enable_media_mail = true;


        // From the original USPS Module
        // -----
        // If the order's tax-value isn't set (like when a quote is requested from
        // the shipping-estimator), set that value to 0 to prevent follow-on PHP
        // notices from this module's quote processing.

        $this->orders_tax = (!isset($order->info['tax'])) ? 0 : $order->info['tax'];
        $this->uninsured_value = (double)number_format((isset($uninsurable_value)) ? (float) $uninsurable_value : 0, 2);
        $this->shipment_value = (double)number_format(($order->info['subtotal'] > 0) ? ($order->info['subtotal'] + $this->orders_tax) : $_SESSION['cart']->total, 2);
        $this->insured_value = $this->shipment_value - $this->uninsured_value;

        // Breakout the category of exemptions for Media Mail
        $key_values = preg_split('/[\s+]/', MODULE_SHIPPING_USPSR_MEDIA_MAIL_EXCLUDE);

        // Iterate over all the items in the order. If an item is flagged as products_virtual, that means the whole order is excluded.
        // Additionally deduct the value of the non-shipped item from the shipment_value
        foreach ($order->products as $item) {
            if ($item['products_virtual'] === 1) {
                $this->shipment_value -= $item['final_price'];
                $this->uninsured_value += $item['final_price'];
            }

            if (in_array(zen_get_products_category_id($item['id']), $key_values)) {
                $this->enable_media_mail = false;
            }

        }

        if ($order->delivery['country']['iso_code_2'] !== 'US')
            $this->enable_media_mail = false;
    }

    protected function cleanJSON($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->cleanJSON($value);
            }
        } elseif (is_string($data)) {
            $data = trim($data);
        }

        return $data;
    }

    // Mimics the ScriptedInstallerBase updateConfigurationKey, but uses the normal zen_db_perform instead.
    protected function updateConfigurationKey($key_name, $value_array)
    {

        // Add the value array to the outgoing $sql_data_array
        $sql_data_array = $value_array;

        // Add the last updated value to be updated to now()
        $sql_data_array['last_modified'] = "now()";

        zen_db_perform(TABLE_CONFIGURATION, $sql_data_array, 'update', "configuration_key = '$key_name'");
        zen_record_admin_activity('Updated configuration record: ' . print_r($sql_data_array, true), 'warning');

    }

    // Mimics the ScriptedInstallerBase addConfigurationKey, but uses the normal zen_db_perform instead.
    protected function addConfigurationKey($key_name, $value_array)
    {

        // Add the value array to the outgoing $sql_data_array
        $sql_data_array = $value_array;
        $sql_data_array['configuration_key'] = $key_name;

        // Add the last updated value to be updated to now()
        $sql_data_array['last_modified'] = "now()";

        zen_db_perform(TABLE_CONFIGURATION, $sql_data_array);
        zen_record_admin_activity('Added configuration record: ' . print_r($sql_data_array, true), 'warning');
    }

    // Quick delete a config key, should be used sparingly.
    protected function deleteConfigurationKeys(array $key_names): int
    {
        if (empty($key_names)) {
            return 0;
        }

        global $db;
        $keys_list = implode("','", $key_names);

        $sql = "DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key IN ('" . $keys_list . "')";
        $db->Execute($sql);

        $rows = $db->affectedRows();
        zen_record_admin_activity('Deleted configuration record(s): ' . $keys_list . ", $rows rows affected.", 'warning');

        return $rows;
    }

}

// Compatibility for pre-ZC 1.5.8
if (!function_exists('zen_cfg_read_only')) {
    function zen_cfg_read_only($text, $key = '')
    {
        $name = (!empty($key)) ? 'configuration[' . $key . ']' : 'configuration_value';
        $text = htmlspecialchars_decode($text, ENT_COMPAT);

        return $text . zen_draw_hidden_field($name, $text);
    }
}
