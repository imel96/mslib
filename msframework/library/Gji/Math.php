<?php

/*
 * $Id$
 */

namespace Gji;

require_once('Gji/Sort.php');

use Gji\Sort;

class Math {

	static function mapAndSum($data, $mapper)
	{
		return array_sum(array_map($mapper, $data));
	}

	static function median($data, $key)
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
