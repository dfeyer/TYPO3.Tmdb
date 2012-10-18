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
 * A view helper for creating links to TMDb asset page
 *
 * @api
 */
class AlternativeTitlesViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

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
	 * @param \TYPO3\Tmdb\Asset\Movie $movie
	 * @param string $country
	 * @return string
	 */
	public function render(\TYPO3\Tmdb\Asset\Movie $movie, $country = null) {
		$titles = $movie->getAlternativeTitles($country);
		$output = array();
		foreach ($titles as $title) {
			$output[] = $title->title;
		}

		$output = implode(', ', $output);
		if (trim($output) === '') {
			$output = '-';
		}

		return $output;
	}
}


?>
