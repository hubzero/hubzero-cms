<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\MySubmissions;

use Components\Resources\Models\Entry;
use Hubzero\Module\Module;
use Component;
use User;
use App;

/**
 * Module class for displaying a user's submissions and their progress
 */
class Helper extends Module
{
	/**
	 * Check if the type selection step is completed
	 *
	 * @param   object   $row  Resource
	 * @return  boolean  True if step completed
	 */
	public function step_type_check($row)
	{
		return ($row->id ? true : false);
	}

	/**
	 * Check if the compose step is completed
	 *
	 * @param   object   $row  Resource
	 * @return  boolean  True if step completed
	 */
	public function step_compose_check($row)
	{
		return ($row->id ? true : false);
	}

	/**
	 * Check if the attach step is completed
	 *
	 * @param   object   $row  Resource
	 * @return  boolean  True if step completed
	 */
	public function step_attach_check($row)
	{
		if ($row->id)
		{
			$total = $row->children()->total();
		}
		else
		{
			$total = 0;
		}
		return ($total ? true : false);
	}

	/**
	 * Check if the authors step is completed
	 *
	 * @param   object   $row  Resource
	 * @return  boolean  True if step completed
	 */
	public function step_authors_check($row)
	{
		if ($row->id)
		{
			$contributors = $row->authors()->total();
		}
		else
		{
			$contributors = 0;
		}

		return ($contributors ? true : false);
	}

	/**
	 * Check if the tags step is completed
	 *
	 * @param   object   $row  Resource
	 * @return  boolean  True if step completed
	 */
	public function step_tags_check($row)
	{
		$tags = $row->tags();

		if (count($tags) > 0)
		{
			return true;
		}

		return false;
	}

	/**
	 * Check if the review step is completed
	 *
	 * @param   object   $row  Resource
	 * @return  boolean  True if step completed
	 */
	public function step_review_check($row)
	{
		return false;
	}

	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		if (User::isGuest())
		{
			return false;
		}

		include_once Component::path('com_resources') . DS . 'models' . DS . 'entry.php';

		$this->steps = array(
			'Type',
			'Compose',
			'Attach',
			'Authors',
			'Tags',
			'Review'
		);

		$this->rows = Entry::all()
			->whereEquals('standalone', 1)
			->whereEquals('published', 2)
			->where('type', '!=', 7)
			->whereEquals('created_by', User::get('id'))
			->rows();

		require $this->getLayoutPath();
	}
}
