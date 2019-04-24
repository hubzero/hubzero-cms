<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Quotes;

use Hubzero\Module\Module;
use Components\Feedback\Models\Quote;
use Component;
use Request;
use Date;

/**
 * Module class for displaying quotes
 */
class Helper extends Module
{
	/**
	 * Get module contents
	 *
	 * @return  void
	 */
	public function run()
	{
		require_once Component::path('com_feedback') . '/models/quote.php';

		//Get the admin configured settings
		$this->filters = array(
			'limit'         => trim($this->params->get('maxquotes')),
			'id'            => Request::getInt('quoteid', 0),
			'notable_quote' => 1
		);

		// Get quotes
		$sq = Quote::all()->whereEquals('notable_quote', 1);
		if ($this->filters['id'])
		{
			$sq->whereEquals('id', $this->filters['id']);
		}

		$this->quotes = $sq->limit($this->filters['limit'])->rows();

		require $this->getLayoutPath($this->module->module);
	}

	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		// Push the module CSS to the template
		$this->css()
		     ->js();

		if ($content = $this->getCacheContent())
		{
			echo $content;
			return;
		}

		$this->run();
	}
}
