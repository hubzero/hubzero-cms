<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
* Default file asset handler class
*/
class FileAssetHandler extends AssetHandler
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
			'action_message' => 'Post notes or slides (i.e. a downloadable file)',
			'responds_to'    => array('txt', 'pdf', 'jpg', 'jpeg', 'gif', 'png', 'ppt', 'pptx', 'doc', 'docx', 'xls', 'xlsx', 'zip'),
		);

	/**
	 * Create method for this handler
	 *
	 * @return array of assets created
	 **/
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
		$this->asset['subtype']    = (!empty($this->asset['subtype'])) ? $this->asset['subtype'] : 'file';
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

		// Get the final file path
		$target_path = $uploadDirectory . $filename . '.' . $ext;

		// Move the file to the site folder
		set_time_limit(60);

		// Scan for viruses
		exec("clamscan -i --no-summary --block-encrypted {$_FILES['files']['tmp_name'][0]}", $output, $status);
		if ($status == 1)
		{
			// Scan failed, delete asset and association and return an error
			$assetObj->delete();
			$assocObj->delete();
			JFolder::delete($uploadDirectory);
			return array('error' => 'File rejected because the anti-virus scan failed.');
		}

		if(!$move = move_uploaded_file($_FILES['files']['tmp_name'][0], $target_path))
		{
			// Move failed, delete asset and association and return an error
			$assetObj->delete();
			$assocObj->delete();
			JFolder::delete($uploadDirectory);
			return array('error' => 'Move file failed');
		}

		// Get the url to return to the page
		$course_id      = JRequest::getInt('course_id', 0);
		$offering_alias = JRequest::getCmd('offering', '');
		$course         = new CoursesModelCourse($course_id);

		$url = JRoute::_('index.php?option=com_courses&controller=offering&gid='.$course->get('alias').'&offering='.$offering_alias.'&asset='.$assetObj->get('id'));

		$return_info = array(
			'asset_id'       => $this->assoc['asset_id'],
			'asset_title'    => $this->asset['title'],
			'asset_type'     => $this->asset['type'],
			'asset_subtype'  => $this->asset['subtype'],
			'asset_url'      => $url,
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
}