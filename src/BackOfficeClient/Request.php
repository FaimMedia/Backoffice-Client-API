<?php

namespace FaimMedia\BackOfficeClient;

use FaimMedia\BackOfficeClient as Client;

use FaimMedia\BackOfficeClient\Exception\RequestException;

class Request {

	private $_client;
	private $_debug = false;

	protected $_curlOptions = [
		CURLOPT_CONNECTTIMEOUT_MS => 3000,
		CURLOPT_TIMEOUT_MS        => 3000,
		CURLOPT_FAILONERROR       => true,
		CURLOPT_USERAGENT         => 'FaimMedia/PHP-Backoffice-Client-API',
	];

	/**
	 * Set parent Client class
	 */
	public function __construct(Client $client) {
		$this->setClient($client);
	}

	/**
	 * Get parent client
	 */
	public function getClient(): Client {
		return $this->_client;
	}

	/**
	 * Set client
	 */
	protected function setClient(Client $client): self {
		$this->_client = $client;

		return $this;
	}

	/**
	 * Set debug mode
	 */
	public function setDebug(bool $debug = false): self {
		$this->_debug = $debug;

		return $this;
	}

	/**
	 * Check if in debug mode
	 */
	public function isDebug(): bool {
		return $this->_debug;
	}

	/**
	 * Sets default curl options
	 * Use the merge parameter to replace existing values
	 */
	public function setCurlOptions(array $options = [], $merge = true): self {
		if($merge) {
			$this->_curlOptions = array_merge(
				$this->_curlOptions,
				$options
			);

			return $this;
		}

		$this->_curlOptions += $options;

		return $this;
	}

	/**
	 * Returns an array with all specified default Curl options
	 */
	public function getCurlOptions(): array {
		return $this->_curlOptions;
	}

	/**
	 * Get access token
	 */
	public function authorize(int $clientId, string $clientSecret, string $grantType = 'client_credentials') {

		$options = [
			CURLOPT_URL            => $this->createUrl('api/auth/token.json'),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => [
				'grant_type'    => $grantType,
				'client_id'     => $clientId,
				'client_secret' => $clientSecret,
			],
			CURLOPT_HTTPHEADER     => [
				'Expect: ',
			],
		];

		if($this->isDebug()) {
			$options[CURLINFO_HEADER_OUT] = true;
		}

		$options += $this->getCurlOptions();

		$ch = curl_init();
		curl_setopt_array($ch, $options);

		$json = $this->handleResponse($ch);

		return $json;
	}

	/**
	 * Execute request and automatically add access token
	*/
	public function request($uri, $type = 'GET', $data = [], $dataType = 'json') {

		$contentType = 'application/json';
		if($dataType == 'binary') {
			$contentType = 'application/octet-stream';
		}

		$options = [
			CURLOPT_URL            => $this->createUrl($uri),
			CURLOPT_CUSTOMREQUEST  => strtoupper($type),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER     => [
				'Expect: ',
				'Content-Type: '.$contentType,
				'X-Access-Token: '.$this->getClient()->getAccessTokenHeader(),
			],
		];

		if($this->isDebug()) {
			$options[CURLINFO_HEADER_OUT] = true;
		}

		$options += $this->getCurlOptions();

		if(!empty($data)) {
			if($type != 'GET') {

				$json = json_encode($data, JSON_PRETTY_PRINT);

			// is post body
				$options[CURLOPT_POST] = true;
				$options[CURLOPT_POSTFIELDS] = $json;

				$options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
				$options[CURLOPT_HTTPHEADER][] = 'Content-Length: '.strlen($json);

			} else {

			// is url query
				$url = $options[CURLOPT_URL];

				$url .= (strpos($url, '?') !== false ? '&' : '?');
				$url .= http_build_query($data);

				$options[CURLOPT_URL] = $url;
			}
		}

		$ch = curl_init();
		curl_setopt_array($ch, $options);

		return $this->handleResponse($ch, null, $dataType);
	}

	/**
	 * Parse headers string to headers array
	 */
	protected function parseHeaders(string $headers): array {

		$headers = explode("\r\n", $headers);

		$keys = [];
		$values = [];

		foreach($headers as $header) {
			$name = strstr($header, ':', true);

			$keys[] = strtolower($name);
			$values[] = trim(substr($header, strlen($name) + 1));
		}

		$headers = array_combine($keys, $values);

		array_filter($headers);

		return $headers;
	}

	/**
	 * Executes CURL requests and handles responses
	 */
	protected function handleResponse($ch, $response = null, $dataType = 'json') {

		if($response === null) {
			$response = curl_exec($ch);
		}

		$body = $response;

		$info = curl_getinfo($ch);
		$error = curl_error($ch);

		$httpCode = (int)$info['http_code'];
		$contentType = $info['content_type'];

	// check content json
		$isJson = (substr($contentType, 0, 16) == 'application/json' || $dataType == 'json');

		$isError = ((int)substr((string)$httpCode, 0, 2) !== 20);

		$path = parse_url($info['url'], PHP_URL_PATH);

		$exception = new RequestException('Unspecified error occurred');
		$exception->setUri($path);
		$exception->setResponseCode($httpCode);
		$exception->setResponseBody($body);

		if($isJson) {
			$json = json_decode($body, true);

			if(json_last_error() !== JSON_ERROR_NONE) {
				if($isError && $httpCode) {
					$exception->setMessage('Invalid HTTP status code');
					throw $exception;
				}

				$exception->setMessage('The response did not return a valid JSON string');
				throw $exception;
			}

			if($isError) {
				if(isset($json['error'])) {
					$errorMessage = $json['error'];

					if(is_array($errorMessage)) {
						$exception->setMessage($errorMessage[0]['message']);
					}
				}

				if(!empty($json['error'])) {
					error_log(json_encode($json['error'], JSON_PRETTY_PRINT));
				}

				throw $exception;
			}

			return $json;
		}

		if($isError) {
			throw $exception;
		}

		return $body;
	}

	/**
	 * Generate full-URL by prepending protocol and domain to URI string
	 */
	public function createUrl($uri = null): string {
		return $this->getClient()->getUrl().$uri;
	}
}