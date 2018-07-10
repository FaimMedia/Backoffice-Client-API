#!/usr/bin/env php
<?php

require dirname(__DIR__, 4) . '/vendor/autoload.php';

use FaimMedia\BackOfficeClient,
    FaimMedia\BackOfficeClient\Resultset\Folio\Line as FolioLine,
    FaimMedia\BackOfficeClient\Resultset\Folio\LineItem as FolioLineItem;

define('CLIENT_ID', @$argv[1]);
define('CLIENT_SECRET', @$argv[2]);

define('FOLIO_ID', 119);

$client = new BackOfficeClient();
$client
	->setUrl('http://api.backoffice.local')
	->setClientId(CLIENT_ID)
	->setClientSecret(CLIENT_SECRET);

	$folio = $client->folio(FOLIO_ID);

// generate invoice
	$invoice = $folio->generate();

	var_dump($invoice->toArray());

// get and save invoice
	$pdfContent = $invoice->view();

	$filename = tempnam(sys_get_temp_dir(), '').'.pdf';

	$fopen = fopen($filename, 'w+');
	fwrite($fopen, $pdfContent);
	fclose($fopen);

	var_dump($filename);

	exec('open "'.$filename.'"');