<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Helpers\Document\Renderer;

use Components\Groups\Helpers\Document\Renderer;
use Components\Groups\Models\Module\Archive;

class Modules extends Renderer
{
	/**
	 * Render Modules to group template or page
	 *
	 * @param    string
	 */
	public function render()
	{
		// get the page id
		$pageid = ($this->page) ? $this->page->get('id') : 0;

		// get the list of modules
		$groupModuleArchive = Archive::getInstance();
		$modules = $groupModuleArchive->modules('list', array(
			'gidNumber' => $this->group->get('gidNumber'),
			'state'     => array(1),
			'approved'  => ($this->allMods == 1) ? array(0,1) : array(1),
			'position'  => $this->params->position,
			'orderby'   => 'ordering ASC'
		));

		// loop through each module to display
		$content = '';
		foreach ($modules as $module)
		{
			// make sure module can be displayed
			if ($module->displayOnPage($pageid))
			{
				$content .= $module->content('parsed');
			}
		}

		//return final output
		return $content;
	}
}
