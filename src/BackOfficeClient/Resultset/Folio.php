<?php

namespace FaimMedia\BackOfficeClient\Resultset;

use FaimMedia\BackofficeClient\AbstractResults,
    FaimMedia\BackOfficeClient\Resultset\FolioItem;

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

	/**
	 * Get folio line relation
	 */
	public function line($id = null) {
		$folioLine = new FolioLine([], $this->getRequest());

		if($id) {
			return $folioLine->getById($id);
		}

		return $folioLine;
	}
}