#!/usr/bin/env php
<?php

require dirname(__DIR__, 4) . '/vendor/autoload.php';

use FaimMedia\BackOfficeClient;

define('CLIENT_ID', @$argv[1]);
define('CLIENT_SECRET', @$argv[2]);

define('CUSTOMER_ID', 11);
define('COUNTRY_ID', 226);
define('LANGUAGE_ID', 1);

$client = new BackOfficeClient();
$client
	->setUrl('http://api.backoffice.local')
	->setClientId(CLIENT_ID)
	->setClientSecret(CLIENT_SECRET);

// generate customer
	$customerContact = $client->customer()->contact()->create([
		'customer_id'       => CUSTOMER_ID,
		'name_title'        => 1,
		'name_first'        => 'S.B.',
		'name_last'         => 'SquarePants',
		'country_id'        => COUNTRY_ID,
		'language_id'       => LANGUAGE_ID,
		'email_address'     => 'spongebob@squarepants.com',
	]);

	var_dump($customerContact->toArray());