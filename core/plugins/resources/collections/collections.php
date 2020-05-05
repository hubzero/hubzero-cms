<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
		if (!$resource->type->params->get('plg_collections', 0))
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
		if (!User::authorize('core.admin', $option))
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
