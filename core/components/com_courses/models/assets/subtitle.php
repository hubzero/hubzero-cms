<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2011-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models\Assets;

use Components\Courses\Tables;
use Request;
use Filesystem;
use Component;
use User;
use Date;
use stdClass;

/**
 * Subtitle Asset handler class
 */
class Subtitle extends Handler
{
	/**
	 * Class info
	 *
	 * Action message - what the user will see if presented with multiple handlers for this extension
	 * Responds to    - what extensions this handler responds to
	 *
	 * @var array
	 **/
	protected static $info = array(
		'action_message' => 'Add to HUBpresenter Video',
		'responds_to'    => array('srt'),
	);

	/**
	 * Create method for this handler
	 *
	 * @return array of assets created
	 **/
	public function create()
	{
		// Include needed files
		require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'asset.association.php');
		require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'asset.php');
		require_once(dirname(__DIR__) . DS . 'asset.php');

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
		$config = Component::params('com_media');

		// Max upload size
		$sizeLimit = (int) $config->get('upload_maxsize');
		$sizeLimit = $sizeLimit * 1024 * 1024;

		// Check to make sure we have a file and its not too big
		if ($size == 0)
		{
			return array('error' => 'File is empty');
		}
		if ($size > $sizeLimit)
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', \Hubzero\Utility\Number::formatBytes($sizeLimit));
			return array('error' => "File is too large. Max file upload size is $max");
		}

		// get request vars
		$course_id      = Request::getInt('course_id', 0);
		$offering_alias = Request::getCmd('offering', '');
		$scope          = Request::getCmd('scope', 'asset_group');
		$scope_id       = Request::getInt('scope_id', 0);

		// get all assets in group
		$assetGroup = new \Components\Courses\Models\Assetgroup($scope_id);
		$assets = $assetGroup->assets();

		// check to see if any of our assets are html5?
		$hubpresenter = null;
		foreach ($assets as $asset)
		{
			if ($asset->get('type') == 'video'
				&& $asset->get('subtype') == 'video'
				&& in_array($asset->get('state'), array(0,1))
				&& strpos($asset->get('url'), 'zip'))
			{
				$hubpresenter = $asset;
				break;
			}
		}

		// make sure we have asset
		if ($hubpresenter === null)
		{
			return array('error' => 'Unable to locate html5 video or hubpresenter asset to attach subtitle file to.');
		}

		// build path to asset
		$pathToAsset       = $hubpresenter->path($course_id);
		$pathToAssetFolder = trim(dirname($pathToAsset), DS);

		// build target path
		$target_path = PATH_APP . DS . $pathToAssetFolder . DS . $filename . '.' . $ext;

		// Move the file to the site folder
		set_time_limit(60);

		// Scan for viruses
		if (!Filesystem::isSafe($_FILES['files']['tmp_name'][0]))
		{
			// Scan failed, return an error
			return array('error' => 'File rejected because the anti-virus scan failed.');
		}

		// move file
		if (!$move = move_uploaded_file($_FILES['files']['tmp_name'][0], $target_path))
		{
			// Move failed, delete asset and association and return an error
			return array('error' => 'Move file failed');
		}

		// get json file
		$jsonFile = $pathToAssetFolder . DS . 'presentation.json';

		// get manifest
		$manifest = file_get_contents(PATH_APP . DS . $jsonFile);
		$manifest = json_decode($manifest);

		// make sure we have a subtitles section
		$currentSubtitles = array();
		if (!isset($manifest->presentation->subtitles))
		{
			$manifest->presentation->subtitles = array();
		}
		else
		{
			foreach ($manifest->presentation->subtitles as $subtitle)
			{
				$currentSubtitles[] = $subtitle->source;
			}
		}

		// create subtitle details based on filename
		$info      = pathinfo($file);
		$name      = str_replace('-auto', '', $info['filename']);
		$autoplay  = (strstr($info['filename'], '-auto')) ? 1 : 0;
		$source    = $file;

		// use only the last segment from name (ex. ThisIsATest.English => English)
		$nameParts = explode('.', $name);
		$name      = array_pop($nameParts);

		// add subtitle
		$subtitle                            = new stdClass;
		$subtitle->type                      = 'SRT';
		$subtitle->name                      = ucfirst($name);
		$subtitle->source                    = $source;
		$subtitle->autoplay                  = $autoplay;

		// only add sub if we dont already have it
		if (!in_array($subtitle->source, $currentSubtitles))
		{
			$manifest->presentation->subtitles[] = $subtitle;
		}

		// update json file
		file_put_contents(PATH_APP . DS . $jsonFile, json_encode($manifest, JSON_PRETTY_PRINT));

		//parse subtitle file
		$lines = self::_parseSubtitleFile($target_path);

		// make transcript file
		$transcript = '';
		foreach ($lines as $line)
		{
			$transcript .= ' ' . trim($line->text);
		}

		// trim transcript and replace add slide markers
		$transcript = str_replace(array("\r\n", "\n"), array('',''), $transcript);
		$transcript = preg_replace("/\\[([^\\]]*)\\]/ux", "\n\n[$1]", $transcript);

		// add title to transcript
		$transcript  = $manifest->presentation->title . PHP_EOL . str_repeat('==', 20) . PHP_EOL . ltrim($transcript, PHP_EOL);

		// Create our asset table object
		$assetObj = new Tables\Asset($this->db);
		$this->asset['title']      = 'Video Transcript';
		$this->asset['type']       = 'file';
		$this->asset['subtype']    = 'file';
		$this->asset['url']        = $info['filename'] . '.txt';
		$this->asset['created']    = Date::toSql();
		$this->asset['created_by'] = App::get('authn')['user_id'];
		$this->asset['course_id']  = $course_id;

		// Save the asset
		if (!$assetObj->save($this->asset))
		{
			return array('error' => 'Asset save failed');
		}

		// Create asset assoc object
		$assocObj = new Tables\AssetAssociation($this->db);
		$this->assoc['asset_id'] = $assetObj->get('id');
		$this->assoc['scope']    = $scope;
		$this->assoc['scope_id'] = $scope_id;

		// Save the asset association
		if (!$assocObj->save($this->assoc))
		{
			return array('error' => 'Asset association save failed');
		}

		// Get courses config
		$cconfig = Component::params('com_courses');

		// Build the upload path if it doesn't exist
		$uploadDirectory = PATH_APP . DS . trim($cconfig->get('uploadpath', '/site/courses'), DS) . DS . $this->asset['course_id'] . DS . $this->assoc['asset_id'] . DS;

		// Make sure upload directory exists and is writable
		if (!is_dir($uploadDirectory))
		{
			if (!Filesystem::makeDirectory($uploadDirectory))
			{
				return array('error' => 'Server error. Unable to create upload directory');
			}
		}
		if (!is_writable($uploadDirectory))
		{
			return array('error' => 'Server error. Upload directory isn\'t writable');
		}

		// Get the final file path
		$transcript_target_path = $uploadDirectory . $this->asset['url'];

		// make transcript file
		file_put_contents($transcript_target_path, $transcript);

		// Get the url to return to the page
		$course_id      = Request::getInt('course_id', 0);
		$offering_alias = Request::getCmd('offering', '');
		$course         = new \Components\Courses\Models\Course($course_id);

		$url = Route::url('index.php?option=com_courses&controller=offering&gid='.$course->get('alias').'&offering='.$offering_alias.'&asset='.$assetObj->get('id'));

		// build return info
		$return_info = array(
			'asset_id'       => $this->assoc['asset_id'],
			'asset_title'    => $this->asset['title'],
			'asset_type'     => $this->asset['type'],
			'asset_subtype'  => $this->asset['subtype'],
			'asset_url'      => $url,
			'course_id'      => $this->asset['course_id'],
			'offering_alias' => Request::getCmd('offering', ''),
			'scope_id'       => $this->assoc['scope_id'],
			'asset_ext'      => 'txt',
			'upload_path'    => $uploadDirectory,
			'target_path'    => $transcript_target_path
		);

		// Return info
		return array('assets' => $return_info);
	}

	/**
	 * [_parseSubtitleFile description]
	 * @param  [type] $file [description]
	 * @return [type]       [description]
	 */
	private function _parseSubtitleFile($file)
	{
		define('SRT_STATE_SUBNUMBER', 0);
		define('SRT_STATE_TIME', 1);
		define('SRT_STATE_TEXT', 2);
		define('SRT_STATE_BLANK', 3);

		$lines   = file($file);
		$subs    = array();
		$state   = SRT_STATE_SUBNUMBER;
		$subNum  = 0;
		$subText = '';
		$subTime = '';

		foreach ($lines as $line)
		{
			switch ($state)
			{
				case SRT_STATE_SUBNUMBER:
					$subNum = trim($line);
					$state  = SRT_STATE_TIME;
					break;
				case SRT_STATE_TIME:
					$subTime = trim($line);
					$state   = SRT_STATE_TEXT;
					break;
				case SRT_STATE_TEXT:
					if (trim($line) == '')
					{
						$sub = new stdClass;
						$sub->number = $subNum;
						list($sub->startTime, $sub->stopTime) = explode(' --> ', $subTime);
						$sub->text   = $subText;
						$subText     = '';
						$state       = SRT_STATE_SUBNUMBER;

						$subs[]      = $sub;
					}
					else
					{
						$subText .= ' ' . $line;
					}
					break;
			}
		}

		//return subs
		return $subs;
	}
}
