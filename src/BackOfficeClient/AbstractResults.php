<?php

namespace FaimMedia\BackOfficeClient;

use FaimMedia\BackOfficeClient\AbstractResult,
    FaimMedia\BackOfficeClient\Request;

use FaimMedia\BackOfficeClient\Exception\ItemException;

abstract class AbstractResults extends AbstractArray {

	protected $_request;

	/**
	 * Custom constructor to set both data and request method
	 */
	public function __construct($data = [], Request $request = null) {
		$this->_data = $data;
		$this->_request = $request;
	}

	/**
	 * Get request instance
	 */
	public function getRequest(): Request {

		if(!$this->hasRequest()) {
			throw new Exception('This object has no Request handler');
		}

		return $this->_request;
	}

	/**
	 * Check request instance
	 */
	public function hasRequest(): bool {
		return ($this->_request instanceof Request);
	}

	/**
	 * Set request instance
	 */
	public function setRequest(Request $request): self {

	// fail silently
		if($this->hasRequest()) {
			return $this;
		}

		$this->_request = $request;

		return $this;
	}

	abstract protected function getUri(): string;
	abstract protected function getItemClassName(): string;

	/**
	 * Get individual item by ID
	 */
	public function getById($id) {
		if($this->offsetExists($id)) {
			return $this->offsetGet($id);
		}

		$response = $this->getRequest()->request($this->getUri().'get.json', 'GET', [
			'id' => $id,
		]);

		if($response) {
			$className = $this->getItemClassName();

			return new $className($response['data'], $this);
		}

		throw new ItemException('Requested item `'.$id.'` does not exists');
	}

	/**
	 * Get all items
	 */
	public function getAll($cache = true): self {
		if(!$this->count() || !$cache) {
			$response = $this->getRequest()->request($this->getUri().'get-all.json');

			foreach($response['data'] as $item) {
				$className = $this->getItemClassName();

				$item = new $className($item, $this);

				$this->offsetSet($item->getId(), $item);
			}
		}

		return $this;
	}

	/**
	 * Create item
	 */
	public function create($data): AbstractResult {
		$className = $this->getItemClassName();

		$item = new $className($data, $this);

		return $item->save();
	}
}