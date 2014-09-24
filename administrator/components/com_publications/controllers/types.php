<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Manage publication master types
 */
class PublicationsControllerTypes extends \Hubzero\Component\AdminController
{
	/**
	 * List types
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$app 	= JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['limit']    = $app->getUserStateFromRequest(
			$this->_option . '.categories.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.categories.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['search']     = trim($app->getUserStateFromRequest(
			$this->_option . '.categories.search',
			'search',
			''
		));
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.categories.sort',
			'filter_order',
			'id'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.categories.sortdir',
			'filter_order_Dir',
			'ASC'
		));

		$this->view->filters['state'] = 'all';

		// Push some styles to the template
		$document = JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS
			. 'assets' . DS . 'css' . DS . 'publications.css');

		// Instantiate an object
		$rt = new PublicationMasterType($this->database);

		// Get a record count
		$this->view->total = $rt->getCount($this->view->filters);

		// Get records
		$this->view->rows = $rt->getRecords($this->view->filters);

		// initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
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
	 * Add a new type
	 *
	 * @return     void
	 */
	public function addTask()
	{
		$this->view->setLayout('edit');
		$this->editTask();
	}

	/**
	 * Edit block order
	 *
	 * @return     void
	 */
	public function addblockTask()
	{
		// Incoming
		$id = JRequest::getInt('id', 0);

		$this->view->row = new PublicationMasterType($this->database);

		// Load object
		if (!$id || !$this->view->row->load($id))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_PUBLICATIONS_ERROR_LOAD_TYPE'),
				'notice'
			);
			return;
		}

		// Load curation
		$this->view->curation = new PublicationsCuration(
			$this->database,
			$this->view->row->curation
		);

		// Get blocks model
		$blocksModel = new PublicationsModelBlocks($this->database);

		// Get available blocks
		$this->view->blocks = $blocksModel->getBlocks('*',
			" WHERE status=1",
			" ORDER BY ordering, id"
		);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		$this->view->config = $this->config;

		// Push some styles to the template
		$document = JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS . 'assets'
			. DS . 'css' . DS . 'publications.css');
		$document->addScript('components' . DS . $this->_option . DS . 'assets'
			. DS . 'js' . DS . 'curation.js');

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save new block
	 *
	 * @return     void
	 */
	public function saveblockTask($redirect = false)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id       = JRequest::getInt('id', 0);
		$newblock = JRequest::getVar('newblock', '');
		$before   = JRequest::getInt('before', 1);

		$row = new PublicationMasterType($this->database);

		// Load object
		if (!$id || !$row->load($id))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_PUBLICATIONS_ERROR_LOAD_TYPE'),
				'notice'
			);
			return;
		}

		// Load curation
		$curation = new PublicationsCuration(
			$this->database,
			$row->curation
		);

		$url = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			. '&task=edit&id[]=' . $id;

		$manifest   = new stdClass;
		$oManifest  = $curation->_manifest;

		if ($newblock)
		{
			// Get blocks model
			$blocksModel = new PublicationsModelBlocks($this->database);

			// Get max used block and element IDs
			$maxBlockId   = 0;
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
			$row->curation = json_encode($manifest);
			$row->store();
		}

		$this->setRedirect(
			$url,
			JText::_('COM_PUBLICATIONS_SUCCESS_TYPE_BLOCK_ADDED')
		);
	}

	/**
	 * Curation editing for experts
	 *
	 * @return     void
	 */
	public function advancedTask()
	{
		// Incoming
		$id = JRequest::getInt('id', 0);

		$this->view->row = new PublicationMasterType($this->database);

		// Load object
		if (!$id || !$this->view->row->load($id))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_PUBLICATIONS_ERROR_LOAD_TYPE'),
				'notice'
			);
			return;
		}

		// Load curation
		$this->view->curation = new PublicationsCuration(
			$this->database,
			$this->view->row->curation
		);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		$this->view->config = $this->config;

		// Push some styles to the template
		$document = JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS . 'assets'
			. DS . 'css' . DS . 'publications.css');
		$document->addScript('components' . DS . $this->_option . DS . 'assets'
			. DS . 'js' . DS . 'curation.js');

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Curation editing for experts
	 *
	 * @return     void
	 */
	public function saveadvancedTask()
	{
		// Incoming
		$id 		= JRequest::getInt('id', 0);
		$curation 	= JRequest::getVar('curation', '', 'post', 'none', 2);
		$curation = preg_replace('/\s{2,}/u', ' ', preg_replace('/[\n\r\t]+/', '', $curation));

		$row 		= new PublicationMasterType($this->database);

		// Load object
		if (!$id || !$row->load($id))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_PUBLICATIONS_ERROR_LOAD_TYPE'),
				'error'
			);
			return;
		}

		$url = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			. '&task=edit&id[]=' . $id;

		if (!trim($curation) || $this->isJson(trim($curation)))
		{
			$row->curation = trim($curation);
			$row->store();
		}
		else
		{
			$this->setRedirect(
				$url,
				JText::_('COM_PUBLICATIONS_ERROR_SAVING_ADVANCED_CURATION'),
				'error'
			);
			return;
		}

		$this->setRedirect(
			$url,
			JText::_('COM_PUBLICATIONS_SUCCESS_TYPE_CURATION_SAVED')
		);
	}

	/**
	 * Is string valid json?
	 *
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
	 * @return     void
	 */
	public function editelementsTask()
	{
		// Incoming
		$id 	  				= JRequest::getInt('id', 0);
		$this->view->sequence 	= JRequest::getInt('bid', 0);

		$this->view->row 		= new PublicationMasterType($this->database);

		// Load object
		if (!$id || !$this->view->row->load($id))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_PUBLICATIONS_ERROR_LOAD_TYPE'),
				'notice'
			);
			return;
		}

		// Load curation
		$this->view->curation = new PublicationsCuration(
			$this->database,
			$this->view->row->curation
		);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		$this->view->config = $this->config;

		// Push some styles to the template
		$document = JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS . 'assets'
			. DS . 'css' . DS . 'publications.css');
		$document->addScript('components' . DS . $this->_option . DS . 'assets'
			. DS . 'js' . DS . 'curation.js');

		// Output the HTML
		$this->view->display();

	}

	/**
	 * Save block elements
	 *
	 * @return     void
	 */
	public function saveelementsTask()
	{
		// Incoming
		$id 	  	= JRequest::getInt('id', 0);
		$sequence 	= JRequest::getInt('bid', 0);

		$row 		= new PublicationMasterType($this->database);

		// Load object
		if (!$id || !$row->load($id))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_PUBLICATIONS_ERROR_LOAD_TYPE'),
				'notice'
			);
			return;
		}

		// Load curation
		$curation = new PublicationsCuration(
			$this->database,
			$row->curation
		);

		$url = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			. '&task=edit&id[]=' . $id;

		$objC = new PublicationsCuration($this->database, $row->curation);
		$manifest = $objC->_manifest;

		// Get curation configs
		$curation = JRequest::getVar('curation', array(), 'post', 'none', 2);

		// Collect modifications
		if (is_array($curation) && isset($curation['blocks'][$sequence]))
		{
			foreach ($curation['blocks'][$sequence]['elements'] as $elementId => $element)
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
									if (is_array($manifest->blocks->$sequence->elements->$elementId->$dataLabel->$bpName->$tpName))
									{
										$pval = trim($tpValue) ? explode(',', trim($tpValue)) : array();
									}
									else
									{
										$pval = trim($tpValue);
									}
									$manifest->blocks->$sequence->elements->$elementId->$dataLabel->$bpName->$tpName = $pval;
								}
							}
							elseif (is_array($manifest->blocks->$sequence->elements->$elementId->$dataLabel->$bpName))
							{
								$pval = trim($bpValue) ? explode(',', trim($bpValue)) : array();
								$manifest->blocks->$sequence->elements->$elementId->$dataLabel->$bpName = $pval;
							}
							else
							{
								$pval = trim($bpValue);
								$manifest->blocks->$sequence->elements->$elementId->$dataLabel->$bpName = $pval;
							}
						}
					}
					else
					{
						$manifest->blocks->$sequence->elements->$elementId->$dataLabel = trim($dataValue);
					}
				}
			}
		}

		// Store modified curation
		$row->curation = json_encode($manifest);
		$row->store();

		$this->setRedirect(
			$url,
			JText::_('COM_PUBLICATIONS_SUCCESS_TYPE_ELEMENTS_SAVED')
		);

	}

	/**
	 * Edit block order
	 *
	 * @return     void
	 */
	public function editblockorderTask()
	{
		// Incoming
		$id = JRequest::getInt('id', 0);

		$this->view->row = new PublicationMasterType($this->database);

		// Load object
		if (!$id || !$this->view->row->load($id))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_PUBLICATIONS_ERROR_LOAD_TYPE'),
				'notice'
			);
			return;
		}

		// Load curation
		$this->view->curation = new PublicationsCuration(
			$this->database,
			$this->view->row->curation
		);

		// Get blocks model
		$blocksModel = new PublicationsModelBlocks($this->database);

		// Get available blocks
		$this->view->blocks = $blocksModel->getBlocks('*',
			" WHERE status=1",
			" ORDER BY ordering, id"
		);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		$this->view->config = $this->config;

		// Push some styles to the template
		$document = JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS . 'assets'
			. DS . 'css' . DS . 'publications.css');
		$document->addScript('components' . DS . $this->_option . DS . 'assets'
			. DS . 'js' . DS . 'curation.js');

		// Output the HTML
		$this->view->display();

	}

	/**
	 * Save block order
	 *
	 * @return     void
	 */
	public function saveblockorderTask($redirect = false)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id       = JRequest::getInt('id', 0);
		$neworder = JRequest::getVar('neworder', '');
		$order 	  = explode('-', $neworder);

		$row = new PublicationMasterType($this->database);

		// Load object
		if (!$id || !$row->load($id))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_PUBLICATIONS_ERROR_LOAD_TYPE'),
				'notice'
			);
			return;
		}

		// Load curation
		$curation = new PublicationsCuration(
			$this->database,
			$row->curation
		);

		$url = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			. '&task=edit&id[]=' . $id;

		$manifest = new stdClass;
		if ($neworder && !empty($order))
		{
			$oManifest  = $curation->_manifest;
			foreach ($order as $o)
			{
				if (trim($o))
				{
					$manifest->blocks->$o = $oManifest->blocks->$o;
				}
			}

			$manifest->params = $oManifest->params;
			$row->curation = json_encode($manifest);
			$row->store();
		}

		$this->setRedirect(
			$url,
			JText::_('COM_PUBLICATIONS_SUCCESS_TYPE_ORDER_SAVED')
		);
	}

	/**
	 * Edit a type
	 *
	 * @return     void
	 */
	public function editTask( $row = null )
	{
		// Use new curation flow?
		$useBlocks  = $this->config->get('curation', 0);
		if ($useBlocks)
		{
			$this->view->setLayout('curation');
		}

		if ($row)
		{
			$this->view->row = $row;
		}
		else
		{
			// Incoming (expecting an array)
			$id = JRequest::getVar('id', array(0));
			if (is_array($id))
			{
				$id = $id[0];
			}
			else
			{
				$id = 0;
			}

			// Load the object
			$this->view->row = new PublicationMasterType($this->database);
			$this->view->row->load($id);

			// Get curation
			if ($useBlocks)
			{
				$this->view->curation = new PublicationsCuration(
					$this->database,
					$this->view->row->curation
				);

				// Get blocks model
				$blocksModel = new PublicationsModelBlocks($this->database);

				// Get available blocks
				$this->view->blocks = $blocksModel->getBlocks('*',
					" WHERE status=1",
					" ORDER BY ordering, id"
				);

			}
		}

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		$this->view->config = $this->config;

		// Get all active categories
		$objC = new PublicationCategory($this->database);
		$this->view->cats = $objC->getCategories();

		// Push some styles to the template
		$document = JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS . 'assets'
			. DS . 'css' . DS . 'publications.css');
		$document->addScript('components' . DS . $this->_option . DS . 'assets'
			. DS . 'js' . DS . 'curation.js');

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save a publication and fall through to edit view
	 *
	 * @return void
	 */
	public function applyTask()
	{
		$this->saveTask(true);
	}

	/**
	 * Save a type
	 *
	 * @return     void
	 */
	public function saveTask($redirect = false)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Use new curation flow?
		$useBlocks  = $this->config->get('curation', 0);

		$fields = JRequest::getVar('fields', array(), 'post', 'none', 2);
		$fields = array_map('trim', $fields);

		// Initiate extended database class
		$row = new PublicationMasterType($this->database);

		$url = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			. '&task=edit&id[]=' . $fields['id'];

		// Load record
		if ($fields['id'])
		{
			$row->load($fields['id']);
		}

		// Bind new data
		if (!$row->bind($fields))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->setRedirect($url);
			return;
		}

		// Save curation config
		if ($useBlocks && $row->id)
		{
			// Incoming
			$curatorGroup = JRequest::getVar('curatorgroup', '');
			if ($group = \Hubzero\User\Group::getInstance($curatorGroup))
			{
				$row->curatorGroup = $group->get('gidNumber');
			}
			if (!$curatorGroup)
			{
				$row->curatorGroup = 0;
			}

			$objC = new PublicationsCuration($this->database, $row->curation);
			$manifest = $objC->_manifest;

			// Get curation configs
			$curation = JRequest::getVar('curation', array(), 'post');

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
			$params = JRequest::getVar('params', '', 'post');
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
			$this->setRedirect($url, $row->getError(), 'error');
			return;
		}

		// Store new content
		if (!$row->store())
		{
			$this->setRedirect($url, $row->getError(), 'error');
			return;
		}

		// Redirect to edit view?
		if ($redirect)
		{
			$this->setRedirect(
				$url,
				JText::_('COM_PUBLICATIONS_SUCCESS_TYPE_SAVED')
			);
		}
		else
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_PUBLICATIONS_SUCCESS_TYPE_SAVED')
			);
		}
		return;
	}

	public function orderupTask()
	{
		$this->reorderTask(-1);
	}

	public function orderdownTask()
	{
		$this->reorderTask(1);
	}

	/**
	 * Reorders types
	 *
	 * @return     void
	 */
	public function reorderTask($dir = 0)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getVar('id', array(0), '', 'array');

		// Load row
		$row = new PublicationMasterType($this->database);
		$row->load( (int) $id[0]);

		// Update order
		$row->changeOrder($dir);

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect('index.php?option=' . $this->_option . '&controller=' . $this->_controller);
	}

	/**
	 * Remove one or more types
	 *
	 * @return     void Redirects back to main listing
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming (expecting an array)
		$ids = JRequest::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Ensure we have an ID to work with
		if (empty($ids))
		{
			// Redirect with error message
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_PUBLICATIONS_NO_ITEM_SELECTED'),
				'error'
			);
			return;
		}

		$rt = new PublicationMasterType($this->database);

		foreach ($ids as $id)
		{
			// Check if the type is being used
			$total = $rt->checkUsage($id);

			if ($total > 0)
			{
				// Redirect with error message
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					JText::sprintf('COM_PUBLICATIONS_TYPE_BEING_USED', $id),
					'error'
				);
				return;
			}

			// Delete the type
			$rt->delete($id);
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::sprintf('COM_PUBLICATIONS_ITEMS_REMOVED', count($ids))
		);
	}
}
