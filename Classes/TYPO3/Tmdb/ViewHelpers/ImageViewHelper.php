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
use TYPO3\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3\Tmdb\Asset\Movie;
use TYPO3\Tmdb\Service\TmdbService;

/**
 * Display Backdrop Thumbnail
 *
 * @api
 */
class ImageViewHelper extends AbstractViewHelper implements CompilableInterface {

	/**
	 * @Flow\Inject
	 * @var TmdbService
	 */
	protected $tmdbService;

	/**
	 * @param Movie $movie
	 * @param string $type
	 * @return string Rendered URI
	 * @api
	 */
	public function render(Movie $movie, $type) {
		return self::renderStatic($this->arguments, $this->buildRenderChildrenClosure(), $this->renderingContext);
	}

	/**
	 * @param array $arguments
	 * @param \Closure $renderChildrenClosure
	 * @param RenderingContextInterface $renderingContext
	 * @return string
	 */
	static public function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext) {
		$templateVariableContainer = $renderingContext->getTemplateVariableContainer();
		$output = '';
		switch ($arguments['type']) {
			case 'posters':
				$images = array(
					'small' => $arguments['movie']->getPosters('w185', 'fr'),
					'original' => $arguments['movie']->getPosters('original', 'fr')
				);
				break;
			case 'backdrops':
				$images = array(
					'small' => $arguments['movie']->getBackdrops('w185'),
					'original' => $arguments['movie']->getBackdrops('original')
				);
				break;
			default:
				throw new \InvalidArgumentException(
					'Invalid image type',
					1350563812
				);
		}
		foreach (array_keys($images['small']) as $key) {
			$templateVariableContainer->add('small', $images['small'][$key]->file_path);
			$templateVariableContainer->add('original', $images['original'][$key]->file_path);
			$output .= $renderChildrenClosure();
			$templateVariableContainer->remove('small');
			$templateVariableContainer->remove('original');
		}
		return $output;
	}
}