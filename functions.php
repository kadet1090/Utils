<?php
/**
 * Test if given string is serialized or not.
 *
 * @param string $data Data to check.
 *
 * @return bool
 */
function is_serialized($data)
{
    // if it isn't a string, it isn't serialized
    if (!is_string($data))
        return false;

    $data = trim($data);
    if ('N;' == $data)
        return true;

    if (!preg_match('/^([adObis]):/', $data, $badions))
        return false;

    switch ($badions[1]) {
        case 'a' :
        case 'O' :
        case 's' :
            if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data))
                return true;
            break;
        case 'b' :
        case 'i' :
        case 'd' :
            if (preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data))
                return true;
            break;
    }

    return false;
}

function getCaller($no = 0)
{
    $backtrace = debug_backtrace();

    return isset($backtrace[2 + $no]['class']) ?
        $backtrace[2 + $no]['class'] :
        null;
}

function array_filter_keys(array $input, callable $callback)
{
    return array_intersect_key($input, array_flip(array_filter(array_keys($input), $callback)));
}