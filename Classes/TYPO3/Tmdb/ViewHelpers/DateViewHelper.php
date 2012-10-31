<?php
namespace TYPO3\Tmdb\ViewHelpers;

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
 * Get release date by country
 *
 * @api
 */
class DateViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @param array $settings
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * @param string $date
	 * @param string $format
	 * @return string
	 */
	public function render($date, $format = 'd.m.Y') {
		$dateObject = new \DateTime();
		$dateObject->createFromFormat('Y-m-d', $date);
		return $dateObject->format($format);
	}
}


?>
