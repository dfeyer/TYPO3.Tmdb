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
use TYPO3\Flow\Reflection\ObjectAccess;
use TYPO3\Tmdb\Service\TmdbService;
use TYPO3\Tmdb\Utility\Strings;

/**
 * Abstract Asset
 */
abstract class AbstractAsset {

	/**
	 * @var integer
	 */
	protected $id = null;

	/**
	 * @var TmdbService
	 */
	protected $tmdbService;

	/**
	 * @param integer $data
	 */
	public function __construct($data) {
		$this->tmdbService = new TmdbService();
		if (is_numeric($data)) {
			$data = $this->tmdbService->getAssetInformations(static::type, $data);
		}

		foreach ($data as $key => $value) {
			$propertyName = Strings::underscoredToLowerCamelCase($key);
			ObjectAccess::setProperty($this, $propertyName, $value);
		}
	}

	/**
	 * @param integer $id
	 */
	public function setId($id) {
		$this->id = (integer)$id;
	}

	/**
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Get an image base method
	 *
	 * @param string $type backdrop, poster, profile, logo, ...
	 * @param bool|string $size integer or preset name
	 * @param bool $random
	 * @param string $language optional language to return the image in
	 * @return string
	 */
	public function getImageUrl($type, $size = FALSE, $random = FALSE, $language = NULL) {
		$image    = NULL;
		$typeset  = $type . 's';
		$path_key = $type . '_path';

		if ($random === TRUE) {
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