<?php
namespace TYPO3\Tmdb\Utility;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Tmdb".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Strings utility
 *
 * @api
 */
class Strings {

	public static function underscoredToLowerCamelCase($string) {
		$upperCamelCase = str_replace(' ', '', ucwords(str_replace('_', ' ', self::strtolower($string))));
		$lowerCamelCase = self::lcfirst($upperCamelCase);
		return $lowerCamelCase;
	}

	public static function strtolower($str) {
		return strtr((string) $str, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
	}

	public static function lcfirst($string) {
		return self::strtolower(substr($string, 0, 1)) . substr($string, 1);
	}

}

?>