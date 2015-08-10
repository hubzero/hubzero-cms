<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_HZEXEC_') or die();

// Base this model on the backend version.
require_once __DIR__ . '/article.php';

/**
 * Content Component Article Model
 *
 * @package		Joomla.Site
 * @subpackage	com_content
 * @since 1.5
 */
class ContentModelForm extends ContentModelArticle
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();

		// Load state from the request.
		$pk = Request::getInt('a_id');
		$this->setState('article.id', $pk);

		$this->setState('article.catid', Request::getInt('catid'));

		$return = Request::getVar('return', null, 'default', 'base64');
		$this->setState('return_page', urldecode(base64_decode($return)));

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		$this->setState('layout', Request::getCmd('layout'));
	}

	/**
	 * Method to get article data.
	 *
	 * @param	integer	The id of the article.
	 *
	 * @return	mixed	Content item data object on success, false on failure.
	 */
	public function &getItem($pk = NULL)
	{
		// Initialise variables.
		$itemId = $pk;
		$itemId = (int) (!empty($itemId)) ? $itemId : $this->getState('article.id');

		// Get a row instance.
		$table = $this->getTable();

		// Attempt to load the row.
		$return = $table->load($itemId);

		// Check for a table object error.
		if ($return === false && $table->getError())
		{
			$this->setError($table->getError());
			return false;
		}

		$properties = $table->getProperties(1);
		$value = \Hubzero\Utility\Arr::toObject($properties, '\\Hubzero\\Base\\Object');

		// Convert attrib field to Registry.
		$value->params = new \Hubzero\Config\Registry($value->attribs);

		// Compute selected asset permissions.
		$userId = User::get('id');
		$asset  = 'com_content.article.'.$value->id;

		// Check general edit permission first.
		if (User::authorise('core.edit', $asset)) {
			$value->params->set('access-edit', true);
		}
		// Now check if edit.own is available.
		elseif (!empty($userId) && User::authorise('core.edit.own', $asset)) {
			// Check for a valid user and that they are the owner.
			if ($userId == $value->created_by) {
				$value->params->set('access-edit', true);
			}
		}

		// Check edit state permission.
		if ($itemId) {
			// Existing item
			$value->params->set('access-change', User::authorise('core.edit.state', $asset));
		}
		else {
			// New item.
			$catId = (int) $this->getState('article.catid');

			if ($catId) {
				$value->params->set('access-change', User::authorise('core.edit.state', 'com_content.category.'.$catId));
				$value->catid = $catId;
			}
			else {
				$value->params->set('access-change', User::authorise('core.edit.state', 'com_content'));
			}
		}

		$value->articletext = $value->introtext;
		if (!empty($value->fulltext)) {
			$value->articletext .= '<hr id="system-readmore" />'.$value->fulltext;
		}

		return $value;
	}

	/**
	 * Get the return URL.
	 *
	 * @return	string	The return URL.
	 * @since	1.6
	 */
	public function getReturnPage()
	{
		return base64_encode(urlencode($this->getState('return_page')));
	}
}
