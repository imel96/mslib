<?php

namespace Mslib;
use Mslib\Sort;

class Math
{
    /*
     * apply mapper to an array and sum the values of the resulting array
     * $mapper callback function that must return a number
     * $data array of inputs to $mapper
     */
    public static function mapAndSum($data, $mapper)
    {
        return array_sum(array_map($mapper, $data));
    }

    /*
     * sort n+1 of $data using $key and return the median
     * $data array of array
     * $key to be used with inner array
     */
    public static function median($data, $key)
    {
        $n = count ($data);
        $topHalf = $n / 2;

        if (count ($data) % 2) {
            $topHalf = ceil($topHalf);
            $even = false;

        } else {
            $topHalf++;
            $even = true;
        }
        $topHalf = Sort::findTop($topHalf, $data, $key);
        $n = count ($topHalf) - 1;
        if ($even)
            $ret = ($topHalf[$n][$key] + $topHalf[$n - 1][$key]) / 2;
        else
            $ret = $topHalf[$n][$key];
        return $ret;
    }
}
