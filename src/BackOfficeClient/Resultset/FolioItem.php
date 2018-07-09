<?php

namespace FaimMedia\BackOfficeClient\Resultset;

use FaimMedia\BackOfficeClient\AbstractResult,
	FaimMedia\BackOfficeClient\Resultset\CustomerItem,
	FaimMedia\BackOfficeClient\Resultset\Customer\ContactItem as CustomerContactItem,
	FaimMedia\BackOfficeClient\Resultset\Customer\DepartmentItem as CustomerDepartmentItem,
	FaimMedia\BackOfficeClient\Resultset\Folio\LineItem as FolioLineItem;

class FolioItem extends AbstractResult {

	/**
	 * Set API URI
	 */
	protected function getUri(): string {
		return 'folio/folio/';
	}

	/**
	 * Relations
	 */
	protected function getRelations(): array {
		return [
			'customer'            => CustomerItem::class,
			//'customer_department' => CustomerDepartmentItem::class,
			'customer_contact'    => CustomerContactItem::class,
			'folio_line'          => FolioLineItem::class,
		];
	}
}