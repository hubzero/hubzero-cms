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
 * @author    Patrick Mulligan <jpmulligan@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Resources Plugin class for adding collections 
 */
class plgResourcesCollections extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return data on a resource sub view (this will be some form of HTML)
	 *
	 * @param   object   $resource  Current resource
	 * @param   string   $option    Name of the component
	 * @param   integer  $miniview  View style
	 * @return  array
	 */
	public function onResourcesSub($resource, $option, $miniview=0)
	{
		if (!$resource->type->params->get('plg_collections', 0) || User::isGuest())
		{
			return;
		}
		$pparams = Plugin::params('resources', 'collections');
		$collectionType = $pparams->get('collection_alias');
		$allowPublished = $pparams->get('collection_afterpublished');
		$typeObj = Components\Resources\Models\Type::oneByAlias($collectionType);
		if (!($typeObj) || !($typeObj->get('collection')))
		{
			return false;
		}
		$parentIds = $resource->parents->fieldsByKey('id');
		$resources = Components\Resources\Models\Entry::all();
		$resources->whereEquals('standalone', 1);
		$resources->whereEquals('type', $typeObj->get('id'));
		if (!$allowPublished)
		{
			$resources->whereEquals('published', 2);
		}
		if (!empty($parentIds))
		{
			$resources->where('id', 'NOT IN', $parentIds);
		}
		if (User::authorize('core.admin', $option))
		{
			$resources->whereEquals('created_by', User::get('id'), 1);
			// Get the groups the user has access to
			$xgroups = \Hubzero\User\Helper::getGroups(User::get('id'), 'all');
			$usersgroups = array();
			if (!empty($xgroups))
			{
				foreach ($xgroups as $group)
				{
					if ($group->regconfirmed)
					{
						$resources->orWhereLike('group_owner', $group->cn, 1);
					}
				}
			}
		}
		// Instantiate a view
		$view = $this->view('default', 'index');
		$view
			->set('resource', $resource)
			->set('resources', $resources)
			->set('type', $typeObj)
			->js('collections.js')
			->css('collections.css');
		// Return the output
		$arr['html'] = $view->loadTemplate();
		// Return the an array of content
		return $arr;
	}
}
