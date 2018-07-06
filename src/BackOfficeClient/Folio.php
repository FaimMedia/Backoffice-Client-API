<?php

namespace FaimMedia\BackOfficeClient;

use FaimMedia\BackOfficeClient\FolioItem;

class Folio extends AbstractResults {

	/**
	 * Set API URI
	 */
	protected function getUri(): string {
		return 'folio/folio/';
	}

	/**
	 * Set Item class name
	 */
	protected function getItemClassName(): string {
		return FolioItem::class;
	}
}