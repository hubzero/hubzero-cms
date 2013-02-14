<?php

/**
* Url Asset handler class
*/
class NoteAssetHandler extends AssetHandler
{
	private static $info = array(
			'action_message' => 'A textual note',
			'responds_to'    => array('note')
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

		// Create our asset table object
		$assetObj = new CoursesTableAsset($this->db);

		$note = JRequest::getVar('content');

		// @FIXME: make note safe?

		$this->asset['title']      = substr($note, 0, 25);
		$this->asset['type']       = (!empty($this->asset['type'])) ? $this->asset['type'] : 'note';
		$this->asset['content']    = $note;
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

		$return_info = array(
			'asset_id'       => $this->assoc['asset_id'],
			'asset_title'    => $this->asset['title'],
			'asset_type'     => $this->asset['type'],
			'asset_url'      => CoursesModelAsset::getInstance($this->assoc['asset_id'])->path($this->asset['course_id']),
			'course_id'      => $this->asset['course_id'],
			'offering_alias' => JRequest::getCmd('offering', ''),
			'scope_id'       => $this->assoc['scope_id']
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