<?php

namespace FaimMedia\BackOfficeClient\Resultset;

use FaimMedia\BackOfficeClient\AbstractResult,
	FaimMedia\BackOfficeClient\Resultset\CustomerItem,
	FaimMedia\BackOfficeClient\Resultset\Customer\ContactItem as CustomerContactItem,
	FaimMedia\BackOfficeClient\Resultset\Customer\DepartmentItem as CustomerDepartmentItem,
	FaimMedia\BackOfficeClient\Resultset\Folio\LineItem as FolioLineItem,
	FaimMedia\BackOfficeClient\Resultset\InvoiceItem as InvoiceItem;

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

	/**
	 * Custom generate end-point
	 */
	public function generate(): InvoiceItem {
		$this->validateSaved();

		$response = $this->getRequest()->request($this->getUri().'generate.json', 'PUT', [
			'id' => $this->getId(),
		]);

		$data = $response['data'];

		$newData = [
			'id'             => $data['invoice_id'],
			'invoice_number' => $data['invoice_number'],
			'folio_id'       => $data['id'],
		];

		$invoice = new InvoiceItem($newData, $this->getParent());

		return $invoice;
	}
}