<?php

/**
* Default Asset handler class
*/
class FileAssetHandler extends AssetHandler
{
	private static $info = array(
			'action_message' => 'As a standard downloadable file',
			'responds_to'    => array('txt', 'pdf'),
		);

	public static function getMessage()
	{
		return self::$info['action_message'];
	}

	public static function getExtensions()
	{
		return self::$info['responds_to'];
	}

	public function create()
	{
		// Include needed files
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'  . DS . 'com_courses' . DS . 'tables' . DS . 'asset.association.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'  . DS . 'com_courses' . DS . 'tables' . DS . 'asset.php');
		require_once(JPATH_ROOT . DS . 'components'    . DS . 'com_courses' . DS . 'models'      . DS . 'asset.php');

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		// Get the file
		if (isset($_FILES['files']))
		{
			$file = $_FILES['files']['name'][0];
			$size = (int) $_FILES['files']['size'];

			// Get the file extension
			$pathinfo = pathinfo($file);
			$filename = $pathinfo['filename'];
			$ext      = $pathinfo['extension'];
		}
		else
		{
			return array('error' => 'No files provided');
		}

		// @FIXME: should these come from the global settings, or should they be courses specific
		// Get config
		$config =& JComponentHelper::getParams('com_media');

		// Max upload size
		$sizeLimit = $config->get('upload_maxsize');

		// Check to make sure we have a file and its not too big
		if ($size == 0) 
		{
			return array('error' => 'File is empty');
		}
		if ($size > $sizeLimit) 
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', Hubzero_View_Helper_Html::formatSize($sizeLimit));
			return array('error' => "File is too large. Max file upload size is $max");
		}

		// Create our asset table object
		$assetObj = new CoursesTableAsset($this->db);

		$this->asset['title']      = $filename;
		$this->asset['type']       = (!empty($this->asset['type'])) ? $this->asset['type'] : 'file';
		$this->asset['url']        = $file;
		$this->asset['created']    = date('Y-m-d H:i:s');
		$this->asset['created_by'] = JFactory::getApplication()->getAuthn('user_id');
		$this->asset['course_id']  = JRequest::getInt('course_id', 0);

		// Save the asset
		if (!$assetObj->save($this->asset))
		{
			return array('error' => 'Asset save failed');
		}

		// Create asset assoc object
		$assocObj = new CoursesTableAssetAssociation($this->db);

		$this->assoc['asset_id'] = $assetObj->get('id');
		$this->assoc['scope']    = JRequest::getCmd('scope', 'asset_group');
		$this->assoc['scope_id'] = JRequest::getInt('scope_id', 0);

		// Save the asset association
		if (!$assocObj->save($this->assoc))
		{
			return array('error' => 'Asset association save failed');
		}

		// Get courses config
		$cconfig =& JComponentHelper::getParams('com_courses');

		// Build the upload path if it doesn't exist
		$uploadDirectory = JPATH_ROOT . DS . trim($cconfig->get('uploadpath', '/site/courses'), DS) . DS . $this->asset['course_id'] . DS . $this->assoc['asset_id'] . DS;

		// Make sure upload directory exists and is writable
		if (!is_dir($uploadDirectory))
		{
			if (!JFolder::create($uploadDirectory))
			{
				return array('error' => 'Server error. Unable to create upload directory');
			}
		}
		if (!is_writable($uploadDirectory))
		{
			return array('error' => 'Server error. Upload directory isn\'t writable');
		}

		// @FIXME: cleanup asset and asset association if directory creation fails

		// Get the final file path
		$target_path = $uploadDirectory . $filename . '.' . $ext;

		// Move the file to the site folder
		// FIXME: is this ok?
		set_time_limit(60);
		move_uploaded_file($_FILES['files']['tmp_name'][0], $target_path);

		$return_info = array(
			'asset_id'       => $this->assoc['asset_id'],
			'asset_title'    => $this->asset['title'],
			'asset_type'     => $this->asset['type'],
			'asset_url'      => CoursesModelAsset::getInstance($this->assoc['asset_id'])->path($this->asset['course_id']),
			'course_id'      => $this->asset['course_id'],
			'offering_alias' => JRequest::getCmd('offering', ''),
			'scope_id'       => $this->assoc['scope_id'],
			'asset_ext'      => $ext,
			'upload_path'    => $uploadDirectory,
			'target_path'    => $target_path
		);

		// Return info
		return array('assets' => $return_info);
	}

	public function edit()
	{

	}

	public function delete()
	{

	}

	public function render()
	{

	}
}