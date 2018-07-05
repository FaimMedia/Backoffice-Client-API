<?php

namespace FaimMedia\BackOfficeClient;

use FaimMedia\BackOfficeClient,
    FaimMedia\BackOfficeClient\Helper\Text;

use FaimMedia\BackOfficeClient\Exception\SaveException;

class AbstractItem {

	protected $request;
	protected $data = [];

	private $_stats;
	private $_links;

	protected $isSaved = false;

	public function __construct(Request $request, $data = []) {
		$this->request = $request;

		if(array_key_exists('stats', $data)) {
			$this->stats = $data['stats'];

			unset($data['stats']);
		}

		if(array_key_exists('_links', $data)) {
			$this->_links = $data['_links'];

			unset($data['_links']);
		}

		$this->set($data);
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
					$array = new static($this->request, $value);

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
				return new static($this->request, $value);
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

	public function getData() {
		return $this->toArray();
	}

	public function toArray() {
		return $this->data;
	}

	public function getStats() {
		return $this->_stats;
	}

	public function getLinks() {
		return $this->_links;
	}

}