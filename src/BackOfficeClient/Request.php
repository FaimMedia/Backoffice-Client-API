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
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->createUrl('api/auth/token.json'));

		$options = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST           => 1,
			CURLOPT_POSTFIELDS     => [
				'grant_type'    => $grantType,
				'client_id'     => $clientId,
				'client_secret' => $clientSecret,
			],
			CURLOPT_HTTPHEADER     => [
				'Content-Type: application/json; charset=UTF-8',
			],
		];

		curl_setopt_array($ch, $options);

		$json = $this->handleResponse($ch);

		var_dump($json);
		die;
	}

	/**
	 * Execute request and automatically add access token
	*/
	public function request($uri, $type = 'GET', $data = []) {
		$options = [
			CURLOPT_CUSTOMREQUEST  => strtoupper($type),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERAGENT      => self::USER_AGENT,
			CURLOPT_HTTPHEADER     => [
				'Content-Type: application/json; charset=UTF-8',
				'X-Access-Token: '.$this->getClient()->getAccessTokenHeader(),
			],
		];

		if($type != 'GET' && !empty($data)) {
			$json = json_encode($data);

			$options += [
				CURLOPT_POSTFIELDS => $json,
			];
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->createUrl($uri));
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

		$info = curl_getinfo($ch);
		$error = curl_error($ch);

		$httpCode = (int)$info['http_code'];

		$isError = ((int)substr((string)$httpCode, 0, 2) !== 20);

		$json = json_decode($response, true);

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
			$errorMessage = 'Unspecified error occurred';

			if(isset($json['error'])) {
				$errorMessage = $json['error'];

				if(is_array($errorMessage)) {
					$errorMessage = $errorMessage[0]['message'];
				}
			}

			if(!empty($json['error'])) {
				error_log(json_encode($json['error'], JSON_PRETTY_PRINT));
			}

			throw new RequestException('Backoffice API error: '.$errorMessage, (int)$httpCode * -1);
		}

		return $json;
	}

	/**
	 * Generate full-URL by prepending protocol and domain to URI string
	 */
	public function createUrl($uri = null): string {
		return $this->getClient()->getUrl().$uri;
	}
}