<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport( 'joomla.application.component.controlleradmin' );

/**
 * The Menu Item Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @since		1.6
 */
class MenusControllerItems extends JControllerAdmin
{
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('unsetDefault',	'setDefault');
	}

	/**
	 * Proxy for getModel
	 * @since	1.6
	 */
	function getModel($name = 'Item', $prefix = 'MenusModel', $config = array())
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
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

		$this->setRedirect('index.php?option=com_menus&view=items');

		// Initialise variables.
		$model = $this->getModel();

		if ($model->rebuild())
		{
			// Reorder succeeded.
			$this->setMessage(Lang::txt('COM_MENUS_ITEMS_REBUILD_SUCCESS'));
			return true;
		}
		else
		{
			// Rebuild failed.
			$this->setMessage(Lang::txt('COM_MENUS_ITEMS_REBUILD_FAILED'));
			return false;
		}
	}

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

	/**
	 * Method to set the home property for a list of items
	 *
	 * @since	1.6
	 */
	function setDefault()
	{
		// Check for request forgeries
		Session::checkToken('request') or die(Lang::txt('JINVALID_TOKEN'));

		// Get items to publish from the request.
		$cid   = Request::getVar('cid', array(), '', 'array');
		$data  = array('setDefault' => 1, 'unsetDefault' => 0);
		$task  = $this->getTask();
		$value = \Hubzero\Utility\Arr::getValue($data, $task, 0, 'int');

		if (empty($cid))
		{
			throw new Exception(Lang::txt($this->text_prefix.'_NO_ITEM_SELECTED'), 500);
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			\Hubzero\Utility\Arr::toInteger($cid);

			// Publish the items.
			if (!$model->setHome($cid, $value))
			{
				throw new Exception($model->getError(), 500);
			}
			else
			{
				if ($value == 1)
				{
					$ntext = 'COM_MENUS_ITEMS_SET_HOME';
				}
				else
				{
					$ntext = 'COM_MENUS_ITEMS_UNSET_HOME';
				}
				$this->setMessage(Lang::txts($ntext, count($cid)));
			}
		}

		$this->setRedirect(Route::url('index.php?option='.$this->option.'&view='.$this->view_list, false));
	}
}
