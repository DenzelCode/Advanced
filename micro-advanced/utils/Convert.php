<?php  
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
 */

namespace advanced\utils;

/**
* Convert class
*/
class Convert {

	public static function numToString(object $number) : string {
		if ($number >= 1000000000000) {
			$num = substr($number, 1, -11);
			$sec = strlen($num) - 1;
			if ($num[$sec] > 0) $num = '.' . $num[$sec]; else $num = null;
			$return = number_format(substr($number, 0, -12)) . $num . 'B';
		} else if ($number >= 1000000000) {
			$num = substr($number, 1, -8);
			$sec = strlen($num) - 1;
			if ($num[$sec] > 0) $num = '.' . $num[$sec]; else $num = null;
			$return = number_format(substr($number, 0, -9)) . $num . 'k M';
		} else if ($number >= 1000000) {
			$num = substr($number, 1, -5);
			$sec = strlen($num) - 1;
			if ($num[$sec] > 0) $num = '.' . $num[$sec]; else $num = null;
			$return = number_format(substr($number, 0, -6)) . $num . 'M';
		} else if ($number >= 1000) {
			$num = substr($number, 1, -2);
			$sec = strlen($num) - 1;
			if ($num[$sec] > 0) $num = '.' . $num[$sec]; else $num = null;
			$return = number_format(substr($number, 0, -3)) . $num . 'k';
		} else $return = number_format($number);

		return $return;
	}
}
