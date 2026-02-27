<?php
/**
 * USPS Shipping (RESTful) for Zen Cart
 * Version 1.8.3
 *
 * @copyright Portions Copyright 2004-2026 Zen Cart Team
 * @copyright Portions adapted from 2012 osCbyJetta
 * @author Paul Williams (retched)
 * @version $Id: usps.extra_functions.php 2026-02-26 retched Version 1.8.3 $
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

function zen_cfg_uspsr_dimmensions($key_value, $key = '')
{
    $key_values = array_filter(explode(', ', $key_value));
    array_walk($key_values, function (&$value) {
        $value = trim($value);
    }); // Quickly remove white space

    // Length
    $domm_length = zen_draw_input_field('configuration[MODULE_SHIPPING_USPSR_DIMMENSIONS][]', $key_values[0], 'size="10" class="form-control" style="text-align: center;"');
    $intl_length = zen_draw_input_field('configuration[MODULE_SHIPPING_USPSR_DIMMENSIONS][]', $key_values[1], 'size="10" class="form-control" style="text-align: center;"');

    // Width
    $domm_width = zen_draw_input_field('configuration[MODULE_SHIPPING_USPSR_DIMMENSIONS][]', $key_values[2], 'size="10" class="form-control" style="text-align: center;"');
    $intl_width = zen_draw_input_field('configuration[MODULE_SHIPPING_USPSR_DIMMENSIONS][]', $key_values[3], 'size="10" class="form-control" style="text-align: center;"');

    // Height
    $domm_height = zen_draw_input_field('configuration[MODULE_SHIPPING_USPSR_DIMMENSIONS][]', $key_values[4], 'size="10" class="form-control" style="text-align: center;"');
    $intl_height = zen_draw_input_field('configuration[MODULE_SHIPPING_USPSR_DIMMENSIONS][]', $key_values[5], 'size="10" class="form-control" style="text-align: center;"');


    $table = <<<EOF
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

function zen_cfg_uspsr_ltr_dimmensions($key_value, $key = '')
{
    $key_values = array_filter(explode(', ', $key_value));
    array_walk($key_values, function (&$value) {
        $value = trim($value);
    }); // Quickly remove white space

    // Length
    $domm_length = zen_draw_input_field('configuration[MODULE_SHIPPING_USPSR_LTR_DIMMENSIONS][]', $key_values[0], 'size="10" class="form-control" style="text-align: center;"');
    $intl_length = zen_draw_input_field('configuration[MODULE_SHIPPING_USPSR_LTR_DIMMENSIONS][]', $key_values[1], 'size="10" class="form-control" style="text-align: center;"');

    // Height
    $domm_height = zen_draw_input_field('configuration[MODULE_SHIPPING_USPSR_LTR_DIMMENSIONS][]', $key_values[2], 'size="10" class="form-control" style="text-align: center;"');
    $intl_height = zen_draw_input_field('configuration[MODULE_SHIPPING_USPSR_LTR_DIMMENSIONS][]', $key_values[3], 'size="10" class="form-control" style="text-align: center;"');

    // Thickness
    $domm_thickness = zen_draw_input_field('configuration[MODULE_SHIPPING_USPSR_LTR_DIMMENSIONS][]', $key_values[4], 'size="10" class="form-control" style="text-align: center;"');
    $intl_thickness = zen_draw_input_field('configuration[MODULE_SHIPPING_USPSR_LTR_DIMMENSIONS][]', $key_values[5], 'size="10" class="form-control" style="text-align: center;"');


    $table = <<<EOF
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
            <div class="three-column-cell">Height</div>
            <div class="three-column-cell border-div align-center">$domm_height</div>
            <div class="three-column-cell align-center">$intl_height</div>
        </div>
        <div class="three-column-row">
            <div class="three-column-cell">Thickness</div>
            <div class="three-column-cell border-div align-center">$domm_thickness</div>
            <div class="three-column-cell align-center">$intl_thickness</div>
        </div>
    </div>
EOF;

    return $table;
}

function zen_cfg_uspsr_services($select_array, $key_value, $key = '')
{
    $key_values = explode(', ', $key_value);
    array_walk($key_values, function (&$value) {
        $value = trim($value);
    }); // Quickly remove extra white space

    $name = ($key) ? ('configuration[' . $key . '][]') : 'configuration_value';



    $w20pxl = 'width:20px;float:left;text-align:center;';
    $w60pxl = 'width:60px;float:left;text-align:center;';
    $frc = 'float:right;text-align:center;';

    $string =
        '<b>' .
        '<div style="' . $w20pxl . '">&nbsp;</div>' .
        '<div style="' . $w60pxl . '">Min</div>' .
        '<div style="' . $w60pxl . '">Max</div>' .
        '<div style="float:left;"></div>' .
        '<div style="' . $frc . '">Handling</div>' .
        '</b>' .
        '<div style="clear:both;"></div>';
    $string_spacing = '<div><br><br><b>&nbsp;International Rates:</b><br></div>' . $string;
    $string_spacing_international = 0;
    $string = '<div><br><b>&nbsp;Domestic Rates:</b><br></div>' . $string;
    for ($i = 0, $n = count($select_array); $i < $n; $i++) {
            $servicename =  trim(preg_replace(
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
                    '/Machinable( DDU)?/',
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
                    ''
                ],
                $select_array[$i]
            ));

        $stripped_servicename = str_replace(' ', '', $servicename);
        if (stripos($select_array[$i], 'international') !== false) {
            $string_spacing_international++;
        }
        if ($string_spacing_international === 1) {
            $string .= $string_spacing;
        }

        $string .= '<div id="' . $key . $i . '">';
        $string .=
            '<div style="' . $w20pxl . '">' .
            zen_draw_checkbox_field($name, $select_array[$i], (in_array($select_array[$i], $key_values) ? 'CHECKED' : ''), '', 'id="'. $stripped_servicename . '"') .
            '</div>';
        if (in_array($select_array[$i], $key_values)) {
            next($key_values);
        }

        $string .=
            '<div style="' . $w60pxl . '">' .
            zen_draw_input_field($name, current($key_values), 'size="5"') .
            '</div>';
        next($key_values);

        $string .=
            '<div style="' . $w60pxl . '">' .
            zen_draw_input_field($name, current($key_values), 'size="5"') .
            '</div>';
        next($key_values);

        $string .=
            '<div style="float:left;">&nbsp;&nbsp;&nbsp;<label style="font-weight: normal" for="'. $stripped_servicename .'">' .
            $servicename
            .
            '</label></div>';
        $string .=
            '<div style="' . $frc . '">$' .
            zen_draw_input_field($name, current($key_values), 'size="4" style="text-align: right;"') .
            '</div>';
        next($key_values);

        $string .= '<div style="clear:both;"></div></div>';
    }
    return $string;
}

function zen_cfg_uspsr_extraservices($destination, $key_value, $key = '')
{
    $key_values = array_filter(explode(', ', $key_value));
    array_walk($key_values, function (&$value) {
        $value = trim($value);
    }); // Quickly remove white space

    $name = ($key) ? ('configuration[' . $key . '][]') : 'configuration_value';

    $output_str = '';

    $focus = 0;

    switch ($destination) {
        case "domestic":
            $focus = 1;
            break;
        case "international":
            $focus = 2;
            break;
        case "domestic-letters":
            $focus = 4;
            break;
        case "intl-letters":
            $focus = 8;
            break;
    }

    // Establish a list of codes.
    // Format: (API Code) => ['Name of Service', Bitfield (0 = Nope, 1 = Domestic Pkg, 2 = International Pkg, 4 = Domestic Letters, 8 = International Letters)]
    $options = [
        910 => ['Certified Mail', 1 + 4],
        930 => ['Insurance', 1 + 2 + 4],
        925 => ['Priority Mail Express Merchandise Insurance', 1],
        923 => ['Adult Signature Restricted Delivery', 1],
        922 => ['Adult Signature Required', 1],
        940 => ['Registered Mail', 1 + 4 + 8],
        915 => ['Collect on Delivery', 1],
        955 => ['Return Receipt', 1 + 4 + 8],
        957 => ['Return Receipt Electronic', 1 + 4],
        921 => ['Signature Confirmation', 1],
        911 => ['Certified Mail Restricted Delivery', 1 + 4],
        912 => ['Certified Mail Adult Signature Required', 1],
        913 => ['Certified Mail Adult Signature Restricted Delivery', 1],
        917 => ['Collect on Delivery Restricted Delivery', 1],
        924 => ['Signature Confirmation Restricted Delivery', 1],
        941 => ['Registered Mail Restricted Delivery', 1 + 4],
        984 => ['Parcel Locker Delivery', 1],
        981 => ['Signature Requested (Priority Mail Express only)', 1],
        986 => ['PO to Addressee (Priority Mail Express only)', 1],
        991 => ['Sunday Delivery (Priority Mail + Priority Mail Express)', 1],
        934 => ['Insurance Restricted Delivery', 1 + 4],
        856 => ['Live Animal Transportation Fee', 1],
        857 => ['Hazardous Materials', 1 + 2],
     'RMCC' => ['Registered Mail COD (Collect on Delivery) Charge', 1], // This is a special case since the API doesn't return a code for this service, but we need to track it.
    ];

    foreach ($options as $code => $service) {
        if ($service[1] & $focus) { // Does the service pass the bit check? If so, add it.
            $output_str .= zen_draw_checkbox_field($name, $code, (in_array($code, $key_values) ? TRUE : FALSE), '', " id=\"$destination-$code\"") . "&nbsp;&nbsp;<label for=\"$destination-$code\" style=\"font-weight:normal;\">" .  $service[0] . "</label><br>" . "\n";
        }
    }

    $output_str .= zen_draw_hidden_field($name, "-1"); // Have to keep this so that fields are kept inline.

    return $output_str;
}

function zen_cfg_uspsr_account_display($key_value)
{
    // The key_value is either something or nothing

    if (zen_not_null($key_value) && !empty($key_value)) {
        return trim($key_value);
    } else {
        return "--none--";
    }

}

function zen_cfg_uspsr_extraservices_display($key_value)
{
    // Display the Values as a Comma-Separated List.

    // Delete the -1 value (It's just a placeholder to keep the array intact)
    $key_values = array_filter(explode(', ', $key_value), function ($value) {
        return !empty($value) && $value != -1;
    });

    array_walk($key_values, function (&$value) {
        $value = (int)trim($value);
    }); // Quickly remove white space


    $output = '';
    $options = [
        -1 => '', // Hidden placeholder, should not be visible.
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
     'RMCC' => 'Registered Mail COD Charge', // This is a special case since the API doesn't return a code for this service, but we need to track it.
    ];

    if (!empty($key_values)) {
        $end = end($key_values);
        foreach ($key_values as $code) {
            $output .= $options[$code] . ($code !== $end ? ", " : "");
        }
    }
    if (!zen_not_null($output))
        $output = '--none--';

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
                        '/\s{2,}/',
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
            } else {
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

    $output = "<b>Domestic Methods:</b><br> " . (zen_not_null($output_domestic) ? $output_domestic : '--none--') . "<br><br>\n" . "<b>International Methods</b>: <br>" . (zen_not_null($output_intl) ? $output_intl : '--none--');

    return $output . "\n";
}

function zen_cfg_uspsr_showdimmensions($key_value)
{
    $key_values = explode(', ', $key_value);
    $key_values = array_filter($key_values, function ($value) {
        if (zen_not_null($value)) {
            return "--none--";
        }
    });

    // Domestic Measures are 0 x 2 x 4
    // International Measures are 1 x 3 x 5

    // Check if the measurement setting exists and if it does, check that it's in inches.
    // If it doesn't or if it is set to inches, do nothing.
    if (defined('SHIPPING_DIMENSION_UNITS') && SHIPPING_DIMENSION_UNITS !== "inches") {
        foreach ($key_values as &$dimmension) {
            $dimmension = (float) $dimmension / 2.54;
        }
    }

    $output_str = '';
    $output_str .= "<em>Domestic Measurements (LWH):</em> " . $key_values[0] . " × " . $key_values[2] . " × " . $key_values[4] . "<br>\n";
    $output_str .= "<em>International Measurements (LWH):</em> " . $key_values[1] . " × " . $key_values[3] . " × " . "$key_values[5]";

    return $output_str;
}

function uspsr_pretty_json_print($json)
{
    $result = '';
    $level = 0;
    $in_quotes = false;
    $in_escape = false;
    $ends_line_level = NULL;

    $encoded_json = json_decode($json, TRUE); // Read the JSON into an array

    unset($encoded_json['client_secret']);

    $sanitized_json = json_encode($encoded_json, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES);

    $json_length = strlen($sanitized_json);

    for ($i = 0; $i < $json_length; $i++) {
        $char = $sanitized_json[$i];
        $new_line_level = NULL;
        $post = "";
        if ($ends_line_level !== NULL) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if ($in_escape) {
            $in_escape = false;
        } else if ($char === '"') {
            $in_quotes = !$in_quotes;
        } else if (!$in_quotes) {
            switch ($char) {
                case '}':
                case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    break;

                case '{':
                case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ":
                case "\t":
                case "\n":
                case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
            }
        } else if ($char === '\\') {
            $in_escape = true;
        }
        if ($new_line_level !== NULL) {
            $result .= "\n" . str_repeat("    ", $new_line_level);
        }
        $result .= $char . $post;
    }

    return $result;
}

function uspsr_validate_zipcode($entry)
{
    // Don't do anything if $entry is NULL (likely because the page loaded)
    if (zen_not_null($entry)) {
        // Remove any non-digit characters, US Zip codes are only digits.
        $digits = preg_replace('/\D/', '', $entry);

        // Handle 5 digits or 9 digits by returning the first five.
        if ((strlen($digits) === 5) || (strlen($digits) === 9)) {
            return substr($digits, 0, 5); // Only the initial five digits are necessary, filter anything else.
        }
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
            '/Machinable( DDU)?/',
            '/(Basic|Single-Piece)/i',
            '/USPS\s+/',
            '/Non-Soft Pack Tier 1/',
            '/Oversized/',
            '/Nonstandard/',
            '/(Non)?rectangular/i',
            '/Dimmensional/'
        ],
        ''
        ,
        $entry
    );

    return trim(preg_replace('/\s+/', ' ', $entry));
}

function uspsr_get_categories($key_value)
{

    $limit_list = preg_split("/[\s,]/", trim($key_value));
    $limit_list = array_filter($limit_list);

    $output_str = '';

    foreach ($limit_list as $limit) {
        $output_str .= (zen_not_null(zen_get_category_name($limit)) ? zen_get_category_name($limit) : '') . (end($limit_list) && !zen_not_null($output_str) == $limit ? '' : ',');
    }

    if (!zen_not_null($output_str)) {
        $output_str = "--none--";
    }

    return $output_str;
}

function uspsr_check_connect_local($lookup)
{
    $connect_local = FALSE;

    // Disabling the search for CONNECT_LOCAL as you can't just drop your package at any post office.
    // It has to be the one that is closest to the zip code. So if you don't specify the ZIP, the module will turn it off.
    if (!zen_not_null(MODULE_SHIPPING_USPSR_CONNECT_LOCAL_ZIP))
        return false;

    $limit_list = preg_split("/[\s,]/", trim(MODULE_SHIPPING_USPSR_CONNECT_LOCAL_ZIP));
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

        foreach ($key_values as $zipcode) {
            $output .= $zipcode . ($zipcode != end($key_values) ? ", " : "");
        }

        return $output;

    } else {
        return "--none--";
    }
}

function zen_uspsr_estimate_days($data)
{
    $output = '';

    if (!defined('MODULE_SHIPPING_USPSR_HANDLING_TIME')) {
        return $data;
    }

    $daystoadd = (int) MODULE_SHIPPING_USPSR_HANDLING_TIME;

    // Simply put, put the number before the word.
    if (preg_match("/\d+\-\d+/", $data)) {

        // Split the range of days off and add the handling time to each end.
        $days = explode('-', $data);
        foreach ($days as &$day) {
            $day = (int)$day + $daystoadd;
        }
        $data = implode('-', $days); // Collapse the array back into a - string. (This should still only have two values)
        $output = $data . " " . MODULE_SHIPPING_USPSR_TEXT_DAYS;

    } elseif (is_numeric($data) && ($data > 1 || $data == 0)) {
        $output = (int)$data + $daystoadd . " " . MODULE_SHIPPING_USPSR_TEXT_DAYS;
    } else {
        $days = (int)$data + $daystoadd;
        $output = "~" . $days . " " . ($days == 1 ? MODULE_SHIPPING_USPSR_TEXT_DAY : MODULE_SHIPPING_USPSR_TEXT_DAYS);
    }

    return $output;
}
