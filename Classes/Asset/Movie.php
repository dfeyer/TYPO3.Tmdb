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
class Movie extends AbstractAsset {

	const type = 'movie';

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
	 * @link http://help.themoviedb.org/kb/api/movie-alternative-titles
	 */
	public function getAlternativeTitles($country=''){
		return $this->tmdbService->getAssetInformations(self::type, $this->id, 'alternative_titles', array('country'=>$country));
	}

	/**
	 * @link http://help.themoviedb.org/kb/api/movie-casts
	 */
	public function getCasts(){
		$casts = array();
		$info = $this->tmdbService->getAssetInformations(self::type, $this->id, 'casts');
		foreach($info as $group => $persons){
			if(!is_array($persons)) continue;
			foreach($persons as $index => $person){
				$casts[$group][$person->id] = new Person($person);
			}
		}
		return $casts;
	}

	/**
	 * @link http://help.themoviedb.org/kb/api/movie-images
	 * @param language string, you can seperate multi language selects by using a ;
	 *        the API currently does not include null or empty language values in the result
	 *        so if you want all images in english including the ones that have null or empty language in database,
	 *        you can use 'null;;en' as a language parameter value.
	 */
	public function getImages($language=null, $size=false){
		$languages = array();

		if(!is_null($language)){
			$languages = explode(';',$language);
			if($language != $languages[0]){
				$language = '';
			}
		}

		if ($language !== NULL)
		$info = $this->tmdbService->getAssetInformations(self::type, $this->id, 'images', array('language'=>$language));
		foreach($info as $type => $images){
			if(!is_array($images)) continue;
			foreach($images as $index => $data) {
				if(is_null($language) || in_array($data->iso_639_1, $languages)) {
					if($size){
						$info->{$type}[$index]->file_path = $this->tmdbService->getImageUrl(substr($type, 0, strlen($type)-1), $size, $data->file_path);
					}
				} else {
					unset($info->{$type}[$index]);
				}
			}
		}

		return $info;
	}

	/**
	 * @link http://help.themoviedb.org/kb/api/movie-keywords
	 */
	public function getKeywords(){
		return $this->tmdbService->getAssetInformations(self::type, $this->id, 'keywords');
	}

	/**
	 * @link http://help.themoviedb.org/kb/api/movie-release-info
	 */
	public function getReleases(){
		return $this->tmdbService->getAssetInformations(self::type, $this->id, 'releases');
	}

	/**
	 * @link http://help.themoviedb.org/kb/api/movie-trailers
	 */
	public function getTrailers($language=null){
		return $this->tmdbService->getAssetInformations(self::type, $this->id, 'trailers', array('language'=>$language));
	}

	/**
	 * @link http://help.themoviedb.org/kb/api/movie-translations
	 */
	public function getTranslations(){
		return $this->tmdbService->getAssetInformations(self::type, $this->id, 'translations');
	}

	/**
	 * @link http://help.themoviedb.org/kb/api/movie-similar-movies
	 */
	public function getSimilarMovies($language=null, $page=1){
		$movies = array();
		$info = $this->tmdbService->getAssetInformations(self::type, $this->id, 'similar_movies', array('language'=>$language, 'page'=>$page));
		foreach($info->results as $index => $movie){
			$movies[$movie->id] = new Movie($movie);
		}
		return $movies;
	}

	////////////////////////////////////////////////////////////////////
	// Additional helpers

	/**
	 * Get the collection as an Collection object
	 */
	public function getCollection(){
		$collection = false;

		if(isset($this->data['belongs_to_collection'])){
			$collection = new Collection($this->data['belongs_to_collection']->id);
		}

		return $collection;
	}

	/**
	 * Get the backdrops
	 */
	public function backdrop($size=false, $random=false, $language=null){
		return $this->getImageUrl('backdrop', $size, $random, $language);
	}

	public function backdrops($size, $language=null){
		$images = $this->getImages($language, $size);
		return $images->backdrops;
	}

	/**
	 * Get the posters
	 */
	public function poster($size=false, $random=false, $language=null){
		return $this->getImageUrl('poster', $size, $random, $language);
	}

	public function posters($size, $language=null){
		$images = $this->getImages($language, $size);
		return $images->posters;
	}

}

?>