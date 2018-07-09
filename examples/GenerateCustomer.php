#!/usr/bin/env php
<?php

require dirname(__DIR__, 4) . '/vendor/autoload.php';

use FaimMedia\BackOfficeClient,
FaimMedia\BackOfficeClient\Resultset\Customer\AddressItem as CustomerAddress;

define('CLIENT_ID', @$argv[1]);
define('CLIENT_SECRET', @$argv[2]);
define('US_COUNTRY_ID', 226);

$client = new BackOfficeClient();
$client
	->setUrl('http://api.backoffice.local')
	->setClientId(CLIENT_ID)
	->setClientSecret(CLIENT_SECRET);

// set customer address
	$customerAddress = new CustomerAddress([
		'address'               => 'Sand Rd.',
		'address_number'        => 8,
		'address_number_suffix' => 'A',
		'zipcode'               => '51000',
		'city'                  => 'BIKINI BOTTOM',
		'country_id'            => US_COUNTRY_ID,
	]);

// generate customer
	$customer = $client->customer()->create([
		'name'              => 'Krusty Krab Ltd.',
		'payment_method_id' => 5,
		'customer_address'  => $customerAddress,
	]);