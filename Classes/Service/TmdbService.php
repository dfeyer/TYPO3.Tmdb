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

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * @FLOW3\Scope("singleton")
 */
class TmdbService {

	const apiUrlPattern = '@url/@version/@method?@query';

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @var array
	 */
	protected $configuration;

	/**
	 * @var array
	 */
	protected $settings = array();

	protected $error;
	protected $response;

	/**
	 * Inject package settings
	 *
	 * @param array $settigns
	 */
	public function injectSettings(array $settigns) {
		$this->settings = $settigns;
	}

	/**
	 * Initialize object
	 */
	public function initializeObject() {
		if (!isset($this->configuration)) {
			$response = $this->sendRequest('configuration');
			if (!$response->error) {
				$this->configuration = $response->data;
			} else {
				$this->error = $response->error;
			}
		}
	}

	/**
	 * Search
	 *
	 * @param string $type
	 * @param array $params
	 * @param bool $expand
	 * @return array
	 */
	public function search($type, $params, $expand = false) {
		$results = array();

		$response = $this->sendRequest('search/' . $type, $params);
		if (!$response->error) {

			$assetClass = ucfirst($type); // NOTE: As long as we can map the methods to the class name, this works...
			$assetInterface = $this->settings['asset'][$assetClass]['class'];
			$results    = array();
			foreach ($response->data->results as $asset) {
				if ($expand) {
					$info = $this->getAssetInformations($type, $asset->id);
					if ($info) {
						$asset = $info;
					}
				}
				$results[$asset->id] = $this->objectManager->get($assetInterface, array($asset));
			}

		} else {
			throw new \TYPO3\Tmdb\Exception\ResponseException(
				$response->error['code'] . ' - ' . $response->error['message'],
				1350150189
			);
		}

		return $results;
	}

	/**
	 * Asset information API
	 *
	 * @param string $type
	 * @param int $id
	 * @param bool $method
	 * @param array $params
	 * @return array
	 * @api
	 */
	public function getAssetInformations($type, $id, $method = false, $params = array()) {
		$result = array();
		if ($method) {
			$response = $this->sendRequest($type . '/' . $id . '/' . $method, $params);
		} else {
			$response = $this->sendRequest($type . '/' . $id);
		}
		if (!$response->error) {
			$result = $response->data;
		} else {
			$this->error = $response->error;
		}

		return $result;
	}

	/**
	 * Sending requests to TMDB
	 *
	 * @param string $method
	 * @param array $params
	 * @param array $data
	 * @return stdClass
	 */
	protected function sendRequest($method, $params = array(), $data = array()) {

		$response = new \stdClass();

		$params = $this->paramsMerge($params);

		$query = http_build_query($params);

		$url = str_replace(
			array('@url', '@version', '@method', '@query'),
			array($this->settings['api']['url'], $this->settings['api']['version'], $method, $query),
			self::apiUrlPattern
		);

		if (!extension_loaded('curl')) {
			throw new \TYPO3\Tmdb\Exception\ResponseException(
				'Curl extension not loaded',
				1350153754
			);
		}

		// Initializing curl
		$connextionHandler = curl_init();
		if ($connextionHandler) {
			$headers   = array();
			$headers[] = 'Accept: application/json';
			$headers[] = 'Accept-Charset: utf-8';

			if (!empty($data) && is_array($data) && count($array) > 0) {
				$jsonData = json_encode($data, TRUE);
				curl_setopt($connextionHandler, CURLOPT_POSTFIELDS, json_encode($data));
				$headers[] = 'Content-Type: application/json';
				$headers[] = 'Content-Length: ' . strlen($jsonData);
			}

			curl_setopt($connextionHandler, CURLOPT_URL, $url);
			curl_setopt($connextionHandler, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($connextionHandler, CURLOPT_HEADER, false);
			curl_setopt($connextionHandler, CURLOPT_RETURNTRANSFER, true);

			$response->data = json_decode(curl_exec($connextionHandler));
			$response->headers = curl_getinfo($connextionHandler);

			curl_close($connextionHandler);

			if ($response->data instanceof \stdClass) {
				if (!$this->settings['paged'] && isset($response->data->total_pages) && $response
					->data->page < $response->data->total_pages
				) {
					$paged_response = $this
						->sendRequest($method, $params + array(
						'page' => $response->data->page + 1
					));

					if (!$paged_response->error) {
						$response->data->page        = 1;
						$response->data->results     = array_merge($response->data->results, $paged_response
							->data->results);
						$response->data->total_pages = 1;
					} else {
						$results     = array();
						$this->error = $response->error;
						return $results;
					}
				}

				$response->error = false;
			} else {
				var_dump($url);
				throw new \TYPO3\Tmdb\Exception\ResponseException(
					'Invalid response from TMDB',
					1350153754
				);
			}

		} else {
			$response->error = array(
				'code'    => -1,
				'message' => 'Failed to init CURL'
			);
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

		$result = \TYPO3\FLOW3\Utility\Arrays::arrayMergeRecursiveOverrule($defaults, $params, FALSE, FALSE);
		$result = \TYPO3\FLOW3\Utility\Arrays::removeEmptyElementsRecursively($result);

		return $result;
	}

}

?>