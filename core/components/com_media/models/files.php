<?php

namespace Components\Media\Models;
use Hubzero\Database\Relational;
use Filesystem;
use stdClass;

class Files extends Relational
{
	//...
	protected $namespace = 'media';
	public $orderBy = 'publish_up';
	public $params = null;
	protected $adapter = null;

	public function getDirectoryTree($directory = '/var/www/hub/app/site/media')
	{
		$obj = new stdClass;
		$obj->name = "Name";
		$obj->absolute = $directory;

		$return = array(
			'data' => $obj,
			'children' => null,
		);
		ddie($return);
	}
}
