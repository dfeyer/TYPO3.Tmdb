<?php
namespace TYPO3\Tmdb\ViewHelpers\Image;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Tmdb".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Display Backdrop Thumbnail
 *
 * @api
 */
class BackdropsViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Tmdb\Service\TmdbService
	 */
	protected $tmdbService;

	/**
	 * @param \TYPO3\Tmdb\Asset\Movie $movie
	 * @return string Rendered URI
	 * @api
	 */
	public function render(\TYPO3\Tmdb\Asset\Movie $movie) {
		$output = '';
		$images = $movie->getImages('fr;;null', 'w300')->backdrops;
		foreach ($images as $image) {
			$output .= '<img src="' . $image->file_path . '">';
		}
		return $output;
	}
}


?>
