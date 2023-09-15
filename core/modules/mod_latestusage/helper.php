<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\LatestUsage;

use Hubzero\Module\Module;

/**
 * Module class for displaying latest usage
 */
class Helper extends Module
{
	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		$database = \App::get('db');

		include_once \Component::path('com_usage') . DS . 'helpers' . DS . 'helper.php';
		$udb = \Components\Usage\Helpers\Helper::getUDBO();

		$this->cls = trim($this->params->get('moduleclass_sfx',''));

		if ($udb)
		{
			$udb->setQuery('SELECT value FROM summary_user_vals WHERE datetime = (SELECT CAST(MAX(datetime) AS CHAR) FROM summary_user_vals) AND period = "12" AND colid = "1" AND rowid = "1"');
			$this->users = $udb->loadResult();

			$udb->setQuery('SELECT value FROM summary_simusage_vals WHERE datetime  = (SELECT CAST(MAX(datetime) AS CHAR) FROM summary_simusage_vals) AND period = "12" AND colid = "1" AND rowid = "2"');
			$this->sims = $udb->loadResult();
		}
		else
		{
			$database->setQuery("SELECT COUNT(*) FROM `#__users`");
			$this->users = $database->loadResult();

			$this->sims = 0;
		}

		$database->setQuery("SELECT COUNT(*) FROM `#__resources` WHERE standalone=1 AND published=1 AND access!=1 AND access!=4");
		$this->resources = $database->loadResult();

		$database->setQuery("SELECT COUNT(*) FROM `#__resources` WHERE standalone=1 AND published=1 AND access!=1 AND access!=4 AND type=7");
		$this->tools = $database->loadResult();

		require $this->getLayoutPath();
	}
}
