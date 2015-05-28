<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once dirname(__FILE__).'/articles.php';

/**
 * @package		Joomla.Administrator
 * @subpackage	com_content
 */
class ContentControllerFeatured extends ContentControllerArticles
{
	/**
	 * Removes an item
	 */
	function delete()
	{
		// Check for request forgeries
		Session::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// Initialise variables.
		$ids = Request::getVar('cid', array(), '', 'array');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!User::authorise('core.delete', 'com_content.article.'.(int) $id))
			{
				// Prune items that you can't delete.
				unset($ids[$i]);
				Notify::warning(Lang::txt('JERROR_CORE_DELETE_NOT_PERMITTED'));
			}
		}

		if (empty($ids))
		{
			Notify::error(Lang::txt('JERROR_NO_ITEMS_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Remove the items.
			if (!$model->featured($ids, 0))
			{
				throw new Exception($model->getError(), 500);
			}
		}

		$this->setRedirect('index.php?option=com_content&view=featured');
	}

	/**
	 * Method to publish a list of articles.
	 *
	 * @return	void
	 * @since	1.0
	 */
	function publish()
	{
		parent::publish();

		$this->setRedirect('index.php?option=com_content&view=featured');
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Feature', $prefix = 'ContentModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}
