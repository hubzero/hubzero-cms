<?php
/**
 * JDate constructor tests
 *
 * @package Joomla
 * @subpackage UnitTest
 * @version $Id: JCacheStorage-0000-main-Test.php 14408 2010-01-26 15:00:08Z louis $
 * @author Anthony Ferrara
 */

jimport('joomla.cache.cache');

/*
 * Mock classes
 */
// Include mocks here
/*
 * We now return to our regularly scheduled environment.
 */

class JCacheStorageTest_Main extends PHPUnit_Framework_TestCase
{

	public function setUp() {
		require_once(dirname(dirname(__FILE__)) . DS . 'handler' .  DS . 'JCacheRaw.php');
	}

	public static function provider() {
		static $ret = array();
		if(empty($ret)) {
			$names = JCache::getStores();
			foreach ($names AS $name) {
				$ret[] = array($name);
			}
		}
		return $ret;
	}

	/**
	 * @dataProvider provider
	 */
	function testCacheHit($store) {
		$id = 'randomTestID';
		$group = 'testing';
		$data = 'testData';
		$cache =& JCache::getInstance('raw', array('storage'=>$store));
		$this->assertTrue($cache->store($data, $id, $group), 'Initial Store Failed');
		unset($cache);
		$cache =& JCache::getInstance('raw', array('storage'=>$store));
		$new = $cache->get($id, $group);
		$this->assertSame($new, $data, 'Expected: '.$data.' Actual: '.$new);
		unset($cache);
	}

	/**
	 * @dataProvider provider
	 */
	function testCacheMiss($store) {
		$id = 'randomTestID2423423';
		$group = 'testing';
		$data = 'testData';
		$cache =& JCache::getInstance('raw', array('storage'=>$store));
		$new = $cache->get($id, $group);
		$this->assertFalse($new, 'Expected: false Actual: '.$new);
		unset($cache);
	}

	/**
	 * @dataProvider provider
	 */
	function testCacheTimeout($store) {
		if($store == 'xcache') {
			$this->markTestSkipped('Due to an xcache "bug/feature", this test will not function as expected, skipped');
		}
		$id = 'randomTestID';
		$group = 'testing';
		$data = 'testData';
		$cache =& JCache::getInstance('raw', array('storage'=>$store));
		$cache->setLifeTime(2);
		$this->assertTrue($cache->store($data, $id, $group), 'Initial Store Failed');
		unset($cache);
		sleep(5);
		$cache =& JCache::getInstance('raw', array('storage'=>$store));
		$cache->setLifeTime(2);
		$new = $cache->get($id, $group);
		$this->assertFalse($new, 'Expected: false Actual: '.((string) $new));
		unset($cache);
	}

	/**
	 * @dataProvider provider
	 */
	function testCacheRemove($store) {
		$id = 'randomTestID';
		$group = 'testing';
		$data = 'testData';
		$cache =& JCache::getInstance('raw', array('storage'=>$store));
		$this->assertTrue($cache->store($data, $id, $group), 'Initial Store Failed');
		unset($cache);
		$cache =& JCache::getInstance('raw', array('storage'=>$store));
		$this->assertTrue($cache->remove($id, $group), 'Removal Failed');
		unset($cache);
		$cache =& JCache::getInstance('raw', array('storage'=>$store));
		$new = $cache->get($id, $group);
		$this->assertFalse($new, 'Expected: false Actual: '.((string) $new));
		unset($cache);
	}

	/**
	 * @dataProvider provider
	 */
	function testCacheClearGroup($store) {
		if($store == 'xcache' || $store == 'eaccelerator' || $store == 'apc' || $store == 'memcache') {
			$this->markTestSkipped('XCache does not support clearing of the cache');
		}
		$id = 'randomTestID';
		$group = 'testing';
		$data = 'testData';
		$cache =& JCache::getInstance('raw', array('storage'=>$store));
		$this->assertTrue($cache->store($data, $id, $group), 'Initial Store Failed');
		unset($cache);
		$cache =& JCache::getInstance('raw', array('storage'=>$store));
		$this->assertTrue($cache->clean($group), 'Clean Failed');
		unset($cache);
		$cache =& JCache::getInstance('raw', array('storage'=>$store));
		$new = $cache->get($id, $group);
		$this->assertFalse($new, 'Expected: false Actual: '.((string) $new));
		unset($cache);
	}

	/**
	 * @dataProvider provider
	 */
	function testCacheClearNotGroup($store) {
		if($store == 'xcache') {
			$this->markTestSkipped('XCache does not support clearing of the cache');
		}

		$id = 'randomTestID';
		$group = 'testing';
		$data = 'testData';
		$cache =& JCache::getInstance('raw', array('storage'=>$store));
		$this->assertTrue($cache->store($data, $id, $group), 'Initial Store Failed');
		unset($cache);
		$cache =& JCache::getInstance('raw', array('storage'=>$store));
		$this->assertTrue($cache->clean($group, 'notgroup'), 'Clean Failed');
		unset($cache);
		$cache =& JCache::getInstance('raw', array('storage'=>$store));
		$new = $cache->get($id, $group);
		$this->assertSame($new, $data, 'Expected: '.$data.' Actual: '.((string) $new));
		unset($cache);
	}

}

