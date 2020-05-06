<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Publications Plugin class for adding collections 
 */
class plgPublicationsCollections extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return data on a publication sub view (this will be some form of HTML)
	 *
	 * @param   object   $publication  Current publication
	 * @param   string   $option    Name of the component
	 * @param   integer  $miniview  View style
	 * @return  array
	 */
	public function onPublicationsSub($publication, $option, $miniview=0)
	{
		if (!$publication->type->params->get('plg_collections', 0))
		{
			return;
		}
		$pparams = Plugin::params('publications', 'collections');
		$collectionType = $pparams->get('collection_alias');
		$allowPublished = $pparams->get('collection_afterpublished');
		$typeObj = Components\Publications\Models\Type::oneByAlias($collectionType);
		if (!($typeObj) || !($typeObj->get('collection')))
		{
			return false;
		}
		$parentIds = $publication->parents->fieldsByKey('id');
		$publications = Components\Publications\Models\Entry::all();
		$publications->whereEquals('standalone', 1);
		$publications->whereEquals('type', $typeObj->get('id'));
		if (!$allowPublished)
		{
			$publications->whereEquals('published', 2);
		}
		if (!empty($parentIds))
		{
			$publications->where('id', 'NOT IN', $parentIds);
		}
		if (!User::authorize('core.admin', $option))
		{
			$publications->whereEquals('created_by', User::get('id'), 1);
			// Get the groups the user has access to
			$xgroups = \Hubzero\User\Helper::getGroups(User::get('id'), 'all');
			$usersgroups = array();
			if (!empty($xgroups))
			{
				foreach ($xgroups as $group)
				{
					if ($group->regconfirmed)
					{
						$publications->orWhereLike('group_owner', $group->cn, 1);
					}
				}
			}
		}
		// Instantiate a view
		$view = $this->view('default', 'index');
		$view
			->set('publication', $publication)
			->set('publications', $publications)
			->set('type', $typeObj)
			->js('collections.js')
			->css('collections.css');
		// Return the output
		$arr['html'] = $view->loadTemplate();
		// Return the an array of content
		return $arr;
	}
}
