<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * helper for check if string is empty or null
 */
if (!function_exists('empty_or_null'))
{
	function empty_or_null($str){
        return (!isset($str) || trim($str) === '');
	}
}

if (!function_exists('concat_array_comma'))
{
	function concat_array_comma($arr){
		if(!is_array($arr)) return '';
		$str = '';
		foreach($arr as $val){
			$str = $str . (!next( $arr )) ? '' : ',' . $val;
		}
		return $str;
	}
}