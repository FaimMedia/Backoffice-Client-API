<?php

namespace FaimMedia\BackOfficeClient\Resultset;

use FaimMedia\BackofficeClient\AbstractResults,
    FaimMedia\BackOfficeClient\Resultset\CustomerItem,
    FaimMedia\BackOfficeClient\Resultset\Customer\Contact as CustomerContact,
    FaimMedia\BackOfficeClient\Resultset\Customer\Address as CustomerAddress;

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

	/**
	 * Get customer address relation
	 */
	public function address($id = null) {
		$customerAddress = new CustomerAddress([], $this->getRequest());

		if($id) {
			return $customerAddress->getById($id);
		}

		return $customerAddress;
	}
	/**
	 * Get customer contact relation
	 */
	public function contact($id = null) {
		$customerContact = new CustomerContact([], $this->getRequest());

		if($id) {
			return $customerContact->getById($id);
		}

		return $customerContact;
	}
}