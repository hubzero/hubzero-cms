<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Incoming
$d = Request::getString('d', 'inline');

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
	App::abort(404, Lang::txt('COM_COURSES_FILE_NOT_FOUND'));
	return;
}

// Add PATH_APP
$filename = PATH_APP . $path;

// Ensure the file exist
if (!file_exists($filename))
{
	App::abort(404, Lang::txt('COM_COURSES_FILE_NOT_FOUND') . ' ' . $filename);
	return;
}

// Force certain extensions to the 'attachment' disposition
$ext = strtolower(Filesystem::extension($filename));
if (!in_array($ext, array('jpg', 'jpeg', 'jpe', 'gif', 'png', 'pdf', 'htm', 'html', 'txt', 'json', 'xml')))
{
	$d = 'attachment';
}

// Initiate a new content server and serve up the file
$xserver = new \Hubzero\Content\Server();
$xserver->filename($filename);
//$xserver->saveas($this->model->get('title') . '.' . $ext);
$xserver->disposition($d);
$xserver->acceptranges(false); // @TODO fix byte range support

if (!$xserver->serve())
{
	// Should only get here on error
	App::abort(500, Lang::txt('COM_COURSES_SERVER_ERROR'));
}
else
{
	// Just exit (i.e. no template)
	exit;
}
