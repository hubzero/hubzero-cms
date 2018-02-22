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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Publications\Tables;
use stdClass;
use Request;
use Config;
use Route;
use Lang;
use App;

/**
 * Manage publication master types
 */
class Types extends AdminController
{
	/**
	 * List types
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$this->view->filters = array(
			'limit' => Request::getState(
				$this->_option . '.types.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.types.limitstart',
				'limitstart',
				0,
				'int'
			),
			'search' => Request::getState(
				$this->_option . '.types.search',
				'search',
				''
			),
			'sort' => Request::getState(
				$this->_option . '.types.sort',
				'filter_order',
				'id'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.types.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		$this->view->filters['state'] = 'all';

		// Instantiate an object
		$database = App::get('db');
		$rt = new \Components\Publications\Tables\MasterType($database);

		// Get a record count
		$this->view->total = $rt->getCount($this->view->filters);

		// Get records
		$this->view->rows = $rt->getRecords($this->view->filters);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		$this->view->config = $this->config;

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Add a new type
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit block order
	 *
	 * @return  void
	 */
	public function addblockTask()
	{
		Request::setVar('hidemainmenu', 1);

		// Incoming
		$id = Request::getInt('id', 0);
		$database = App::get('db');

		$this->view->row = new \Components\Publications\Tables\MasterType($database);

		// Load object
		if (!$id || !$this->view->row->load($id))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_PUBLICATIONS_ERROR_LOAD_TYPE'),
				'notice'
			);
			return;
		}

		// Load curation
		$this->view->curation = new \Components\Publications\Models\Curation(
			$this->view->row->curation
		);

		// Get blocks model
		$blocksModel = new \Components\Publications\Models\Blocks($database);

		// Get available blocks
		$this->view->blocks = $blocksModel->getBlocks(
			'*',
			" WHERE status=1",
			" ORDER BY ordering, id"
		);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		$this->view->config = $this->config;

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Removes a block
	 *
	 * @return  void
	 */
	public function removeblockTask()
	{
		// Going to the confirmation page
		if (!(Request::getMethod() === 'POST'))
		{
			Request::setVar('hidemainmenu', 1);

			// Incoming
			$id = Request::getInt('id', 0);
			$this->view->blockid = Request::getInt('blockid', 0);
			$database = App::get('db');

			$this->view->row = new \Components\Publications\Tables\MasterType($database);

			// Load object
			if (!$id || !$this->view->row->load($id))
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
					Lang::txt('COM_PUBLICATIONS_ERROR_LOAD_TYPE'),
					'notice'
				);
				return;
			}

			// Load curation
			$this->view->curation = new \Components\Publications\Models\Curation(
				$this->view->row->curation
			);

			// Set any errors
			if ($this->getError())
			{
				$this->view->setError($this->getError());
			}

			$this->view->config = $this->config;

			// Output the HTML
			$this->view->display();
		}
		//Actual deletion page
		else
		{
			// Check for request forgeries
			Request::checkToken();

			// Incoming
			$id       = Request::getInt('id', 0);
			$confirm  = Request::getBool('confirmdel', false);
			$blockid = Request::getInt('blockid', 0);
			$this->view->blockid = $blockid;
			$database = App::get('db');

			$this->view->row = new \Components\Publications\Tables\MasterType($database);
			$row = $this->view->row;

			// Load object
			if (!$id || !$this->view->row->load($id))
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
					Lang::txt('COM_PUBLICATIONS_ERROR_LOAD_TYPE'),
					'notice'
				);
				return;
			}

			// Load curation
			$this->view->curation = new \Components\Publications\Models\Curation(
				$this->view->row->curation
			);
			$curation = $this->view->curation;

			// Go back to the confirmation page
			if (!$confirm)
			{
				$this->setError(Lang::txt('COM_PUBLICATIONS_BLOCK_ERROR_CONFIRM_REMOVAL'));

				foreach ($this->getErrors() as $error)
				{
					$this->view->setError($error);
				}
				$this->view->config = $this->config;

				$this->view->display();

				return;
			}
			// Delete and redirect to the main type page
			else
			{
				$url = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id=' . $id, false);

				$manifest   = new stdClass;
				$manifest->blocks = new stdClass;
				$oManifest  = $curation->_manifest;

				if ($blockid)
				{
					// Get blocks model
					$blocksModel = new \Components\Publications\Models\Blocks($database);

					// Insert new block
					foreach ($oManifest->blocks as $oId => $oBlock)
					{
						if ($oId != $blockid) {
							$manifest->blocks->$oId = $oBlock;
						}
					}
					$manifest->params = $oManifest->params;
					if (isset($oManifest->maxid))
					{
						$manifest->maxid = $oManifest->maxid;
					}
					$row->curation = json_encode($manifest);
					$row->store();
				}

				App::redirect(
					$url,
					Lang::txt('COM_PUBLICATIONS_SUCCESS_TYPE_BLOCK_REMOVED')
				);
			}
		}
	}

	/**
	 * Save new block
	 *
	 * @param   boolean  $redirect
	 * @return  void
	 */
	public function saveblockTask($redirect = false)
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$id       = Request::getInt('id', 0);
		$newblock = Request::getVar('newblock', '');
		$before   = Request::getInt('before', 1);

		$database = App::get('db');

		$row = new \Components\Publications\Tables\MasterType($database);

		// Load object
		if (!$id || !$row->load($id))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_PUBLICATIONS_ERROR_LOAD_TYPE'),
				'notice'
			);
			return;
		}

		// Load curation
		$curation = new \Components\Publications\Models\Curation(
			$row->curation
		);

		$url = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id=' . $id, false);

		$manifest   = new stdClass;
		$manifest->blocks = new stdClass;
		$oManifest  = $curation->_manifest;

		if ($newblock)
		{
			// Get blocks model
			$blocksModel = new \Components\Publications\Models\Blocks($database);

			// Get max used block and element IDs
			$maxBlockId   = isset($oManifest->maxid) ? $oManifest->maxid : 0;
			$maxElementId = 0;
			foreach ($oManifest->blocks as $oId => $oBlock)
			{
				if ($oId > $maxBlockId)
				{
					$maxBlockId = $oId;
				}
				$parentBlock = $blocksModel->getBlockProperty($oBlock->name, '_parentname');
				$pnBlock     = $blocksModel->getBlockProperty($newblock, '_parentname');

				if ($parentBlock == $pnBlock && $oBlock->elements)
				{
					foreach ($oBlock->elements as $elId => $el)
					{
						if ($elId > $maxElementId)
						{
							$maxElementId = $elId;
						}
					}
				}
			}

			// Get new block default manifest
			$defaultManifest = $blocksModel->getManifest($newblock, true);

			// Determine IDs for new block (must be unique)
			$nextBlockId   = $maxBlockId + 1;
			$nextElementId = $maxElementId + 1;

			// Re-configure default manifest
			$newManifest = $defaultManifest;
			if ($defaultManifest->elements)
			{
				$els = new stdClass;
				foreach ($defaultManifest->elements as $dElId => $dEl)
				{
					$els->$nextElementId = $dEl;
					$nextElementId++;
				}
				$newManifest->elements = $els;
			}
			$newManifest->active = 0;

			// Insert new block
			foreach ($oManifest->blocks as $oId => $oBlock)
			{
				if ($oId == $before)
				{
					$manifest->blocks->$nextBlockId = $newManifest;
				}
				$manifest->blocks->$oId = $oBlock;
			}
			$manifest->params = $oManifest->params;
			$manifest->maxid = $nextBlockId;
			$row->curation = json_encode($manifest);
			$row->store();
		}

		App::redirect(
			$url,
			Lang::txt('COM_PUBLICATIONS_SUCCESS_TYPE_BLOCK_ADDED')
		);
	}

	/**
	 * Curation editing for experts
	 *
	 * @return  void
	 */
	public function advancedTask()
	{
		Request::setVar('hidemainmenu', 1);

		// Incoming
		$id = Request::getInt('id', 0);

		$database = App::get('db');

		$this->view->row = new \Components\Publications\Tables\MasterType($database);

		// Load object
		if (!$id || !$this->view->row->load($id))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_PUBLICATIONS_ERROR_LOAD_TYPE'),
				'notice'
			);
			return;
		}

		// Load curation
		$this->view->curation = new \Components\Publications\Models\Curation(
			$this->view->row->curation
		);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		$this->view->config = $this->config;

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Curation editing for experts
	 *
	 * @return  void
	 */
	public function saveadvancedTask()
	{
		// Incoming
		$id       = Request::getInt('id', 0);
		$curation = Request::getVar('curation', '', 'post', 'none', 2);
		$curation = preg_replace('/\s{2,}/u', ' ', preg_replace('/[\n\r\t]+/', '', $curation));
		$database = App::get('db');

		$row = new \Components\Publications\Tables\MasterType($database);

		// Load object
		if (!$id || !$row->load($id))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_PUBLICATIONS_ERROR_LOAD_TYPE'),
				'error'
			);
			return;
		}

		$url = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id=' . $id, false);

		if (!trim($curation) || $this->isJson(trim($curation)))
		{
			$row->curation = trim($curation);
			$row->store();
		}
		else
		{
			App::redirect(
				$url,
				Lang::txt('COM_PUBLICATIONS_ERROR_SAVING_ADVANCED_CURATION'),
				'error'
			);
			return;
		}

		App::redirect(
			$url,
			Lang::txt('COM_PUBLICATIONS_SUCCESS_TYPE_CURATION_SAVED')
		);
	}

	/**
	 * Is string valid json?
	 *
	 * @param   string   $string
	 * @return  boolean
	 */
	public function isJson($string)
	{
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	/**
	 * Edit block elements
	 *
	 * @return  void
	 */
	public function editelementsTask()
	{
		Request::setVar('hidemainmenu', 1);

		// Incoming
		$id = Request::getInt('id', 0);
		$this->view->blockId = Request::getInt('bid', 0);
		$database = App::get('db');

		$this->view->row = new \Components\Publications\Tables\MasterType($database);

		// Load object
		if (!$id || !$this->view->row->load($id))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_PUBLICATIONS_ERROR_LOAD_TYPE'),
				'notice'
			);
			return;
		}

		// Load curation
		$this->view->curation = new \Components\Publications\Models\Curation(
			$this->view->row->curation
		);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		$this->view->config = $this->config;

		// Output the HTML
		$this->view->display();

	}

	/**
	 * Save block elements
	 *
	 * @return  void
	 */
	public function saveelementsTask()
	{
		// Incoming
		$id = Request::getInt('id', 0);
		$blockId = Request::getInt('bid', 0);
		$database = App::get('db');

		$row = new \Components\Publications\Tables\MasterType($database);

		// Load object
		if (!$id || !$row->load($id))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_PUBLICATIONS_ERROR_LOAD_TYPE'),
				'notice'
			);
			return;
		}

		// Load curation
		$curation = new \Components\Publications\Models\Curation(
			$row->curation
		);

		$url = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id[]=' . $id, false);

		$objC = new \Components\Publications\Models\Curation($row->curation);
		$manifest = $objC->_manifest;

		// Get curation configs
		$curation = Request::getVar('curation', array(), 'post', 'none', 2);

		// Collect modifications
		if (is_array($curation) && isset($curation['blocks'][$blockId]))
		{
			foreach ($curation['blocks'][$blockId]['elements'] as $elementId => $element)
			{
				foreach ($element as $dataLabel => $dataValue)
				{
					if ($dataLabel == 'params')
					{
						// Save block params
						foreach ($dataValue as $bpName => $bpValue)
						{
							// Type params?
							if ($bpName == 'typeParams')
							{
								foreach ($bpValue as $tpName => $tpValue)
								{
									if (is_array($manifest->blocks->$blockId->elements->$elementId->$dataLabel->$bpName->$tpName))
									{
										$pval = trim($tpValue) ? explode(',', trim($tpValue)) : array();
									}
									else
									{
										$pval = trim($tpValue);
									}
									$manifest->blocks->$blockId->elements->$elementId->$dataLabel->$bpName->$tpName = $pval;
								}
							}
							elseif (is_array($manifest->blocks->$blockId->elements->$elementId->$dataLabel->$bpName))
							{
								$pval = trim($bpValue) ? explode(',', trim($bpValue)) : array();
								$manifest->blocks->$blockId->elements->$elementId->$dataLabel->$bpName = $pval;
							}
							else
							{
								$pval = trim($bpValue);
								$manifest->blocks->$blockId->elements->$elementId->$dataLabel->$bpName = $pval;
							}
						}
					}
					else
					{
						$manifest->blocks->$blockId->elements->$elementId->$dataLabel = trim($dataValue);
					}
				}
			}
		}

		// Store modified curation
		$row->curation = json_encode($manifest);
		$row->store();

		App::redirect(
			$url,
			Lang::txt('COM_PUBLICATIONS_SUCCESS_TYPE_ELEMENTS_SAVED')
		);

	}

	/**
	 * Edit block order
	 *
	 * @return  void
	 */
	public function editblockorderTask()
	{
		Request::setVar('hidemainmenu', 1);

		// Incoming
		$id = Request::getInt('id', 0);
		$database = App::get('db');

		$this->view->row = new \Components\Publications\Tables\MasterType($database);

		// Load object
		if (!$id || !$this->view->row->load($id))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_PUBLICATIONS_ERROR_LOAD_TYPE'),
				'notice'
			);
			return;
		}

		// Load curation
		$this->view->curation = new \Components\Publications\Models\Curation(
			$this->view->row->curation
		);

		// Get blocks model
		$blocksModel = new \Components\Publications\Models\Blocks($database);

		// Get available blocks
		$this->view->blocks = $blocksModel->getBlocks(
			'*',
			" WHERE status=1",
			" ORDER BY ordering, id"
		);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		$this->view->config = $this->config;

		// Output the HTML
		$this->view->display();

	}

	/**
	 * Save block order
	 *
	 * @param   boolean  $redirect
	 * @return  void
	 */
	public function saveblockorderTask($redirect = false)
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$id       = Request::getInt('id', 0);
		$neworder = Request::getVar('neworder', '');
		$order    = explode('-', $neworder);
		$database = App::get('db');

		$row = new \Components\Publications\Tables\MasterType($database);

		// Load object
		if (!$id || !$row->load($id))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_PUBLICATIONS_ERROR_LOAD_TYPE'),
				'notice'
			);
			return;
		}

		// Load curation
		$curation = new \Components\Publications\Models\Curation(
			$row->curation
		);

		$url = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id=' . $id, false);

		$manifest = new stdClass;
		if ($neworder && !empty($order))
		{
			$oManifest = $curation->_manifest;
			foreach ($order as $o)
			{
				if (trim($o))
				{
					if (!isset($manifest->blocks))
					{
						$manifest->blocks = new stdClass;
					}
					$manifest->blocks->$o = $oManifest->blocks->$o;
				}
			}

			$manifest->params = $oManifest->params;
			if (isset($oManifest->maxid))
			{
				$manifest->maxid = $oManifest->maxid;
			}
			$row->curation = json_encode($manifest);
			$row->store();
		}

		App::redirect(
			$url,
			Lang::txt('COM_PUBLICATIONS_SUCCESS_TYPE_ORDER_SAVED')
		);
	}

	/**
	 * Edit a type
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row = null)
	{
		Request::setVar('hidemainmenu', 1);

		$database = App::get('db');

		if (!is_object($row))
		{
			// Incoming (expecting an array)
			$id = Request::getVar('id', array(0));
			$id = is_array($id) ? $id[0] : $id;

			// Load the object
			$row = new \Components\Publications\Tables\MasterType($database);
			$row->load($id);
		}

		$this->view->row = $row;

		$this->view->curation = new \Components\Publications\Models\Curation(
			$this->view->row->curation
		);

		// Get blocks model
		$blocksModel = new \Components\Publications\Models\Blocks($database);

		// Get available blocks
		$this->view->blocks = $blocksModel->getBlocks(
			'*',
			" WHERE status=1",
			" ORDER BY ordering, id"
		);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		$this->view->config = $this->config;

		// Get all active categories
		$objC = new \Components\Publications\Tables\Category($database);
		$this->view->cats = $objC->getCategories();

		// Output the HTML
		$this->view
		     ->setLayout('curation')
		     ->display();
	}

	/**
	 * Save a publication and fall through to edit view
	 *
	 * @return  void
	 */
	public function applyTask()
	{
		$this->saveTask(true);
	}

	/**
	 * Save a type
	 *
	 * @param   boolean  $redirect
	 * @return  void
	 */
	public function saveTask($redirect = false)
	{
		// Check for request forgeries
		Request::checkToken();

		$fields = Request::getVar('fields', array(), 'post', 'none', 2);
		$fields = array_map('trim', $fields);
		$database = App::get('db');

		// Initiate extended database class
		$row = new \Components\Publications\Tables\MasterType($database);

		$url = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id=' . $fields['id'], false);

		// Load record
		if ($fields['id'])
		{
			$row->load($fields['id']);
		}

		// Bind new data
		if (!$row->bind($fields))
		{
			App::redirect($url, $row->getError(), 'error');
			return;
		}

		// Save curation config
		if ($row->id)
		{
			// Incoming
			$curatorGroup = Request::getVar('curatorgroup', '');
			if ($group = \Hubzero\User\Group::getInstance($curatorGroup))
			{
				$row->curatorGroup = $group->get('gidNumber');
			}
			if (!$curatorGroup)
			{
				$row->curatorGroup = 0;
			}

			$objC = new \Components\Publications\Models\Curation($row->curation);
			$manifest = $objC->_manifest;

			// Get curation configs
			$curation = Request::getVar('curation', array(), 'post');

			// Collect modifications
			if (is_array($curation))
			{
				// Save params
				if (isset($curation['params']))
				{
					foreach ($curation['params'] as $cpName => $cpValue)
					{
						$manifest->params->$cpName = trim($cpValue);
					}
				}
				// Save blocks
				if (isset($curation['blocks']))
				{
					foreach ($curation['blocks'] as $blockId => $blockData)
					{
						foreach ($blockData as $dataLabel => $dataValue)
						{
							if ($dataLabel == 'params')
							{
								// Save block params
								foreach ($dataValue as $bpName => $bpValue)
								{
									// Determine value type
									if (is_array($manifest->blocks->$blockId->$dataLabel->$bpName))
									{
										$pval = trim($bpValue) ? explode(',', trim($bpValue)) : array();
									}
									else
									{
										$pval = trim($bpValue);
									}
									$manifest->blocks->$blockId->$dataLabel->$bpName = $pval;
								}
							}
							else
							{
								$manifest->blocks->$blockId->$dataLabel = trim($dataValue);
							}
						}
					}
				}
			}

			// Store modified curation
			$row->curation = json_encode($manifest);
		}
		else
		{
			// Get parameters
			$params = Request::getVar('params', '', 'post');
			if (is_array($params))
			{
				$txt = array();
				foreach ($params as $k => $v)
				{
					$txt[] = "$k=$v";
				}
				$row->params = implode("\n", $txt);
			}
		}

		// Check content
		if (!$row->check())
		{
			App::redirect($url, $row->getError(), 'error');
			return;
		}

		// Store new content
		if (!$row->store())
		{
			App::redirect($url, $row->getError(), 'error');
			return;
		}

		// Redirect to edit view?
		if ($redirect)
		{
			App::redirect(
				$url,
				Lang::txt('COM_PUBLICATIONS_SUCCESS_TYPE_SAVED')
			);
		}
		else
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_PUBLICATIONS_SUCCESS_TYPE_SAVED')
			);
		}
	}

	/**
	 * Reorder up
	 *
	 * @return  void
	 */
	public function orderupTask()
	{
		$this->reorderTask(-1);
	}

	/**
	 * Reorder down
	 *
	 * @return  void
	 */
	public function orderdownTask()
	{
		$this->reorderTask(1);
	}

	/**
	 * Reorders types
	 *
	 * @param   integer  $dir
	 * @return  void
	 */
	public function reorderTask($dir = 0)
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$id = Request::getVar('id', array(0), '', 'array');
		$database = App::get('db');

		// Load row
		$row = new \Components\Publications\Tables\MasterType($database);
		$row->load( (int) $id[0]);

		// Update order
		$row->changeOrder($dir);

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Remove one or more types
	 *
	 * @return  void  Redirects back to main listing
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming (expecting an array)
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Ensure we have an ID to work with
		if (empty($ids))
		{
			// Redirect with error message
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_PUBLICATIONS_NO_ITEM_SELECTED'),
				'error'
			);
			return;
		}

		$database = App::get('db');
		$rt = new \Components\Publications\Tables\MasterType($database);

		foreach ($ids as $id)
		{
			// Check if the type is being used
			$total = $rt->checkUsage($id);

			if ($total > 0)
			{
				// Redirect with error message
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
					Lang::txt('COM_PUBLICATIONS_TYPE_BEING_USED', $id),
					'error'
				);
				return;
			}

			// Delete the type
			$rt->delete($id);
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_PUBLICATIONS_ITEMS_REMOVED', count($ids))
		);
	}
}
