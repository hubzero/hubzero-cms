<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Helpers\Document\Renderer;

use Components\Groups\Helpers\Document\Renderer;
use Components\Groups\Models\Module\Archive;

class Module extends Renderer
{
	/**
	 * Render Module to group template or page
	 *
	 * @param    string
	 */
	public function render()
	{
		// var to hold content
		$content = '';

		// get the page id
		$pageid = ($this->page) ? $this->page->get('id') : null;

		// get the list of modules
		$groupModuleArchive = Archive::getInstance();
		$modules = $groupModuleArchive->modules('list', array(
			'gidNumber' => $this->group->get('gidNumber'),
			'state'     => array(1),
			'approved'  => ($this->allMods == 1) ? array(0,1) : array(1),
			'title'     => $this->params->title,
			'orderby'   => 'ordering DESC'
		));

		// make sure we have a module
		if ($modules->count() < 1)
		{
			return $content;
		}

		// get first module
		$module = $modules->first();

		// make sure module can be displayed
		if ($module->displayOnPage($pageid))
		{
			$content .= $module->content('parsed');
		}

		//return final output
		return $content;
	}
}
