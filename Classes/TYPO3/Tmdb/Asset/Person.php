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
class Person extends AbstractAsset {

	const type = 'person';

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $character;

	/**
	 * @var string
	 */
	protected $department;

	/**
	 * @link http://help.themoviedb.org/kb/api/person-credits
	 */
	public function credits($language=null){
		$info = $this->tmdbService->getAssetInformations(self::type, $this->id, 'credits', array('language'=>$language));
		return $info;
	}

	/**
	 * @link http://help.themoviedb.org/kb/api/person-images
	 */
	public function images($size=false){
		$info = $this->tmdbService->getAssetInformations(self::type, $this->id, 'images');
		foreach($info as $type => $images){
			if(!is_array($images)) continue;
			foreach($images as $index => $data) {
				if($size){
					$info->{$type}[$index]->file_path = $this->tmdbService->getImageUrl(substr($type, 0, strlen($type)-1), $size, $data->file_path);
				}
			}
		}
		return $info;
	}

	////////////////////////////////////////////////////////////////////
	// Additional helpers

	/**
	 * Get the profile picture
	 */
	public function profile($size=false) {
		return $this->getImageUrl('profile', $size);
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $character
	 */
	public function setCharacter($character) {
		$this->character = $character;
	}

	/**
	 * @return string
	 */
	public function getCharacter() {
		return $this->character;
	}

	/**
	 * @param string $department
	 */
	public function setDepartment($department) {
		$this->department = $department;
	}

	/**
	 * @return string
	 */
	public function getDepartment() {
		return $this->department;
	}

}

?>