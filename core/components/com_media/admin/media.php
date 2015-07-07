<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

// Access check.
$asset = Request::getCmd('asset');
$author = Request::getCmd('author');

if (!User::authorise('core.manage', 'com_media')
	&&	(!$asset or (
			!User::authorise('core.edit', $asset)
		&&	!User::authorise('core.create', $asset)
		&& 	count(User::getAuthorisedCategories($asset, 'core.create')) == 0)
		&&	!(User::get('id') == $author && User::authorise('core.edit.own', $asset))))
{
	return App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
}

$params = Component::params('com_media');

// Load the admin HTML view
require_once JPATH_COMPONENT.'/helpers/media.php';

// Set the path definitions
$popup_upload = Request::getCmd('pop_up', null);
$path = "file_path";

$view = Request::getCmd('view');
if (substr(strtolower($view), 0, 6) == "images" || $popup_upload == 1)
{
	$path = "image_path";
}

define('COM_MEDIA_BASE', PATH_APP . '/' . $params->get($path, 'images'));
define('COM_MEDIA_BASEURL', Request::root() . $params->get($path, 'images'));

$controller	= JControllerLegacy::getInstance('Media');
$controller->execute(Request::getCmd('task'));
$controller->redirect();
