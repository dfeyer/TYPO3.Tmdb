<?php
namespace TYPO3\Tmdb\ViewHelpers;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Tmdb".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Display Backdrop Thumbnail
 *
 * @api
 */
class ImageViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper implements \TYPO3\Fluid\Core\ViewHelper\Facets\CompilableInterface {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Tmdb\Service\TmdbService
	 */
	protected $tmdbService;

	/**
	 * @param \TYPO3\Tmdb\Asset\Movie $movie
	 * @param string $type
	 * @return string Rendered URI
	 * @api
	 */
	public function render(\TYPO3\Tmdb\Asset\Movie $movie, $type) {
		return self::renderStatic($this->arguments, $this->buildRenderChildrenClosure(), $this->renderingContext);
	}

	/**
	 * @param array $arguments
	 * @param \Closure $renderChildrenClosure
	 * @param \TYPO3\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
	 * @return string
	 * @throws \TYPO3\Fluid\Core\ViewHelper\Exception
	 */
	static public function renderStatic(array $arguments, \Closure $renderChildrenClosure, \TYPO3\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
		$templateVariableContainer = $renderingContext->getTemplateVariableContainer();
		$output = '';
		switch ($arguments['type']) {
			case 'posters':
				$images = $arguments['movie']->getPosters('w185', 'fr');
				break;
			case 'backdrops':
				$images = $arguments['movie']->getBackdrops('w300');
				break;
			default:
				throw new \InvalidArgumentException(
					'Invalid image type',
					1350563812
				);
		}
		foreach ($images as $image) {
			$src = $image->file_path;
			$templateVariableContainer->add('src', $src);
			$output .= $renderChildrenClosure();
			$templateVariableContainer->remove('src');
		}
		return $output;
	}
}


?>
