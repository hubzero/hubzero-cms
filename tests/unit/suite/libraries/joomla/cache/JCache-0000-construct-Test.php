<?php
/**
 * JDate constructor tests
 *
 * @package Joomla
 * @subpackage UnitTest
 * @version $Id: JCache-0000-construct-Test.php 14408 2010-01-26 15:00:08Z louis $
 * @author Anthony Ferrara 
 */

/*
 * Now load the Joomla environment
 */


/*
 * Mock classes
 */
// Include mocks here
/*
 * We now return to our regularly scheduled environment.
 */



class JCacheTest_Construct extends PHPUnit_Framework_TestCase
{

	public function setUp() {
		jimport('joomla.cache.cache');
	}

	public static function provider() {
		return array(
				array('callback'), 
				array('output'), 
				array('page'), 
				array('view')
				);
	}

	/**
	 * @dataProvider provider
	 */
	function testConstruct($type) {
		$class = 'JCache'.ucfirst($type);
		$cache =& JCache::getInstance($type);
		$this -> assertTrue(($cache instanceof $class), 
			'Expecting= '.$class.' Returned= '.get_class($cache)
		); 
		$cache2 =& JCache::getInstance($type);
		$this -> assertTrue(($cache !== $cache2),
			'Type: '.$type.' Recieved the same instance twice'
		);
	}

}

