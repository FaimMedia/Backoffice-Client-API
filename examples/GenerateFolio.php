#!/usr/bin/env php
<?php

require dirname(__DIR__, 4) . '/vendor/autoload.php';

use FaimMedia\BackOfficeClient,
    FaimMedia\BackOfficeClient\Resultset\Folio\Line as FolioLine,
    FaimMedia\BackOfficeClient\Resultset\Folio\LineItem as FolioLineItem;

define('CLIENT_ID', @$argv[1]);
define('CLIENT_SECRET', @$argv[2]);

define('CUSTOMER_ID', 11);
define('CUSTOMER_CONTACT_ID', 3);

$client = new BackOfficeClient();
$client
	->setUrl('http://api.backoffice.local')
	->setClientId(CLIENT_ID)
	->setClientSecret(CLIENT_SECRET);

// set folio lines
	$folioLines = [
		new FolioLineItem([
			'article_id'     => 5,
			'title'          => 'Small plan',
			'frequency_type' => FolioLineItem::FREQUENCY_TYPE_ONCE,
			'amount'         => 10.00,
		]),
		new FolioLineItem([
			'article_id'     => 6,
			'title'          => 'Credit card fee',
			'frequency_type' => FolioLineItem::FREQUENCY_TYPE_ONCE,
			'amount'         => 3.5,
		]),
	];

	$folioLines = new FolioLine($folioLines);

// generate folio
	$folio = $client->folio()->create([
		'customer_id'         => CUSTOMER_ID,
		'customer_contact_id' => CUSTOMER_CONTACT_ID,
		'name'                => 'Kruispuntdatabank',
		'folio_line'          => $folioLines,
		'vat_reversed'        => 0,
		'prepaid_method_id'   => 2,
	]);

	var_dump($folio->toArray());

