<?php
/**
 * USPS Shipping (RESTful) for Zen Cart
 * Version 0.3.0
 *
 * @package shippingMethod
 * @copyright Portions Copyright 2004-2024 Zen Cart Team
 * @copyright Portions adapted from 2012 osCbyJetta
 * @author Paul Williams (retched)
 * @version $Id: uspsr.php 2025-02-07 retched Version 0.3.0 $
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
if (!defined('IS_ADMIN_FLAG')) {
    exit('Illegal Access');
}

// Start of USPSRestful
class uspsr extends base
{
    /**
     * Contains the unique identifier for this shipping-module; normally set to the module’s class name.
     * @var string
     */
    public $code;

    /**
     * Returns the icon for displaying into the webcart.
     * @var
     */
    public $icon;

    /**
     * Contains the title displayed for the shipping-module in both the Admin
     * Modules > Shipping and the storefront shipping quote. This variable
     * is normally initialized during class construction to a (shopfront)
     * language-file definition in the shipping module, e.g.
     * MODULE_SHIPPING_(module name)_TEXT_TITLE
     *
     * @var string
     */
    public $title;

    /**
     * Determines whether (true) or not (false) the module is enabled for use
     * during the current storefront checkout.
     *
     * @var bool
     */
    public $enabled;

    /**
     * A description of the module displayed only in the Admin, Modules >
     * Shipping. This variable is normally initialized during class
     * construction to a (shopfront) language-file definition in the
     * shipping module, e.g. MODULE_SHIPPING_(module name)_TEXT_DESCRIPTION
     *
     * @var string
     */
    public $description;

    /**
     * Identifies the tax_class_id associated with the tax to be applied to
     * this shipping module’s costs. If the value is 0, the shipping cost
     * is untaxed.
     *
     * @var integer
     */
    public $tax_class;

    /**
     * When the shipping cost is taxed, identifies the basis for the tax
     * computation: either Billing, Shipping or Store.
     *
     * @var string
     */
    public $tax_basis;

    /**
     * Sort order of display.
     *
     * @var int
     */
    public $sort_order = 0;

    /**
     * Flag to see if Debug mode is enabled, print error_logs where necessary.
     *
     * @var bool
     */
    protected $debug_enabled = FALSE;

    protected $typeCheckboxesSelected = [];

    /**
     * The name of the debug filename.
     */
    protected $debug_filename;

    /**
     * Summary of bearerToken
     * @var
     */
    protected $bearerToken;
    protected $quote_weight;
    protected $_check;
    protected $machinable;
    protected $shipment_value = 0;
    protected $insured_value = 0;
    protected $uninsured_value = 0;
    protected $orders_tax = 0;
    protected $is_us_shipment;
    protected $is_apo_dest = FALSE;
    protected $usps_countries;
    protected $enable_media_mail;

    /**
     * Variable to hold the weight standard of the site. If the site is running
     * ZC 1.5.7 and onward, there is a definition that will contain the unit.
     * Otherwise, assume pounds.
     *
     * @var string
     */
    protected $_standard;
    /**
     * Contains the array of methods being quoted.
     *
     * @var array
     */
    protected $quotes = [];
    protected $standards = [];
    protected $uspsQuote; //
    protected $uspsStandards;
    /**
     * Contains the array of "standards" (aka, estimated delivery)
     * @var array
     */

    protected $commError, $commErrNo, $commInfo;

    private const USPSR_CURRENT_VERSION = '0.3.0';

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

        // Should this line be removed?
        if ($this->sort_order === null) {
            return false;
        }

        $this->enabled = (MODULE_SHIPPING_USPSR_STATUS === 'True');

        $this->tax_class = (int)MODULE_SHIPPING_USPSR_TAX_CLASS;
        $this->tax_basis = MODULE_SHIPPING_USPSR_TAX_BASIS;

        // -----
        // Set debug-related variables for use by the uspsrDebug method.
        //
        $this->debug_enabled = (MODULE_SHIPPING_USPSR_DEBUG_MODE !== 'Off');
        $this->debug_filename = DIR_FS_LOGS . '/SHIP_uspsr_Debug_' . date('Ymd_His') . '.log';

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
        $this->notify('NOTIFY_SHIPPING_USPS_CHECK_CART', 'uspsr', $contents_ok);
        if ($contents_ok === false) {
            $this->enabled = false;
            return;
        }

        /**
         * use USPS translations for US shops (USPS treats certain regions as
         * "US States" instead of as different "countries", so we translate here)
         */
        $this->usps_countries = $this->usps_translation();

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


    public function quote()
    {
        global $order, $shipping_weight, $shipping_num_boxes, $currencies;

        // Make a token for the API
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

        /**
         * Determine machinable or not
         *
         * By definition, weight must be less than 35lbs and greater than 6 ounces or it is not machinable.
         * If all else fails, follow the module setting.
         */

        switch (true) {
            // force machinable for $0.49 remove the false && from the first case
            case (false && ($this->is_us_shipment === true && ($this->quote_weight <= 0.0625))):
                // override admin choice, too light. Once ounce is machinable.
                $this->machinable = 'Machinable';
                break;

            case ($this->is_us_shipment === true && ($this->quote_weight < 0.0625)):
                // override admin choice, too light, default regulation
                $this->machinable = 'Irregular';
                break;

            case (!$this->is_us_shipment === true && ($this->quote_weight < 0.21875)):
                // override admin choice, too light, More than one ounce but less than 3.5
                $this->machinable = 'Irregular';
                break;

            case ($this->quote_weight > 35):
                // override admin choice, too heavy, 35 lbs is the limit.
                $this->machinable = 'Irregular';
                break;

            default:
                // admin choice on what to use
                $this->machinable = MODULE_SHIPPING_USPSR_PROCESSING_CLASS;
                break;
        }

        // -----
        // Log, if enabled, the base USPS configuration for this quote request.
        //
        $this->_calcCart();
        $this->quoteLogConfiguration();

        // request quotes
        $this->notify('NOTIFY_SHIPPING_USPS_BEFORE_GETQUOTE', [], $order, $this->quote_weight, $shipping_num_boxes);

        // Create the main quote
        $this->_getQuote();

        // Okay let's start processing this.
        // Take the result of the CURL call and make it into an array.
        $uspsQuote = json_decode($this->uspsQuote, TRUE);

        // Let's clean up the quote to remove any extra spaces from the parts as necessary.
        // Looking at you, USPS Connect Local
        $uspsQuote = $this->cleanJSON($uspsQuote);


        $this->notify('NOTIFY_SHIPPING_USPS_AFTER_GETQUOTE', [], $order, $usps_shipping_weight, $shipping_num_boxes, $uspsQuote);


        if (zen_not_null($this->uspsStandards)) {
            $uspsStandards = json_decode($this->uspsStandards, TRUE);
        }

        // Go through each of the $this->typeCheckboxesSelected and build a list.
        $selected_methods = [];
        $build_quotes = [];
        for ($i = 0; $i <= count($this->typeCheckboxesSelected) - 1; $i++)
        {
            if (!is_numeric($this->typeCheckboxesSelected[$i]) && zen_not_null($this->typeCheckboxesSelected[$i])) {
                $selected_methods[] = ['method' => $this->typeCheckboxesSelected[$i], 'handling' => $this->typeCheckboxesSelected[$i+1]];
            }
        }


        $message =  '';
        $message .= "\n" . '===============================================' . "\n";
        $message .= 'Reviewing selected method options...' . "\n";
        $message .= print_r($selected_methods, TRUE);
        $this->uspsrDebug($message);

        /**
         * Okay we have a list of all the selected dots from the backend and the handling. (Having both
         * domestic AND international in the same array won't be a problem)
         *
         * Go through the result of the cURL call and pull out each method and assign it to the output array.
         *
         * The path to each rate is: rateOptions > X.
         *
         * Each X has:
         *  - a "totalBasePrice" and/or totalPrice (that's where the amount of the method comes from).
         *    totalPrice is defined only when there's options set, otherwise totalBasePrice is the main value of the method.
         *  - a "rates" "array" (it only has one sub-value), that "rates" contains an "X"
         *      - each X contains a description, a "productDefinition".
         *        We're going to filter the "description" to make the "Pretty Name" later. The "productDefinition" would contain the "service levels".
         *        (The estimates API is buggy and isn't working for some reason.)
         *
         */
        $message = '';
        $message .= "\n" . '===============================================' . "\n";
        $message .= 'Building options...' . "\n";
        $this->uspsrDebug($message);


        // Order Handling Costs
        if ($order->delivery['country']['id'] === SHIPPING_ORIGIN_COUNTRY || $this->is_us_shipment === true) {
            // domestic/national
            $usps_handling_fee = (double)MODULE_SHIPPING_USPSR_HANDLING_DOMESTIC;
        } else {
            // international
            $usps_handling_fee = (double)MODULE_SHIPPING_USPSR_HANDLING_INTL;
        }

        // -----
        // Give an observer the opportunity to modify the overall USPS handling fee for the order.
        //
        $this->notify('NOTIFY_SHIPPING_USPS_AFTER_HANDLING', [], $order, $shipping_weight, $shipping_num_boxes, $usps_handling_fee);

        // Order/Box Handling Cost Calculation
        // Okay go through each quote and add in the appropriate amount for the handling.

        // Are we applying the cost per box or the whole order?
        $handling_ext = $usps_handling_fee * (MODULE_SHIPPING_USPSR_HANDLING_METHOD === 'Box' ? $shipping_num_boxes : 1);

        if (isset($uspsQuote['rateOptions'])) {

            // I am 99% sure there is probably a more efficient way to do this...
            foreach ($uspsQuote['rateOptions'] as $rate) {
                // Does the description match an option from the $selected_method?

                $m = 0; // Index for making the quote id's with
                foreach($selected_methods as $method)
                {
                    $match = FALSE;
                    $quote = []; //Temp holder, if overriden, this gets skipped.
                    $method_to_add = TRUE;

                    // Plainly, the final quote price is the quote + handling + order handling. Return that.
                    $price = (isset($rate['totalPrice']) ? (double)$rate['totalPrice'] : (double)$rate['totalBasePrice'] ) + (double)$method['handling'] + (double)$handling_ext;

                    // If this package is NOT going to an APO/FPO/DPO, skip and continue to the next
                    // Currently this is the only rate which has a different rate for APO/FPO/DPO rates.
                    if (!$this->is_apo_dest && ($method['method'] === 'Priority Mail Machinable Large Flat Rate Box APO/FPO/DPO')) continue;

                    // We found a match, add it to the potential quotes.
                    if (($method['method'] == $rate['rates'][0]['description'])) {

                        /**
                         * Each member of $build_quotes is made up of the follow core pieces
                         *
                         * 'id' : ZenCart Req -
                         */
                        $quotes = [
                            'id' => 'usps' . $m,
                            'title' => uspsr_filter_gibberish($rate['rates'][0]['description']),
                            'cost' => $price,
                            'mailClass' => $rate['rates'][0]['mailClass']
                        ];

                        $match = TRUE;

                    } elseif ($method['method'] == "Media Mail") {
                        // If the item is "Media Mail"... let's narrow it down.
                        if ($this->machinable == 'Machinable' && ($rate['rates'][0]['description'] == 'Media Mail Machinable Basic' || $rate['rates'][0]['description'] == 'Media Mail Machinable Single-piece')) {

                            $quotes = [
                                'id' => 'usps' . $m,
                                'title' => uspsr_filter_gibberish($rate['rates'][0]['description']),
                                'cost' => $price,
                                'mailClass' => $rate['rates'][0]['mailClass']
                            ];

                            $match = TRUE;


                        } elseif (MODULE_SHIPPING_USPSR_PROCESSING_CLASS == 'Irregular' && ($rate['rates'][0]['description'] == 'Media Mail Irregular Basic' || $rate['rates'][0]['description'] == 'Media Mail Irregular Single-piece')) {

                            $quotes = [
                                'id' => 'usps' . $m,
                                'title' => uspsr_filter_gibberish($rate['rates'][0]['description']),
                                'cost' => $price,
                                'mailClass' => $rate['rates'][0]['mailClass']
                            ];

                            $match = TRUE;

                        }
                    }
                    // Insurance is directly pulled from the quote and added, setting this to allow "extra" insurance by an observer.
                    // Maybe make it an extra field on the admin config? $extra_insurance in this case does NOT need the currency symbol, leave it off.
                    // Additionally, there is no need to make the extra insurance, just add it in to the cost.

                    $this->notify('NOTIFY_USPS_UPDATE_OR_DISALLOW_TYPE', $method['method'], $method_to_add, $quotes['title'], $quotes['cost']);

                    $message = '';
                    if ($match && $method_to_add && !empty($quotes)) {

                        // The Observer did not block this.. And we have something add, so add it to the main list.
                        $build_quotes[] = $quotes;

                        $message .= "\n" . 'Adding option : ' . $quotes['title'] . "\n";
                        $message .= 'Price From Quote : ' . (isset($rate['totalPrice']) ? $currencies->format((double)$rate['totalPrice']) : $currencies->format((double)$rate['totalBasePrice'] )) . " , Handling : " . $currencies->format((double)$method['handling']) . " , Order Handling : " . $currencies->format($handling_ext) . "\n";
                        $message .= 'Final Price (Quote + Handling + Order Handling) : ' . $currencies->format($price) . "\n";

                    } elseif (!$method_to_add) {
                        // The observer rejected/blocked this from being added.

                        $message .= 'The Observer class blocked the method "' . $quotes['title'] . '" from being added to the list. So it was set aside.';
                    }

                    if (zen_not_null($message)) $this->uspsrDebug($message);
                    $m++;
                }
            }

            // Go through each one of the the $build_quotes and tack on the transit time as needed.
            // @todo Can this be moved back into the main loop?
            if (isset($uspsStandards) && is_array($uspsStandards)) {
                foreach ($uspsStandards as $standard) {
                    foreach ($build_quotes as &$quote) { // Adding the & since we're modifying the original
                        if ($quote['mailClass'] === $standard['mailClass']) {
                            // we have a match...

                            // If this matches, pull the "days" off the JSON and attach it to the title.
                            if (MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT == "Estimate Delivery") {

                                // The format of 'scheduledDeliveryDateTime' is '2024-12-30T18:00:00'.
                                // Let's change that around to Y-m-D

                                $est_delivery = new DateTime($standard['delivery']['scheduledDeliveryDateTime']);
                                $est_delivery = $est_delivery->format(DATE_FORMAT);

                                $quote['title'] .= " [" . MODULE_SHIPPING_USPSR_TEXT_ESTIMATED_DELIVERY . " " . $est_delivery . "]";

                            } elseif (MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT == "Estimate Transit Time") { // MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT == "Estimate Transit Time"

                                // We only need the number of days from the JSON.
                                $quote['title'] .= " [" . MODULE_SHIPPING_USPSR_TEXT_ESTIMATED . " " . zen_uspsr_estimate_days($standard['serviceStandard']) .  "]";
                            } else {
                                // Don't show anything.
                            }
                        }
                    }
                }
            }

            // Okay we have our list of Build Quotes, so now... we need to sort pursurant to options
            switch (MODULE_SHIPPING_USPSR_QUOTE_SORT) {
                case 'Alphabetical':
                    usort($build_quotes, function ($a, $b) { return $a['title'] <=> $b['title']; });
                    break;
                case 'Price-LowToHigh':
                    usort($build_quotes, function ($a, $b) { return $a['cost'] <=> $b['cost']; });
                    break;
                case 'Price-HighToLow':
                    usort($build_quotes, function ($a, $b) { return $b['cost'] <=> $a['cost']; });
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
                if ($this->debug_enabled === false) {
                    $this->quotes = [
                        'id' => $this->code,
                        'icon' => zen_image($this->icon),
                        'methods' => [],
                        'module' => $this->title,
                        'error' => MODULE_SHIPPING_USPSR_TEXT_ERROR
                    ];
                } else {
                    $this->enabled = false;
                }

            }


        } else { // If there isn't a 'rateOptions' filed, that means we have an 'error' field. Output that along with an error message.

            // Only show this during debugging
            if ($this->debug_enabled === false) {
                $this->quotes = [
                    'id' => $this->code,
                    'icon' => zen_image($this->icon),
                    'methods' => [],
                    'module' => $this->title,
                    'error' => MODULE_SHIPPING_USPSR_TEXT_SERVER_ERROR . '<br><br><pre style="white-space: pre-wrap;word-wrap: break-word;">' . $uspsQuote['error']['message'] . "</pre>"
                ];
            } else {
                $this->enabled = false;
            }

            $this->notify('NOTIFY_SHIPPING_USPS_QUOTES_READY_TO_RETURN');

        }


        /**
         * Before ending and returning the completed list, let's invalidate this token.
         *
         * Granted tokens expire after eight hours and will be reissued on each call,
         * but no use in making an achilles heel out of things.
         *
         */

        $message = '';
        $message .= "\n" . '===============================================' . "\n";
        $message .= 'Revoking Bearer Token...' . "\n";
        $this->uspsrDebug($message);


        $this->revokeBearerToken();

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
        if (($is_text || $is_mediumtext || $is_blob) === FALSE ) {
            $db->Execute("ALTER TABLE " . TABLE_ORDERS . " MODIFY shipping_method varchar(255) NOT NULL DEFAULT ''");
        }

        $this->notify('NOTIFY_SHIPPING_USPS_CHECK');
        return $this->_check;
    }

    public function install()
    {
        global $db;

        // Build the options for the module.

        /**
         * Display the version number
         */
        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added)
             VALUES
                ('USPSr Version Date', 'MODULE_SHIPPING_USPSR_VERSION', '" . self::USPSR_CURRENT_VERSION . "', 'You have installed:', 6, 0, 'zen_cfg_select_option([\'" . self::USPSR_CURRENT_VERSION . "\'], ', now())"
        );

        /**
         * Toggle to enable USPS Shipping
         */
        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added)
             VALUES
                ('Enable USPS Shipping', 'MODULE_SHIPPING_USPSR_STATUS', 'True', 'Do you want to offer USPS shipping?', 6, 0, 'zen_cfg_select_option([\'True\', \'False\'], ', now())"
        );

        /**
         * Toggle to display the full or abbreviated name
         */
        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added)
             VALUES
                ('Full Name or Short Name', 'MODULE_SHIPPING_USPSR_TITLE_SIZE', 'Short', 'Do you want to use a Long or Short name for USPS shipping?', 6, 0, 'zen_cfg_select_option([\'Long\', \'Short\'], ', now())"
        );

        /**
         * API Credentials
         */
         $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function)
             VALUES
                ('Enter the USPS API Consumer Key', 'MODULE_SHIPPING_USPSR_API_KEY', 'NONE', 'Enter your USPS API Consumer Key assigned to the app dedicated for this website.<br><br><strong>NOTE:</strong> This is NOT the same as the WebTools USERID and is NOT your USPS.com account Username.', 6, 0, now(), 'zen_cfg_password_display')"
        );
        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function)
             VALUES
                ('Enter the USPS API Consumer Secret', 'MODULE_SHIPPING_USPSR_API_SECRET', 'NONE', 'Enter the USPS API Consumer Secret assigned to the app dedicated for this website.<br><br><strong>NOTE:</strong> This is NOT the same as the WebTools PASSWORD and is NOT your USPS.com account Password.', 6, 0, now(), 'zen_cfg_password_display')"
        );

        /**
         * Module Sort Order
         */

        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added)
             VALUES
                ('Quote Sort Order', 'MODULE_SHIPPING_USPSR_QUOTE_SORT', 'Price-LowToHigh', 'Sorts the returned quotes using the service name Alphanumerically or by Price. Unsorted will give the order provided by USPS.', 6, 0, 'zen_cfg_select_option([\'Unsorted\',\'Alphabetical\', \'Price-LowToHigh\', \'Price-HighToLow\'], ', now())"
        );

        /**
         * Handling Fees
         */
        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added)
             VALUES
                ('Overall Handling Fee - US', 'MODULE_SHIPPING_USPSR_HANDLING_DOMESTIC', '0', 'Domestic Handling fee for this shipping method.', 6, 0, now())"
        );
        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added)
             VALUES
                ('Overall Handling Fee - International', 'MODULE_SHIPPING_USPSR_HANDLING_INTL', '0', 'International Handling fee for this shipping method.', 6, 0, now())"
        );
        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added)
             VALUES
                ('Handling Per Order or Per Box', 'MODULE_SHIPPING_USPSR_HANDLING_METHOD', 'Box', 'Do you want to charge Handling Fee Per Order or Per Box?', 6, 0, 'zen_cfg_select_option([\'Order\', \'Box\'], ', now())"
        );

        /**
         * Sales Tax Handling
         */
        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added)
             VALUES
                ('Tax Class', 'MODULE_SHIPPING_USPSR_TAX_CLASS', '0', 'Use the following tax class on the shipping fee.', 6, 0, 'zen_get_tax_class_title', 'zen_cfg_pull_down_tax_classes(', now())"
        );
        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added)
             VALUES
                ('Tax Basis', 'MODULE_SHIPPING_USPSR_TAX_BASIS', 'Shipping', 'On what basis is Shipping Tax calculated. Options are<br>Shipping - Based on customers Shipping Address<br>Billing Based on customers Billing address<br>Store - Based on Store address if Billing/Shipping Zone equals Store zone', 6, 0, 'zen_cfg_select_option([\'Shipping\', \'Billing\', \'Store\'], ', now())"
        );

        /**
         * Only allow this Zone to use USPS
         */
        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added)
             VALUES
                ('Shipping Zone', 'MODULE_SHIPPING_USPSR_ZONE', '0', 'If a zone is selected, only enable this shipping method for that zone.', 6, 0, 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())"
        );


        /**
         * Machineability Options
         */
        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added)
             VALUES
                ('Typical Package Processing Class', 'MODULE_SHIPPING_USPSR_PROCESSING_CLASS', 'Machinable', 'Are your packages typically machinable?<br><br>\"Machinable\" means a mail piece that is designed and sized to be processed by automated postal equipment. Typically this is mail that is rigid, fits a certain shape, and is within a certain weight (roughly at least 6 ounces but no more than 35 pounds). If your normal packages are within these guidelines, set this flag to \"Machinable\". Otherwise, set this to \"Irregular\". (If your customer order\'s total weight falls outside of this limit, regardless of the setting, the module will set the package to \"Irregular\".)', 6, 0, 'zen_cfg_select_option([\'Machinable\', \'Irregular\'], ', now())"
        );

        /**
         * Transit time display
         */
        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added)
             VALUES
                ('Display Transit Time', 'MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT', 'No', 'Would you like to display an estimated delivery date (ex. \"est. delivery: 12/25/2025\") or estimate delivery time (ex. \"est. 2 days\") for the service? This is pulled from the service guarantees listed by the USPS. If the service doesn\'t have a set guideline, no time quote will be displayed. Only applies to US based deliveries.', 6, 0, 'zen_cfg_select_option([\'No\', \'Estimate Delivery\', \'Estimate Transit Time\'], ', now())"
        );

        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added)
             VALUES
                ('Handling Time', 'MODULE_SHIPPING_USPSR_HANDLING_TIME', '1', 'In whole numbers, how many days does it take for you to dispatch your packages to the USPS. (Enter as a whole number only. This will be added to the estimated delivery date or time.)', 6, 0, 'zen_cfg_uspsr_numericupdown(', 'zen_uspsr_estimate_days', now())"
        );

        /**
         * Package Dimmensions
         * The Small Flat Rate Box is 8-5/8" x 5-3/8" x 1-5/8". That's the minimum.
         * These two rows control the same functionality, but only one will be inserted.
         *
         * @todo Figure out how the new ZenCart uses the product length, width, and height fields.
         */

         if(defined('SHIPPING_DIMENSION_UNITS') && SHIPPING_DIMENSION_UNITS == "centimeters") {
            $db->Execute(
                "INSERT INTO " . TABLE_CONFIGURATION . "
                    (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added)
                 VALUES
                    ('Typical Package Dimmensions (Domestic and International)',  'MODULE_SHIPPING_USPSR_DIMMENSIONS',  '21.9075, 21.9075, 13.6525, 13.6525, 4.1275, 4.1275', 'The Minimum Length, Width and Height are used to determine shipping methods available for International Shipping.<br><br>While dimensions are not supported at this time, the Minimums are sent to USPS for obtaining Rate Quotes.<br><br>In most cases, these Minimums should never have to be changed.<br>These measurements will be converted to inches.<br>', 6, 0, 'zen_cfg_uspsr_showdimmensions', 'zen_cfg_uspsr_dimmensions(', now())"
            );
         } else {
            $db->Execute(
                "INSERT INTO " . TABLE_CONFIGURATION . "
                    (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added)
                 VALUES
                    ('Typical Package Dimmensions (Domestic and International)',  'MODULE_SHIPPING_USPSR_DIMMENSIONS',  '8.625, 8.625, 5.375, 5.375, 1.625, 1.625', 'The Minimum Length, Width and Height are used to determine shipping methods available for International Shipping.<br><br>While dimensions are not supported at this time, the Minimums are sent to USPS for obtaining Rate Quotes.<br><br>In most cases, these Minimums should never have to be changed.<br>These measurements should be in inches.<br>', 6, 0, 'zen_cfg_uspsr_showdimmensions', 'zen_cfg_uspsr_dimmensions(', now())"
            );
         }


        /**
         * Shipping Methods
         */
        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added)
             VALUES
                ('Shipping Methods (Domestic and International)',  'MODULE_SHIPPING_USPSR_TYPES', '0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00', '<b>Checkbox:</b> Select the services to be offered<br><b>Handling:</b> A handling charge for that particular method (will be added on to the quote plus any services applicable).<br><br>USPS returns methods based on cart weights.', 6, 0, 'zen_cfg_uspsr_showservices', 'zen_cfg_uspsr_services([\'USPS Ground Advantage Machinable Single-piece\', \'USPS Ground Advantage Machinable Cubic Non-Soft Pack Tier 1\', \'Media Mail\', \'Connect Local Machinable DDU\', \'Connect Local Machinable DDU Flat Rate Box\', \'Connect Local Machinable DDU Small Flat Rate Bag\', \'Connect Local Machinable DDU Large Flat Rate Bag\', \'Priority Mail Machinable Single-piece\', \'Priority Mail Machinable Cubic Non-Soft Pack Tier 1\', \'Priority Mail Flat Rate Envelope\', \'Priority Mail Padded Flat Rate Envelope\', \'Priority Mail Legal Flat Rate Envelope\', \'Priority Mail Machinable Small Flat Rate Box\', \'Priority Mail Machinable Medium Flat Rate Box\', \'Priority Mail Machinable Large Flat Rate Box\', \'Priority Mail Machinable Large Flat Rate Box APO/FPO/DPO\', \'Priority Mail Express Machinable Single-piece\', \'Priority Mail Express Flat Rate Envelope\', \'Priority Mail Express Padded Flat Rate Envelope\', \'Priority Mail Express Legal Flat Rate Envelope\', \'First-Class Package International Service Machinable ISC Single-piece\', \'Priority Mail International Machinable ISC Single-piece\', \'Priority Mail International ISC Flat Rate Envelope\', \'Priority Mail International Machinable ISC Padded Flat Rate Envelope\', \'Priority Mail International ISC Legal Flat Rate Envelope\', \'Priority Mail International Machinable ISC Small Flat Rate Box\', \'Priority Mail International Machinable ISC Medium Flat Rate Box\', \'Priority Mail International Machinable ISC Large Flat Rate Box\', \'Priority Mail Express International ISC Single-piece\', \'Priority Mail Express International ISC Flat Rate Envelope\', \'Priority Mail Express International ISC Legal Flat Rate Envelope\', \'Priority Mail Express International ISC Padded Flat Rate Envelope\'], ', now())"
        );


        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function)
             VALUES
                ('Categories to Exclude for Media Mail', 'MODULE_SHIPPING_USPSR_MEDIA_MAIL_EXCLUDE', '', 'Enter the Category ID of the categories (separated by commas, white space is OK) that fail Media Mail Standards.<br><br>During checkout, if a product matches a category listed here, it will cause that entire order to be disqualified from Media Mail.<br>', 6, 0, now(), 'uspsr_get_categories')"
        );

        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function)
             VALUES
                ('Zip Codes Allowed for USPS Connect Local', 'MODULE_SHIPPING_USPSR_CONNECT_LOCAL_ZIP', '', 'Enter the list of zip codes (only the five digit part, separated by commas) of the zip codes that can be offered any of the USPS Connect Local options.', 6, 0, now(), 'uspsr_get_connect_zipcodes')"
        );

        /**
         * Shipping Add-ons
         */
        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added)
            VALUES
                ('Shipping Add-ons (Domestic)', 'MODULE_SHIPPING_USPSR_DMST_SERVICES', '', 'Pick which add-ons you wish to add on to the shipping cost quote. CAUTION: Not all options apply to all services. (The USPS API will do the math as necessary.)<br>', 6, 0, 'zen_cfg_uspsr_extraservices(\'domestic\', ', 'zen_cfg_uspsr_extraservices_display', now())"
        );

        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added)
            VALUES
                ('Shipping Add-ons (International)', 'MODULE_SHIPPING_USPSR_INTL_SERVICES', '', 'Pick which add-ons you wish to add on to the shipping cost quote. CAUTION: Not all options apply to all services. (The USPS API will do the math as necessary.)<br>', 6, 0, 'zen_cfg_uspsr_extraservices(\'international\', ', 'zen_cfg_uspsr_extraservices_display', now())"
        );

        /**
         * Pricing Levels
         */
        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added)
             VALUES
                ('Which pricing level to use?', 'MODULE_SHIPPING_USPSR_PRICING', 'Retail', 'What pricing level do you want to display to the customer during the quote?<br><br><em>Retail</em> - This is the price as if you went to the counter at the post office and bought the postage for your package.<br><em>Commercial</em> - This is the price if you\'re buying label online via an authorized USPS reseller or through Click-N-Ship on a Business account.<br><em>Contract</em> - If you have a negotiated service agreement with the USPS, select Contract. Then be sure to specify what kind of contract and the number of the contract you have in the options below.', 6, 0, 'zen_cfg_select_option([\'Retail\', \'Commercial\', \'Contract\'], ', now())"
        );

        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added)
             VALUES
                ('NSA Contract Type', 'MODULE_SHIPPING_USPSR_CONTRACT_TYPE', 'None', 'What kind of payment account do you have with the US Postal Service?<br><br><em>EPS</em> - Enterprise Payment System<br><em>Permit</em> - If you have a Mailing Permit whcih would entitle you a special discount on postage pricing, choose this option.<br><em>Meter</em> - If you have a licensed postage meter that grants you a special discount with the USPS, choose this option.', 6, 0, 'zen_cfg_select_option([\'None\', \'EPS\', \'Permit\', \'Meter\'], ', now())"
        );

        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added)
             VALUES
                ('USPS Account Number', 'MODULE_SHIPPING_USPSR_ACCT_NUMBER', '', 'What is the associated EPS Account Number, Meter Number, or NSA Contract Number you have with the United States Postal Service. (Leave blank if none.)', 6, 0, '', now())"
        );

        /**
         * Debug Logging Options
         */
        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added)
             VALUES
                ('Debug Mode', 'MODULE_SHIPPING_USPSR_DEBUG_MODE', 'Off', 'Would you like to enable debug mode?  If set to <em>Logs</em>, a file will be written to the store\'s /logs directory on each USPS request.', 6, 0, 'zen_cfg_select_option([\'Off\', \'Logs\'], ', now())"
        );

        /**
         * Sort Order
         */
        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added)
             VALUES
                ('Sort Order', 'MODULE_SHIPPING_USPSR_SORT_ORDER', '0', 'Sort order of the modules display. <small>(where do you want this to be place along the other shipping modules)</small>', 6, 0, now())"
        );

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
            'MODULE_SHIPPING_USPSR_PROCESSING_CLASS',
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
        if ($this->debug_enabled === true) {
            error_log(date('Y-m-d H:i:s') . ': ' . $message . PHP_EOL, 3, $this->debug_filename);
        }
    }

    protected function quoteLogConfiguration()
    {
        global $order, $currencies;

        if ($this->debug_enabled === false) {
            return;
        }

        /**
         * Pull the LWH values from the database..
         */
        $dimmensions = array_filter(explode(', ', MODULE_SHIPPING_USPSR_DIMMENSIONS));
        array_walk($dimmensions, function(&$value) { $value = trim($value);}); // Quickly remove white space

        $domm_length = (double)$dimmensions[0];
        $intl_length = (double)$dimmensions[1];

        $domm_width = (double)$dimmensions[2];
        $intl_width = (double)$dimmensions[3];

        $domm_height = (double)$dimmensions[4];
        $intl_height = (double)$dimmensions[5];

        // "Decrypt" the weights and ounces
        $pounds = floor($this->quote_weight);
        $ounces = ($this->quote_weight - $pounds) * 16;

        $message = '' . "\n\n";
        $message .= "USPSRestful Configuration Report\n";
        $message .= "=========================================================\n";
        $message .= 'USPSr build: ' . MODULE_SHIPPING_USPSR_VERSION . "\n\n";
        $message .= 'Quote Request Rate Type: ' . MODULE_SHIPPING_USPSR_PRICING . "\n";
        $message .= 'Quote from main_page: ' . $_GET['main_page'] . "\n";
        $message .= 'Display Transit Time: ' . MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT . "\n";

        $message .= 'Site Weights Based in: ' . SHIPPING_WEIGHT_UNITS . ' (' . (SHIPPING_WEIGHT_UNITS == 'lbs' ? 'Pounds' : 'Kilograms') . ')' . "\n";
        $message .= 'Site Measurements Based in: ' . (defined('SHIPPING_DIMENSION_UNITS') ? ucwords(SHIPPING_DIMENSION_UNITS) : "Inches" ) . "\n";
        $message .= 'Shipping ZIP Code Origin: ' . SHIPPING_ORIGIN_ZIP . "\n\n";

        $message .= 'Cart Weight: ' . $_SESSION['cart']->weight . " " . SHIPPING_WEIGHT_UNITS . (SHIPPING_WEIGHT_UNITS == 'kgs' ? " (" . $_SESSION['cart']->weight * 0.453592 . " lbs)" : '' ) . "\n";
        $message .= 'Total Quote Weight: ' . $this->quote_weight . ' lbs. , Pounds: ' . $pounds . ', Ounces: ' . $ounces . "\n";
        $message .= 'Maximum: ' . SHIPPING_MAX_WEIGHT . ' Tare Rates: Small/Medium: ' . SHIPPING_BOX_WEIGHT . ' Large: ' . SHIPPING_BOX_PADDING . "\n";
        $message .= 'Order Handling method: ' . MODULE_SHIPPING_USPSR_HANDLING_METHOD . ', Handling fee Domestic (Order): ' . $currencies->format(MODULE_SHIPPING_USPSR_HANDLING_DOMESTIC) . ', Handling fee International (Order): ' . $currencies->format(MODULE_SHIPPING_USPSR_HANDLING_INTL) . "\n";

        $message .= "\n" . 'Services Selected: ' . "\n". strip_tags(zen_cfg_uspsr_showservices(MODULE_SHIPPING_USPSR_TYPES)) . "\n";
        $message .= "Categories Excluded from Media Mail: " . strip_tags(uspsr_get_categories(MODULE_SHIPPING_USPSR_MEDIA_MAIL_EXCLUDE)) . "\n";
        $message .= "Zip Codes Allowed for USPS Connect : " . uspsr_get_connect_zipcodes(MODULE_SHIPPING_USPSR_CONNECT_LOCAL_ZIP) . "\n";

        $message .= 'Add-Ons Enabled (Domestic): ' . strip_tags(zen_cfg_uspsr_extraservices_display(MODULE_SHIPPING_USPSR_DMST_SERVICES)) . "\n";
        $message .= 'Add-Ons Enabled (International): ' . strip_tags(zen_cfg_uspsr_extraservices_display(MODULE_SHIPPING_USPSR_INTL_SERVICES)) . "\n\n";

        $message .= 'Assumed Domestic Package Size - Length: ' . $domm_length . ', Width: ' . $domm_width . ', Height: ' . $domm_height . "\n";
        $message .= 'Assumed International Package Size - Length: ' . $intl_length . ', Width: ' . $intl_width . ', Height: ' . $intl_height . "\n";

        $message .= 'Are Packages are Machinable? : ' . $this->machinable . "\n";
        $message .= 'Sort the returned quotes by: ' . MODULE_SHIPPING_USPSR_QUOTE_SORT . "\n\n";
        $message .= 'Order is eligible for Media Mail ? ' . ($this->enable_media_mail ? 'YES' : 'NO') . "\n";

        $message .= 'Order SubTotal: ' . $currencies->format($order->info['subtotal']) . "\n";
        $message .= 'Order Total: ' . $currencies->format($order->info['total']) . "\n";
        $message .= 'Uninsurable Portion: ' . $currencies->format($this->uninsured_value) . "\n";
        $message .= 'Insurable Value: ' . $currencies->format($this->shipment_value) . "\n";

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

            foreach ($check->fields['zone_id'] as $zone) {
                if ($zone < 1 || $zone === $order->delivery['zone_id']) $check_flag = true;
            }

            if ($check_flag == false) {
                $this->enabled = false;
            }
        }

        $this->notify('NOTIFY_SHIPPING_USPS_UPDATE_STATUS');
    }

    protected function adminInitializationChecks()
    {
        global $db, $messageStack;

        if ($this->debug_enabled === true) {
            $this->title .=  '<span class="alert"> (Debug is ON: ' . MODULE_SHIPPING_USPSR_DEBUG_MODE . ')</span>';
        }

        // -----
        // If still enabled, check to make sure that at least one shipping-method has been chosen (otherwise,
        // no quotes can be returned on the storefront.  If the condition is found, indicate that the module
        // is disabled so that the amber warning symbol appears in the admin shipping-modules' listing.
        //
        if ($this->enabled === true) {
            $this->checkConfiguration();
        }
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
        if ( ((strtolower(MODULE_SHIPPING_USPSR_API_KEY) != 'none') && (strtolower(MODULE_SHIPPING_USPSR_API_KEY)) != 'none' ) && !zen_not_null($this->bearerToken)) {
            $this->enabled = false;
            if (IS_ADMIN_FLAG === true) {
                $messageStack->add_session(MODULE_SHIPPING_USPSR_ERROR_REJECTED_CREDENTIALS, 'error');
            }

        } else {
            // Revoke the test token
            $this->revokeBearerToken();
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
        $dimmensions = array_filter(explode(', ', MODULE_SHIPPING_USPSR_DIMMENSIONS));
        array_walk($dimmensions, function(&$value) { $value = trim($value);}); // Quickly remove white space

        // Check if the measurement setting exists and if it does, check that it's in inches.
        // If it doesn't or if it is set to inches, do nothing.
        if (defined('SHIPPING_DIMENSION_UNITS') && SHIPPING_DIMENSION_UNITS !== "inches") {
            foreach ($dimmensions as &$dimmension) {
                $dimmension = (double)$dimmension / 2.54;
            }
        }

        // The order won't matter as the USPS considers the biggest measurement to the length, then the width, then the height.
        $domm_length = max((double)$dimmensions[0], 8.625);
        $intl_length = max((double)$dimmensions[1], 8.625);

        $domm_width = max((double)$dimmensions[2], 5.375);
        $intl_width = max((double)$dimmensions[3], 5.375);

        $domm_height = max((double)$dimmensions[4], 1.625);
        $intl_height = max((double)$dimmensions[5], 1.625);

        $services_dmst = array_filter(explode(', ', MODULE_SHIPPING_USPSR_DMST_SERVICES));

        /**
         * If 930 is in the array, add 931. The API will intelligently pull the
         * appropriate value. That is if the total cart value is less than
         * $500, code 930 (Insurance <= $500) applies. If more than or equal
         * to $500, code 931 applies (Insurance > $500).
         */
        if (in_array(930, $services_dmst)) {
            $services_dmst[] = 931;
        }

        // Make sure that we only have numbers in the array
        foreach ($services_dmst as &$service) {
            $service = (int)$service;
        }

        $services_intl = array_filter(explode(', ', MODULE_SHIPPING_USPSR_INTL_SERVICES));
        if (in_array(930, $services_intl)) {
            $services_intl[] = 931;
        }

        // Make sure that we only have numbers in the array
        foreach ($services_intl as &$service) {
            $service = (int)$service;
        }

        /**
         * Build the JSON Call to the server
         */
        $json_body = [];

        // Prepare a Standards Query
        $standards_query = [];
        if ($this->usps_countries == 'US') {
            // Set focus to the Domestic API
            $focus = "rates-domestic";

            // There are only three classes needed: Ground Advantage, Priority Mail, Priority Mail Express
            $mailClasses = [
                "USPS_GROUND_ADVANTAGE",
                "PRIORITY_MAIL",
                "PRIORITY_MAIL_EXPRESS"
            ];

            /**
             * Is this package going to a APO/FPO/DPO?
             */
            $this->is_apo_dest = in_array(uspsr_validate_zipcode($order->delivery['postcode'] ?? '00000'), self::USPSR_MILITARY_MAIL_ZIP);

            /**
             * Check to see if the products in the cart are ALL eligible for USPS Media Mail.
             */
            if ($this->enable_media_mail) { $mailClasses[] = "MEDIA_MAIL"; }

            // Check to see if the order fits for USPS Connect Local
            if (uspsr_check_connect_local($order->delivery['postcode'] ?? '00000')) $mailClasses[] = "USPS_CONNECT_LOCAL";

            $json_body = [
                'originZIPCode' => uspsr_validate_zipcode(SHIPPING_ORIGIN_ZIP),
                'destinationZIPCode' => uspsr_validate_zipcode($order->delivery['postcode'] ?? '00000'),
                'weight' => $shipping_weight,
                'length' => $domm_length,
                'width' => $domm_width,
                'height' => $domm_height,
                'mailClasses' => $mailClasses,
                'priceType' => strtoupper(MODULE_SHIPPING_USPSR_PRICING),
                'extraServices' => $services_dmst,
                'itemValue' => $this->shipment_value,
            ];

            // Let's make a standards request now.
            $standards_query = [
                'originZIPCode' => uspsr_validate_zipcode(SHIPPING_ORIGIN_ZIP),
                'destinationZIPCode' => uspsr_validate_zipcode($order->delivery['postcode'] ?? '00000'),
                'mailClass' => 'ALL',
                'weight' => $shipping_weight
            ];

            $todays_date = new DateTime();
            $daystoadd = (int)MODULE_SHIPPING_USPSR_HANDLING_TIME;

            $todays_date_plus = $todays_date->modify("+{$daystoadd} days");
            $standards_query['acceptanceDate'] = $todays_date_plus->format('Y-m-d');
            $street_address = trim($order->delivery['street_address']  ?? '');

            // If the address contains "PO BOX" or "BOX" in the address line 1, that makes it a PO BOX.
            if (preg_match("/^(PO BOX|BOX)/i", $street_address)) {
                $standards_query['destinationType'] = "PO_BOX";
            } else {
                $standards_query['destinationType'] = "STREET";
            }

            $this->notify('NOTIFY_SHIPPING_USPS_US_DELIVERY_REQUEST_READY', [], $json_body);


        } else {
            // Set focus to the International API
            $focus = "rates-intl";

            $json_body = [
                "originZIPCode" => uspsr_validate_zipcode(SHIPPING_ORIGIN_ZIP),
                "foreignPostalCode" => $order->delivery['postcode'],
                "destinationCountryCode" => $order->delivery['country']['iso_code_2'],
                "weight" => $shipping_weight,
                'length' => $intl_length,
                'width' => $intl_width,
                'height' => $intl_height,
                "priceType" => strtoupper(MODULE_SHIPPING_USPSR_PRICING),
                "mailClass" => "ALL", // Do not change this. There is no "mailClasses" on the International API, so we have to pull all of them.
                'itemValue' => $this->shipment_value,
                "extraServices" => $services_intl,
            ];

            $this->notify('NOTIFY_SHIPPING_USPS_INTL_DELIVERY_REQUEST_READY', [], $json_body);

        }

            $todays_date = new DateTime();
            $daystoadd = (int)MODULE_SHIPPING_USPSR_HANDLING_TIME;

            $todays_date_plus = $todays_date->modify("+{$daystoadd} days");
            $json_body['mailingDate'] = $todays_date_plus->format('Y-m-d');

            // If the Pricing is Contract, add the Contract Type and AccountNumber
            if (MODULE_SHIPPING_USPSR_PRICING == 'Contract') {
                $json_body['accountType']   = MODULE_SHIPPING_USPSR_CONTRACT_TYPE;
                $json_body['accountNumber'] = MODULE_SHIPPING_USPSR_ACCT_NUMBER;
            }

            $request_data = json_encode($json_body);


        // Okay we have our request body ready.
        // Let's pull it.
        $this->uspsQuote = $this->_makeQuotesCall($request_data, $focus);

        // Are we looking up the time frames? If not, don't send the request for Standards
        if (defined('MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT') && MODULE_SHIPPING_USPSR_DISPLAY_TRANSIT !== 'No') {
            if (!empty($standards_query)) $this->uspsStandards = $this->_makeStandardsCall($standards_query);
        }

    }

    protected function _makeStandardsCall($query) {
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
            CURLOPT_URL => 'https://apis.usps.com/service-standards/v3/estimates?' . $paramsBuild,
            CURLOPT_REFERER => ($request_type == 'SSL') ? (HTTPS_SERVER . DIR_WS_HTTPS_CATALOG) : (HTTP_SERVER . DIR_WS_CATALOG),
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $this->bearerToken],
            CURLOPT_VERBOSE => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_USERAGENT => 'ZenCart',
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
        curl_close($ch);

        // -----
        // Log the CURL response (will also capture the time the response was received) to capture any
        // CURL-related errors and the shipping-methods being requested.  If a CURL error was returned,
        // no JSON is returned in the response (aka $body).
        //
        //$this->quoteLogParamsBody($query);

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
    protected function _makeQuotesCall($call_body, $method)
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



        $usps_calls = [
            'rates-domestic' => 'https://apis.usps.com/prices/v3/total-rates/search',
            'rates-intl' => 'https://apis.usps.com/international-prices/v3/total-rates/search',
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
            CURLOPT_USERAGENT => 'ZenCart',
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
        $this->uspsrDebug('Sending Services request to USPS');

        // -----
        // Submit the request to USPS via CURL.
        //
        $body = curl_exec($ch);
        $this->commError = curl_error($ch);
        $this->commErrNo = curl_errno($ch);
        $this->commInfo = curl_getinfo($ch);

        // done with CURL, so close connection
        curl_close($ch);

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
            CURLOPT_URL => 'https://apis.usps.com/oauth2/v3/token',
            CURLOPT_REFERER => ($request_type == 'SSL') ? (HTTPS_SERVER . DIR_WS_HTTPS_CATALOG) : (HTTP_SERVER . DIR_WS_CATALOG),
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_VERBOSE => 0,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials&client_id=' . MODULE_SHIPPING_USPSR_API_KEY . '&client_secret=' . MODULE_SHIPPING_USPSR_API_SECRET,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_USERAGENT => 'Zen Cart',
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded"
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
        $message .= "\n" . 'Requesting Token from USPS' . "\n";
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
        curl_close ($ch);

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

        $this->bearerToken = $body['access_token'];

        return;
    }
    protected function revokeBearerToken()
    {
        global $request_type;

        $call_body = json_encode([
            'client_id' => MODULE_SHIPPING_USPSR_API_KEY,
            'client_secret' => MODULE_SHIPPING_USPSR_API_SECRET,
            'revoke_token' => $this->bearerToken,
            "token_type" => 'access_token',
        ]);

        $ch = curl_init();
        $curl_options = [
            CURLOPT_URL => 'https://apis.usps.com/oauth2/v3/revoke',
            CURLOPT_REFERER => ($request_type == 'SSL') ? (HTTPS_SERVER . DIR_WS_HTTPS_CATALOG) : (HTTP_SERVER . DIR_WS_CATALOG),
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_VERBOSE => 0,
            CURLOPT_POST => 1,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => MODULE_SHIPPING_USPSR_API_KEY . ":" . MODULE_SHIPPING_USPSR_API_SECRET,
            CURLOPT_POSTFIELDS => 'token=' . $this->bearerToken . "&token_type=access_token",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_USERAGENT => 'Zen Cart',
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded"
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
        $message .= 'Revoking a Token from USPS' . "\n";
        $message .= 'Revocation Request' . "\n" . uspsr_pretty_json_print($call_body) . "\n";

        $this->uspsrDebug($message);
        // -----
        // Submit the request to USPS via CURL.
        //
        $body = curl_exec($ch);
        $this->commError = curl_error($ch);
        $this->commErrNo = curl_errno($ch);
        $this->commInfo = curl_getinfo($ch);

        // done with CURL, so close connection
        curl_close ($ch);

        // -----
        // Log the CURL response (will also capture the time the response was received) to capture any
        // CURL-related errors and the shipping-methods being requested.  If a CURL error was returned,
        // no JSON is returned in the response (aka $body).
        //
        // If there is a resonse here... something went wrong. Make a log of it, but don't break or anything.
        if (zen_not_null($body)) {
            $this->quoteLogCurlResponse($body);
        } else {
            $this->uspsrDebug('No response received from the USPS... assuming the token has been revoked.' . "\n");
        }


        //if communication error, return -1 because no quotes were found, and user doesn't need to see the actual error message (set DEBUG mode to get the messages logged instead)
        if ($this->commErrNo != 0) {
            return -1;
        }
        // EOF CURL

        // Return JUST the token
        $body = json_decode($body, TRUE);

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
        // @TODO
        // reduce order value by products not shipped (Done here)


        $this->orders_tax = (!isset($order->info['tax'])) ? 0 : $order->info['tax'];
        $this->uninsured_value = (isset($uninsurable_value)) ? (float)$uninsurable_value : 0;
        $this->shipment_value = ($order->info['subtotal'] > 0) ? ($order->info['subtotal'] + $this->orders_tax) : $_SESSION['cart']->total;
        $this->insured_value = $this->shipment_value - $this->uninsured_value;

        // Breakout the category of exemptions for Media Mail
        $key_values = preg_split('/[\s+]/', MODULE_SHIPPING_USPSR_MEDIA_MAIL_EXCLUDE);

        // Iterate over all the items in the order. If an item is flagged as products_virtual, that means the whole order is excluded.
        // Additionally deduct the value of the non-shipped item from the shipment_value
        foreach($order->products as $item) {
            if ($item['products_virtual'] === 1) {
                $this->shipment_value -= $item['final_price'];
                $this->uninsured_value += $item['final_price'];
            }

            if (in_array(zen_get_products_category_id($item['id']), $key_values)) { $this->enable_media_mail = false; }

        }

        if ($order->delivery['country']['iso_code_2'] !== 'US') $this->enable_media_mail = false;
    }

    protected function cleanJSON($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) { $data[$key] = $this->cleanJSON($value); }
        } elseif (is_string($data)) { $data = trim($data); }

        return $data;
    }
}

function zen_cfg_uspsr_dimmensions($key_value, $key='') {
    $key_values = array_filter(explode(', ', $key_value));
    array_walk($key_values, function(&$value) { $value = trim($value);}); // Quickly remove white space

    // Length
    $domm_length    = zen_draw_input_field('configuration[MODULE_SHIPPING_USPSR_DIMMENSIONS][]', $key_values[0], 'size="10" class="form-control" style="text-align: center;"');
    $intl_length    = zen_draw_input_field('configuration[MODULE_SHIPPING_USPSR_DIMMENSIONS][]', $key_values[1], 'size="10" class="form-control" style="text-align: center;"');

    // Width
    $domm_width     = zen_draw_input_field('configuration[MODULE_SHIPPING_USPSR_DIMMENSIONS][]', $key_values[2], 'size="10" class="form-control" style="text-align: center;"');
    $intl_width     = zen_draw_input_field('configuration[MODULE_SHIPPING_USPSR_DIMMENSIONS][]', $key_values[3], 'size="10" class="form-control" style="text-align: center;"');

    // Height
    $domm_height    = zen_draw_input_field('configuration[MODULE_SHIPPING_USPSR_DIMMENSIONS][]', $key_values[4], 'size="10" class="form-control" style="text-align: center;"');
    $intl_height    = zen_draw_input_field('configuration[MODULE_SHIPPING_USPSR_DIMMENSIONS][]', $key_values[5], 'size="10" class="form-control" style="text-align: center;"');


    $table=<<<EOF
    <style>
        .three-column {display: block;border-collapse: collapse;}
        .three-column-row {display:table-row;}
        .three-column-cell {display:table-cell;}
        .border-div {border-right: 1px #000 solid; padding:5px;}
        .align-center {text-align: center;}
    </style>
    <div class="three-column" style="width: 75%; margin: auto;">
        <div class="three-column-row">
            <div class="three-column-cell" style="width: 24%;">&nbsp;</div>
            <div class="three-column-cell border-div align-center" style="width: 38%;font-weight: bold;">Domestic</div>
            <div class="three-column-cell align-center" style="width: 38%;font-weight: bold;">International</div>
        </div>
        <div class="three-column-row">
            <div class="three-column-cell">Length</div>
            <div class="three-column-cell border-div align-center">$domm_length</div>
            <div class="three-column-cell align-center">$intl_length</div>
        </div>
        <div class="three-column-row">
            <div class="three-column-cell">Width</div>
            <div class="three-column-cell border-div align-center">$domm_width</div>
            <div class="three-column-cell align-center">$intl_width</div>
        </div>
        <div class="three-column-row">
            <div class="three-column-cell">Height</div>
            <div class="three-column-cell border-div align-center">$domm_height</div>
            <div class="three-column-cell align-center">$intl_height</div>
        </div>
    </div>
    EOF;

    return $table;
}

function zen_cfg_uspsr_services($select_array, $key_value, $key = '')
{
    $key_values = explode(', ', $key_value);
    array_walk($key_values, function(&$value) { $value = trim($value);}); // Quickly remove extra white space

    $name = ($key) ? ('configuration[' . $key . '][]') : 'configuration_value';

    $w20pxl = 'width:20px;float:left;text-align:center;';
    $w60pxl = 'width:60px;float:left;text-align:center;';
    $frc = 'float:right;text-align:center;';

    $string =
        '<b>' .
            '<div style="' . $w20pxl . '">&nbsp;</div>' .
            '<div style="float:left;"></div>' .
            '<div style="' . $frc . '">Handling</div>' .
        '</b>' .
        '<div style="clear:both;"></div>';
    $string_spacing = '<div><br><br><b>&nbsp;International Rates:</b><br></div>' . $string;
    $string_spacing_international = 0;
    $string = '<div><br><b>&nbsp;Domestic Rates:</b><br></div>' . $string;
    for ($i = 0, $n = count($select_array); $i < $n; $i++) {
        if (stripos($select_array[$i], 'international') !== false) {
            $string_spacing_international++;
        }
        if ($string_spacing_international === 1) {
            $string .= $string_spacing;
        }

        $string .= '<div id="' . $key . $i . '">';
        $string .=
            '<div style="' . $w20pxl . '">' .
                zen_draw_checkbox_field($name, $select_array[$i], (in_array($select_array[$i], $key_values) ? 'CHECKED' : '')) .
            '</div>';
        if (in_array($select_array[$i], $key_values)) {
            next($key_values);
        }

        $string .=
            '<div style="float:left;">&nbsp;&nbsp;&nbsp;' .
                trim(preg_replace(
                    [
                        '/International/',
                        '/Envelope/',
                        '/ Mail/',
                        '/Large/',
                        '/Medium/',
                        '/Small/',
                        '/First/',
                        '/Legal/',
                        '/Padded/',
                        '/Flat Rate/',
                        '/Express Guaranteed /',
                        '/Package\hService\h-\hRetail/',
                        '/Package Service/',
                        '/ISC/',
                        '/Machinable DDU/',
                        '/Machinable/',
                        '/(Basic|Single-Piece)/i',
                        '/USPS\s+/',
                        '/Non-Soft Pack Tier 1/',
                    ],
                    [
                        'Intl',
                        'Env',
                        '',
                        'Lg.',
                        'Md.',
                        'Sm.',
                        '1st',
                        'Leg.',
                        'Pad.',
                        'F/R',
                        'Exp Guar',
                        'Pkgs - Retail',
                        'Pkgs - Comm',
                        '',
                        '',
                        '',
                        '',
                        '',
                        ''
                    ],
                    $select_array[$i]
                )) .
            '</div>';
        $string .=
            '<div style="'. $frc . '">$' .
                zen_draw_input_field($name, current($key_values), 'size="4"') .
            '</div>';
        next($key_values);

        $string .= '<div style="clear:both;"></div></div>';
    }
    return $string;
}

function zen_cfg_uspsr_extraservices($destination, $key_value, $key = '')
{
    $key_values = array_filter(explode(', ', $key_value));
    array_walk($key_values, function(&$value) { $value = trim($value);}); // Quickly remove white space

    $name = ($key) ? ('configuration[' . $key . '][]') : 'configuration_value';

    $output_str = '';

    $focus = ($destination == 'domestic');

    // Establish a list of codes.
    // Format: (API Code) => ['Name of Service', Is this International Friendly (TRUE/FALSE)]
    $services = [
        910 => ['Certified Mail', FALSE],
        930 => ['Insurance', TRUE],
        925 => ['Priority Mail Express Merchandise Insurance', FALSE],
        923 => ['Adult Signature Restricted Delivery', FALSE],
        922 => ['Adult Signature Required', FALSE],
        940 => ['Registered Mail', FALSE],
        915 => ['Collect on Delivery', FALSE],
        955 => ['Return Receipt', FALSE],
        957 => ['Return Receipt Electronic', FALSE],
        921 => ['Signature Confirmation', FALSE],
        911 => ['Certified Mail Restricted Delivery', FALSE],
        912 => ['Certified Mail Adult Signature Required', FALSE],
        913 => ['Certified Mail Adult Signature Restricted Delivery', FALSE],
        917 => ['Collect on Delivery Restricted Delivery', FALSE],
        924 => ['Signature Confirmation Restricted Delivery', FALSE],
        941 => ['Registered Mail Restricted Delivery', FALSE],
        984 => ['Parcel Locker Delivery', FALSE],
        981 => ['Signature Requested (Priority Mail Express only)', FALSE],
        986 => ['PO to Addressee (Priority Mail Express only)', FALSE],
        991 => ['Sunday Delivery (Priority Mail + Priority Mail Express)', FALSE],
        934 => ['Insurance Restricted Delivery', FALSE],
        856 => ['Live Animal Transportation Fee', FALSE],
        857 => ['Hazardous Materials', TRUE],
    ];

    if ($focus) {
        // If this is a Domestic, search through
        foreach ($services as $code => $service) {
            $output_str .= zen_draw_checkbox_field($name, $code, (in_array($code, $key_values) ? TRUE : FALSE)) . "&nbsp;&nbsp;" . $service[0] . "<br>" . "\n";
        }
    } else {
        foreach ($services as $code => $service) {
            if ($service[1]) $output_str .= zen_draw_checkbox_field($name, $code, (in_array($code, $key_values) ? TRUE : FALSE)) . "&nbsp;&nbsp;" . $service[0] . "<br>" . "\n";
        }

    }


    return $output_str;
}

function zen_cfg_uspsr_extraservices_display($key_value)
{
    // Display the Values as a Comma-Separated List.

    $key_values = array_filter(explode(', ', $key_value));
    array_walk($key_values, function(&$value) { $value = trim($value);}); // Quickly remove white space

    $output = '';
    $services = [
        910 => 'Certified Mail',
        930 => 'Insurance',
        925 => 'Priority Mail Express Merchandise Insurance',
        923 => 'Adult Signature Restricted Delivery',
        922 => 'Adult Signature Required',
        940 => 'Registered Mail',
        915 => 'Collect on Delivery',
        955 => 'Return Receipt',
        957 => 'Return Receipt Electronic',
        921 => 'Signature Confirmation',
        911 => 'Certified Mail Restricted Delivery',
        912 => 'Certified Mail Adult Signature Required',
        913 => 'Certified Mail Adult Signature Restricted Delivery',
        917 => 'Collect on Delivery Restricted Delivery',
        924 => 'Signature Confirmation Restricted Delivery',
        941 => 'Registered Mail Restricted Delivery',
        984 => 'Parcel Locker Delivery',
        981 => 'Signature Requested (Priority Mail Express only)',
        986 => 'PO to Addressee (Priority Mail Express only)',
        991 => 'Sunday Delivery (Priority Mail + Priority Mail Express)',
        934 => 'Insurance Restricted Delivery',
        856 => 'Live Animal Transportation Fee',
        857 => 'Hazardous Materials',
    ];

    if (!empty($key_values)) {
        $end = end($key_values);
        foreach ($key_values as $service) {
            $output .= $services[$service] . ($service !== $end ? ", " : "" );
        }
    }
    if (!zen_not_null($output)) $output = '<em>- None -</em>';

    return $output;
}

function zen_cfg_uspsr_showservices($key_value)
{
    // Split up Key Value into an array, then go through that array and find the non-numeric values. That should be the name of a method.
    $key_values = array_filter(explode(', ', $key_value));

    $methods_dom = [];
    $methods_intl = [];

    $output_domestic = '';
    $output_intl = '';

    foreach ($key_values as $methods) {
        if (!is_numeric($methods)) {
            // This is a string, not a number. Check to see if the value contains the word International, otherwise, it's a domestic

            if (preg_match('/International/', $methods)) {
                $methods_intl[] = preg_replace(
                    [
                        '/International/',
                        '/Envelope/',
                        '/ Mail/',
                        '/Large/',
                        '/Medium/',
                        '/Small/',
                        '/First/',
                        '/Legal/',
                        '/Padded/',
                        '/Flat Rate/',
                        '/Express Guaranteed /',
                        '/Package\hService\h-\hRetail/',
                        '/Package Service/',
                        '/ISC/',
                        '/Machinable DDU/',
                        '/Machinable\s+/',
                        '/(Basic|Single-Piece)/i',
                        '/USPS\s+/',
                        '/Non-Soft Pack Tier 1/',
                        '/\s{2,}/'
                    ],
                    [
                        'Intl',
                        'Env',
                        '',
                        'Lg.',
                        'Md.',
                        'Sm.',
                        '1st',
                        'Leg.',
                        'Pad.',
                        'F/R',
                        'Exp Guar',
                        'Pkgs - Retail',
                        'Pkgs - Comm',
                        '',
                        '',
                        '',
                        ' ',
                        '',
                        '',
                        ' '
                    ],
                    $methods
                );
            }
            else {
                $methods_dom[] = preg_replace(
                    [
                        '/International/',
                        '/Envelope/',
                        '/ Mail/',
                        '/Large/',
                        '/Medium/',
                        '/Small/',
                        '/First/',
                        '/Legal/',
                        '/Padded/',
                        '/Flat Rate/',
                        '/Express Guaranteed /',
                        '/Package\hService\h-\hRetail/',
                        '/Package Service/',
                        '/ISC/',
                        '/Machinable DDU\s+/',
                        '/Machinable\s+/',
                        '/(Basic|Single-Piece)/i',
                        '/USPS\s+/',
                        '/Non-Soft Pack Tier 1/',
                    ],
                    [
                        'Intl',
                        'Env',
                        '',
                        'Lg.',
                        'Md.',
                        'Sm.',
                        '1st',
                        'Leg.',
                        'Pad.',
                        'F/R',
                        'Exp Guar',
                        'Pkgs - Retail',
                        'Pkgs - Comm',
                        '',
                        '',
                        '',
                        '',
                        '',
                        ''
                    ],
                    $methods
                );
            }
        }
    }

    foreach ($methods_dom as $method) {
        $output_domestic .= trim($method) . ($method == end($methods_dom) ? '' : ', ');
    }

    foreach ($methods_intl as $method) {
        $output_intl .= trim($method) . ($method == end($methods_intl) ? '' : ', ');
    }

    $output = "<b>Domestic Methods:</b><br> " . (zen_not_null($output_domestic) ? $output_domestic : '<em>- None -</em>') . "<br><br>\n" . "<b>International Methods</b>: <br>" . (zen_not_null($output_intl) ? $output_intl : '<em>- None -</em>');

    return $output . "\n";
}

function zen_cfg_uspsr_showdimmensions($key_value)
{
    $key_values = explode(', ', $key_value);
    $key_values = array_filter($key_values, function($value) { if(zen_not_null($value)) {return "<em>- NONE -</em>";} });

    // Domestic Measures are 0 x 2 x 4
    // International Measures are 1 x 3 x 5

    // Check if the measurement setting exists and if it does, check that it's in inches.
    // If it doesn't or if it is set to inches, do nothing.
    if (defined('SHIPPING_DIMENSION_UNITS') && SHIPPING_DIMENSION_UNITS !== "inches") {
        foreach ($key_values as &$dimmension) {
            $dimmension = (double)$dimmension / 2.54;
        }
    }

    $output_str =<<<EOF
<em>Domestic Measurements (LWH):</em> {$key_values[0]} × {$key_values[2]} × {$key_values[4]}<br>
<em>International Measurements (LWH):</em> {$key_values[1]} × {$key_values[3]} × {$key_values[5]}
EOF;

return $output_str;
}

function uspsr_pretty_json_print($json)
{
    $result = '';
    $level = 0;
    $in_quotes = false;
    $in_escape = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if ( $in_escape ) {
            $in_escape = false;
        } else if( $char === '"' ) {
            $in_quotes = !$in_quotes;
        } else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    break;

                case '{': case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "\t": case "\n": case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
            }
        } else if ( $char === '\\' ) {
            $in_escape = true;
        }
        if( $new_line_level !== NULL ) {
            $result .= "\n".str_repeat( "\t", $new_line_level );
        }
        $result .= $char.$post;
    }

    return $result;
}

function uspsr_validate_zipcode($entry)
{
    // Remove any non-digit characters, US Zip codes are only digits.
    $digits = preg_replace('/\D/', '', $entry);

    // Handle 5 digits or 9 digits by returning the first five.
    if ( (strlen($digits) === 5) || (strlen($digits) === 9) ) {
        return substr($digits, 0, 5);
    }

    // Return false if it doesn't have 5 or 9 digits. That generally means it's an invalid zip.
    return false;
}

// Filter out the "gibberish" and make the title pretty
function uspsr_filter_gibberish($entry)
{
    $entry = preg_replace(
        [
            '/ISC/',
            '/Machinable DDU/',
            '/Machinable/',
            '/(Basic|Single-Piece)/i',
            '/USPS\s+/',
            '/Non-Soft Pack Tier 1/',
        ],
        ''
        ,
        $entry
    );

    return trim(preg_replace('/\s+/', ' ', $entry));
}

function uspsr_get_categories($key_value)
{

    $limit_list = preg_split("/[\s,]/",trim($key_value));
    $limit_list = array_filter($limit_list);

    $output_str = '';

    foreach ($limit_list as $limit) {
        $output_str .= (zen_not_null(zen_get_category_name($limit)) ? zen_get_category_name($limit) : '') . (end($limit_list) && !zen_not_null($output_str) == $limit ? '' : ',');
    }

    if (!zen_not_null($output_str)) {
        $output_str = "<em>- None -</em>";
    }

    return $output_str;
}

function uspsr_check_connect_local($lookup)
{
    $connect_local = FALSE;

    // Disabling the search for CONNECT_LOCAL as you can't just drop your package at any post office.
    // It has to be the one that is closest to the zip code. So if you don't specify the ZIP, the module will turn it off.
    if (!zen_not_null(MODULE_SHIPPING_USPSR_CONNECT_LOCAL_ZIP)) return false;

    $limit_list = preg_split("/[\s,]/",trim(MODULE_SHIPPING_USPSR_CONNECT_LOCAL_ZIP));
    $limit_list = array_filter($limit_list);

    if (in_array(uspsr_validate_zipcode($lookup), $limit_list)) {
        $connect_local = TRUE;
    }

    return $connect_local;

}

function uspsr_get_connect_zipcodes($data)
{
    // Split up the incoming data by commas (remove the blanks)

    if (zen_not_null($data)) {
        $output = '';
        $key_values = preg_split('/[\s+]/', $data);
        array_filter($key_values);

        foreach($key_values as $zipcode) {
            $output .= $zipcode . ($zipcode != end($key_values) ? ", " : "");
        }

        return $output;

    } else {
        return "- None -";
    }
}

function zen_uspsr_estimate_days($data)
{
    $output = '';
    // Simply put, put the number before the word.
    if ( preg_match("/\d+\-\d+/", $data) ) { $output = $data . " " . MODULE_SHIPPING_USPSR_TEXT_DAYS; }
    elseif ( is_numeric($data) && ($data > 1 || $data == 0) ) $output = $data . " " . MODULE_SHIPPING_USPSR_TEXT_DAYS;
    else $output = "~" . $data . " " . MODULE_SHIPPING_USPSR_TEXT_DAY;


    return $output;
}

function zen_cfg_uspsr_numericupdown($key_value, $key = '')
{

    $output_str = zen_draw_input_field($key, $key_value, 'class="form-control" min="0" step="1"', FALSE, 'number');

    return $output_str;
}
