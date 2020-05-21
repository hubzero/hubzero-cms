<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Models\Item;

use Components\Collections\Models\Item as GenericItem;
use Components\Collections\Models\Asset;
use Components\Publications\Models\Publication;
use Components\Publications\Helpers;
use Request;
use Route;
use Lang;

require_once dirname(__DIR__) . DS . 'item.php';

/**
 * Collections model for an item
 */
class Publications extends GenericItem
{
	/**
	 * Item type
	 *
	 * @var  string
	 */
	protected $_type = 'publication';

	/**
	 * Get the item type
	 *
	 * @param   string  $as  Return type as?
	 * @return  string
	 */
	public function type($as=null)
	{
		if ($as == 'title')
		{
			return Lang::txt('Resource');
		}
		return parent::type($as);
	}

	/**
	 * Check if we're on a URL where an item can be collected
	 *
	 * @return  boolean
	 */
	public function canCollect()
	{
		if (Request::getCmd('option') != 'com_publications')
		{
			return false;
		}

		if (!Request::getInt('id', 0))
		{
			if (!Request::getString('v', ''))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Add publication image as an asset
	 * 
	 * @param object  $resource  Publication model
	 * @return string
	 */
	private function addImage($resource)
	{
		// Get the image if exists
		$source = $resource->hasImage('master');

		// Default image
		if (!$source)
		{
			$source = PATH_APP . DS . trim($resource->config('masterimage', 'components/com_publications/site/assets/img/master.png'), DS);
		}

		$filename = basename($source);
		
		// Save publication image as asset
		// - Borrowing code from app/components/com_collections/site/controllers/media.php, ajaxUploadTask
		$asset = new Asset();
		
		// Define upload directory and make sure its writable
		$destination = $asset->filespace() . DS . $this->get('id');
		if (!is_dir($destination))
		{
			if (!Filesystem::makeDirectory($destination))
			{
				echo json_encode(array('error' => Lang::txt('COM_COLLECTIONS_ERROR_UNABLE_TO_COLLECT_PUBLICATION')));
				return;
			}
		}

		if (!is_writable($destination))
		{
			echo json_encode(array('error' => Lang::txt('COM_COLLECTIONS_ERROR_UNABLE_TO_COLLECT_PUBLICATION')));
			return;
		}

		$destination = $destination . DS . $filename;
		if (!copy($source, $destination))
		{
			echo json_encode(array('error' => Lang::txt('COM_COLLECTIONS_ERROR_UNABLE_TO_COLLECT_PUBLICATION')));
			return;
		}

		// Create database entry
		$asset->set('item_id', $this->get('id'));
		$asset->set('filename', $filename);
		if ($asset->image())
		{
			$hi = new \Hubzero\Image\Processor($destination);
			if (count($hi->getErrors()) == 0)
			{
				$hi->autoRotate();
				$hi->save();
			}
		}
		$asset->set('description', '');
		$asset->set('state', 1);
		$asset->set('type', 'file');

		if (!$asset->store())
		{
			echo json_encode(array(
				'error' => $asset->getError()
			));
			return;
		}
	}

	/**
	 * Create an item entry for a publication
	 *
	 * @param   integer  $id  Optional ID to use
	 * @return  boolean
	 */
	public function make($id=null)
	{
		if ($this->exists())
		{
			return true;
		}

		$id = ($id ?: Request::getInt('id', 0));
		$v = Request::getInt('v', 0);

		include_once \Component::path('com_publications') . DS . 'models' . DS . 'publication.php';
		$resource = null;

		if ($id && $v) {
			$resource = new \Components\Publications\Models\Publication($id, $v, null);
			$uid = $resource->version->id;
		} else {
			return false;
		}

		$this->_tbl->loadType($uid, $this->_type);

		if ($this->exists())
		{
			return true;
		}

		if (!$uid)
		{
			$this->setError(Lang::txt('Resource not found.'));
			return false;
		}

		$this->set('type', $this->_type)
		     ->set('object_id', $uid)
		     ->set('created', $resource->created)
		     ->set('created_by', $resource->created_by)
		     ->set('title', $resource->title)
		     ->set('description', $resource->abstract)
		     ->set('url', Route::url('index.php?option=com_publications&id=' . $id . '&v=' . $v));

		if (!$this->store())
		{
			return false;
		}

		$this->addImage($resource);

		return true;
	}
}
