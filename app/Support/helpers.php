<?php

function php_encode($value)
{
    if (is_array($value)) {
        $res = "[\n";
        $i = 0;
        foreach ($value as $k => $item) {
            if ($k === $i) {
                $res .= "    " . php_encode($item) . ",\n";
                $i++;
            } else {
                $res .= "    '$k' => " . php_encode($item) . ",\n";
            }
        }
        return $res . "]";
    } else {
        return var_export($value, true);
    }
}

function indent($code, $indentation = "    ", $firstLine = false)
{
    return ($firstLine ? $indentation : '') .
        str_replace("\n", "\n$indentation", $code);
}
