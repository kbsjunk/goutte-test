<?php

class EurovisionCrawler {

	private static $patterns = array(
		'city year' => '(?P<city>.*) (?P<year>[0-9]{4})',
		'pt goes to' => '(?P<points>[0-9]*)pt from (?P<from>.*) goes to (?P<to>.*)',
		'youtube' => 'v=(?P<youtube>[^&]*)',
		'song video' => '(?P<song>.*?)\s*Watch video',
		'location city country' => '(?P<venue>.*), *(?P<city>.*), *(?P<country>.*)',
		'performer from country' => '(?P<performer>.*?) *from *(?P<country>.*)?',
		);


	public static function extract($text, $pattern) {

		$pattern = \EurovisionCrawler::_pattern($pattern);

		if (!$pattern) return $text;

		preg_match_all($pattern, trim($text), $result);

		$return = new stdClass();

		foreach ($result as $key => $value) {
			if (!is_numeric($key)) {
				$return->$key = trim($value[0]);
			}
		}

		return $return;
	}

	private static function _pattern($key) {

		if (isset(\EurovisionCrawler::$patterns[$key])) {
			return '/'.\EurovisionCrawler::$patterns[$key].'/';
		}
		else {
			return false;
		}
	}

	public static function split($text, $delimiter = ',') {

		$text = explode($delimiter, trim(trim($text), $delimiter));

		array_walk($text, function(&$n) {
			$n = trim($n);
		}); 

		return $text;

	}

	public static function liftArray($array) {
		$return = array();

		foreach ($array as $id => $subarray) {
			foreach ($subarray as $key => $value) {
				$return[$key] = $value;
			}
		}

		return $return;

	}

}

class EurovisionEvent {



}