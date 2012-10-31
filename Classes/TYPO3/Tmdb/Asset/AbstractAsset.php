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
 * Asset Interface
 */
abstract class AbstractAsset {

	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * @var \TYPO3\Tmdb\Service\TmdbService
	 */
	protected $tmdbService;

	/**
	 * @param int $data
	 */
	public function __construct($data) {
		$this->tmdbService = new \TYPO3\Tmdb\Service\TmdbService();

		if (is_numeric($data)) {
			$data = $this->tmdbService->getAssetInformations(static::type, $data);
		}

		foreach ($data as $key => $value) {
			$propertyName = \TYPO3\Tmdb\Utility\Strings::underscoredToLowerCamelCase($key);
			\TYPO3\Flow\Reflection\ObjectAccess::setProperty($this, $propertyName, $value);
		}
	}

	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = (int)$id;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Get an image base method
	 *
	 * @param string $type backdrop, poster, profile, logo, ...
	 * @param string $size integer or preset name
	 * @param bool   $random
	 * @param string $language optional language to return the image in
	 */
	public function getImageUrl($type, $size = false, $random = false, $language = null) {
		$image    = false;
		$typeset  = $type . 's'; // multiple
		$path_key = $type . '_path';

		// Todo create a specific helper for this
		if ($random) {
			$images = $this->getImages($language, false);
			if (count($images) > 1) {
				$index             = rand(0, count($images->{$typeset}) - 1);
				$this->data[$path_key] = $images->data[$typeset][$index]->file_path;
			}
		}

		if (isset($this->{$path_key})) {
			if ($size) {
				$image = $this->tmdbService->getImageUrl($type, $size, $this->data[$path_key]);
			} else {
				$image = $this->{$path_key};
			}
		}

		return $image;
	}
}

?>