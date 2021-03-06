<?php

namespace Mslib;

class Sort
{
    /*
     * this is basically a selection sort implementation where it
     * only sort $top number of element (faster).
     * $data array of array
     * $key to be used with inner array
     */
    public static function findTop($top, $data, $key)
    {
        $n = count ($data);
        $keys = array_keys($data);

        for ($i = 0; $i < $top; $i++) {
            $max = null;
            $maxKey = null;

            for ($j = $i; $j < $n; $j++)

                if (!$max || $data[$keys[$j]][$key] > $max[$key]) {
                    $maxKey = $j;
                    $max = $data[$keys[$j]];
                }
            $data[$keys[$maxKey]] = $data[$keys[$i]];
            $data[$keys[$i]] = $max;
        }
        return array_slice($data, 0, $top);
    }
}
