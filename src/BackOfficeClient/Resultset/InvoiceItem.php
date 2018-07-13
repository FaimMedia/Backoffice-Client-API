<?php

namespace FaimMedia\BackOfficeClient\Resultset;

use FaimMedia\BackOfficeClient\AbstractResult,
	FaimMedia\BackOfficeClient\Resultset\CustomerItem,
	FaimMedia\BackOfficeClient\Resultset\Customer\ContactItem as CustomerContactItem,
	FaimMedia\BackOfficeClient\Resultset\Customer\DepartmentItem as CustomerDepartmentItem,
	FaimMedia\BackOfficeClient\Resultset\Folio\LineItem as FolioLineItem;

class InvoiceItem extends AbstractResult {

	/**
	 * Set API URI
	 */
	protected function getUri(): string {
		return 'invoice/invoice/';
	}

	/**
	 * Relations
	 */
	protected function getRelations(): array {
		return [
			'customer'            => CustomerItem::class,
			//'customer_department' => CustomerDepartmentItem::class,
			'customer_contact'    => CustomerContactItem::class,
		];
	}

	/**
	 * Disable delete method
	 */
	public function delete(): bool {
		return false;
	}

	/**
	 * Disable save method
	 */
	public function save(): bool {
		return false;
	}

	/**
	 * Custom view end-point
	 */
	public function view(): string {

		$response = $this->getRequest()->request($this->getUri().'view/'.$this->getInvoiceNumber(), 'GET', [], 'binary');

		return $response;
	}
}