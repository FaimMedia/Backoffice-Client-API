<?php

namespace FaimMedia\BackOfficeClient\Resultset\Folio;

use FaimMedia\BackOfficeClient\AbstractResults,
    FaimMedia\BackOfficeClient\Resultset\Folio\LineItem as FolioLineItem;

class Line extends AbstractResults {

	/**
	 * Set API URI
	 */
	protected function getUri(): string {
		return 'folio/line/';
	}

	/**
	 * Set Item class name
	 */
	protected function getItemClassName(): string {
		return FolioLineItem::class;
	}
}