<?php
echo 'START';
echo '<pre>';
// https://github.com/fabpot/Goutte

require 'vendor/autoload.php';
require 'functions.php';

use Goutte\Client;
use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;

$client = new Client();
$ev = new \EurovisionEvent();

// --------------------------------------------------- //

// $crawler = $client->request('GET', 'http://www.eurovision.tv/page/history/year');

// $contests = $crawler->filter('.event-entry a')->each(function (Crawler $node, $i) {

// 	$city = \EurovisionCrawler::extract($node->text(), 'city year');

// 	return array(
// 		'city' => $city->city,
// 		'year' => $city->year,
// 		'link' => $node->link()->getUri()
// 		);
// });

// print_r($contests);

// --------------------------------------------------- //

// 

$crawler = $client->request('GET', 'http://www.eurovision.tv/page/history/by-year/contest?event=1543');//273

$ev->contest = $crawler->filter('.cb-EventInfo-story h1')->text();

$details = $crawler->filter('.cb-EventInfo-facts .detail-list')->filter('h3')->each(function (Crawler $node, $i) {

	$key = trim($node->text());
	$value = trim($node->nextAll()->first()->text());//filter('p.info, ul.qualifiers')->

	switch ($key) {

		case 'Date':
		$value = new Carbon($value);
		break;

		case 'Location':
		$value = \EurovisionCrawler::extract($value, 'location city country');
		break;

		case 'Hosts':
		case 'Executive Producer':
		case 'EBU Scrutineer':
		case 'Director':
		case 'Interval Act':
		$value = \EurovisionCrawler::split($value);
		break;

		case 'Winner':
		$value = \EurovisionCrawler::extract($value, 'performer from country');
		break;

		case 'Qualifiers':
		$value = $node->nextAll()->first()->filter('li.qualifiers a')->each(function (Crawler $node, $i) {
			return $node->text();
		});
		break;

	}

	return array(
		$key => $value,
		);
});

$ev->details = \EurovisionCrawler::liftArray($details);

// --------------------------------------------------- //

$heats = $crawler->filter('.cb-EventInfo-allevents ul.events li a')->each(function (Crawler $node, $i) {

	return array(
		'heat' => $node->text(),
		'link' => $node->link()->getUri()
		);
});

$ev->heats = $heats;


// --------------------------------------------------- //

$scoreboard = $crawler->filter('.cb-EventInfo-scoreboard .votes_crosstable')->filter('tbody tr')->each(function (Crawler $node, $i) {

	$votes = $node->filter('td:not(.participant):not(.total):not(.rank)')->each(function (Crawler $node, $i) {

		$votes = \EurovisionCrawler::extract($node->attr('title'), 'pt goes to');

		if ($votes->points) {
			return array(
				'points' => $votes->points,
				'from' => $votes->from,
				);
		}

		return false;

	});

	$votes = array_filter($votes);

	$participant = trim($node->filter('td.participant .country')->text());
	$points = intval($node->filter('td.total')->text());
	$place = intval($node->filter('td.rank')->text());

	return array(
		'country' => $participant,
		'votes' => $votes,
		'points' => $points,
		'place' => $place
		);

});

$ev->scoreboard = $scoreboard;

// --------------------------------------------------- //

$participants = $crawler->filter('.cb-ParticipantList table.participants')->filter('tbody tr')->each(function (Crawler $node, $i) {

	$order = intval($node->filter('td.ro')->text());
	$country = trim($node->filter('td.country a')->text());
	$broadcaster = trim($node->filter('td.country .broadcaster')->text());

	$performer = $node->filter('td.country')->nextAll()->first();
	if (count($performer->filter('.credits a'))) $profile = $performer->filter('.credits a')->link()->getUri();

	$song = $node->filter('td.points')->previousAll()->first();
	if (count($song->filter('.youtube-watch'))) $video = \EurovisionCrawler::extract($song->filter('.youtube-watch')->link()->getUri(), 'youtube');

	if (stristr($song->text(), 'Watch video')) {
		$song = \EurovisionCrawler::extract($song->text(), 'song video')->song;
	}
	else {
		$song = $song->text();
	}

	$points = intval($node->filter('td.points')->text());
	$place = intval($node->filter('td.place')->text());

	return array(
		'order' => $order,
		'country' => $country,
		'broadcaster' => $broadcaster,
		'performer' => trim($performer->filter('span')->text()),
		'link' => @$profile,
		'song' => trim($song),
		'youtube' => @$video->youtube,
		'points' => $points,
		'place' => $place,
		);

});

$ev->participants = $participants;

// --------------------------------------------------- //

print_r($ev);

echo '</pre>';
echo 'END';