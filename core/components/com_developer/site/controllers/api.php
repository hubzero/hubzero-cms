<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Developer\Site\Controllers;

use Hubzero\Component\SiteController;
use Hubzero\Api\Doc\Generator;
use Request;
use Pathway;
use Config;
use Lang;

/**
 * API Controller
 */
class Api extends SiteController
{
	/**
	 * General intro display
	 * 
	 * @return  void
	 */
	public function displayTask()
	{
		$this->_buildPathway();

		$this->view->display();
	}

	/**
	 * Documentation
	 * 
	 * @return  void
	 */
	public function docsTask()
	{
		// build pathway
		$this->_buildPathway();

		// generate documentation
		$generator = new Generator(!Config::get('debug'));

		// render view
		$this->view
			->set('documentation', $generator->output('array'))
			->display();
	}

	/**
	 * Endpoint Documentation
	 * 
	 * @return  void
	 */
	public function endpointTask()
	{
		// build pathway
		$this->_buildPathway();

		$active = Request::getString('active', '');

		// generate documentation
		$generator = new Generator(!Config::get('debug'));
		$documentation = $generator->output('array');

		if (!isset($documentation['sections'][$active]))
		{
			throw new \Exception(Lang::txt('Endpoint not found'), 404);
		}

		// render view
		$this->view
			->set('active', $active)
			->set('documentation', $documentation)
			->display();
	}

	/**
	 * Console 
	 * 
	 * @return  void
	 */
	public function consoleTask()
	{
		// build pathway
		$this->_buildPathway();

		// render view
		$this->view->display();
	}

	/**
	 * Status 
	 * 
	 * @return  void
	 */
	public function statusTask()
	{
		// build pathway
		$this->_buildPathway();

		// render view
		$this->view->display();
	}

	/**
	 * Build Pathway
	 * 
	 * @return  void
	 */
	private function _buildPathway()
	{
		// create breadcrumbs
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}

		Pathway::append(
			Lang::txt(strtoupper($this->_option . '_' . $this->_controller)),
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);

		if (isset($this->_task) && $this->_task != '')
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option . '_' . $this->_controller . '_' . $this->_task)),
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task
			);
		}
	}
}
