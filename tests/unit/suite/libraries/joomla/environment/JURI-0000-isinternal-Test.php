<?php
/**
 * Joomla! v1.5 Unit Test Facility
 *
 * @package Joomla
 * @subpackage UnitTest
 * @copyright Copyright (C) 2005 - 2008 Open Source Matters, Inc.
 * @version $Id: JURI-0000-isinternal-Test.php 14408 2010-01-26 15:00:08Z louis $
 *
 */


class JURITest_IsInternal extends PHPUnit_Framework_TestCase {
	/**
	 * Scratch storage for the environment's live_site
	 *
	 * @var string
	 */
	public $liveSiteSave;

	/**
	 * Generate data set for isInternal.
	 *
	 * This test coded somewhat quickly, it should be extracted to a stand-alone
	 * class.
	 */
	static public function isInternalData() {
		/*
		 * Non-empty live sites will have http:// added.
		 */
		$liveSites = array(
			'',
			'www.example.com',
			'www.example.com/subdomain'
		);
		/*
		 * Base urls with expected result: 0 = false; 1 = true; 2 = true if
		 * live_site not empty and url begins with live_site;
		 */
		$base = array(
			array('/foo', 1),
			array ('foo/', 1),
			array('/foo/', 1),
			array('http://www.external.com/foo', 0),
			array ('http://www.external.com/foo/', 0),
			array('https://www.external.com/foo', 0),
			array ('https://www.external.com/foo/', 0),
			array('ftp://www.external.com/foo', 0),
			array ('ftp://www.external.com/foo/', 0),
			array('http://www.example.com', 2),
			array('http://www.example.com/subdomain', 2),
			array('https://www.example.com', 2),
			array('https://www.example.com/subdomain', 2),
			array('ftp://www.example.com', 0),
			array('ftp://www.example.com/subdomain', 0),
		);
		$requests = array(
			'',
			'?bar',
			'?bar=2',
			'?bar&snafu',
			'?bar=2&snafu',
			'?bar=2&snafu=4',
			'?bar&snafu=4',
			'?bar&amp;valid',
			'?bar=2&amp;valid',
			'?bar=2&amp;valid=4',
			'?bar&amp;valid=4',
			'?url=http://www.example.com/invalid',
			'?url=' . urlencode('http://www.example.com/valid'),
		);
		$dataSet = array();
		foreach ($liveSites as $site) {
			$dataSet[] = array($site, null, false);
			foreach ($base as $def) {
				switch($def[1]) {
					case 0: {
						$expect = false;
					}
					break;

					case 1: {
						$expect = true;
					}
					break;

					case 2: {
						if ($site == '') {
							$expect = false;
						} else {
							$scan = strpos($def[0], '//');
							$posn = strpos($def[0], $site, $scan + 2);
							//echo $site . ' def=' . $def[0] . ' scan=' . $scan . ' posn=' . $posn;
							//exit();
							$expect = ($posn === 0);
						}
					}
					break;
				}
				if ($site == '') {
					$start = $site;
				} else {
					$start = 'http://' . $site;
				}
				foreach ($requests as $req) {
					$dataSet[] = array($start, $def[0] . $req, $expect);
				}
			}
		}
		return $dataSet;
	}

	/**
	 * Test JURI::isInternal()
	 *
	 * @dataProvider isInternalData
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	function testIsInternal($site, $url, $expect) {
                require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/bootstrap.php');
                require_once dirname(__FILE__).DS.'JURI-mock-general.php';
		jimport('joomla.environment.uri');
		$_SERVER['HTTP_HOST'] = 'www.example.com';
		$_SERVER['PHP_SELF'] = '/index.php';
		JRegistry::$liveSite = $site;
		$actual = JURI::isInternal($url);
		$this -> assertEquals($actual, $expect, 'URL: ' . $url);
	}

}
