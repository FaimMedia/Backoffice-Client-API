<?php

namespace FaimMedia;

use FaimMedia\BackOfficeClient\Request,
	FaimMedia\BackOfficeClient\Folio,
	FaimMedia\BackOfficeClient\Invoice;

use DateTime;

class BackOfficeClient {

	protected $_url;
	protected $_clientId;
	protected $_clientSecret;

	protected $_accessToken;
	protected $_accessTokenExpire;

	private $request;

	/**
	 * Initialize request
	 */
	public function __construct() {
		$this->request = new Request($this);
	}

	/**
	 * Set the server URL
	 */
	public function setUrl(string $url): self {
		$this->_url = $url;

		return $this;
	}

	/**
	 * Get the server URL
	 */
	public function getUrl(): string {
		return $this->_url;
	}

	/**
	 * Set client ID
	 */
	public function setClientId($clientId): self {
		$this->_clientId = $clientId;

		return $this;
	}

	/**
	 * Set client secret
	 */
	public function setClientSecret(string $clientSecret): self {
		$this->_clientSecret = $clientSecret;

		return $this;
	}

	/**
	 * Gets an active access token
	 * If the access token is empty it attempts to get one
	 * If the access token has expired, it attempts to get a new one with the refresh token
	 */
	public function getAccessToken(): string {
		if(!$this->_accessToken || $this->isAccessTokenExpired()) {

			$this->request->authorize();

		}

		return $this->_accessToken;
	}

	/**
	 * Set a custom access token
	 */
	public function setAccessToken(string $accessToken, DateTime $accessTokenExpire): self {
		$this->_accessToken = $accessToken;
		$this->_accessTokenExpire = $accessTokenExpire;

		return $this;
	}

	/**
	 * Checks if access token has expired
	 */
	public function isAccessTokenExpired(): bool {
		$now = new DateTime();

		if($this->_accessTokenExpire instanceof DateTime && $this->_accessTokenExpire > $now) {
			return false;
		}

		return true;
	}

	/**
	 * Get folio
	 */
	public function folio($id = null) {
		$folio = new Folio($this->request);

		if($id) {
			return $folio->getById($id);
		}

		return $folio;
	}

	/**
	 * Get invoice
	 */
	public function invoice($id = null) {
		$invoice = new Invoice($this->request);

		if($id) {
			return $invoice->getById($id);
		}

		return $invoice;
	}

	public static function uncamelize($str) {
		$str = preg_replace(
			["/([A-Z]+)/", "/_([A-Z]+)([A-Z][a-z])/"],
			["_$1", "_$1_$2"],
			lcfirst($str)
		);

		return strtolower($str);
	}

}