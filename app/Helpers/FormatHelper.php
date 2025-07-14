<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class FormatHelper
{
    function formatCurrencyID($number) {
    if (is_string($number)) {
        $number = (float)$number;
    }

    $str_number = (string)$number;
    $formatted_number = number_format($str_number, 2, ',', '.');
    $exploded_number = explode(",", $formatted_number);

    if ( (int)$exploded_number[1] === 0 ) {
        $formatted_number = number_format($str_number, 0, ',', '.') . ",-";
    } else {
        if (strlen($exploded_number[1]) === 1) {
            $formatted_number = "$exploded_number[0],$exploded_number[1]0";
        } else {
            $formatted_number = "$exploded_number[0],$exploded_number[1]";
        }
    }

    // dump($formatted_number);

    return $formatted_number;
}
}
