<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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