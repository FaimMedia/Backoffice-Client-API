<?php

namespace FaimMedia\BackOfficeClient\Exception;

use Exception;

abstract class BaseException extends Exception {

	protected $_uri;
	protected $_responseCode;
	protected $_responseBody;

	/**
	 * Set the current Request URI
	 */
	public function setUri(string $uri = null): self {
		$this->_uri = $uri;

		return $this;
	}

	/**
	 * Get the requests Uri
	 */
	public function getUri(): ?string {
		return $this->_uri;
	}

	/**
	 * Set the HTTP response code
	 */
	public function setResponseCode(int $responseCode = null): self {
		$this->_responseCode = $responseCode;

		return $this;
	}

	/**
	 * Get the HTTP response code
	 */
	public function getResponseCode(): ?int {
		return $this->_responseCode;
	}

	/**
	 * Set response body
	 */
	public function setResponseBody(string $body = null): self {
		$this->_responseBody = $body;

		return $this;
	}

	/**
	 * Get response body
	 */
	public function getResponseBody(): ?string {
		return $this->_responseBody;
	}

}