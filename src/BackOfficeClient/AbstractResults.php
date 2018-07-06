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
	public function __construct($data = [], Request $request) {
		$this->_data = $data;
		$this->_request = $request;
	}

	/**
	 * Get request instance
	 */
	public function getRequest(): Request {
		return $this->_request;
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

			return new $className($response, $this);
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