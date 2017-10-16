<?php
/**
 * Image class
 */
class Image extends CCObject
{
	/**
	 * @var  string    $name
	 * @var  integer   $id
	 * @var  string    $link
	 * @var  $updated
	 * @var  string    $imageUrl
	 * @var  integer   $height
	 * @var  integer   $width
	 * @var  string    $description
	 * @var  string    $folder
	 * @var  string    $md5hash
	 * @var  integer   $fileSize
	 * @var  string    $fileType
	 */
	public $name;
	public $id;
	public $link;
	public $updated;
	public $imageUrl;
	public $height;
	public $width;
	public $description;
	public $folder;
	public $md5hash;
	public $fileSize;
	public $fileType;

	/**
	 * Constructor
	 *
	 * @param  array  $params  Associative array of properties/values
	 */
	public function __construct($params = array())
	{
		$this->name = (isset($params['name'])) ? $params['name'] : '';
		$this->link = (isset($params['link'])) ? $params['link'] : '';
		$this->id = (isset($params['id'])) ? $params['id'] : '';
		$this->updated = (isset($params['updated'])) ? $params['updated'] : '';
		$this->imageUrl = (isset($params['imageUrl'])) ? $params['imageUrl'] : '';
		$this->height = (isset($params['height'])) ? $params['height'] : '';
		$this->width = (isset($params['width'])) ? $params['width'] : '';
		$this->description = (isset($params['description'])) ? $params['description'] : '';
		$this->folder = (isset($params['folder'])) ? $params['folder'] : '';
		$this->md5hash = (isset($params['md5hash'])) ? $params['md5hash'] : '';
		$this->fileSize = (isset($params['fileSize'])) ? $params['fileSize'] : '';
		$this->fileType = (isset($params['fileType'])) ? $params['fileType'] : '';
	}

	/**
	 * Create associate array of object from XML
	 *
	 * @static
	 * @param  SimpleXMLElement  $parsedReturn  Parsed XML
	 * @return
	 */
	public static function createStruct($parsedReturn)
	{
		$image['link'] = (string) $parsedReturn->link->Attributes()->href;
		$image['id'] = (string) $parsedReturn->id;
		$image['name'] = (string) $parsedReturn->title;
		$image['updated'] = (string) $parsedReturn->updated;
		$content = $parsedReturn->content->children();
		$image['imageUrl'] = (string) $content->Image->ImageURL;
		$image['height'] = (string) $content->Image->Height;
		$image['width'] = (string) $content->Image->Width;
		$image['description'] = (string) $content->Image->Description;
		$image['folder'] = (string) $content->Image->Folder->Name;
		$image['md5hash'] = (string) $content->Image->MD5Hash;
		$image['fileSize'] = (string) $content->Image->FileSize;
		$image['fileType'] = (string) $content->Image->FileType;
		return $image;
	}

	/**
	 * Create XML Representation of the object
	 *
	 * @return SimpleXMLElement
	 */
	public function createXml()
	{
		$this->validate(array('name', 'id'));
		$xml = simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?><atom:entry xmlns:atom='http://www.w3.org/2005/Atom'/>");
		$entry = $xml->asXML();
		return $entry;
	}
}
