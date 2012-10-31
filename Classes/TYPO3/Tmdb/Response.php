<?php
namespace TYPO3\Tmdb;

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
 * TMDb Webservice Response
 */
class Response {

	/**
	 * @var array
	 */
	protected $error = array();

	/**
	 * @var \stdClass
	 */
	protected $data = array();

	/**
	 * @param array $error
	 */
	public function setError($error) {
		$this->error = $error;
	}

	/**
	 * @return array
	 */
	public function getError() {
		return $this->error;
	}

	/**
	 * @param string $code
	 * @param string $message
	 */
	public function registerError($code, $message) {
		$this->error['code']    = $code;
		$this->error['message'] = $message;
	}

	/**
	 * @return string
	 */
	public function getErrorAsString() {
		return $this->error['code'] . ' - ' . $this->error['message'];
	}

	/**
	 * @return bool
	 */
	public function hasError() {
		if (count($this->error)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * @param \stdClass $data
	 */
	public function setData($data) {
		$this->data = $data;
	}

	/**
	 * @return \stdClass
	 */
	public function getData() {
		return $this->data;
	}

}

?>