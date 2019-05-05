<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Help\Site\Controllers;

use Components\Help\Helpers\Finder;
use Hubzero\Component\SiteController;
use Hubzero\Component\View;
use Exception;
use Request;
use Lang;
use App;

/**
 * Help controller class
 */
class Help extends SiteController
{
	/**
	 * Display Help Article Pages
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Force help template
		Request::setVar('tmpl', 'help');

		// Get the page we are trying to access
		$page      = Request::getWord('page', 'index');
		$component = Request::getWord('component', 'com_help');
		$name      = str_replace('com_', '', $component);
		$extension = Request::getWord('extension', '');

		$tmpl = App::get('template')->template;
		$lang = Lang::getTag();

		$finalHelpPage = Finder::page($component, $extension, $page);

		$content = '';

		// If we have an existing page
		if ($finalHelpPage != '')
		{
			ob_start();
			require_once $finalHelpPage;
			$content = ob_get_contents();
			ob_end_clean();
		}
		else if (isset($component) && $component != '' && $page == 'index')
		{
			// Get list of component pages
			$pages[] = Finder::pages($component);

			//display page
			$view = with(new View(array(
					'name'   => $this->_controller,
					'layout' => 'index'
				)))
				->set('option', $this->_option)
				->set('controller', $this->_controller)
				->set('layoutExt', $this->layoutExt)
				->set('pages', $pages);

			$content = $view->loadTemplate();
		}
		else
		{
			// Raise error to avoid security bug
			throw new Exception(Lang::txt('COM_HELP_PAGE_NOT_FOUND'), 404);
		}

		// Set vars for views
		$this->view
			->set('modified', filemtime($finalHelpPage))
			->set('component', $component)
			->set('extension', $extension)
			->set('content', $content)
			->set('page', $page)
			->display();
	}
}
