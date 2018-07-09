<?php

namespace FaimMedia\BackOfficeClient\Resultset\Folio;

use FaimMedia\BackOfficeClient\AbstractResult,
    FaimMedia\BackOfficeClient\Resultset\FolioItem;

class LineItem extends AbstractResult {

	const FREQUENCY_TYPE_ONCE = 0;

	/**
	 * Set API URI
	 */
	protected function getUri(): string {
		return 'folio/line/';
	}

	/**
	 * Relations
	 */
	protected function getRelations(): array {
		return [
			'folio'  => FolioItem::class,
		];
	}
}