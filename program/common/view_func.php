<?php

// Relative datetime function.

function d($input)
{
    if (!ctype_digit($input)) $input = strtotime($input);
    $diff = time() - $input;
    if ($diff < 0) return 'the future';
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return ($i = round($diff / 60)) . ' minute' . ($i == 1 ? '' : 's') . ' ago';
    if ($diff < 86400) return ($i = round($diff / 3600)) . ' hour' . ($i == 1 ? '' : 's') . ' ago';
    if ($diff < 864000) return ($i = round($diff / 86400)) . ' day' . ($i == 1 ? '' : 's') . ' ago';
    return date('d M Y', $input);
}

// String escape function.

function e($input, $preserve_line_breaks = false)
{
    $escaped = htmlentities($input, ENT_COMPAT | ENT_IGNORE, 'UTF-8', false);
    echo $preserve_line_breaks ? nl2br($escaped, true) : $escaped;
}

// Number formatting function.

function f($input)
{
    $input = (int)$input;
    if ($input < 1024) return $input . 'B';
    if ($input < 1048576) return number_format($input / 1024, 1, '.', '') . 'K';
    if ($input < 1073741824) return number_format($input / 1048576, 1, '.', '') . 'M';
    if ($input < 1099511627776) return number_format($input / 1073741824, 1, '.', '') . 'G';
    return number_format($input / 1099511627776, 1, '.', '') . 'T';
}
