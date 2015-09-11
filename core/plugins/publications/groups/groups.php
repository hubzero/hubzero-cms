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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Display sponsors on a resource page
 */
class plgPublicationsGroups extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param      object $publication 	Current publication
	 * @return     array
	 */
	public function &onPublicationSubAreas( $publication )
	{
		$areas = array();
		if ($publication->category()->_params->get('plg_groups', 1) == 1)
		{
			$areas = array(
				'groups' => Lang::txt('PLG_PUBLICATIONS_GROUPS')
			);
		}
		return $areas;
	}

	/**
	 * Return data on a resource sub view (this will be some form of HTML)
	 *
	 * @param      object $publication 	Current publication
	 * @param      string  $option    Name of the component
	 * @param      integer $miniview  View style
	 * @return     array
	 */
	public function onPublicationSub( $publication, $option, $miniview=0 )
	{
		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		$areas = array('groups');
		if (!array_intersect( $areas, $this->onPublicationSubAreas( $publication ) )
		&& !array_intersect( $areas, array_keys( $this->onPublicationSubAreas( $publication ) ) ))
		{
			return false;
		}

		if (!$publication->groupOwner())
		{
			return $arr;
		}

		// Get recommendations
		$this->database = App::get('db');

		// Instantiate a view
		$this->view = new \Hubzero\Plugin\View(array(
			'folder'  => $this->_type,
			'element' => $this->_name,
			'name'    => 'display'
		));

		if ($miniview)
		{
			$this->view->setLayout('mini');
		}

		// Pass the view some info
		$this->view->option   		= $option;
		$this->view->publication 	= $publication;
		$this->view->params   		= $this->params;
		$this->view->group    		= $publication->groupOwner();

		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Return the output
		$arr['html'] = $this->view->loadTemplate();

		return $arr;
	}
}