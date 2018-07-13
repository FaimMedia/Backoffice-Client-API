<?php

namespace FaimMedia\BackOfficeClient\Resultset\Customer;

use FaimMedia\BackOfficeClient\AbstractResults,
    FaimMedia\BackOfficeClient\Resultset\CustomerItem,
    FaimMedia\BackOfficeClient\Resultset\Customer\ContactItem as CustomerContactItem;

class Contact extends AbstractResults {

	/**
	 * Set API URI
	 */
	protected function getUri(): string {
		return 'customer/contact/';
	}

	/**
	 * Set Item class name
	 */
	protected function getItemClassName(): string {
		return CustomerContactItem::class;
	}
}