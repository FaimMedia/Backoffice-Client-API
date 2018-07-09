<?php

namespace FaimMedia\BackOfficeClient\Resultset\Customer;

use FaimMedia\BackOfficeClient\AbstractResults,
    FaimMedia\BackOfficeClient\Resultset\CustomerItem,
    FaimMedia\BackOfficeClient\Resultset\Customer\AddressItem as CustomerAddressItem;

class Address extends AbstractResults {

	/**
	 * Set API URI
	 */
	protected function getUri(): string {
		return 'customer/address/';
	}

	/**
	 * Set Item class name
	 */
	protected function getItemClassName(): string {
		return CustomerAddressItem::class;
	}
}