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
defined('_JEXEC') or die( 'Restricted access' );

include_once(dirname(__FILE__) . DS . 'format.php');
include_once(dirname(__FILE__) . DS . 'block.php');

include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'
	. DS . 'com_publications' . DS . 'tables' . DS . 'block.php');

/**
 * Publications blocks class
 *
 */
class PublicationsModelBlocks extends JObject
{
	/**
	 * JDatabase
	 *
	 * @var object
	 */
	public $_db   			= NULL;

	/**
	 * Table class
	 *
	 * @var object
	 */
	public $_objBlock   	= NULL;

	/**
	* @var    array  Loaded elements
	*/
	protected $_blocks 		= array();

	/**
	* @var    array  Directories, where block types can be stored
	*/
	protected $_blockPath 	= array();

	/**
	 * Constructor
	 *
	 * @param      object  &$db      	 JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		$this->_db 		 	= $db;
		$this->_blockPath[] = dirname(__FILE__) . DS . 'blocks';

		$this->_objBlock 	= new PublicationBlock($db);
	}

	/**
	 * Get status for a block within publication
	 *
	 * @return object
	 */
	public function getStatus($name, $pub = NULL, $manifest = NULL, $sequence = 0, $elementId = NULL)
	{
		// Load block
		$block = $this->loadBlock($name);

		if ($block === false || !$pub || !is_object($pub))
		{
			$status = new PublicationsModelStatus();
		}
		else
		{
			$status = $block->getStatus($pub, $manifest, $elementId);
		}

		// Return status
		return $status;

	}

	/**
	 * Loads a block
	 *
	 * @return  object
	 */
	public function loadBlock( $name, $sequence = 0, $new = false )
	{
		$signature = md5($name);

		if ((isset($this->_blocks[$signature])
			&& !($this->_blocks[$signature] instanceof __PHP_Incomplete_Class))
			&& $new === false)
		{
			return	$this->_blocks[$signature];
		}

		$elementClass = 'PublicationsBlock' . ucfirst($name);
		if (!class_exists($elementClass))
		{
			if (isset($this->_blockPath))
			{
				$dirs = $this->_blockPath;
			}
			else
			{
				$dirs = array();
			}

			$file = JFilterInput::getInstance()->clean(str_replace('_', DS, $name).'.php', 'path');

			jimport('joomla.filesystem.path');
			if ($elementFile = JPath::find($dirs, $file))
			{
				include_once $elementFile;
			}
			else
			{
				$false = false;
				return $false;
			}
		}

		if (!class_exists($elementClass))
		{
			$false = false;
			return $false;
		}

		$this->_blocks[$signature] = new $elementClass($this);
		return $this->_blocks[$signature];
	}

	/**
	 * Get list of all available blocks
	 *
	 * @return  array  An array of all available blocks
	 */
	public function getBlocks( $select = '*', $where = '', $order = '')
	{
		return $this->_objBlock->getBlocks($select, $where, $order);
	}

	/**
	 * Get default block manifest
	 *
	 * @param   string  $name   	Name of block to render
	 * @return  object
	 */
	public function getManifest( $name, $new = false )
	{
		// Load block
		$block = $this->loadBlock($name);

		if ($block === false)
		{
			$this->setError('Error loading block');
			return false;
		}
		else
		{
			return $block->getManifest($new);
		}
	}

	/**
	 * Get default element manifest for block
	 *
	 * @param   string  $name   	Name of block to render
	 * @return  object
	 */
	public function getElementManifest( $name )
	{
		// Load block
		$block = $this->loadBlock($name);

		if ($block === false)
		{
			$this->setError('Error loading block');
			return false;
		}
		else
		{
			$manifest = $block->getManifest();
			if ($manifest->elements)
			{
				return $block->getElementManifest();
			}
		}
		return false;
	}

	/**
	 * Get block property
	 *
	 * @param   string  $property   	Name of property
	 * @return  object
	 */
	public function getBlockProperty( $name = NULL, $property = NULL )
	{
		if ($property === NULL)
		{
			return false;
		}

		// Load block
		$block = $this->loadBlock($name);

		if ($block === false)
		{
			return false;
		}
		else
		{
			return $block->getProperty($property);
		}
	}

	/**
	 * Transfers data
	 *
	 * @return  boolean
	 */
	public function transferData( $name, $manifest = NULL, $pub = NULL, $old, $new )
	{
		// Load block
		$block = $this->loadBlock($name);

		if ($block === false)
		{
			return false;
		}
		else
		{
			return $block->transferData($manifest, $pub, $old, $new);
		}
	}

	/**
	 * Renders a block
	 *
	 * @param   string  $name   Name of block to render
	 * @param   string  $view   Name of rendering view (edit / curation / admin / review)
	 * @return  string HTML
	 */
	public function renderBlock( $name, $viewname = 'edit', $manifest = NULL, $pub = NULL, $sequence = 0 )
	{
		// Load block
		$block = $this->loadBlock($name);

		$html = '';
		if ($block === false || !$pub || !is_object($pub))
		{
			$this->setError('Error rendering block');
			return false;
		}
		else
		{
			// Are we allowed to edit?
			$viewname = $viewname == 'edit'
						&& $this->checkFreeze($manifest->params, $pub)
						? 'freeze' : $viewname;

			// Render
			$html = $block->display($pub, $manifest, $viewname, $sequence);
		}

		return $html;
	}

	/**
	 * Check if changes are allowed
	 *
	 * @return  boolean
	 */
	public function checkFreeze($blockParams, $pub)
	{
		// Allow changes in non-draft version?
		$freeze 	= isset($blockParams->published_editing)
					 && $blockParams->published_editing == 0
					 && ($pub->state == 1 || $pub->state == 5 )
					? true : false;

		return $freeze;
	}

	/**
	 * Saves input in a block
	 *
	 * @param   string  $name   Name of block to save
	 * @return  string HTML
	 */
	public function saveBlock( $name, $manifest, $sequence, $pub, $actor = 0, $elementId = 0 )
	{
		// Load block
		$block = $this->loadBlock($name);

		if ($block === false || !$pub || !is_object($pub))
		{
			$this->setError('Error saving block');
			return false;
		}
		else
		{
			$block->save($manifest, $sequence, $pub, $actor, $elementId);

			// Pick up error messages
			if ($block->getError())
			{
				$this->setError($block->getError());
			}

			// Set success message
			if ($block->get('_message'))
			{
				$this->set('_message', $block->get('_message'));
			}

			return true;
		}
	}

	/**
	 * Reorders items in block/element
	 *
	 * @param   string  $name   Name of block to save
	 * @return  string HTML
	 */
	public function reorder( $name, $manifest, $sequence, $pub, $actor = 0, $elementId = 0 )
	{
		// Load block
		$block = $this->loadBlock($name);

		if ($block === false || !$pub || !is_object($pub))
		{
			$this->setError('Error saving block');
			return false;
		}
		else
		{
			$block->reorder($manifest, $sequence, $pub, $actor, $elementId);

			// Pick up error messages
			if ($block->getError())
			{
				$this->setError($block->getError());
			}

			// Set success message
			if ($block->get('_message'))
			{
				$this->set('_message', $block->get('_message'));
			}

			return true;
		}
	}

	/**
	 * Save block/element item
	 *
	 * @param   string  $name   Name of block to save
	 * @return  string HTML
	 */
	public function saveItem( $name, $manifest, $sequence, $pub, $actor = 0, $elementId = 0 )
	{
		// Load block
		$block = $this->loadBlock($name);

		if ($block === false || !$pub || !is_object($pub))
		{
			$this->setError('Error saving block');
			return false;
		}
		else
		{
			$block->saveItem($manifest, $sequence, $pub, $actor, $elementId);

			// Pick up error messages
			if ($block->getError())
			{
				$this->setError($block->getError());
			}

			// Set success message
			if ($block->get('_message'))
			{
				$this->set('_message', $block->get('_message'));
			}

			return true;
		}
	}

	/**
	 * Save block/element item
	 *
	 * @param   string  $name   Name of block to save
	 * @return  string HTML
	 */
	public function deleteItem( $name, $manifest, $sequence, $pub, $actor = 0, $elementId = 0 )
	{
		// Load block
		$block = $this->loadBlock($name);

		if ($block === false || !$pub || !is_object($pub))
		{
			$this->setError('Error saving block');
			return false;
		}
		else
		{
			$block->deleteItem($manifest, $sequence, $pub, $actor, $elementId);

			// Pick up error messages
			if ($block->getError())
			{
				$this->setError($block->getError());
			}

			// Set success message
			if ($block->get('_message'))
			{
				$this->set('_message', $block->get('_message'));
			}

			return true;
		}
	}

	/**
	 * Add item
	 *
	 * @param   string  $name   Name of block to save
	 * @return  string HTML
	 */
	public function addItem( $name, $manifest, $sequence, $pub, $actor = 0, $elementId = 0 )
	{
		// Load block
		$block = $this->loadBlock($name);

		if ($block === false || !$pub || !is_object($pub))
		{
			$this->setError('Error saving block');
			return false;
		}
		else
		{
			$block->addItem($manifest, $sequence, $pub, $actor, $elementId);

			// Pick up error messages
			if ($block->getError())
			{
				$this->setError($block->getError());
			}

			// Set success message
			if ($block->get('_message'))
			{
				$this->set('_message', $block->get('_message'));
			}

			return true;
		}
	}
}
