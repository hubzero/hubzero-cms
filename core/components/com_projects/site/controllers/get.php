<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Projects\Tables;

/**
 * Projects Public links controller class
 */
class Get extends SiteController
{
	/**
	 * Pub view for project files, notes etc.
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'publicstamp.php';

		// Incoming
		$stamp = Request::getString('s', '');

		// Clean up stamp value (only numbers and letters)
		$regex  = array('/[^a-zA-Z0-9]/');
		$stamp  = preg_replace($regex, '', $stamp);

		// Load item reference
		$objSt = new Tables\Stamp($this->database);
		if (!$stamp || !$objSt->loadItem($stamp))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
			return;
		}

		// Can only serve files or notes at the moment
		if (!in_array($objSt->type, array('files', 'notes', 'publications')))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
			return;
		}

		// Serve requested item
		$content = Event::trigger('projects.serve', array($objSt->type, $objSt->projectid, $objSt->reference));

		// Output
		foreach ($content as $out)
		{
			if ($out)
			{
				return $out;
			}
		}

		// Redirect if nothing fetched
		App::redirect(
			Route::url('index.php?option=' . $this->_option)
		);
	}
}
