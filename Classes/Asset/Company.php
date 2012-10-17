<?php
namespace TYPO3\Tmdb\Asset;

/*                                                                        *
 * This script belongs to the FLOW3 package "Imagine".                    *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Collection Interface
 */
class Company extends AbstractAsset {

	const type = 'company';

	/**
	 * @param int $data
	 */
	public function __construct($data) {
		$this->tmdbService = new \TYPO3\Tmdb\Service\TmdbService();

		if (is_numeric($data)) {
			$data = $this->tmdbService->getAssetInformations(self::type, $data);
		}

		$this->processData($data);
	}

	/**
	 * @link http://help.themoviedb.org/kb/api/company-movies
	 */
	public function movies($language = null, $page = 1) {
		$movies = array();
		$info   = $this->tmdbService->getAssetInformations(self::type, $this->id, 'movies', array('language'=> $language, 'page'=> $page));
		foreach ($info->results as $index => $movie) {
			$movies[$movie->id] = new Movie($movie);
		}
		return $movies;
	}

	////////////////////////////////////////////////////////////////////
	// Additional helpers

	/**
	 * Get the logo pictures
	 */
	public function logo($size = false) {
		return $this->getImageUrl('logo', $size);
	}

}

?>