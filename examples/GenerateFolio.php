#!/usr/bin/env php
<?php

require dirname(__DIR__, 4) . '/vendor/autoload.php';

use FaimMedia\BackOfficeClient,
    FaimMedia\BackOfficeClient\CustomerAddressItem;

$client = new BackOfficeClient();
$client
	->setUrl('http://api.backoffice.local')
	->setClientId(1)
	->setClientSecret('fdsfd');

define('US_COUNTRY_ID', 226);

// set customer address
	$customerAddress = new CustomerAddressItem([
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

	var_dump($customer->toArray());

