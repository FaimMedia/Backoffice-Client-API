<?php

namespace FaimMedia\BackOfficeClient;

use FaimMedia\BackOfficeClient as Client;

use FaimMedia\BackOfficeClient\Exception\RequestException;

class Request {

	const USER_AGENT = 'FaimMedia/PHP-Backoffice-Client-API';

	private $_client;
	private $_debug = false;

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
	 * Get access token
	 */
	public function authorize(int $clientId, string $clientSecret, string $grantType = 'client_credentials') {

		$options = [
			CURLOPT_URL            => $this->createUrl('api/auth/token.json'),
			CURLOPT_USERAGENT      => self::USER_AGENT,
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
			CURLOPT_USERAGENT      => self::USER_AGENT,
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

		$json = $this->handleResponse($ch);

		return $json;
	}

	/**
	 * Executes CURL requests and handles responses
	 */
	protected function handleResponse($ch, $response = null) {

		if($response === null) {
			$response = curl_exec($ch);
		}

		$body = $response;

		$info = curl_getinfo($ch);
		$error = curl_error($ch);

		$httpCode = (int)$info['http_code'];
		$contentType = $info['content_type'];

	// check content json
		$isJson = (substr($contentType, 0, 16) == 'application/json');

		$isError = ((int)substr((string)$httpCode, 0, 2) !== 20);

		if($isJson) {
			$json = json_decode($body, true);

			if(json_last_error() !== JSON_ERROR_NONE) {
				if($error) {
					throw new RequestException('Invalid HTTP status code: '.$httpCode);
				}

				if(!$isError) {
					return true;
				}

				throw new RequestException('The response did not return a valid JSON string');
			}

			if($isError) {
				$errorMessage = null;

				if(isset($json['error'])) {
					$errorMessage = $json['error'];

					if(is_array($errorMessage)) {
						$errorMessage = $errorMessage[0]['message'];
					}
				}

				if(!empty($json['error'])) {
					error_log(json_encode($json['error'], JSON_PRETTY_PRINT));
				}

				if(!$errorMessage) {
					$errorMessage = 'Unspecified error occurred';
				}

				if($this->isDebug()) {
					var_dump($info);
					var_dump($body);
				}

				throw new RequestException('Backoffice API error: '.$errorMessage, (int)$httpCode * -1);
			}

			return $json;
		}

		if($isError) {
			if($this->isDebug()) {
				var_dump($info);
				var_dump($body);
			}

			throw new RequestException('Backoffice API error ['.$httpCode.']', (int)$httpCode * -1);
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