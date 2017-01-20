<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

jimport('joomla.application.component.controlleradmin');

/**
 * The Categories List Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class CategoriesControllerCategories extends JControllerAdmin
{
	/**
	 * Proxy for getModel
	 *
	 * @param	string	$name	The model name. Optional.
	 * @param	string	$prefix	The class prefix. Optional.
	 *
	 * @return	object	The model.
	 * @since	1.6
	 */
	function getModel($name = 'Category', $prefix = 'CategoriesModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * Rebuild the nested set tree.
	 *
	 * @return	bool	False on failure or error, true on success.
	 * @since	1.6
	 */
	public function rebuild()
	{
		Session::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		$extension = Request::getCmd('extension');
		$this->setRedirect(Route::url('index.php?option=com_categories&view=categories&extension='.$extension, false));

		// Initialise variables.
		$model = $this->getModel();

		if ($model->rebuild())
		{
			// Rebuild succeeded.
			$this->setMessage(Lang::txt('COM_CATEGORIES_REBUILD_SUCCESS'));
			return true;
		}
		else
		{
			// Rebuild failed.
			$this->setMessage(Lang::txt('COM_CATEGORIES_REBUILD_FAILURE'));
			return false;
		}
	}

	/**
	 * Save the manual order inputs from the categories list page.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function saveorder()
	{
		Session::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// Get the arrays from the Request
		$order = Request::getVar('order', null, 'post', 'array');
		$originalOrder = explode(',', Request::getString('original_order_values'));

		// Make sure something has changed
		if (!($order === $originalOrder))
		{
			parent::saveorder();
		}
		else
		{
			// Nothing to reorder
			$this->setRedirect(Route::url('index.php?option='.$this->option.'&view='.$this->view_list, false));
			return true;
		}
	}

	/** Deletes and returns correctly.  
	 *
	 * @return	void
	 * @since	2.5.12
	 */
	public function delete()
	{
		Session::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = Request::getVar('cid', array(), '', 'array');
		$extension = Request::getVar('extension', null);

		if (!is_array($cid) || count($cid) < 1)
		{
			Notify::error(Lang::txt($this->text_prefix . '_NO_ITEM_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			\Hubzero\Utility\Arr::toInteger($cid);

			// Remove the items.
			if ($model->delete($cid))
			{
				$this->setMessage(Lang::txts($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}

		$this->setRedirect(Route::url('index.php?option=' . $this->option . '&extension=' . $extension, false));
	}
}
