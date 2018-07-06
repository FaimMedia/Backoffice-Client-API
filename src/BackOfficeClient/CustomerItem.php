<?php

namespace FaimMedia\BackOfficeClient;

use FaimMedia\BackOfficeClient\AbstractResult;

use UnexpectedValueException;

class CustomerItem extends AbstractResult {

	/**
	 * Set API URI
	 */
	protected function getUri(): string {
		return 'customer/customer/';
	}
}