<?php
// Declare the namespace.
namespace Components\Drwho\Site\Controllers;

use Hubzero\Component\SiteController;

/**
 * Drwho controller for show seasons
 */
class Seasons extends SiteController
{
	/**
	 * Default task. Displays a list of characters.
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$this->view
			->setLayout('display')
			->display();
	}

	/**
	 * Display one
	 *
	 * @return  void
	 */
	public function oneTask()
	{
		// Output the view
		$this->view
			->display();
	}

	/**
	 * Display two
	 *
	 * @return  void
	 */
	public function twoTask()
	{
		$this->view
			->display();
	}

	/**
	 * Display three
	 *
	 * @return  void
	 */
	public function threeTask()
	{
		$this->view
			->display();
	}

	/**
	 * Display four
	 *
	 * @return  void
	 */
	public function fourTask()
	{
		$this->view
			->display();
	}
}
