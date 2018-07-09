<?php

namespace FaimMedia\BackOfficeClient\Resultset;

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