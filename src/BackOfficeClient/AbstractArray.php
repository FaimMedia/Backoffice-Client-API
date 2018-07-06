<?php

namespace FaimMedia\BackOfficeClient;

use ArrayAccess,
    Iterator,
    Countable;

/**
 * Custom array access class
 */

abstract class AbstractArray implements ArrayAccess, Iterator, Countable {

	protected $request;
	protected $_data;

	/**
	 * Constructor to set
	 */
	public function __construct($request, $data = []) {
		$this->request = $request;

		$this->_data = $data;
	}

	/**
	 * Get all data
	 */
	public function getData(): ?array {
		return $this->_data;
	}

	/**
	 * Get offset
	 */
	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			$this->_data[] = $value;
		} else {
			$this->_data[$offset] = $value;
		}
	}

	/**
	 * Check if key exists
	 */
	public function offsetExists($offset) {
		return isset($this->_data[$offset]);
	}

	/**
	 * Unset key
	 */
	public function offsetUnset($offset) {
		unset($this->_data[$offset]);
	}

	/**
	 * Get item by key
	 */
	public function offsetGet($offset) {
		return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
	}

	/**
	 * Get data count
	 */
	public function count() {
		return count($this->_data);
	}

	/**
	 * Rewind iterator
	 */
	public function rewind() {
		return reset($this->_data);
	}

	/**
	 * Get current iterator position
	 */
	public function current() {
		return current($this->_data);
	}

	/**
	 * Get current key position
	 */
	public function key() {
		return key($this->_data);
	}

	/**
	 * Increase iterator
	 */
	public function next() {
		return next($this->_data);
	}

	/**
	 * Check if current iterator position is valid
	 */
	public function valid() {
		return isset($this->_data[$this->key()]);
	}

	/**
	 * Get first item
	 */
	public function getFirst() {
		foreach($this->_data as $item) {
			return $item;
		}
	}

	/**
	 * Get last item
	 */
	public function getLast() {
		$array = $this->_data;

		if(!$array) {
			return null;
		}

		end($array);
		$key = key($array);

		return $this->_data[$key];
	}

	/**
	 * Convert all to array
	 */
	public function toArray(): array {
		$results = [];
		foreach($this as $item) {
			if($item instanceof AbstractResult) {
				$results[] = $item->toArray();
				continue;
			}

			$results[] = $item;
		}

		return $results;
	}
}