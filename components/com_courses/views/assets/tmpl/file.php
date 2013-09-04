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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Get some needed libraries
ximport('Hubzero_Content_Server');

// Incoming
$d = JRequest::getVar('d', 'inline');

//make sure we have a proper disposition
if ($d != "inline" && $d != "attachment")
{
	$d = "inline";
}

// File path
$path = $this->model->path($this->course->get('id'));

// Ensure we have a path
if (empty($path)) 
{
	JError::raiseError(404, JText::_('COM_COURSES_FILE_NOT_FOUND'));
	return;
}
/*if (preg_match("/^\s*http[s]{0,1}:/i", $resource->path)) 
{
	JError::raiseError(404, JText::_('COM_RESOURCES_BAD_FILE_PATH'));
	return;
}
if (preg_match("/^\s*[\/]{0,1}index.php\?/i", $resource->path)) 
{
	JError::raiseError(404, JText::_('COM_RESOURCES_BAD_FILE_PATH'));
	return;
}
// Disallow windows drive letter
if (preg_match("/^\s*[.]:/", $resource->path)) 
{
	JError::raiseError(404, JText::_('COM_RESOURCES_BAD_FILE_PATH'));
	return;
}
// Disallow \
if (strpos('\\', $resource->path)) 
{
	JError::raiseError(404, JText::_('COM_RESOURCES_BAD_FILE_PATH'));
	return;
}
// Disallow ..
if (strpos('..', $resource->path)) 
{
	JError::raiseError(404, JText::_('COM_RESOURCES_BAD_FILE_PATH'));
	return;
}*/

// Get the configured upload path
/*$base_path = $this->config->get('uploadpath', '/site/resources');
if ($base_path) 
{
	$base_path = DS . trim($base_path, DS);
}

// Does the path start with a slash?
if (substr($resource->path, 0, 1) != DS) 
{
	$resource->path = DS . $resource->path;
	// Does the beginning of the $resource->path match the config path?
	if (substr($resource->path, 0, strlen($base_path)) == $base_path) 
	{
		// Yes - this means the full path got saved at some point
	} 
	else 
	{
		// No - append it
		$resource->path = $base_path . $resource->path;
	}
}*/

// Add JPATH_ROOT
$filename = JPATH_ROOT . $path;

// Ensure the file exist
if(!file_exists($filename)) 
{
	JError::raiseError(404, JText::_('COM_COURSES_FILE_NOT_FOUND') . ' ' . $filename);
	return;
}

// Force certain extensions to the 'attachment' disposition
jimport('joomla.filesystem.file');
$ext = strtolower(JFile::getExt($filename));
if (!in_array($ext, array('jpg', 'jpeg', 'jpe', 'gif', 'png', 'pdf', 'htm', 'html', 'txt', 'json', 'xml')))
{
	$d = 'attachment';
}

// Initiate a new content server and serve up the file
$xserver = new Hubzero_Content_Server();
$xserver->filename($filename);
//$xserver->saveas($this->model->get('title') . '.' . $ext);
$xserver->disposition($d);
$xserver->acceptranges(false); // @TODO fix byte range support

if (!$xserver->serve()) 
{
	// Should only get here on error
	JError::raiseError(500, JText::_('COM_COURSES_SERVER_ERROR'));
} 
else 
{
	// Just exit (i.e. no template)
	exit;
}