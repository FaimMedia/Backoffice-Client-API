<?php

namespace FaimMedia\BackOfficeClient\Resultset;

use FaimMedia\BackofficeClient\AbstractResults,
    FaimMedia\BackOfficeClient\Resultset\InvoiceItem;

class Invoice extends AbstractResults {

	/**
	 * Set API URI
	 */
	protected function getUri(): string {
		return 'invoice/invoice/';
	}

	/**
	 * Set Item class name
	 */
	protected function getItemClassName(): string {
		return InvoiceItem::class;
	}

	/**
	 * Get Invoice PDF file
	 */
	public function viewByInvoiceNumber($invoiceNumber) {
		$invoiceItem = new InvoiceItem([
			'invoice_number' => $invoiceNumber,
		], $this);

		return $invoiceItem->view();
	}
}