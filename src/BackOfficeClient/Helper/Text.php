<?php

namespace FaimMedia\BackOfficeClient\Helper;

class Text {

	/**
	 * Uncamelize text
	 */
	public static function uncamelize(string $str): string {
		$str = preg_replace(
			["/([A-Z]+)/", "/_([A-Z]+)([A-Z][a-z])/"],
			["_$1", "_$1_$2"],
			lcfirst($str)
		);

		return strtolower($str);
	}

	/**
	 * Camelize text
	 */
	public static function camelize(string $str): string {
	// split string by '-'
		$words = explode('-', $str);

	// make a strings first character uppercase
		$words = array_map('ucfirst', $words);

	// join array elements with '-'
		$str = implode('-', $words);

		return $string;
	}
}