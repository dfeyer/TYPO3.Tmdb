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
 * Collection Interface
 */
class Movie extends AbstractAsset {

	/**
	 * Asset type
	 */
	const type = 'movie';

	/**
	 * @var bool
	 */
	protected $adult;

	/**
	 * @var string
	 */
	protected $imdbId;

	/**
	 * @var \stdClass
	 */
	protected $belongToCollection;

	/**
	 * @var int
	 */
	protected $budget;

	/**
	 * @var array
	 */
	protected $genres;

	/**
	 * @var string
	 */
	protected $homepage;

	/**
	 * @var string
	 */
	protected $overview;

	/**
	 * @var string
	 */
	protected $backdropPath;

	/**
	 * @var string
	 */
	protected $posterPath;

	/**
	 * @var string
	 */
	protected $originalTitle;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var array
	 */
	protected $alternativeTitles;

	/**
	 * @var string
	 */
	protected $tagline;

	/**
	 * @var \DateTime
	 */
	protected $releaseDate;

	/**
	 * @var float
	 */
	protected $popularity;

	/**
	 * @var float
	 */
	protected $voteAverage;

	/**
	 * @var int
	 */
	protected $voteCount;

	/**
	 * @var array
	 */
	protected $productionCompany;

	/**
	 * @var array
	 */
	protected $productionCountries;

	/**
	 * @var int
	 */
	protected $revenue;

	/**
	 * @var int
	 */
	protected $runtime;

	/**
	 * @var array
	 */
	protected $spokenLanguages;

	/**
	 * @var string
	 */
	protected $status;


	/**
	 * Get alternative titles
	 *
	 * @param string $country
	 * @return array
	 * @link http://help.themoviedb.org/kb/api/movie-alternative-titles
	 */
	public function getAlternativeTitles($country = ''){
		$alternativeTitles = $this->tmdbService->getAssetInformations(self::type, $this->id, 'alternative_titles', array('country'=>$country));
		$this->alternativeTitles = $alternativeTitles->titles;

		return $this->alternativeTitles;
	}

	/**
	 * Get the casts
	 *
	 * @return array
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
	 * Get the cast filtered by type
	 *
	 * @param string $type
	 * @return array
	 * @link http://help.themoviedb.org/kb/api/movie-casts
	 */
	protected function getCastsByType($type){
		$casts = array();
		$info = $this->tmdbService->getAssetInformations(self::type, $this->id, 'casts');
		foreach($info as $group => $persons){
			if(!is_array($persons) || $group !== $type) continue;
			foreach($persons as $index => $person){
				$casts[$person->id] = new Person($person);
			}
		}
		return $casts;
	}

	/**
	 * Get the actors only
	 *
	 * @return array
	 * @link http://help.themoviedb.org/kb/api/movie-casts
	 */
	public function getActors(){
		return $this->getCastsByType('cast');
	}

	/**
	 * Get the actors only
	 *
	 * @return array
	 * @link http://help.themoviedb.org/kb/api/movie-casts
	 */
	public function getCrews(){
		return $this->getCastsByType('crew');
	}

	/**
	 * Get all the related images
	 *
	 * You can seperate multi language selects by using a ;
	 * the API currently does not include null or empty language values in the result
	 * so if you want all images in english including the ones that have null or empty language in database,
	 * you can use 'null;;en' as a language parameter value.
	 *
	 * @param string $language
	 * @param bool $size
	 * @return array
	 * @link http://help.themoviedb.org/kb/api/movie-images
	 */
	public function getImages($language = null, $size = false){
		$languages = array();

		if(!is_null($language)){
			$languages = explode(';',$language);
			if($language != $languages[0]){
				$language = '';
			}
		}

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
	 * Get attached keywords
	 *
	 * @link http://help.themoviedb.org/kb/api/movie-keywords
	 */
	public function getKeywords(){
		return $this->tmdbService->getAssetInformations(self::type, $this->id, 'keywords');
	}

	/**
	 * Get releases dates
	 *
	 * @param string $country
	 * @link http://help.themoviedb.org/kb/api/movie-release-info
	 */
	public function getReleases($country = null){
		$releases = $this->tmdbService->getAssetInformations(self::type, $this->id, 'releases');
		if ($country !== null) {
			foreach ($releases->countries as $release) {
				if ($release->iso_3166_1 === $country) {
					return $release;
				}
			}
		} else {
			return $releases;
		}
	}

	/**
	 * Get the trailer
	 *
	 * @param string $language
	 * @link http://help.themoviedb.org/kb/api/movie-trailers
	 */
	public function getTrailers($language = null){
		return $this->tmdbService->getAssetInformations(self::type, $this->id, 'trailers', array('language'=>$language));
	}

	/**
	 * Get available translations
	 *
	 * @link http://help.themoviedb.org/kb/api/movie-translations
	 */
	public function getTranslations(){
		return $this->tmdbService->getAssetInformations(self::type, $this->id, 'translations');
	}

	/**
	 * Get similar movies
	 *
	 * @param string $language
	 * @param int $page
	 * @link http://help.themoviedb.org/kb/api/movie-similar-movies
	 */
	public function getSimilarMovies($language = null, $page = 1){
		$movies = array();
		$info = $this->tmdbService->getAssetInformations(self::type, $this->id, 'similar_movies', array('language'=>$language, 'page'=>$page));
		foreach($info->results as $index => $movie){
			$movies[$movie->id] = new Movie($movie);
		}
		return $movies;
	}

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
	 * Get the main backdrops
	 */
	public function getBackdrop($size = false, $random = false, $language = null){
		return $this->getImageUrl('backdrop', $size, $random, $language);
	}

	/**
	 * Get all the backdrops
	 *
	 * @param string|bool $size
	 * @param string $language
	 * @return mixed
	 */
	public function getBackdrops($size = false, $language = null){
		$images = $this->getImages($language, $size);

		return $images->backdrops;
	}

	/**
	 * Get the main poster
	 *
	 * @param string|bool $size
	 * @param bool $random
	 * @param null $language
	 * @return bool|string
	 */
	public function getPoster($size = false, $random = false, $language = null){
		return $this->getImageUrl('poster', $size, $random, $language);
	}

	/**
	 * Get all poster
	 *
	 * @param bool $size
	 * @param null $language
	 * @return mixed
	 */
	public function getPosters($size = false, $language = null){
		$images = $this->getImages($language, $size);

		return $images->posters;
	}

	/**
	 * @param boolean $adult
	 */
	public function setAdult($adult) {
		$this->adult = $adult;
	}

	/**
	 * @return boolean
	 */
	public function getAdult() {
		return $this->adult;
	}

	/**
	 * @param string $backdropPath
	 */
	public function setBackdropPath($backdropPath) {
		$this->backdropPath = $backdropPath;
	}

	/**
	 * @return string
	 */
	public function getBackdropPath() {
		return $this->backdropPath;
	}

	/**
	 * @param string $originalTitle
	 */
	public function setOriginalTitle($originalTitle) {
		$this->originalTitle = $originalTitle;
	}

	/**
	 * @return string
	 */
	public function getOriginalTitle() {
		return $this->originalTitle;
	}

	/**
	 * @param float $popularity
	 */
	public function setPopularity($popularity) {
		$this->popularity = $popularity;
	}

	/**
	 * @return float
	 */
	public function getPopularity() {
		return $this->popularity;
	}

	/**
	 * @param string $posterPath
	 */
	public function setPosterPath($posterPath) {
		$this->posterPath = $posterPath;
	}

	/**
	 * @return string
	 */
	public function getPosterPath() {
		return $this->posterPath;
	}

	/**
	 * @param \DateTime $releaseDate
	 */
	public function setReleaseDate($releaseDate) {
		$this->releaseDate = $releaseDate;
	}

	/**
	 * @return \DateTime
	 */
	public function getReleaseDate() {
		return $this->releaseDate;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param float $voteAverage
	 */
	public function setVoteAverage($voteAverage) {
		$this->voteAverage = $voteAverage;
	}

	/**
	 * @return float
	 */
	public function getVoteAverage() {
		return $this->voteAverage;
	}

	/**
	 * @param int $voteCount
	 */
	public function setVoteCount($voteCount) {
		$this->voteCount = $voteCount;
	}

	/**
	 * @return int
	 */
	public function getVoteCount() {
		return $this->voteCount;
	}

	/**
	 * @param string $imdbId
	 */
	public function setImdbId($imdbId) {
		$this->imdbId = $imdbId;
	}

	/**
	 * @return string
	 */
	public function getImdbId() {
		return $this->imdbId;
	}

	/**
	 * @param string $overview
	 */
	public function setOverview($overview) {
		$this->overview = $overview;
	}

	/**
	 * @return string
	 */
	public function getOverview() {
		return $this->overview;
	}

	/**
	 * @param \stdClass $belongToCollection
	 */
	public function setBelongToCollection($belongToCollection) {
		$this->belongToCollection = $belongToCollection;
	}

	/**
	 * @return \stdClass
	 */
	public function getBelongToCollection() {
		return $this->belongToCollection;
	}

	/**
	 * @param int $budget
	 */
	public function setBudget($budget) {
		$this->budget = $budget;
	}

	/**
	 * @return int
	 */
	public function getBudget() {
		return $this->budget;
	}

	/**
	 * @param array $genres
	 */
	public function setGenres($genres) {
		$this->genres = $genres;
	}

	/**
	 * @return array
	 */
	public function getGenres() {
		return $this->genres;
	}

	/**
	 * @param string $homepage
	 */
	public function setHomepage($homepage) {
		$this->homepage = $homepage;
	}

	/**
	 * @return string
	 */
	public function getHomepage() {
		return $this->homepage;
	}

	/**
	 * @param array $productionCompany
	 */
	public function setProductionCompany($productionCompany) {
		$this->productionCompany = $productionCompany;
	}

	/**
	 * @return array
	 */
	public function getProductionCompany() {
		return $this->productionCompany;
	}

	/**
	 * @param array $productionCountries
	 */
	public function setProductionCountries($productionCountries) {
		$this->productionCountries = $productionCountries;
	}

	/**
	 * @return array
	 */
	public function getProductionCountries() {
		return $this->productionCountries;
	}

	/**
	 * @param int $revenue
	 */
	public function setRevenue($revenue) {
		$this->revenue = $revenue;
	}

	/**
	 * @return int
	 */
	public function getRevenue() {
		return $this->revenue;
	}

	/**
	 * @param int $runtime
	 */
	public function setRuntime($runtime) {
		$this->runtime = $runtime;
	}

	/**
	 * @return int
	 */
	public function getRuntime() {
		return $this->runtime;
	}

	/**
	 * @param array $spokenLanguage
	 */
	public function setSpokenLanguages($spokenLanguage) {
		$this->spokenLanguages = $spokenLanguage;
	}

	/**
	 * @return array
	 */
	public function getSpokenLanguages() {
		return $this->spokenLanguages;
	}

	/**
	 * @param string $status
	 */
	public function setStatus($status) {
		$this->status = $status;
	}

	/**
	 * @return string
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @param string $tagline
	 */
	public function setTagline($tagline) {
		$this->tagline = $tagline;
	}

	/**
	 * @return string
	 */
	public function getTagline() {
		return $this->tagline;
	}

}

?>