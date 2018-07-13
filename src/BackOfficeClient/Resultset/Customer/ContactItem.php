<?php

namespace FaimMedia\BackOfficeClient\Resultset\Customer;

use FaimMedia\BackOfficeClient\AbstractResult,
    FaimMedia\BackOfficeClient\Resultset\CustomerItem;

class ContactItem extends AbstractResult {

	/**
	 * Set API URI
	 */
	protected function getUri(): string {
		return 'customer/contact/';
	}

	/**
	 * Relations
	 */
	protected function getRelations(): array {
		return [
			'customer'  => CustomerItem::class,
		];
	}
}