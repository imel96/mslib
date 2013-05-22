<?php

/*
 * copied from  http://svn.corp.local/svn/mms/trunk/library/Gji/Sort.php
 */

namespace Gji;

class Sort {

	/*
	 * this is basically a selection sort implementation where it
	 * only sort $top number of element (faster).
	 */
	static function findTop($top, $data, $key)
	{
		$n = count ($data);

		for ($i = 0; $i < $top; $i++) {
			$max = null;
			$maxKey = null;

			for ($j = $i; $j < $n; $j++)

				if (!$max || $data[$j][$key] > $max[$key]) {
					$maxKey = $j;
					$max = $data[$j];
				}
			$data[$maxKey] = $data[$i];
			$data[$i] = $max;
		}
		return array_slice($data, 0, $top);
	}
}
