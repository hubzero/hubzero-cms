<?php
/**
 * Folder class
 */
class Folder extends CCObject
{
	/**
	 * @var  string   $name
	 * @var  integer  $id
	 * @var  string   $link
	 */
	public $name;
	public $id;
	public $link;

	/**
	 * Constructor
	 *
	 * @param  array  $params  associative array of properties/values
	 */
	public function __construct($params = array())
	{
		$this->name = (isset($params['name'])) ? $params['name'] : '';
		$this->link = (isset($params['link'])) ? $params['link'] : '';
		$this->id = (isset($params['id'])) ? $params['id'] : '';
	}

	/**
	 * Create associate array of object from XML
	 *
	 * @static
	 * @param  SimpleXMLElement  $parsedReturn  parsed XML
	 * @return
	 */
	public static function createStruct($parsedReturn)
	{
		$folder['link'] = (string) $parsedReturn->link->Attributes()->href;
		$folder['id'] = (string) $parsedReturn->id;
		$folder['name'] = (string) $parsedReturn->title;
		return $folder;
	}

	/**
	 * Create XML Representation of the object
	 *
	 * @return  SimpleXMLElement
	 */
	public function createXml()
	{
		$this->validate(array('name'));
		$xml = simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?><atom:entry xmlns:atom='http://www.w3.org/2005/Atom'/>");
		$content = $xml->addChild("content");
		$folder = $content->addChild("Folder", "", "");
		$folder->addChild("Name", $this->name, "");
		$entry = $xml->asXML();
		return $entry;
	}
}
