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
class Collection extends AbstractAsset {

	const type = 'collection';

	/**
	 * Retrieve the parts as Movie objects
	 * @link http://help.themoviedb.org/kb/api/collection-info
	 */
	public function parts(){
		$parts = array();
		foreach($this->data['parts'] as $index => $part){
			$parts[$part->id] = new Movie($part->id);
		}
		return $parts;
	}

}

?>