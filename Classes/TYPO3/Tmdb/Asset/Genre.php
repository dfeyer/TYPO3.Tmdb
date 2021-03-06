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

use TYPO3\Flow\Annotations as Flow;

/**
 * Genre
 */
class Genre extends AbstractAsset {

	const type = 'genre';

	/**
	 * @link http://help.themoviedb.org/kb/api/genre-list
	 */
	public function getList($language = null) {
		$info = $this->tmdbService->getAssetInformations(self::type, $this->id, 'list', array('language' => $language));
		return $info;
	}

	/**
	 * @link http://help.themoviedb.org/kb/api/genre-movies
	 */
	public function getMovies($language = null, $page = 1) {
		$movies = array();
		$info   = $this->tmdbService->getAssetInformations(self::type, $this->id, 'movies', array('language' => $language, 'page' => $page));
		foreach ($info->results as $index => $movie) {
			$movies[$movie->id] = new Movie($movie);
		}
		return $movies;
	}

}

?>