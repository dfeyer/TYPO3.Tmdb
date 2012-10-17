<?php
namespace TYPO3\Tmdb\ViewHelpers\Link;

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
class UriViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @param int $asset the identifier of the asset
	 * @param string $type the type of the asset
	 * @return string Rendered URI
	 * @api
	 */
	public function render($asset, $type) {
		$uri = 'http://www.themoviedb.org/' . $type . '/' . $asset;

		return $uri;
	}
}


?>
