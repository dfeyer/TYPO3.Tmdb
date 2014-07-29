<?php
namespace TYPO3\Tmdb\Service;

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
use TYPO3\Flow\Cache\Frontend\StringFrontend;
use TYPO3\Flow\Object\ObjectManagerInterface;
use TYPO3\Flow\Utility\Arrays;
use TYPO3\Tmdb\Exception\ResponseException;

/**
 * @Flow\Scope("singleton")
 */
class TmdbService {

	/**
	 * @var string
	 */
	const apiUrlPattern = '@url/@version/@method?@query';

	/**
	 * @Flow\Inject
	 * @var ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @var array
	 */
	protected $configuration;

	/**
	 * @var StringFrontend
	 */
	protected $cache;

	/**
	 * @var array
	 */
	protected $settings = array();

	protected $error;

	protected $response;

	/**
	 * @param array $settigns
	 */
	public function injectSettings(array $settigns) {
		$this->settings = $settigns;
	}

	/**
	 * @param StringFrontend $cache Cache for foo data
	 * @return void
	 */
	public function setCache(StringFrontend $cache) {
		$this->cache = $cache;
	}

	/**
	 * Initialize object
	 */
	public function initializeObject() {
		if (!isset($this->configuration)) {
			$response = $this->sendRequest('configuration');
			if ($response->hasError()) {
				$this->error = $response->getError();
			} else {
				$this->configuration = $response->getData();
			}
		}
	}

	/**
	 * @return array
	 */
	public function getConfiguration() {
		return $this->configuration;
	}

	/**
	 * @param string $type
	 * @param object $info
	 * @return object
	 */
	protected function getAssetObject($type, $info) {
		$assetClass = ucfirst($type);
		$assetInterface = $this->settings['asset'][$assetClass]['class'];

		return $this->objectManager->get($assetInterface, $info);
	}

	/**
	 * @param string $title
	 * @return array
	 */
	public function searchByTitle($title) {
		return $this->search('movie', array(
			'query' => trim($title)
		));
	}

	/**
	 * @param string $type
	 * @param array $params
	 * @param boolean $expand
	 * @param integer $limit
	 * @return array
	 * @throws ResponseException
	 */
	public function search($type, $params, $expand = FALSE, $limit = 10) {
		$response = $this->sendRequest('search/' . $type, $params);
		if (!$response->hasError()) {

			if (count($response->getData()->results) <= $limit) {
				$results    = array();
				foreach ($response->getData()->results as $asset) {
					if ($expand) {
						$info = $this->getAssetInformations($type, $asset->id);
						if ($info) {
							$asset = $info;
						}
					}
					$results[$asset->id] = $this->getAssetObject($type, $asset);
				}
			}

			return $results;

		} else {
			throw new ResponseException(
				$response->getErrorAsString(),
				1350150189
			);
		}
	}

	/**
	 * Asset information API
	 *
	 * @param string $type
	 * @param integer $id
	 * @param bool $method
	 * @param array $params
	 * @return array
	 * @api
	 */
	public function getAssetInformations($type, $id, $method = FALSE, $params = array()) {
		$result = array();
		if ($method) {
			$response = $this->sendRequest($type . '/' . $id . '/' . $method, $params);
		} else {
			$response = $this->sendRequest($type . '/' . $id);
		}
		if ($response->hasError()) {
			$this->error = $response->getError();
		} else {
			$result = $response->getData();
		}

		return $result;
	}

	/**
	 * @param string $method
	 * @param array $params
	 * @param array $data
	 * @return \TYPO3\Tmdb\Response
	 */
	protected function sendRequest($method, $params = array(), $data = array()) {

		$response = new \TYPO3\Tmdb\Response();

		$params = $this->paramsMerge($params);

		$query = http_build_query($params);

		$url = str_replace(
			array('@url', '@version', '@method', '@query'),
			array($this->settings['api']['url'], $this->settings['api']['version'], $method, $query),
			self::apiUrlPattern
		);
		$cacheHash = sha1($url);

		// Initializing curl
		$connextionHandler = curl_init();
		if ($connextionHandler) {
			if ($this->cache->has($cacheHash)) {
				$data = $this->cache->get($cacheHash);
			} else {
				$headers   = array();
				$headers[] = 'Accept: application/json';
				$headers[] = 'Accept-Charset: utf-8';

				if (!empty($data) && is_array($data) && count($data) > 0) {
					$jsonData = json_encode($data, TRUE);
					curl_setopt($connextionHandler, CURLOPT_POSTFIELDS, json_encode($data));
					$headers[] = 'Content-Type: application/json';
					$headers[] = 'Content-Length: ' . strlen($jsonData);
				}

				curl_setopt($connextionHandler, CURLOPT_URL, $url);
				curl_setopt($connextionHandler, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($connextionHandler, CURLOPT_HEADER, FALSE);
				curl_setopt($connextionHandler, CURLOPT_RETURNTRANSFER, true);

				$data = curl_exec($connextionHandler);
				if ($data) {
					$this->cache->set($cacheHash, $data);
				}
			}

			$response->setData(json_decode($data));

			curl_close($connextionHandler);

			if (empty($response->getData()->status_code) && $response->getData() !== NULL) {
				$data = $response->getData();
				// Todo add parameter to set max limit
				if (!$this->settings['paged'] && isset($data->total_pages) && $data->page < 2) {
					$pagedResponse = $this->sendRequest($method, $params + array('page' => $data->page + 1));

					if (!$pagedResponse->hasError()) {
						$this->response = array();
						$this->error = $response->getError();
					} else {
						$data->page        = 1;
						$data->results     = array_merge($data->results, $pagedResponse->getData()->results);
						$data->total_pages = 1;
					}
				}
			} else {
				if ($response->getData() === NULL) {
					$response->registerError(1350512167, 'Empty response');
				} else {
					$response->registerError($response->getData()->status_code, $response->getData()->status_message);
				}
			}
		}

		$this->response = $response;

		return $response;
	}

	/**
	 * @param string $type
	 * @param mixed $width
	 * @param string $filePath
	 * @return string
	 * @api
	 */
	public function getImageUrl($type, $width, $filePath) {
		$preset = 'original';
		$type .= '_sizes';

		if (is_numeric($width)) {
			foreach ($this->configuration->images->$type as $size) {
				$matches = array();
				if (preg_match('/w([0-9]+)/', $size, $matches) && $matches[1] >= $width) {
					$preset = $size;
					break;
				}
			}
		} else if (in_array($width, $this->configuration->images->$type)) {
			$preset = $width;
		}

		return $this->configuration->images->base_url . $preset . $filePath;
	}

	/**
	 * @param array $params
	 * @return array
	 */
	protected function paramsMerge(array $params) {
		$defaults = array(
			'api_key'       => $this->settings['api']['key'],
			'include_adult' => $this->settings['include_adult'],
			'language'      => $this->settings['language'],
		);

		$result = Arrays::arrayMergeRecursiveOverrule($defaults, $params, FALSE, TRUE);
		$result = $this->removeEmptyElementsRecursively($result);

		return $result;
	}

	/**
	 * @param array $array
	 * @return array
	 */
	protected function removeEmptyElementsRecursively(array $array) {
		$result = $array;
		foreach ($result as $key => $value) {
			if (is_array($value)) {
				$result[$key] = $this->removeEmptyElementsRecursively($value);
				if ($result[$key] === array()) {
					unset($result[$key]);
				}
			} elseif (trim($value) === '') {
				unset($result[$key]);
			}
		}
		return $result;
	}

}