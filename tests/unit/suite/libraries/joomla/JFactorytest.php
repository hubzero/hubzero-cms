<?php
/**
 * Test class for JFactory.
 *
 * @package Joomla
 * @subpackage UnitTest
 * @version     $Id: JFactorytest.php 14408 2010-01-26 15:00:08Z louis $
 */


/* class to test */
//require_once 'libraries/joomla/factory.php';

# TODO: should be handled by factory.php
//require_once 'libraries/joomla/filter/input.php';

require_once dirname(__FILE__).DS.'JFactory.helper.php';

class TestOfJFactory extends PHPUnit_Framework_TestCase
{
	var $have_db = false;


	function tearDown()
	{
		$config =& JFactory::getConfig();
		$config = null;
	}

	function testGetConfig()
	{
		$obj =& JFactory::getConfig();
		//$this->assertIsA($obj, 'JRegistry');
		//$this->assertIsA($obj, 'JObject');
		$this->assertTrue($obj instanceof JRegistry);
		$this->assertTrue($obj instanceof JObject);
	}

	function testGetConfig_reference()
	{
		$first  =& JFactory::getConfig();
		$second =& JFactory::getConfig();
		//$this->assertReference($first, $second);

		// creates namespace 'foo' with item 'bar' = 'baz'
		$second->setValue('foo.bar', 'baz');
		$this->assertSame($first, $second);

	}
	function testGetConfig_unset()
	{
		$config =& JFactory::getConfig();
		$config->setValue('foo.bar', null);
		$this->assertNull($config->getValue('foo.bar'));

		// how do you remove namespace 'foo' thru the interface ??
	}

	function testGetSession()
	{
		if (headers_sent()) {
			//return $this->_reporter->setMissingTestCase('Test unreliable: headers_sent()');
		}
		if (!class_exists('JFilterInput')) {
			//return $this->_reporter->setMissingTestCase('Unhandled class dependency: JFilterInput');
		}

		$obj =& JFactory::getSession();
		$this->assertTrue($obj instanceof JSession);
		$this->assertTrue($obj instanceof JObject);
	}

	function testGetLanguage()
	{
		$obj =& JFactory::getLanguage();
		$this->assertTrue($obj instanceof JLanguage);
		$this->assertTrue($obj instanceof JObject);
	}

	function testGetDocument()
	{
		$obj =& JFactory::getDocument();
		$this->assertTrue($obj instanceof JDocumentHTML);
		$this->assertTrue($obj instanceof JObject);
	}

	function testGetCache()
	{
		$obj =& JFactory::getCache();
		$this->assertTrue($obj instanceof JCacheCallback);
		$this->assertTrue($obj instanceof JObject);
	}

	function testGetTemplate()
	{
		// TODO: Fix undefined index 'option' / 'Itemid'
		settype($GLOBALS['option'], 'string');
		settype($GLOBALS['Itemid'], 'string');

		$obj =& JFactory::getTemplate();
		$this->assertTrue($obj instanceof JTemplate);
		$this->assertTrue($obj instanceof patTemplate);
	}

	/**
	 * getMailer() mutations
	 */
	function testGetMailer()
	{
		$obj =& JFactory::getMailer();
		$this->assertTrue($obj instanceof JMail);
		$this->assertTrue($obj instanceof PHPMailer);
	}
	function testGetMailer_smtp()
	{
		JFactoryTestHelper::GetMailer_smtp();

		$obj =& JFactory::getMailer();
		$this->assertEquals($obj->Mailer, 'smtp');
	}
	function testGetMailer_sendmail()
	{
		$config =& JFactory::getConfig();
		$config->setValue('config.mailer', $config->getValue('sendmail'));

		# FIX: JFactory::_createMailer() call to useSendmail()
		$obj =& JFactory::getMailer();
		$this->assertEquals($obj->Mailer, 'sendmail');
	}

	/**
	 * getXMLParser() mutations
	 */
	function testGetXMLParser()
	{
		$obj =& JFactory::getXMLParser();
		$this->assertTrue($obj instanceof DOMIT_Lite_Document);
	}

	/**
	 * @todo: fix JFactory::getXMLParser(), see TODO.txt
	 */
	function testGetXMLParser_feeds()
	{
		return;
		$expectedError = new PatternExpectation('/Undefined index/', 'JFactory::getXMLParser() %s');

		$this->expectError($expectedError);
		$rss    =& JFactory::getXMLParser('RSS');
		$this->assertNull($rss);

		$this->expectError($expectedError);
		$atom   =& JFactory::getXMLParser('Atom');
		$this->assertNull($atom);
	}

	/**
	 * @todo: find some example live-feeds to validate objects (Devel Google group?)
	 */
	function testGetXMLParser_feeds_live()
	{
		//return $this->_reporter->setMissingTestCase('TODO: find some example live-feeds to validate objects');

		$options = array('rssUrl' => 'http://blabla/feed.xml');
		$rss    =& JFactory::getXMLParser('RSS', $options);
		$atom   =& JFactory::getXMLParser('Atom', $options);
		$this->assertTrue($rss instanceof SimplePie);
		$this->assertTrue($atom instanceof SimplePie);
	}

	function testGetXMLParser_xml()
	{
		$simple =& JFactory::getXMLParser('Simple');
		$this->assertTrue($simple instanceof JSimpleXML);
		$this->assertTrue($simple instanceof JObject);
	}
	function testGetXMLParser_domdefault()
	{
		$dom    =& JFactory::getXMLParser('DOM');
		$std    =& JFactory::getXMLParser();
	}

	function testGetEditor()
	{
		$obj =& JFactory::getEditor();
		$this->assertTrue($obj instanceof JEditor);
		$this->assertTrue($obj instanceof JObject);
	}

	function testGetURI()
	{
		$obj =& JFactory::getURI();
		$this->assertTrue($obj instanceof JURI);
		$this->assertTrue($obj instanceof JObject);
	}

	/**
	 * Database related stuff require a working database setup which is
	 * prepared in {@link JFactoryTestHelper::GetDBO()}
	 */
	function testGetDBO()
	{
		JFactoryTestHelper::GetDBO();

		return $this->_reporter->setMissingTestCase('cannot test now, need to resolve prerequisites');

		$obj =& JFactory::getDBO();
		// flag testcase to skip
		$this->_should_skip = $this->assertIsA($obj, 'JDatabaseMySQL');

		if (!$this->_should_skip) {
			return $this->_reporter->setMissingTestCase('getDBO() failure, expect skip');
		}

		$this->assertTrue($obj instanceof JDatabase);
		$this->assertTrue($obj instanceof JObject);
	}

	function testGetUser()
	{
		return $this->_reporter->setMissingTestCase('depends on testGetDBO()');

		$obj =& JFactory::getUser();
		$this->assertTrue($obj instanceof JUser);
		$this->assertTrue($obj instanceof JObject);
	}

	function testGetACL()
	{
		return $this->_reporter->setMissingTestCase('depends on testGetDBO()');

		$obj =& JFactory::getACL();
		$this->assertTrue($obj instanceof JAuthorization);
		$this->assertTrue($obj instanceof JObject);
	}

}
