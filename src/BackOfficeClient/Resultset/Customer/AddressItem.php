<?php

namespace FaimMedia\BackOfficeClient\Resultset\Customer;

use FaimMedia\BackOfficeClient\AbstractResult;

class AddressItem extends AbstractResult {

	/**
	 * Set API URI
	 */
	protected function getUri(): string {
		return 'customer/address/';
	}
}