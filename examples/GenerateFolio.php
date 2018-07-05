#!/usr/bin/env php
<?php

require dirname(__DIR__, 4) . '/vendor/autoload.php';

use FaimMedia\BackOfficeClient;

$client = new BackOfficeClient();
$client
	->setUrl('http://api.backoffice.local')
	->setClientId(1)
	->setClientSecret('fdsfd');


$client->folio()->create([

]);