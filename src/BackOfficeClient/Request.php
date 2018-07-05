<?php

namespace FaimMedia\BackOfficeClient;

use FaimMedia\BackOfficeClient as Client;

use FaimMedia\BackOfficeClient\Exception\RequestException;

class Request {

	const API_URL_SUFFIX = '.api.mailchimp.com/3.0/';
	const USER_AGENT = 'FaimMedia/PHP-Backoffice-Client-API';

	private $_client;

	private $dataCenter;
	private $apiKey;

	private $fullApiKey;

	/**
	 * Set parent Client class
	 */
	public function __construct(Client $client) {
		$this->setClient($client);
	}

	public function getClient(): Client {
		return $this->_client;
	}

	protected function setClient(Client $client): self {
		$this->_client = $client;

		return $this;
	}

	/**
	 * Get access token
	 */
	public function authorize() {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->createUrl('api/auth/token.json'));

		$options = [
			CURLOPT_POSTFIELDS => [
				'grant_type'    => 'client_credentials',
				'client_id'     => $this->getClient()->getClientId(),
				'client_secret' => $this->getClient()->getClientSecret(),
			],
		];

		curl_setopt_array($ch, $options);

		$response = curl_exec($ch);

		$info = curl_getinfo($ch);
		$error = curl_error($ch);
	}

	/**
	 * Execute request and automatically add access token
	*/
	public function request($uri, $type = 'GET', $data = []) {
		$options = [
			CURLOPT_CUSTOMREQUEST  => strtoupper($type),
			CURLOPT_HEADER         => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERAGENT      => self::USER_AGENT,
			CURLOPT_HTTPHEADER     => [
				'Content-Type: application/json; charset=UTF-8',
				'X-Access-Token: '.$this->getClient()->getAccessToken(),
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

		$response = curl_exec($ch);

		$info = curl_getinfo($ch);
		$error = curl_error($ch);

		$httpCode = (int)$info['http_code'];

		$isError = ((int)substr((string)$httpCode, 0, 2) !== 20);

		@list($header, $body) = explode("\r\n\r\n", $response, 2);

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
			if(!empty($json['errors'])) {
				error_log(json_encode($json['errors']));
			}

			if(is_array($json) && (!empty($json['detail']) || !empty($json['title']))) {
				throw new RequestException('MailChimp API error: '.(!empty($json['detail']) ? $json['detail'] : $json['title']));
			}

			throw new RequestException('Undefined MailChimp API error, HTTP status code: '.$httpCode);
		}

		return $json;
	}

	public function createUrl($uri = null) {
		return 'https://'.$this->getDataCenter().self::API_URL_SUFFIX.$uri;
	}

}