<?php

/**
* Default Asset handler class
*/
class VideoFileAssetHandler extends FileAssetHandler
{
	private static $info = array(
			'action_message' => 'As an HTML5/HUBpresenter Video',
			'responds_to'    => array('zip'),
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
		$this->asset['type'] = 'video';

		$return_info = parent::create();

		$asset = $return_info['assets'];

		// Exapand zip file if applicable - we're assuming zips are hubpresenter videos
		if(!array_key_exists('error', $asset) && $asset['asset_ext'] == 'zip')
		{
			set_time_limit(60);
			$escaped_file = escapeshellarg($asset['target_path']);
			// @FIXME: check for symlinks and other potential security concerns
			if($result = shell_exec("unzip {$escaped_file} -d {$asset['upload_path']}"))
			{
				// Remove original archive
				JFile::delete($asset['target_path']);

				// Remove MACOSX dirs if there
				JFolder::delete($asset['upload_path'] . '__MACOSX');
			}
		}

		// Return info
		return $return_info;
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