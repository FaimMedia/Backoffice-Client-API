<?php

namespace FaimMedia\BackOfficeClient;

use FaimMedia\BackOfficeClient,
	FaimMedia\BackOfficeClient\AbstractResults,
    FaimMedia\BackOfficeClient\Request,
    FaimMedia\BackOfficeClient\Helper\Text;

use FaimMedia\BackOfficeClient\Exception\SaveException;

abstract class AbstractResult {

	private $_parent;

	protected $data = [];

	protected $isSaved = false;

	/**
	 * Constructor, sets the request instance and any data
	 */
	public function __construct($data = [], AbstractResults $parent = null) {
		if($parent) {
			$this->setParent($parent);
		}

		$this->set($data);
	}

	/**
	 * Get request of parent
	 */
	protected function getRequest(): Request {
		return $this->getParent()->getRequest();
	}

	/**
	 * Set parent class
	 */
	protected function setParent(AbstractResults $parent): self {
		$this->_parent = $parent;

		return $this;
	}

	/**
	 * Get parent
	 */
	protected function getParent(): AbstractResults {
		return $this->_parent;
	}

	/**
	 * Validate if instance has a parent
	 */
	protected function hasParent(): bool {
		return ($this->_parent instanceof AbstractArray);
	}

	/**
	 * Set the URI
	 */
	abstract protected function getUri(): string;

	/**
	 * Makes it possible to set save fields that should be ignore when sending the request
	 */
	public function ignoreSaveFields() {
		return [
			'date_created',
			'date_modified',
		];
	}

	public function __call($name, $arguments) {

		if(substr($name, 0, 3) == 'get') {
			$key = substr($name, 3);

			if($key !== strtoupper($key)) {
				$key = Text::uncamelize($key);
			}

			if(array_key_exists($key, $this->data)) {
				$value = $this->data[$key];

				if(is_array($value)) {
					$array = new self($value, $this->getParent());

					if(!empty($arguments[0])) {
						return $array->{$arguments[0]};
					}

					return $array;
				}

				return $value;
			}

			return null;
		}
	}

	public function __get($name) {
		return call_user_func([$this, 'get'.ucfirst($name)]);
	}

	public function __set($name, $value) {
		$this->data[$name] = $value;
	}

	public function get($name) {
		if(array_key_exists($name, $this->data)) {
			$value = $this->data[$name];

			if(is_array($value)) {
				return new static($this->getRequest(), $value);
			}

			return $value;
		}

		return null;
	}

	public function set($name, $value = null, $merge = true) {
		if(is_array($name)) {
			if($merge) {
				$this->data = array_merge($this->data, $name);
			} else {
				$this->data += $name;
			}
		} else {
			if($merge && array_key_exists($name, $this->data) && is_array($name, $this->data)) {
				$this->data[$name] = array_merge($this->data[$name], $name);
			} else {
				$this->data[$name] = $value;
			}
		}

		return $this;
	}

	protected function isSaved() {
		return $this->isSaved;
	}

	protected function validateSaved() {
		if(!$this->isSaved()) {
			throw new SaveException('This item is not saved and could not be modified');
		}
	}

	/**
	 * Alias for toArray
	 */
	public function getData() {
		return $this->toArray();
	}

	/**
	 * Generate an array from all data and subdata,
	 * Also ignores save field keys if parameter is specified
	 */
	public function toArray($ignoreKeys = null) {
		$array = [];

		if($ignoreKeys === true) {
			if(method_exists($this, 'ignoreSaveFields')) {
				$ignoreKeys = $this->ignoreSaveFields();
			}
		}

		foreach($this->data as $key => $data) {
			if(is_array($ignoreKeys) && in_array($key, $ignoreKeys)) {
				continue;
			}

			if($data instanceof self) {
				$array[$key] = $data->toArray();
				continue;
			}

			if($data instanceof ArrayResults) {
				$array[$key] = $data->toArray();
			}

			$array[$key] = $data;
		}

		return $array;
	}

/**
 * Common API routes
 */

	/**
	 * Delete's the current item
	 */
	public function delete() {
		$this->getRequest()->request($this->getUri().'delete.json', 'DELETE', [
			'id' => $this->getId(),
		]);

		$this->data = null;
	}

	/**
	 * Send request to API and save the currently modified data
	 */
	public function save() {
		$data = $this->toArray(true);

	// validate data
		$this->validate($data);

		if($this->getId()) {
			$response = $this->getRequest()->request($this->getUri().'save.json', 'PATCH', $data);
		} else {
			$response = $this->getRequest()->request($this->getUri().'save.json', 'POST', $data);
		}

		if($response) {
			$this->isSaved = true;

			return $this->set($response, null, false);
		}

		return false;
	}
}