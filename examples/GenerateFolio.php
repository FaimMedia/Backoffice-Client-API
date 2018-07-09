#!/usr/bin/env php
<?php

require dirname(__DIR__, 4) . '/vendor/autoload.php';

use FaimMedia\BackOfficeClient;

define('CLIENT_ID', @$argv[1]);
define('CLIENT_SECRET', @$argv[2]);

define('CUSTOMER_ID', 11);
define('CUSTOMER_CONTACT_ID', 16);

$client = new BackOfficeClient();
$client
	->setUrl('http://api.backoffice.local')
	->setClientId(CLIENT_ID)
	->setClientSecret(CLIENT_SECRET);

define('US_COUNTRY_ID', 226);

// generate folio
	$folio = $client->folio()->create([
		'customer_id'         => CUSTOMER_ID,
		'customer_contact_id' => CUSTOMER_CONTACT_ID,
		'name'                => 'Kruispuntdatabank',

	]);

	var_dump($folio);