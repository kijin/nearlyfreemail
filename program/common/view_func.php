<?php

// Relative datetime function.

function d($input)
{
    if (!ctype_digit($input)) $input = strtotime($input);
    $diff = time() - $input;
    if ($diff < 0) return e('the future');
    if ($diff < 60) return e('just now');
    if ($diff < 3600) return e(($i = round($diff / 60)) . ' minute' . ($i == 1 ? '' : 's') . ' ago');
    if ($diff < 86400) return e(($i = round($diff / 3600)) . ' hour' . ($i == 1 ? '' : 's') . ' ago');
    if ($diff < 864000) return e(($i = round($diff / 86400)) . ' day' . ($i == 1 ? '' : 's') . ' ago');
    return e(date('d M Y', $input));
}

// String escape function.

function e($input, $preserve_line_breaks = false)
{
    $escaped = htmlentities($input, ENT_COMPAT | ENT_IGNORE, 'UTF-8', false);
    $output = $preserve_line_breaks ? nl2br($escaped, true) : $escaped;
    echo $output;
    return $output;
}

// Number formatting function.

function f($input)
{
    $input = (int)$input;
    if ($input < 1024) return e($input . 'B');
    if ($input < 1048576) return e(number_format($input / 1024, 1, '.', '') . 'K');
    if ($input < 1073741824) return e(number_format($input / 1048576, 1, '.', '') . 'M');
    if ($input < 1099511627776) return e(number_format($input / 1073741824, 1, '.', '') . 'G');
    return e(number_format($input / 1099511627776, 1, '.', '') . 'T');
}

// URL generation function.

function u( /* args */ )
{
    static $base = false;
    if (!$base) $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    $args = func_get_args();
    return e(str_replace('//', '/', $base . '/' . implode('/', $args)));
}
