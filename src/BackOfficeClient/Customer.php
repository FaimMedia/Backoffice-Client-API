<?php

namespace FaimMedia\BackOfficeClient;

use FaimMedia\BackOfficeClient\CustomerItem;

class Customer extends AbstractResults {

	/**
	 * Set API URI
	 */
	protected function getUri(): string {
		return 'customer/customer/';
	}

	/**
	 * Set Item class name
	 */
	protected function getItemClassName(): string {
		return CustomerItem::class;
	}
}