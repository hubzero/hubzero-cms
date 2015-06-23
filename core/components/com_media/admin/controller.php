<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

/**
 * Media Manager Component Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @version 1.5
 */
class MediaController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		Plugin::import('content');
		$vName = Request::getCmd('view', 'media');
		switch ($vName)
		{
			case 'images':
				$vLayout = Request::getCmd('layout', 'default');
				$mName = 'manager';

				break;

			case 'imagesList':
				$mName = 'list';
				$vLayout = Request::getCmd('layout', 'default');

				break;

			case 'mediaList':
				$mName = 'list';
				$vLayout = Request::getState('media.list.layout', 'layout', 'thumbs', 'word');
				break;

			case 'media':
			default:
				$vName = 'media';
				$vLayout = Request::getCmd('layout', 'default');
				$mName = 'manager';
				break;
		}

		$vType = Document::getType();

		// Get/Create the view
		$view = $this->getView($vName, $vType);

		// Get/Create the model
		if ($model = $this->getModel($mName))
		{
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}

		// Set the layout
		$view->setLayout($vLayout);

		// Display the view
		$view->display();

		return $this;
	}

	function ftpValidate()
	{
		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');
	}
}
