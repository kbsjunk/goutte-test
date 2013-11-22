<?php
echo 'START';
echo '<pre>';
// https://github.com/fabpot/Goutte

require 'vendor/autoload.php';
require 'functions.php';

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

$client = new Client();

// --------------------------------------------------- //

$crawler = $client->request('GET', 'http://www.eurovision.tv/page/history/year');

$contests = $crawler->filter('.event-entry a')->each(function (Crawler $node, $i) {

	$city = \EurovisionCrawler::extract($node->text(), 'city year');

	return array(
		'city' => $city->city,
		'year' => $city->year,
		'link' => $node->link()->getUri()
		);
});

print_r($contests);

// --------------------------------------------------- //

// 

$crawler = $client->request('GET', 'http://www.eurovision.tv/page/history/by-year/contest?event=273');

$details = $crawler->filter('.cb-EventInfo-facts .detail-list')->filter('h3, p.info')->each(function (Crawler $node, $i) {

return $node->text();

	//$city = \EurovisionCrawler::extract($node->text(), 'city year');

	// return array(
	// 	'city' => $city->city,
	// 	'year' => $city->year,
	// 	'link' => $node->link()->getUri()
	// 	);
});

print_r($details);

// --------------------------------------------------- //

echo '</pre>';
echo 'END';