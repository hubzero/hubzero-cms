<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\RandomQuote;

use Hubzero\Module\Module;
use Components\Feedback\Models\Quote;
use Component;

/**
 * Module class for displaying a random quote
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

		// Get the admin configured settings
		$this->charlimit  = $this->params->get('charlimit', 150);
		$this->showauthor = $this->params->get('show_author', 1);
		$this->showall    = $this->params->get('show_all_link', 1);

		$quotesrc = $this->params->get('quotesrc', 'miniquote');

		// Get quotes
		$quote = Quote::all()
			->whereEquals('notable_quote', ($this->params->get('quotepool') == 'notable_quotes' ?  1 : 0))
			->whereEquals('miniquote', ($quotesrc == 'miniquote' ?  1 : 0))
			->limit(1)
			->rows()
			->first();

		/*$quote = $sq->find('one', array(
			'limit'         => 1,
			'notable_quote' => ($this->params->get('quotepool') == 'notable_quotes' ?  1 : 0),
			'miniquote'     => ($quotesrc == 'miniquote' ?  1 : 0),
			'sort'          => 'RAND()',
			'sort_Dir'      => ''
		));*/

		if ($quote)
		{
			$this->quote_to_show = ($quotesrc == 'miniquote') ? stripslashes($quote->get('miniquote')) : stripslashes($quote->get('short_quote'));
		}
		else
		{
			$this->quote_to_show = '';
		}
		$this->quote = $quote;

		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}

	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		// Push some CSS to the template
		$this->css();

		if ($content = $this->getCacheContent())
		{
			echo $content;
			return;
		}

		$this->run();
	}
}
