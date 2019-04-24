<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Featuredblog;

use Hubzero\Module\Module;
use Components\Blog\Models\Entry;

/**
 * Module class for displaying a random, featured blog entry
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		if ($content = $this->getCacheContent())
		{
			echo $content;
			return;
		}

		$this->run();
	}

	/**
	 * Build module contents
	 *
	 * @return  void
	 */
	public function run()
	{
		include_once \Component::path('com_blog') . DS . 'models' . DS . 'entry.php';

		$this->row = null;

		$filters = array(
			'state'      => 1,
			'access'     => 1,
			'sort'       => "RAND()",
			'sort_Dir'   => '',
			'search'     => '',
			'scope'      => 'member',
			'scope_id'   => 0,
			'authorized' => false
		);

		$row = Entry::all()
			->whereEquals('scope', $filters['scope'])
			->whereEquals('state', $filters['state'])
			->whereEquals('access', $filters['access'])
			->row();

		// Did we have a result to display?
		if ($row->get('id'))
		{
			$this->row = $row;
			$this->cls = trim($this->params->get('moduleclass_sfx'));
			$this->txt_length = trim($this->params->get('txt_length'));

			require $this->getLayoutPath();
		}
	}
}
