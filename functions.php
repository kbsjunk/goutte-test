<?php

class EurovisionCrawler {

	private static $patterns = array(
		'city year' => '(?P<city>.*) (?P<year>[0-9]{4})'
		);


	public static function extract($text, $pattern) {

		preg_match_all(\EurovisionCrawler::_pattern($pattern), $text, $result);
		$return = new stdClass();
		foreach ($result as $key => $value)
			if (!is_numeric($key)) {
				$return->$key = $value[0];
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

	}