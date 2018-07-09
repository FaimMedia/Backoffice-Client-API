<?php

namespace FaimMedia\BackOfficeClient\Resultset;

use FaimMedia\BackOfficeClient\AbstractResult;

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
			'folio_line'  => FolioLineItem::class,
		];
	}
}